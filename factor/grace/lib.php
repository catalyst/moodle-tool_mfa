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
 * Hook library
 *
 * @package     factor_grace
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function factor_grace_after_require_login() {
    global $USER;
    // Check if MFA is ready.
    if (tool_mfa_ready()) {
        // Check if a login time is already recorded.
        if (empty(get_user_preferences('factor_grace_first_login', null, $USER))) {
            set_user_preference('factor_grace_first_login', time(), $USER);
        }
    }
}

function factor_grace_mfa_post_pass_state() {
    global $SESSION, $USER;

    if (isset($SESSION->grace_message_shown) && $SESSION->grace_message_shown) {
        return;
    }

    // Ensure grace factor passed before displaying notification.
    $grace = \tool_mfa\plugininfo\factor::get_factor('grace');
    if ($grace->get_state() == \tool_mfa\plugininfo\factor::STATE_PASS) {
        $SESSION->grace_message_shown = true;

        $url = new moodle_url('/admin/tool/mfa/user_preferences.php');
        $link = \html_writer::link($url, get_string('preferences', 'factor_grace'));

        // Can never be null here, STATE_PASS above.
        $starttime = get_user_preferences('factor_grace_first_login', null, $USER);
        $timeremaining = ($starttime + get_config('factor_grace', 'graceperiod')) - time();
        $time = format_time($timeremaining);

        $data = array('url' => $link, 'time' => $time);
        $message = get_string('setupfactors', 'factor_grace', $data);
        \core\notification::info($message);
    }
}