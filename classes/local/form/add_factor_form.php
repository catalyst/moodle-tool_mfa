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
 * Add factor form
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_mfa\local\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/formslib.php");

class add_factor_form extends \moodleform
{
    /**
     * {@inheritDoc}
     * @see moodleform::definition()
     */
    public function definition() {
        global $OUTPUT;
        $mform = $this->_form;

        $factorname = $this->_customdata['factorname'];
        $factor = \tool_mfa\plugininfo\factor::get_factor($factorname);
        $mform = $factor->add_factor_form_definition($mform);

        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $factorname = $this->_customdata['factorname'];
        $factor = \tool_mfa\plugininfo\factor::get_factor($factorname);
        $errors +=  $factor->add_factor_form_validation($data);

        return $errors;
    }

    function definition_after_data() {
        $mform = $this->_form;

        $factorname = $this->_customdata['factorname'];
        $factor = \tool_mfa\plugininfo\factor::get_factor($factorname);
        $mform = $factor->add_factor_form_definition_after_data($mform);
    }
}
