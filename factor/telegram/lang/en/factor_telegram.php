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
 * Language strings.
 *
 * @package     factor_telegram
 * @subpackage  tool_mfa
 * @author      Jan Dageförde, Laura Troost
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['telegram:subject'] = 'Your confirmation code';
$string['telegram:message'] = 'You are trying to log in to Moodle. Your confirmation code is \'{$a->secret}\'.
     Alternatively you can click {$a->link} from the same device to authorise this session.';
$string['telegram:ipinfo'] = 'IP Information';
$string['telegram:originatingip'] = 'This login request was made from \'{$a}\'';
$string['telegram:uadescription'] = 'Browser identity for this request:';
$string['telegram:browseragent'] = 'The browser details for this request are: \'{$a}\'';
$string['telegram:revokelink'] = 'If this wasn\'t you, follow {$a} to stop this login attempt.';
$string['telegram:geoinfo'] = 'This request appears to have originated from approximately {$a->city}, {$a->country}.';
$string['telegram:link'] = 'this link';
$string['telegram:revokesuccess'] = 'This code has been successfully revoked. All sessions for {$a} have been ended.
    telegram will not be usable as a factor until account security has been verified.';
$string['telegram:accident'] = 'If you did not request this telegram, click continue to attempt to invalidate the login attempt.
    If you clicked this link by accident, click cancel, and no action will be taken.';
$string['settings:duration'] = 'Validity duration';
$string['settings:duration_help'] = 'The period of time that the code is valid.';
$string['settings:suspend'] = 'Suspend unauthorised accounts';
$string['settings:suspend_help'] = 'Check this to suspend user accounts if an unauthorised telegram verification is received.';
$string['event:unauthtelegram'] = 'Unauthorised telegram received';
$string['unauthtelegram'] = 'Unauthorised telegram';
$string['error:wrongverification'] = 'Incorrect verification code';
$string['error:badcode'] = 'Code was not found. This may be an old link, a new code may have been send to Telegram, or the login attempt with this code was successful.';
$string['error:parameters'] = 'Incorrect page parameters.';
$string['loginsubmit'] = 'Verify Code';
$string['loginskip'] = "I didn't receive a code";
$string['info'] = '<p>Built-in factor. Uses e-mail address mentioned in user profile for sending verification codes</p>';
$string['pluginname'] = 'Telegram Factor';
$string['privacy:metadata'] = 'The Telegram Factor plugin does not store any personal data';
$string['setupfactor'] = 'Telegram Factor setup';
$string['verificationcode'] = 'Enter verification code for confirmation';
$string['verificationcode_help'] = 'Verification code has been sent to your telegram address';
$string['summarycondition'] = 'has valid telegram setup';
$string['settings:telegrambottoken'] = 'Token of Telegram bot';