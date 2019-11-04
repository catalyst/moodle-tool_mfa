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

$wantsurl  = optional_param('wantsurl', '', PARAM_LOCALURL);

if (empty($wantsurl)) {
    $wantsurl = '/';
}

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/mfa/auth.php');
$PAGE->set_pagelayout('popup');
$pagetitle = $SITE->shortname.': '.get_string('mfa', 'tool_mfa');
$PAGE->set_title($pagetitle);


$OUTPUT = $PAGE->get_renderer('tool_mfa');

$params = array('wantsurl' => $wantsurl);
$currenturl = new moodle_url('/admin/tool/mfa/auth.php', $params);
$form = new login_form($currenturl);

if ($form->is_cancelled()) {
    tool_mfa_logout();
    redirect(new moodle_url('/'));
}

if ($form->is_submitted()) {
    if ($data = $form->get_data()) {
        $_SESSION['USER']->tool_mfa_authenticated = true;
        redirect(new moodle_url($wantsurl));
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'tool_mfa'));
$form->display();
echo $OUTPUT->footer();
