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

// Configure the lookback period for the report.
$days = optional_param('days', 0, PARAM_INT);
if ($days === 0) {
    $lookback = 0;
} else {
    $lookback = time() - (DAYSECS * $days);
}

$factors = \tool_mfa\plugininfo\factor::get_factors();

// Setup 2 arrays, one with internal names, one pretty.
$columns = array('');
$displaynames = $columns;
$colclasses = array('center');

// Force the first 2 columns to custom data.
$displaynames[] = get_string('totalusers', 'tool_mfa');
$displaynames[] = get_string('usersauthedinperiod', 'tool_mfa');
$colclasses[] = 'center';
$colclasses[] = 'center';

foreach ($factors as $factor) {
    $columns[] = $factor->name;
    $displaynames[] = get_string('pluginname', 'factor_'.$factor->name);
    $colclasses[] = 'right';
}

// Add total column to the end.
$displaynames[] = get_string('total');
$colclasses[] = 'center';

$table = new \html_table();
$table->head = $displaynames;
$table->align = $colclasses;

// Manually handle Total users and MFA users.
$alluserssql = "SELECT auth,
                       COUNT(id)
                  FROM {user}
                 WHERE deleted = 0
                   AND suspended = 0
              GROUP BY auth";
$allusersinfo = $DB->get_records_sql($alluserssql, []);

$mfauserssql = "SELECT auth,
                       COUNT(DISTINCT tm.userid)
                  FROM {tool_mfa} tm
                  JOIN {user} u ON u.id = tm.userid
                 WHERE tm.lastverified >= ?
                   AND u.deleted = 0
                   AND u.suspended = 0
              GROUP BY u.auth";
$mfausersinfo = $DB->get_records_sql($mfauserssql, [$lookback]);

$factorsusedsql = "SELECT CONCAT(u.auth, '_', tm.factor) as id,
                          COUNT(*)
                     FROM {tool_mfa} tm
                     JOIN {user} u ON u.id = tm.userid
                    WHERE tm.lastverified >= ?
                      AND u.deleted = 0
                      AND u.suspended = 0
                      AND (tm.revoked = 0 OR (tm.revoked = 1 AND tm.timemodified > ?))
                 GROUP BY CONCAT(u.auth, '_', tm.factor)";
$factorsusedinfo = $DB->get_records_sql($factorsusedsql, [$lookback, $lookback]);

// Auth rows.
$authtypes = get_enabled_auth_plugins(true);
foreach ($authtypes as $authtype) {
    $row = array();
    $row[] = \html_writer::tag('b', $authtype);

    // Setup the overall totals columns.
    $row[] = $allusersinfo[$authtype]->count ?? '-';
    $row[] = $mfausersinfo[$authtype]->count ?? '-';

    // Create a running counter for the total
    $authtotal = 0;

    // Now for each factor add the count from the factor query, and increment the running total.
    foreach ($columns as $column) {
        if (!empty($column)) {
            // Get the information from the data key.
            $key = $authtype . '_' . $column;
            $count = $factorsusedinfo[$key]->count ?? 0;
            $authtotal += $count;

            $row[] = $count ? format_float($count, 0) : '-';
        }
    }

    // Append the total of all factors to final column
    $row[] = $authtotal ? format_float($authtotal, 0) : '-';

    $table->data[] = $row;
}

// Total row.
$totals = [0 => html_writer::tag('b', get_string('total'))];
for ($colcounter = 1; $colcounter < count($row); $colcounter++) {
    $column = array_column($table->data, $colcounter);
    // Transform string to int forcibly, remove -.
    $column = array_map(function($element) {
        return $element === '-' ? 0 : (int) $element;
    }, $column);
    $columnsum = array_sum($column);
    $colvalue = $columnsum === 0 ? '-' : $columnsum;
    $totals[$colcounter] = $colvalue;
}
$table->data[] = $totals;

// Construct a select to use for viewing time periods.
$selectarr = [
    0 => get_string('alltime', 'tool_mfa'),
    1 => get_string('numday', '', 1),
    7 => get_string('numweek', '', 1),
    31 => get_string('nummonth', '', 1),
    90 => get_string('nummonths', '', 3),
    180 => get_string('nummonths', '', 6),
    365 => get_string('numyear', '', 1)
];
$select = new single_select($PAGE->url, 'days', $selectarr);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('factorreport', 'tool_mfa'));
echo html_writer::tag('p', get_string('selectperiod', 'tool_mfa'));
echo $OUTPUT->render($select);
echo html_writer::table($table);
echo $OUTPUT->footer();
