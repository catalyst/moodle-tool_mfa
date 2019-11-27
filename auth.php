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
 * MFA page
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/admin/tool/mfa/lib.php');
require_once($CFG->libdir.'/adminlib.php');

use tool_mfa\local\form\login_form;

require_login(null, false);

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/mfa/auth.php');
$PAGE->set_pagelayout('secure');
$pagetitle = $SITE->shortname.': '.get_string('mfa', 'tool_mfa');
$PAGE->set_title($pagetitle);

$OUTPUT = $PAGE->get_renderer('tool_mfa');

$currenturl = new moodle_url('/admin/tool/mfa/auth.php');

// Perform state check.
\tool_mfa\manager::check_status();

// If not pass/fail, check next factor state.
$factor = \tool_mfa\plugininfo\factor::get_next_user_factor();
if ($factor->get_state() == \tool_mfa\plugininfo\factor::STATE_FAIL) {
    // Catch any instant fail state not caught by manager (fallback).
    \tool_mfa\manager::cannot_login();
}

// If not, perform form actions for input factor.
$form = new login_form($currenturl, array('factor' => $factor));
if ($form->is_submitted()) {
    $form->is_validated();

    // Set state from user actions.
    if ($form->is_cancelled()) {
        $factor->set_state(\tool_mfa\plugininfo\factor::STATE_NEUTRAL);
    } else {
        if ($data = $form->get_data()) {
            $factor->set_state(\tool_mfa\plugininfo\factor::STATE_PASS);
        }
    }

    // Move to next factor.
    \tool_mfa\manager::check_status(true);
}

echo $OUTPUT->header();

\tool_mfa\manager::display_debug_notification();

echo $OUTPUT->heading(get_string('pluginname', 'factor_'.$factor->name));
$form->display();
echo $OUTPUT->footer();
