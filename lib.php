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

/**
 * Main hook.
 * If MFA Plugin is ready check tool_mfa_authenticated USER property and
 * start MFA authentication if it's not set or false.
 *
 * @return void
 * @throws \moodle_exception
 */
function tool_mfa_after_require_login() {
    global $SESSION, $ME, $CFG;

    if (!tool_mfa_ready()) {
        return;
    }

    if (empty($SESSION->tool_mfa_authenticated) || !$SESSION->tool_mfa_authenticated) {
        if (empty($SESSION->wantsurl)) {
            $SESSION->wantsurl = qualified_me();
            $SESSION->tool_mfa_setwantsurl = true;
        }

        $clearurl = str_replace($CFG->wwwroot, '', $ME);

        if (strpos($clearurl, '/admin/tool/mfa/') !== 0) {
            redirect(new moodle_url('/admin/tool/mfa/auth.php'));
        }
    }
}

/**
 * Checks if MFA Plugin is enabled and has enabled factor.
 * If plugin is disabled or there is no enabled factors,
 * it means there is nothing to do from user side.
 * Thus, login flow shouldn't be extended with MFA.
 *
 * @return bool
 * @throws \dml_exception
 */
function tool_mfa_ready() {

    if (!empty($CFG->upgraderunning)) {
        return;
    }

    $pluginenabled = get_config('tool_mfa', 'enabled');
    $enabledfactors = \tool_mfa\plugininfo\factor::get_enabled_factors();

    if (empty($pluginenabled) || count($enabledfactors) == 0) {
        return false;
    }

    return true;
}

/**
 * Logout user.
 *
 * @return void
 */
function tool_mfa_logout() {
    $authsequence = get_enabled_auth_plugins();
    foreach ($authsequence as $authname) {
        $authplugin = get_auth_plugin($authname);
        $authplugin->logoutpage_hook();
    }
    require_logout();
}

/**
 * Sets config variable for given factor.
 *
 * @param array $data
 * @param string $factor
 *
 * @return bool true or exception
 * @throws dml_exception
 */
function tool_mfa_set_factor_config($data, $factor) {
    foreach ($data as $key => $newvalue) {
        $oldvalue = get_config($factor, $key);
        if ($oldvalue != $newvalue) {
            set_config($key, $newvalue, $factor);
            add_to_config_log($key, $oldvalue, $newvalue, $factor);
        }
    }
    return true;
}

/**
 * Checks that given factor exists.
 *
 * @param string $factorname
 *
 * @return bool
 */
function tool_mfa_factor_exists($factorname) {
    $factors = \tool_mfa\plugininfo\factor::get_factors();
    foreach ($factors as $factor) {
        if ($factorname == $factor->name) {
            return true;
        }
    }
    return false;
}

/**
 * Extends navigation bar and injects MFA Preferences menu to user preferences.
 *
 * @param navigation_node $navigation
 * @param stdClass $user
 * @param context_user $usercontext
 * @param stdClass $course
 * @param context_course $coursecontext
 *
 * @return void or null
 * @throws \moodle_exception
 */
function tool_mfa_extend_navigation_user_settings($navigation, $user, $usercontext, $course, $coursecontext) {
    global $PAGE;

    // Only inject if user is on the preferences page.
    $onpreferencepage = $PAGE->url->compare(new moodle_url('/user/preferences.php'), URL_MATCH_BASE);
    if (!$onpreferencepage) {
        return null;
    }

    if (tool_mfa_ready()) {
        $url = new moodle_url('/admin/tool/mfa/user_preferences.php');
        $node = navigation_node::create(get_string('preferences:header', 'tool_mfa'), $url,
            navigation_node::TYPE_SETTING);
        $usernode = $navigation->find('useraccount', navigation_node::TYPE_CONTAINER);
        $usernode->add_node($node);
    }
}

/**
 * Checks that user passed enough factors to be authenticated.
 *
 * @return bool
 */
function tool_mfa_user_passed_enough_factors() {

    $totalweight = \tool_mfa\manager::get_total_weight();
    if ($totalweight >= 100) {
        return true;
    }

    return false;
}

/**
 * Changes the order for given factor.
 *
 * @param string $factorname
 * @param string $action
 *
 * @return void
 * @throws dml_exception
 */
function tool_mfa_change_factor_order($factorname, $action) {
    $order = explode(',', get_config('tool_mfa', 'factor_order'));
    $key = array_search($factorname, $order);

    switch ($action) {
        case 'up':
            if ($key >= 1) {
                $fsave = $order[$key];
                $order[$key] = $order[$key - 1];
                $order[$key - 1] = $fsave;
                tool_mfa_set_factor_config(array('factor_order' => implode(',', $order)), 'tool_mfa');
            }
            break;

        case 'down':
            if ($key < (count($order) - 1)) {
                $fsave = $order[$key];
                $order[$key] = $order[$key + 1];
                $order[$key + 1] = $fsave;
                tool_mfa_set_factor_config(array('factor_order' => implode(',', $order)), 'tool_mfa');
            }
            break;

        case 'enable':
            if (!$key) {
                $order[] = $factorname;
                tool_mfa_set_factor_config(array('factor_order' => implode(',', $order)), 'tool_mfa');
            }
            break;

        case 'disable':
            if ($key) {
                unset($order[$key]);
                tool_mfa_set_factor_config(array('factor_order' => implode(',', $order)), 'tool_mfa');
            }
            break;

        default:
            break;
    }
}
