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
 * Moodle MFA plugin lib
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function tool_mfa_after_require_login() {
    if (empty($_SESSION['USER']->tool_mfa_authenticated) || true !== $_SESSION['USER']->tool_mfa_authenticated) {
        if ($GLOBALS['ME'] != '/admin/tool/mfa/auth.php') {
            redirect(new moodle_url('/admin/tool/mfa/auth.php'));
        }
    }
}

function tool_mfa_logout() {
    $authsequence = get_enabled_auth_plugins();
    foreach($authsequence as $authname) {
        $authplugin = get_auth_plugin($authname);
        $authplugin->logoutpage_hook();
    }
    require_logout();
}

function tool_mfa_set_factor_config($data, $factor) {
    foreach ($data as $key => $value) {
        set_config($key, $value, $factor);
    }
}

function tool_mfa_factor_exists($factorname) {
    $factors = \tool_mfa\plugininfo\factor::get_factors();
    foreach ($factors as $factor) {
        if ($factorname == $factor->name) {
            return true;
        }
    }
    return false;
}

function tool_mfa_get_factor_actions() {
    $actions = \tool_mfa\plugininfo\factor::get_factor_actions();
    return $actions;
}

function tool_mfa_get_enabled_factors() {
    return \tool_mfa\plugininfo\factor::get_enabled_factors();
}

function tool_mfa_extend_navigation_user_settings($navigation, $user, $usercontext, $course, $coursecontext) {
    global $PAGE;

    // Only inject if user is on the preferences page
    $onpreferencepage = $PAGE->url->compare(new moodle_url('/user/preferences.php'), URL_MATCH_BASE);
    if (!$onpreferencepage) {
        return null;
    }

    $url = new moodle_url('/admin/tool/mfa/user_preferences.php');
    $node = navigation_node::create(get_string('preferences:header', 'tool_mfa'), $url,
        navigation_node::TYPE_SETTING);
    $usernode = $navigation->find('useraccount', navigation_node::TYPE_CONTAINER);
    $usernode->add_node($node);
}