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
