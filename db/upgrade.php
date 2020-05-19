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
 * MFA upgrade library.
 *
 * @package    tool_mfa
 * @copyright  2020 Peter Burnett <peterburnett@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_tool_mfa_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020050700) {

        // Define field lockcounter to be added to tool_mfa.
        $table = new xmldb_table('tool_mfa');
        $field = new xmldb_field('lockcounter', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '0', 'revoked');

        // Conditionally launch add field lockcounter.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // MFA savepoint reached.
        upgrade_plugin_savepoint(true, 2020050700, 'tool', 'mfa');
    }

    if ($oldversion < 2020051900) {

        // Define index userid (not unique) to be added to tool_mfa.
        $table = new xmldb_table('tool_mfa');
        $index = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        // Conditionally launch add index userid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index factor (not unique) to be added to tool_mfa.
        $table = new xmldb_table('tool_mfa');
        $index = new xmldb_index('factor', XMLDB_INDEX_NOTUNIQUE, array('factor'));

        // Conditionally launch add index factor.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index lockcounter (not unique) to be added to tool_mfa.
        $table = new xmldb_table('tool_mfa');
        $index = new xmldb_index('lockcounter', XMLDB_INDEX_NOTUNIQUE, array('userid', 'factor', 'lockcounter'));

        // Conditionally launch add index lockcounter.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Mfa savepoint reached.
        upgrade_plugin_savepoint(true, 2020051900, 'tool', 'mfa');
    }
}
