<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Email factor class.
 *
 * @package     factor_telegram
 * @subpackage  tool_mfa
 * @author      Jan Dageförde, Laura Troost
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_telegram;

defined('MOODLE_INTERNAL') || die();

use tool_mfa\local\factor\object_factor_base;

class factor extends object_factor_base {
    /**
     * User input for the generated code.
     *
     * {@inheritDoc}
     */
    public function login_form_definition($mform) {
        $mform->addElement('text', 'verificationcode', get_string('verificationcode', 'factor_telegram'));
        $mform->setType("verificationcode", PARAM_ALPHANUM);
        return $mform;
    }

    /**
     * Generate a token that is sent to the user via Telegram.
     *
     * {@inheritDoc}
     */
    public function login_form_definition_after_data($mform) {
        global $DB, $USER;

        // Get the user's Telegram ID from the tool_mfa configuration.
        $sql = 'SELECT *
                  FROM {tool_mfa}
                 WHERE userid = ?
                   AND factor = \'telegram\'
                   AND label LIKE \'telegram:%\'';
        $record = $DB->get_record_sql($sql, array($USER->id));
        if (empty($record)) {
            throw new \coding_exception('Factor has not been set up for this user!');
        }

        $telegramuserid = substr($record->label, strlen('telegram:'));

        // Send a random code to the user on Telegram.
        $this->generate_and_telegram_code($telegramuserid);
        return $mform;
    }

    /**
     * Validate the entered code.
     *
     * {@inheritDoc}
     */
    public function login_form_validation($data) {
        global $USER;
        $return = array();

        if (!$this->check_verification_code($data['verificationcode'])) {
            $return['verificationcode'] = get_string('error:wrongverification', 'factor_telegram');
        }

        return $return;
    }

    /**
     * Telegram Factor implementation.
     *
     * {@inheritDoc}
     */
    public function get_all_user_factors($user) {
        global $DB;

        $records = $DB->get_records('tool_mfa', array(
            'userid' => $user->id,
            'factor' => $this->name, // TODO look for prefix
        ));
        return $records;
    }

    public function has_setup() {
        return true;
    }

    /**
     * E-Mail Factor implementation.
     *
     * {@inheritDoc}
     */
    public function get_state() {
        global $USER;
        $userfactors = $this->get_active_user_factors($USER);

        // If no codes are setup then we must be neutral not unknown.
        if (count($userfactors) == 0) {
            return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        }

        return parent::get_state();
    }

    /**
     * Checks whether user telegram is correctly configured.
     *
     * @return bool
     */
    private static function is_ready() {
        global $DB, $USER;

        // If this factor is revoked, set to not ready.
        // Looking for prefix is not necessary: A single record with "revoked" is sufficient.
        if ($DB->record_exists('tool_mfa', array('userid' => $USER->id, 'factor' => 'telegram', 'revoked' => 1))) {
            return false;
        }
        return true;
    }

