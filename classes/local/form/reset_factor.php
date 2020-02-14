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
 * Form to reset gracemode timer for users.
 *
 * @package     factor_grace
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa\local\form;

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

class reset_factor extends \moodleform {
    public function definition() {
        $mform = $this->_form;
        $factors = $this->_customdata['factors'];
        $factors = array_map(function ($element) {
            return $element->get_display_name();
        }, $factors);

        $mform->addElement('select', 'factor', get_string('selectfactor', 'tool_mfa'), $factors);

        $mform->addElement('text', 'resetfactor', get_string('resetfactor', 'tool_mfa'),
            array('placeholder' => get_string('resetfactorplaceholder', 'tool_mfa')));
        $mform->setType('resetfactor', PARAM_TEXT);
        $mform->addRule('resetfactor', get_string('userempty', 'tool_mfa'), 'required');

        $this->add_action_buttons(true, get_string('resetconfirm', 'tool_mfa'));
    }

    public function validation($data, $files) {
        global $DB, $SESSION;
        $errors = parent::validation($data, $files);

        $userinfo = $data['resetfactor'];
        // Try input as username first, then email.
        $user = $DB->get_record('user', array('username' => $userinfo));
        if (empty($user)) {
            // If not found, try username.
            $user = $DB->get_record('user', array('email' => $userinfo));
        }

        if (empty($user)) {
            $errors['resetfactor'] = get_string('usernotfound', 'tool_mfa');
        } else {
            $SESSION->tool_mfa_resetuser = $user;
        }

        return $errors;
    }
}
