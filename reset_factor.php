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
 * Page to reset factor for users.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
admin_externalpage_setup('tool_mfa_resetfactor');

if (!empty($SESSION->bulk_users)) {
    $bulk = true;
    $users = array_map(function($user) {
        return \core_user::get_user($user);
    }, $SESSION->bulk_users);
} else {
    $bulk = false;
}

$factors = \tool_mfa\plugininfo\factor::get_factors();
$form = new \tool_mfa\local\form\reset_factor(null, array('factors' => $factors, 'bulk' => $bulk));

if ($form->is_cancelled()) {
    if ($bulk) {
        $url = new moodle_url('/admin/user/user_bulk.php');
    } else {
        $url = new moodle_url('/admin/category.php', ['category' => 'toolmfafolder']);
    }
    redirect($url);
} else if ($fromform = $form->get_data()) {
    // Get factor from select index.
    $factor = $factors[$fromform->factor];

    // Setup user array for bulk action.
    $users = $bulk ? $users : [$fromform->user];

    foreach($users as $user) {
        $factor->delete_factor_for_user($user);

        // Add a user preference, to display a notification to the user that their factor was reset.
        $prefname = 'tool_mfa_reset_' . $factor->name;
        set_user_preference($prefname, true, $user);

        // If we are just doing 1 user.
        if (!$bulk) {
            $stringarr = array('factor' => $factor->get_display_name(), 'username' => $user->username);
            \core\notification::success(get_string('resetsuccess', 'tool_mfa', $stringarr));

            // Reload page.
            redirect($PAGE->url);
        }
    }

    \core\notification::success(get_string('resetsuccessbulk', 'tool_mfa', $factor->get_display_name()));
    unset($SESSION->bulk_users);
    // Redirect to bulk actions page.
    redirect(new moodle_url('/admin/user/user_bulk.php'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('resetfactor', 'tool_mfa'));
$form->display();
echo $OUTPUT->footer();
