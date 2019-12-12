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
 * MFA login form
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_mfa\local\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/formslib.php");

class login_form extends \moodleform {
    /**
     * {@inheritDoc}
     * @see moodleform::definition()
     */
    public function definition() {
        $mform = $this->_form;
        $factor = $this->_customdata['factor'];
        $mform = $factor->login_form_definition($mform);
    }

    /**
     * Invokes factor login_form_definition_after_data() method after form data has been set.
     *
     */
    public function definition_after_data() {
        $mform = $this->_form;
        $factor = $this->_customdata['factor'];

        $mform2 = $factor->login_form_definition_after_data($mform);

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('loginsubmit', 'factor_' . $factor->name));
        $buttonarray[] = &$mform->createElement('cancel', '', get_string('loginskip', 'factor_' . $factor->name));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Validates the login form with given factor validation method.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $factor = $this->_customdata['factor'];
        $errors += $factor->login_form_validation($data);

        // Execute sleep time bruteforce mitigation.
        \tool_mfa\manager::sleep_timer();

        return $errors;
    }
}
