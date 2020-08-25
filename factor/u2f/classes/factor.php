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
use u2flib_server\Error;
use u2flib_server\U2F;

class factor extends object_factor_base {
    /**
     * User input for the generated code.
     *
     * {@inheritDoc}
     */
    public function login_form_definition($mform) {
        global $PAGE, $CFG, $USER;

        $mform->addElement('hidden', 'request', '', ["id" => 'id_request']);
        $mform->setType('request', PARAM_RAW);
        $mform->addElement('hidden', 'response_input', '', ['id'=> 'id_response_input']);
        $mform->setType('response_input', PARAM_RAW);
        $renderer = $PAGE->get_renderer('core');

        $url = parse_url($CFG->wwwroot);
        $u2f = new U2F($url['scheme'].'://'.$url['host']);
        $factors = $this->get_all_user_factors($USER);
        $registrations = [];
        foreach ($factors as $f) {
            $registrations[] = json_decode($f->secret);
        }
        $request = $u2f->getAuthenticateData($registrations);

        $script = $renderer->render_from_template('factor_u2f/u2f-login', ['request' => json_encode($request)]);
        $mform->addElement('html', $script);
        //$mform->_form->getAttribute()
        return $mform;
    }


    /**
     * Validate the entered code.
     *
     * {@inheritDoc}
     */
    public function login_form_validation($data) {
        global $USER, $CFG, $DB;
        $return = array();
        if(empty($data['response_input'])) {
            $return['verificationcode'] = get_string('error', 'factor_u2f');
            return $return;
        }

        $factors = $this->get_all_user_factors($USER);
        $registrations = [];
        foreach ($factors as $f) {
            $registrations[$f->id] = json_decode($f->secret);
        }
        $url = parse_url($CFG->wwwroot);
        $u2f = new U2F($url['scheme'].'://'.$url['host']);
        try {
            $authentication = $u2f->doAuthenticate(json_decode($data['request']), $registrations, json_decode($data['response_input']));
            foreach ($registrations as $id => $registration) {
                if ($authentication->keyHandle === $registration->keyHandle) {
                    $row = $DB->get_record('tool_mfa', array('id' => $id));
                    $row->secret = json_encode($authentication);
                    $row->timemodified = time();
                    $row->lastverified = time();

                    $DB->update_record('tool_mfa', $row);

                    break;
                }
            }
        } catch (Error $e) {
            $return['verificationcode'] = get_string('error', 'factor_u2f'); //TODO
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
            'factor' => $this->name,
        ));
        return $records;
    }

    /**
     * Users need to set up their own credentials by registering their individual token(s).
     * @return bool
     */
    public function has_setup() {
        return true;
    }

    /**
     * Users are able to revoke individual U2F factors.
     * @return bool
     */
    public function has_revoke() {
        return true;
    }

    /**
     * U2F Factor implementation.
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
     * U2F Factor implementation.
     *
     * @param $mform
     */
    public function setup_factor_form_definition($mform) {
        global $PAGE, $CFG;



        // Prepare data for U2F request.
        $url = parse_url($CFG->wwwroot);

        if($url['scheme'] == "http"){
            $mform->addElement('html', '<div class="m-element-notification m-element-notification--warning">
            You should use https for u2f authentication.</div></div>');
        } else {
            $renderer = $PAGE->get_renderer('core');
            $pressbuttonhtml = $renderer->render_from_template('factor_u2f/press-button', []);

            $mform->addElement('text', 'u2f_name', get_string('u2f:u2f_name', 'factor_u2f'));
            $mform->setType('u2f_name', PARAM_ALPHANUM);
            $mform->addElement('html', $pressbuttonhtml);
            $mform->addElement('hidden', 'request', '', ["id" => 'id_request']);
            $mform->setType('request', PARAM_RAW);
            $mform->addElement('hidden', 'response_input', '', ['id'=> 'id_response_input']);
            $mform->setType('response_input', PARAM_RAW);
            $u2f = new U2F($url['scheme'].'://'.$url['host']);
            $data = $u2f->getRegisterData([]);
            list($request, $signatures) = $data;

            $script = $renderer->render_from_template('factor_u2f/u2f-registration', ['wwwroot' => $CFG->wwwroot,
                'request' => json_encode($request), 'signatures' => json_encode($signatures)]);
            $mform->addElement('html', $script);
        }
        return $mform;
    }

    /**
     * U2F Factor implementation.
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
     * U2F factor implementation.
     *
     * {@inheritDoc}
     */
    public function possible_states($user) {
        // U2F can return all states.
        return array(
            \tool_mfa\plugininfo\factor::STATE_FAIL,
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL,
            \tool_mfa\plugininfo\factor::STATE_UNKNOWN,
        );
    }
}
