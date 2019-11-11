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
 * User preferences page
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

use tool_mfa\local\form\preferences_form;

require_login(null, false);
if (isguestuser()) {
    throw new require_login_exception('Guests are not allowed here.');
}

$userid = optional_param('userid', $USER->id, PARAM_INT);
$user = core_user::get_user($userid);
if (!$user || !core_user::is_real_user($userid)) {
    throw new moodle_exception('invaliduser', 'error');
}

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/mfa/user_preferences.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('preferences:header', 'tool_mfa'));
$PAGE->set_cacheable(false);

if ($node = $PAGE->settingsnav->find('usercurrentsettings', null)) {
    $PAGE->navbar->add($node->get_content(), $node->action());
}
$PAGE->navbar->add(get_string('preferences:header', 'tool_mfa'));


$OUTPUT = $PAGE->get_renderer('tool_mfa');
$form = new preferences_form();

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
