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
 * IP Range factor class.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_iprange;

defined('MOODLE_INTERNAL') || die();

use tool_mfa\local\factor\object_factor_base;

class factor extends object_factor_base {

    public function define_factor_settings($mform) {

        $mform->addElement('header', 'iprangeheader', get_string('settings:header', 'factor_iprange'));
        $mform->setExpanded('iprangeheader');

        $mform->addElement('text', 'iprangeweight', get_string('settings:weight', 'factor_iprange'));
        $mform->addHelpButton('iprangeweight', 'settings:weight', 'factor_iprange');
        $mform->setType("iprangeweight", PARAM_INT);

        $mform->addElement('advcheckbox', 'iprangeenable', get_string('settings:enable', 'factor_iprange'));
        $mform->addHelpButton('iprangeenable', 'settings:enable', 'factor_iprange');
        $mform->setType("iprangeenable", PARAM_INT);

        return $mform;
    }
}