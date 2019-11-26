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

if (empty($SESSION->wantsurl)) {
    $wantsurl = '/';
} else {
    $wantsurl = $SESSION->wantsurl;
}

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/mfa/auth.php');
$PAGE->set_pagelayout('secure');
$pagetitle = $SITE->shortname.': '.get_string('mfa', 'tool_mfa');
$PAGE->set_title($pagetitle);

$OUTPUT = $PAGE->get_renderer('tool_mfa');

$currenturl = new moodle_url('/admin/tool/mfa/auth.php');

// OVERALL LOGIC
// 1) Check state DONE
//      -- If pass, set pass state -> redirect on
//      -- If fail, cannot_login
//      else v
// 2) get the next factor DONE
// 2.5) Check if fallback factor -> cannot_login DONE
// 3) Display form for factor
// 4) Submit form
// 5) Submit actions ??


$state = \tool_mfa\manager::check_status();
if ($state == \tool_mfa\plugininfo\factor::STATE_PASS) {
    redirect(new moodle_url($wantsurl));
} else if ($state == \tool_mfa\plugininfo\factor::STATE_FAIL) {
    \tool_mfa\manager::cannot_login();
}

$factor = \tool_mfa\plugininfo\factor::get_next_user_factor();

if ($factor->get_state == \tool_mfa\plugininfo\factor::STATE_FAIL) {
    // Catch any instant fail state not caught by manager (fallback)
    \tool_mfa\manager::cannot_login();
}

/*$userfactors = \tool_mfa\plugininfo\factor::get_active_user_factor_types();
if (count($userfactors) > 0) {
    $nextfactor = \tool_mfa\plugininfo\factor::get_next_user_factor();
    $gracemode = false;
    $factorname = $nextfactor ? $nextfactor->name : null;
} else {
    $factorname = null;
    $gracemode = true;
}*/


$form = new login_form($currenturl, array('factor' => $nextfactor));
/*if (isset($nextfactor)) {
    $factor = \tool_mfa\plugininfo\factor::get_factor($factorname);
}*/

// KEEP
if ($form->is_submitted()) {
    $form->is_validated();

    if ($form->is_cancelled()) {
        $factor->set_state(\tool_mfa\plugininfo\factor::STATE_NEUTRAL);
    } else {
        if ($data = $form->get_data()) {
            $factor->set_state(\tool_mfa\plugininfo\factor::STATE_PASS);
        }
    }
}

if ($form->is_submitted()
    && (isset($factor) && $factor->get_state() != \tool_mfa\plugininfo\factor::STATE_FAIL)
    || !isset($factor)) {

    // RIPOUT
    if ($next = \tool_mfa\plugininfo\factor::get_next_user_factor()) {
        // Fallback factor means there are not enough factors setup or answered. Requires special handling.
        if ($next->name == 'fallback') {
            \tool_mfa\manager::mfa_logout();
        } else {
            redirect($currenturl);
        }
    }

    // RIPOUT
    if (tool_mfa_user_passed_enough_factors() || $gracemode) {

        \tool_mfa\manager::set_pass_state();

        if ($gracemode) {
            redirect(new moodle_url('/admin/tool/mfa/user_preferences.php'));
        }

        if (!empty($SESSION->wantsurl)) {
            unset($SESSION->wantsurl);
        }
        redirect(new moodle_url($wantsurl));
    }

    \tool_mfa\manager::mfa_logout();
}

echo $OUTPUT->header();

\tool_mfa\manager::display_debug_notification();

if ($gracemode || empty($factorname)) {
    echo $OUTPUT->heading(get_string('pluginname', 'tool_mfa'));
} else {
    echo $OUTPUT->heading(get_string('pluginname', 'factor_'.$factorname));
}

$form->display();
echo $OUTPUT->footer();
