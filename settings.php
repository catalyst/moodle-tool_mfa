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
 * Settings
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $externalpage = new admin_externalpage('tool_mfa_auth',
        get_string('totp:testpage', 'tool_mfa'),
        new moodle_url('/admin/tool/mfa/auth.php'));
    $ADMIN->add('tools', $externalpage);

    $managemfa = new admin_settingpage('managemfa', new lang_string('mfasettings', 'tool_mfa'));
    $managemfa->add(new \tool_mfa\local\admin_setting_managemfa());
    $ADMIN->add('tools', $managemfa);

    foreach (core_plugin_manager::instance()->get_plugins_of_type('factor') as $plugin) {
        $plugin->load_settings($ADMIN, 'tool_mfa', $hassiteconfig);
    }
}