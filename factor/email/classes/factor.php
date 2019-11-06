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
 * @package     factor_email
 * @subpackage  tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_email;

defined('MOODLE_INTERNAL') || die();

use tool_mfa\local\factor\object_factor_base;

class factor extends object_factor_base {

    public function define_add_factor_form_definition($mform) {
        global $OUTPUT;

        $mform->addElement('html', $OUTPUT->heading(get_string('addingfactor', 'factor_email'), 3));

        $mform->addElement('text', 'useremail', get_string('useremail', 'factor_email'));
        $mform->addHelpButton('useremail', 'useremail', 'factor_email');
        $mform->setType("useremail", PARAM_EMAIL);
        $mform->addRule('useremail', get_string('required'), 'required', null, 'client');

        return $mform;
    }

    public function add_user_factor($data) {
        global $DB, $USER;

        if (!empty($data->useremail)) {
            $row = new \stdClass();
            $row->userid = $USER->id;
            $row->useremail = $data->useremail;
            $row->timecreated = time();
            $row->timemodified = time();
            $row->disabled = 0;

            $DB->insert_record('tool_mfa_factor_email', $row);
            return true;
        }

        return false;
    }

    public function get_all_user_factors($user) {
        global $DB;
        $sql = "SELECT id, 'email' AS name, useremail, timecreated, timemodified, disabled
                  FROM {tool_mfa_factor_email}
                 WHERE userid = ?
              ORDER BY disabled, timemodified";

        $return = $DB->get_records_sql($sql, array($user));
        return $return;
    }
}
