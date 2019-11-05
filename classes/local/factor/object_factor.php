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
 * MFA factor interface.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa\local\factor;

defined('MOODLE_INTERNAL') || die();

interface object_factor {
    public function is_enabled();
    public function get_weight();
    public function get_display_name();
    public function define_add_factor_form_definition($mform);
    public function define_add_factor_form_definition_after_data($mform);
    public function define_login_form($mform);
    public function validation($data);
    public function add_user_factor($data);
    public function get_all_user_factors($user);
    public function get_enabled_user_factors($user);
    public function verify($data);
}
