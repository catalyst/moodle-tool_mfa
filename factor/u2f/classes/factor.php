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
 * U2F factor class (yubikey etc.).
 *
 * @package     factor_u2f
 * @subpackage  tool_mfa
 * @author      Jan Dageförde, Laura Troost
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_u2f;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot."/admin/tool/mfa/factor/u2f/thirdparty/vendor/autoload.php");

use moodleform;
use tool_mfa\local\factor\object_factor_base;
use u2flib_server\U2F;

class factor extends object_factor_base {
    /**
     * User input for the generated code.
     *
     * {@inheritDoc}
     */
    public function login_form_definition($mform) {
        $mform->addElement('text', 'verificationcode', get_string('verificationcode', 'factor_u2f'));
        $mform->setType("verificationcode", PARAM_ALPHANUM);
        return $mform;
    }

    /**
     * Generate a token that is sent to the user via u2f.
     *
     * {@inheritDoc}
     */
    public function login_form_definition_after_data($mform) {
        global $DB, $USER;

        // Get the user's u2f ID from the tool_mfa configuration.
        $sql = 'SELECT *
                  FROM {tool_mfa}
                 WHERE userid = ?
                   AND factor = \'u2f\'
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
            $return['verificationcode'] = get_string('error:wrongverification', 'factor_u2f');
        }

        return $return;
    }

    /**
     * U2F Factor implementation.
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
     * Checks whether user u2f is correctly configured.
     *
     * @return bool
     */
    private static function is_ready() {
        global $DB, $USER;

        // If this factor is revoked, set to not ready.
        // Looking for prefix is not necessary: A single record with "revoked" is sufficient.
        if ($DB->record_exists('tool_mfa', array('userid' => $USER->id, 'factor' => 'u2f', 'revoked' => 1))) {
            return false;
        }
        return true;
    }



    /**
     * Cleans up email records once MFA passed.
     *
     * {@inheritDoc}
     */
    public function post_pass_state() {
        global $DB, $USER;
        // Delete all u2f records except base record.
        $selectsql = 'userid = ?
                  AND factor = ?
                   AND label NOT LIKE \'telegram:%\'';
        $DB->delete_records_select('tool_mfa', $selectsql, array($USER->id, 'u2f'));

        // Update factor timeverified.
        parent::post_pass_state();
    }

    /**
     * TOTP Factor implementation.
     *
     * @param $mform
     */
    public function setup_factor_form_definition($mform) {
        global $PAGE, $CFG, $SITE;

        $mform->addElement('text', 'u2f_name', get_string('u2f:u2f_name', 'factor_u2f'));
        $mform->setType('u2f_name', PARAM_ALPHANUM);
        $mform->addElement('hidden', 'request', '', ["id" => 'id_request']);
        $mform->setType('request', PARAM_RAW);
        $mform->addElement('hidden', 'response_input', '', ['id'=> 'id_response_input']);
        $mform->setType('response_input', PARAM_RAW);
        $renderer = $PAGE->get_renderer('core');

        $url = parse_url($CFG->wwwroot);
        $u2f = new U2F($url['scheme'].'://'.$url['host']);
        $data = $u2f->getRegisterData([]);  //TODO
        list($request, $signatures) = $data;

        $script = $renderer->render_from_template('factor_u2f/u2f-registration', ['wwwroot' => $CFG->wwwroot,
            'request' => json_encode($request), 'signatures' => json_encode($signatures)]);
        $mform->addElement('html', $script);
        //$mform->_form->getAttribute()
        return $mform;
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function setup_user_factor($data) {
        global $DB, $USER, $CFG;

        if (!empty($data->u2f_name)) {
            $url = parse_url($CFG->wwwroot);
            $u2f = new U2F($url['scheme'].'://'.$url['host']);
            $registration = $u2f->doRegister(json_decode($data->request), json_decode($data->response_input));
            $row = new \stdClass();
            $row->userid = $USER->id;
            $row->factor = $this->name;
            $row->label = $data->u2f_name;
            $row->secret = json_encode($registration);
            $row->timecreated = time();
            $row->createdfromip = $USER->lastip;
            $row->timemodified = time();
            $row->lastverified = time();
            $row->revoked = 0;

            $id = $DB->insert_record('tool_mfa', $row);

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
