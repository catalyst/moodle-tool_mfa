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
 * Reporting page for each factor vs auth type
 *
 * @package   tool_mfa
 * @author    Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright 2019 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('factorreport');

$PAGE->set_title(get_string('factorreport', 'tool_mfa'));
$PAGE->set_heading(get_string('factorreport', 'tool_mfa'));

$factors = \tool_mfa\plugininfo\factor::get_enabled_factors();

// Setup 2 arrays, one with internal names, one pretty.
$columns = array('');
$displaynames = $columns;
$colclasses = array('center');
foreach ($factors as $factor) {
    $columns[] = $factor->name;
    $displaynames[] = get_string('pluginname', 'factor_'.$factor->name);
    $colclasses[] = 'right';
}

$table = new \html_table();
$table->head = $displaynames;
$table->align = $colclasses;

// Auth rows.
$authtypes = get_enabled_auth_plugins(true);
foreach ($authtypes as $authtype) {
    $row = array();
    $row[] = \html_writer::tag('b', $authtype);
    foreach ($columns as $column) {
        if (!empty($column)) {
            // Else, write data.
            $sql = 'SELECT COUNT(DISTINCT tm.userid)
                    FROM {tool_mfa} tm
                    JOIN {user} u
                        ON tm.userid = u.id
                    WHERE u.auth = ? AND tm.factor = ?';
            $count = $DB->count_records_sql($sql, array($authtype, $column));
            $row[] = $count ? format_float($count, 0) : '-';
        }
    }
    $table->data[] = $row;
}

// Total row.
$totalrow = array(get_string('total'));
foreach ($columns as $column) {
    if (empty($column)) {
        continue;
    }
    $sql = 'SELECT COUNT(DISTINCT tm.userid)
              FROM {tool_mfa} tm
              JOIN {user} u
                ON tm.userid = u.id
             WHERE tm.factor = ?';

    $count = $DB->count_records_sql($sql, array($column));
    $totalrow[] = format_float($count, 0);
}
$totalrow = array_map(function ($element){
    return \html_writer::tag('b', $element);
}, $totalrow);
$table->data[] = $totalrow;

echo $OUTPUT->header();
echo \html_writer::table($table);
echo $OUTPUT->footer();

