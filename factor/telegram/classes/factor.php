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
     * Telegram Factor implementation.
     *
     * {@inheritDoc}
     */
    public function login_form_definition($mform) {

        $mform->addElement('text', 'verificationcode', get_string('verificationcode', 'factor_telegram'));
        $mform->setType("verificationcode", PARAM_ALPHANUM);
        return $mform;
    }

    /**
     * E-Mail Factor implementation.
     *
     * {@inheritDoc}
     */
    public function login_form_definition_after_data($mform) {
        $this->generate_and_telegram_code();
        return $mform;
    }

    /**
     * E-Mail Factor implementation.
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
            'factor' => $this->name,
            'label' => $user->telegram
        ));

        if (!empty($records)) {
            return $records;
        }

        // Null records returned, build new record.
        $record = array(
            'userid' => $user->id,
            'factor' => $this->name,
            'label' => $user->telegram,
            'createdfromip' => $user->lastip,
            'timecreated' => time(),
            'revoked' => 0,
        );
        $record['id'] = $DB->insert_record('tool_mfa', $record, true);
        return [(object) $record];
    }

    /**
     * E-Mail Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_input() {
        if (self::is_ready()) {
            return true;
        }
        return false;
    }

    /**
     * E-Mail Factor implementation.
     *
     * {@inheritDoc}
     */
    public function get_state() {
        if (!self::is_ready()) {
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

        if (empty($USER->email)) { //TODO
            return false;
        }
        if (!validate_email($USER->email)) { //TODO
            return false;
        }
        if (over_bounce_threshold($USER)) {
            return false;
        }

        // If this factor is revoked, set to not ready.
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
    private function generate_and_telegram_code() {
        global $DB, $USER;

        // Get instance that isnt parent email type (label check).
        // This check must exclude the main singleton record, with the label as the email.
        // It must only grab the record with the user agent as the label.
        $sql = 'SELECT *
                  FROM {tool_mfa}
                 WHERE userid = ?
                   AND factor = ?
               AND NOT label = ?';

        $record = $DB->get_record_sql($sql, array($USER->id, 'telegram', $USER->email)); //TODO
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
               AND NOT label = ?';
        $record = $DB->get_record_sql($sql, array($USER->id, 'telegram', $USER->email)); //TODO

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
        // Delete all email records except base record.
        $selectsql = 'userid = ?
                  AND factor = ?
              AND NOT label = ?';
        $DB->delete_records_select('tool_mfa', $selectsql, array($USER->id, 'telegram', $USER->email)); //TODO

        // Update factor timeverified.
        parent::post_pass_state();
    }

    /**
     * Email factor implementation.
     * Email page must be safe to authorise session from link.
     *
     * {@inheritDoc}
     */
    public function get_no_redirect_urls() {
        $telegram = new \moodle_url('/admin/tool/mfa/factor/telegram/telegram.php');
        return array($telegram);
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