    /**
     * Generates and emails the code for login to the user, stores codes in DB.
     *
     * @return void
     */
    private function generate_and_telegram_code($telegramuserid) {
        global $DB, $USER, $CFG;

        // Get instance that isnt the parent type that defines the username.
        // This check must exclude the main singleton record, with the label that contains the userid.
        // It must only grab the record with the user agent as the label.
        $sql = 'SELECT *
                  FROM {tool_mfa}
                 WHERE userid = ?
                   AND factor = \'telegram\'
                   AND label NOT LIKE \'telegram:%\'';

        $record = $DB->get_record_sql($sql, array($USER->id));
        $duration = get_config('factor_telegram', 'duration');
        $newcode = random_int(100000, 999999);

        if (empty($record)) {
            // No code active, generate new code.
            $instanceid = $DB->insert_record('tool_mfa', array(
                'userid' => $USER->id,
                'factor' => 'telegram',
                'secret' => $newcode,
                'label' => $_SERVER['HTTP_USER_AGENT'],
                'timecreated' => time(),
                'createdfromip' => $USER->lastip,
                'timemodified' => time(),
                'lastverified' => time(),
                'revoked' => 0,
            ), true);
            $token = get_config('factor_telegram', 'telegrambottoken');
            $telegram = new telegram($token);
            $a = new \stdClass();
            $a->sitename = 'Moodle'; // TODO
            $a->code = $newcode;
            $message = get_string('telegram:message', 'factor_telegram', $a);
            $telegram->send_message($telegramuserid, $message);

        } else if ($record->timecreated + $duration < time()) {
            // Old code found. Keep id, update fields.
            $DB->update_record('tool_mfa', array(
                'id' => $record->id,
                'secret' => $newcode,
                'label' => $_SERVER['HTTP_USER_AGENT'],
                'timecreated' => time(),
                'createdfromip' => $USER->lastip,
                'timemodified' => time(),
                'lastverified' => time(),
                'revoked' => 0,
            ));
            $instanceid = $record->id;
            $token = get_config('factor_telegram', 'telegrambottoken');
            $telegram = new telegram($token);
            $a = new \stdClass();
            $a->sitename = 'Moodle'; // TODO
            $a->code = $newcode;
            $message = get_string('telegram:message', 'factor_telegram', $a);
            $telegram->send_message($telegramuserid, $message);
        }
    }

    /**
     * Verifies entered code against stored DB record.
     *
     * @return bool
     */
    private function check_verification_code($enteredcode) {
        global $DB, $USER;
        $duration = get_config('factor_telegram', 'duration');

        // Get instance that isnt parent email type (label check).
        // This check must exclude the main singleton record, with the label as the email.
        // It must only grab the record with the user agent as the label.
        $sql = 'SELECT *
                  FROM {tool_mfa}
                 WHERE userid = ?
                   AND factor = ?
                   AND label NOT LIKE \'telegram:%\'';
        $record = $DB->get_record_sql($sql, array($USER->id, 'telegram'));

        if ($enteredcode == $record->secret) {
            if ($record->timecreated + $duration > time()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cleans up email records once MFA passed.
     *
     * {@inheritDoc}
     */
    public function post_pass_state() {
        global $DB, $USER;
        // Delete all telegram records except base record.
        $selectsql = 'userid = ?
                  AND factor = ?
                   AND label NOT LIKE \'telegram:%\'';
        $DB->delete_records_select('tool_mfa', $selectsql, array($USER->id, 'telegram'));

        // Update factor timeverified.
        parent::post_pass_state();
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function setup_factor_form_definition($mform) {
        $mform->addElement('text', 'telegramuserid', get_string('telegram:telegramuserid', 'factor_telegram'));
        $mform->setType('telegramuserid', PARAM_ALPHANUM);

        return $mform;
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function setup_user_factor($data) {
        global $DB, $USER;

        $sql = 'SELECT *
                  FROM {tool_mfa}
                 WHERE userid = ?
                   AND factor = \'telegram\'
                   AND label LIKE \'telegram:%\'';
        $record = $DB->get_record_sql($sql, array($USER->id));

        if (!empty($data->telegramuserid)) {
            $row = new \stdClass();
            $row->userid = $USER->id;
            $row->factor = $this->name;
            $row->label = 'telegram:'.$data->telegramuserid;
            $row->timecreated = time();
            $row->createdfromip = $USER->lastip;
            $row->timemodified = time();
            $row->lastverified = time();
            $row->revoked = 0;

            if (empty($record)) {
                $id = $DB->insert_record('tool_mfa', $row);
            } else {
                $id = $row->id = $record->id;
                $DB->update_record('tool_mfa', $row);
            }

            $record = $DB->get_record('tool_mfa', array('id' => $id));
            $this->create_event_after_factor_setup($USER);

            return $record;
        }

        return null;
    }

    /**
     * Email factor implementation.
     *
     * {@inheritDoc}
     */
    public function possible_states($user) {
        // Email can return all states.
        return array(
            \tool_mfa\plugininfo\factor::STATE_FAIL,
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL,
            \tool_mfa\plugininfo\factor::STATE_UNKNOWN,
        );
    }
}
