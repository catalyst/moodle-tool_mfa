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
 * Page to revoke and disable an email code.
 *
 * @package     factor_email
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Ignore coding standards for login check, this page does not require login.
// @codingStandardsIgnoreStart
require_once(__DIR__ . '../../../../../../config.php');
$instanceid = required_param('instance', PARAM_INT);
$pass = optional_param('pass', '0', PARAM_INT);
$secret = optional_param('secret', 0, PARAM_INT);
// @codingStandardsIgnoreEnds

$context = context_system::instance();
$PAGE->set_context($context);
$url = new moodle_url('/admin/tool/mfa/factor/email/email.php',
    array('instance' => $instanceid, 'pass' => $pass, 'secret' => $secret));
$PAGE->set_url($url);
$PAGE->set_pagelayout('secure');
$PAGE->set_title(get_string('unauthemail', 'factor_email'));
$PAGE->set_cacheable(false);
$instance = $DB->get_record('tool_mfa', array('id' => $instanceid));

// If pass is set, require login to force $SESSION and user, and pass for that session.
if (!empty($instance) && $pass != 0 && $secret != 0) {
    if ($instance->secret != $secret) {
        print_error('error:parameters');
        die;
    }
    require_login();
    $factor = \tool_mfa\plugininfo\factor::get_factor('email');
    $factor->set_state(\tool_mfa\plugininfo\factor::STATE_PASS);
    // If wantsurl is already set in session, go to it.
    if (!empty($SESSION->wantsurl)) {
        redirect($SESSION->wantsurl);
    } else {
        redirect(new moodle_url('/'));
    }
}

$form = new \factor_email\form\email($url);

if ($form->is_cancelled()) {

    redirect(new moodle_url('/'));
} else if ($fromform = $form->get_data()) {
    if (empty($instance)) {
        $message = get_string('error:badcode', 'factor_email');
    } else {
        $user = $DB->get_record('user', array('id' => $instance->userid));

        // Stop attacker from using email factor at all, by revoking all email until admin fixes.
        $DB->set_field('tool_mfa', 'revoked', 1, array('userid' => $user->id, 'factor' => 'email'));

        // Remotely logout all sessions for user.
        $manager = \core\session\manager::kill_user_sessions($instance->userid);

        // Log event.
        $ip = $instance->createdfromip;
        $useragent = $instance->label;
        $event = \factor_email\event\unauth_email::unauth_email_event($user, $ip, $useragent);
        $event->trigger();

        // Suspend user account.
        if (get_config('factor_email', 'suspend')) {
            $DB->set_field('user', 'suspended', 1, array('id' => $userid));
        }

        $message = get_string('email:revokesuccess', 'factor_email', fullname($user));
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('unauthemail', 'factor_email'));
if (!empty($message)) {
    echo $message;
} else {
    $form->display();
}
echo $OUTPUT->footer();
