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
 * Definition of MFA sub-plugins (factors).
 *
 * @package     tool_mfa
 * @author      Liam Kearney <liam@sproutlabs.com.au>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\hook\after_require_login;
use core\hook\after_config;
use tool_mfa\watcher\login_watcher;
use totara_core\hook\base;

// Hooks are Totara specific.
if (!class_exists(base::class)) {
    return;
}

$watchers = [
    [
        'hookname' => after_require_login::class,
        'callback' => [login_watcher::class, 'ensure_authenticated'],
        'includefile' => null,
        'priority' => 100,
    ],
    [
        'hookname' => after_config::class,
        'callback' => [login_watcher::class, 'check_authenticated'],
        'includefile' => null,
        'priority' => 100,
    ],
];

