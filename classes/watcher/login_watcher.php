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

namespace tool_mfa\watcher;

defined('MOODLE_INTERNAL') || die();

use core\hook\after_require_login;
use core\hook\after_config;
use totara_core\hook\base;

// Hooks are Totara specific.
if (!class_exists(base::class)) {
    return;
}

require_once($CFG->dirroot . '/admin/tool/mfa/lib.php');

/**
 * Watches Totara hooks to ensure a user is authenticated.
 *
 * Forwards on the events until Totara implements:
 * `TODO - PLATFORM-117 to create a watcher to enable those functions via this hook`
 *
 * @package     tool_mfa
 * @author      Liam Kearney <liam@sproutlabs.com.au>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class login_watcher {
    /**
     * Forwards calls to tool_mfa_after_require_login.
     *
     * @param after_require_login $hook
     */
    public static function ensure_authenticated(after_require_login $hook) {
        tool_mfa_after_require_login(
            $hook->courseorid,
            $hook->autologinguest,
            $hook->cm,
            $hook->setwantsurltome,
            $hook->preventredirect
        );
    }

    /**
     * Forwards calls to tool_mfa_after_config.
     *
     * @param after_config $hook
     */
    public static function check_authenticated(after_config $hook) {
        tool_mfa_after_config();
    }
}
