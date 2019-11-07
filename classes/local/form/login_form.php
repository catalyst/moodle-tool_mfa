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

class login_form extends \moodleform
{
    /**
     * {@inheritDoc}
     * @see moodleform::definition()
     */
    public function definition() {
        $mform = $this->_form;
        $factorname = $this->_customdata['factor_name'];

        $mform->addElement('hidden', 'factor_name', $factorname);
        $mform->setType('factor_name', PARAM_ALPHA);

        if ($factorname == 'grace') {
            $mform = $this->define_grace_period_page($mform);
        } else {
            $factor = \tool_mfa\plugininfo\factor::get_factor($factorname);
            $mform = $factor->login_form_definition($mform);
            $this->add_action_buttons();
        }
    }

    function definition_after_data() {
        $mform = $this->_form;

        $factorname = $this->_customdata['factor_name'];
        $factor = \tool_mfa\plugininfo\factor::get_factor($factorname);
        if ($factor) {
            $mform = $factor->login_form_definition_after_data($mform);
        }
    }

    public function define_grace_period_page($mform) {
        global $OUTPUT;

        $mform->addElement('html', $OUTPUT->heading(get_string('graceperiod:notconfigured', 'tool_mfa'), 3));
        $mform->addElement('html', $OUTPUT->heading(get_string('graceperiod:canaccess', 'tool_mfa'), 5));

        // TODO: get grace period expiration date
        $mform->addElement('html', $OUTPUT->heading(get_string('graceperiod:expires', 'tool_mfa', time()), 5));
        $this->add_action_buttons(false, get_string('ok'));

        return $mform;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $factor = \tool_mfa\plugininfo\factor::get_factor($data['factor_name']);
        if ($factor) {
            $errors += $factor->login_form_validation($data);
        }

        return $errors;
    }
}
