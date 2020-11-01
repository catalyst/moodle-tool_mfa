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
 * Telegram Factor class.
 *
 * @package     factor_telegram
 * @subpackage  tool_mfa
 * @author      Jan Dageförde, Laura Troost
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_telegram;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use tool_mfa\local\factor\object_factor_base;

class factor extends object_factor_base {
    /**
     * Login form: Definition.
     *
     * {@inheritDoc}
     */
    public function login_form_definition($mform) {
        $mform->addElement(new \tool_mfa\local\form\verification_field());
        $mform->setType('verificationcode', PARAM_ALPHANUM);
        return $mform;
    }

    /**
     * Login form: Send a secret to the user and add a corresponding note to the form.
     *
     * {@inheritDoc}
     */
    public function login_form_definition_after_data($mform) {
        $instanceid = $this->generate_and_send_code();
        $mform = $this->add_redacted_sent_message($mform, $instanceid);
        // Disable the form check prompt.
        $mform->disable_form_change_checker();
        return $mform;
    }

    /**
     * Login form: Validate secret.
     *
     * {@inheritDoc}
     */
    public function login_form_validation($data) {
        $return = array();

        if (!$this->check_verification_code($data['verificationcode'])) {
            $return['verificationcode'] = get_string('wrongcode', 'factor_telegram');
        }

        return $return;
    }

    /**
     * Gets the string for setup button on preferences page.
     */
    public function get_setup_string() {
        return get_string('setupfactor', 'factor_telegram');
    }

    /**
     * Requires user input to verify.
     *
     * {@inheritDoc}
     */
    public function has_input() {
        return true;
    }

    /**
     * Requires user setup.
     *
     * {@inheritDoc}
     */
    public function has_setup() {
        return true;
    }

    /**
     * If there is already a factor setup, don't allow multiple (for now).
     *
     * {@inheritDoc}
     */
    public function show_setup_buttons() {
        global $DB, $USER;
        $sql = 'SELECT *
                  FROM {tool_mfa}
                 WHERE userid = ?
                   AND factor = ?
                   AND secret = ?
                   AND revoked = 0';

        $record = $DB->get_record_sql($sql, [$USER->id, $this->name, '']);
        return !empty($record) ? false : true;
    }

    /**
     * A factor can be revoked by a user.
     *
     * {@inheritDoc}
     */
    public function has_revoke() {
        return true;
    }

    /**
     * Verifies entered code against stored DB record.
     *
     * @return bool
     */
    private function check_verification_code($enteredcode) {
        $state = $this->secretmanager->validate_secret($enteredcode);
        if ($state === \tool_mfa\local\secret_manager::VALID) {
            return true;
        }
        return false;
    }

    /**
     * The Telegram factor can assume one of these states.
     *
     * {@inheritDoc}
     */
    public function possible_states($user) {
        return array(
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL,
            \tool_mfa\plugininfo\factor::STATE_FAIL,
            \tool_mfa\plugininfo\factor::STATE_UNKNOWN,
        );
    }
}
