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

    $ADMIN->add('tools', new admin_category('toolmfafolder', new lang_string('pluginname', 'tool_mfa'), false));

    $settings = new admin_settingpage('managemfa', new lang_string('mfasettings', 'tool_mfa'));
    $settings->add(new \tool_mfa\local\admin_setting_managemfa());

    $name = new lang_string('graceperiod', 'tool_mfa');
    $description = new lang_string('graceperiod_help', 'tool_mfa');
    $settings->add(new admin_setting_configduration('tool_mfa/graceperiod', $name, $description, '604800'));

    $name = new lang_string('settings:enabled', 'tool_mfa');
    $description = new lang_string('settings:enabled_help', 'tool_mfa');
    $settings->add(new admin_setting_configcheckbox('tool_mfa/enabled', $name, '', false));

    $ADMIN->add('toolmfafolder', $settings);

    foreach (core_plugin_manager::instance()->get_plugins_of_type('factor') as $plugin) {
        $plugin->load_settings($ADMIN, 'toolmfafolder', $hassiteconfig);
    }
}