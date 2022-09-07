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

namespace factor_loginbanner;

use tool_mfa\local\factor\object_factor_base;

/**
 * Policy factor class.
 *
 * @package     factor_loginbanner
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /**
     * Login banner Factor implementation.
     * Factor is a singleton, can only be one instance.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_all_user_factors($user) {
        global $DB;
        $records = $DB->get_records('tool_mfa', ['userid' => $user->id, 'factor' => $this->name]);

        if (!empty($records)) {
            return $records;
        }

        // Null records returned, build new record.
        $record = [
            'userid' => $user->id,
            'factor' => $this->name,
            'timecreated' => time(),
            'createdfromip' => $user->lastip,
            'timemodified' => time(),
            'revoked' => 0,
        ];
        $record['id'] = $DB->insert_record('tool_mfa', $record, true);
        return [(object) $record];
    }

    /**
     * Login banner factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_input() {
        return true;
    }

    /**
     * Login banner factor implementation.
     *
     * @param \MoodleQuickForm $mform
     * @return object $mform
     */
    public function login_form_definition($mform) {
        $mform->addElement('html', get_string('policytext', 'factor_loginbanner'));
        return $mform;
    }

    /**
     * Login banner Factor implementation.
     * A cancel action is a decline for this factor.
     * This should result in an instant fail and unable to auth.
     *
     * {@inheritDoc}
     */
    public function process_cancel_action() {
        global $CFG;

        $this->set_state(\tool_mfa\plugininfo\factor::STATE_FAIL);
        \tool_mfa\manager::mfa_logout();
        redirect($CFG->wwwroot);
    }

    /**
     * Login banner factor implementation.
     *
     * @param \stdClass $user
     */
    public function possible_states($user) {
        // Policy can only return a pass or fail when known.
        return [
            \tool_mfa\plugininfo\factor::STATE_FAIL,
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_UNKNOWN,
        ];
    }
}
