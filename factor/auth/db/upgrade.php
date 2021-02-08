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
 * factor_auth upgrade library.
 *
 * @package    factor_auth
 * @copyright  2021 Peter Burnett <peterburnett@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_factor_auth_upgrade($oldversion) {

    if ($oldversion < 2021020500) {
        $authtypes = get_enabled_auth_plugins(true);
        // Upgrade goodauth config from number to name.
        $goodauth = explode(',', get_config('factor_auth', 'goodauth'));
        $newauths = [];
        foreach ($goodauth as $auth) {
            $newauths[] = $authtypes[$auth];
        }
        set_config('goodauth', implode(',', $newauths), 'factor_auth');

        // MFA savepoint reached.
        upgrade_plugin_savepoint(true, 2021020500, 'factor', 'auth');
    }

    return true;
}