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
 * MFA guidance page.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');

// No require_login, unauthenticated page.
$PAGE->set_context(\context_system::instance());
$PAGE->set_url(new moodle_url('/admin/tool/mfa/guide.php'));
$PAGE->set_title(get_string('guidance', 'tool_mfa'));
$PAGE->set_pagelayout('standard');

// If guidance page isn't enabled, just redir back to home.
if (!get_config('tool_mfa', 'guidance')) {
    redirect(new moodle_url('/'));
}

// Navigation. Target user preferences as previous node if authed.
if (isloggedin() && (!empty($SESSION->tool_mfa_authenticated) || $SESSION->tool_mfa_authenticated)) {
    if ($node = $PAGE->settingsnav->find('usercurrentsettings', null)) {
        $PAGE->navbar->add($node->get_content(), $node->action());
    }
    $PAGE->navbar->add(get_string('preferences:header', 'tool_mfa'), new \moodle_url('/admin/tool/mfa/user_preferences.php'));
} else {
    // Otherwise just point to site home.
    if ($node = $PAGE->settingsnav->find('home', null)) {
        $PAGE->navbar->add($node->get_content(), $node->action());
    }
}
$PAGE->navbar->add(get_string('guidance', 'tool_mfa'), new \moodle_url('/admin/tool/mfa/guide.php'));

echo $OUTPUT->header();
$html = get_config('tool_mfa', 'guidancecontent');

// We need to go through and replace file markups with a matching filename.
$fs = get_file_storage();
$context = context_system::instance();
$files = $fs->get_area_files($context->id, 'tool_mfa', 'guidance', 0, 'filepath, filename', false);
foreach ($files as $file) {
    $filename = $file->get_filename();
    $url = moodle_url::make_pluginfile_url(
        $file->get_contextid(),
        $file->get_component(),
        $file->get_filearea(),
        $file->get_itemid(),
        $file->get_filepath(),
        $file->get_filename()
    );
    $html = str_replace('{{' . $filename . '}}', html_writer::link($url, $url), $html);
}

echo format_text($html);
echo $OUTPUT->footer();
