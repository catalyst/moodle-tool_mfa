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
 * Auth factor class.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_auth;

defined('MOODLE_INTERNAL') || die();

use tool_mfa\local\factor\object_factor_base;

class factor extends object_factor_base {

    public function define_factor_settings($mform) {

        $mform->addElement('header', 'authheader', get_string('settings:header', 'factor_auth'));
        $mform->setExpanded('authheader');

        $mform->addElement('text', 'authweight', get_string('settings:weight', 'factor_auth'));
        $mform->addHelpButton('authweight', 'settings:weight', 'factor_auth');
        $mform->setType("authweight", PARAM_INT);

        $mform->addElement('advcheckbox', 'authenable', get_string('settings:enable', 'factor_auth'));
        $mform->addHelpButton('authenable', 'settings:enable', 'factor_auth');
        $mform->setType("authenable", PARAM_INT);
        
        return $mform;
    }
}