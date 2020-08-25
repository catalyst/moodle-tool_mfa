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
 * @package     factor_u2f
 * @subpackage  tool_mfa
 * @author      Jan Dageförde, Laura Troost
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:revoke'] = 'Revoke U2F Factor';
$string['u2f:subject'] = 'Your confirmation code';
$string['u2f:message'] = 'You are trying to log in to Moodle. Your confirmation code is \'{$a->secret}\'.
     Alternatively you can click {$a->link} from the same device to authorise this session.';
$string['u2f:ipinfo'] = 'IP Information';
$string['u2f:originatingip'] = 'This login request was made from \'{$a}\'';
$string['u2f:message'] = 'This is your login code for {$a->sitename}: {$a->code}';
$string['u2f:uadescription'] = 'Browser identity for this request:';
$string['u2f:browseragent'] = 'The browser details for this request are: \'{$a}\'';
$string['u2f:revokelink'] = 'If this wasn\'t you, follow {$a} to stop this login attempt.';
$string['u2f:geoinfo'] = 'This request appears to have originated from approximately {$a->city}, {$a->country}.';
$string['u2f:link'] = 'this link';
$string['loginsubmit'] = 'submit U2F device';
$string['settings:suspend_help'] = '';
$string['settings:suspend'] = '';
$string['loginskip'] = "Use another authentication method";
$string['info'] = '<p>Built-in factor. Uses a U2F-compatible hardware key (e. g., Yubikey)</p>';
$string['pluginname'] = 'U2F hardware key factor';
$string['privacy:metadata'] = 'The U2F hardware key factor plugin does not store any personal data';
$string['setupfactor'] = 'U2F hardware key factor setup';
$string['verificationcode'] = 'Enter verification code for confirmation';
$string['verificationcode_help'] = 'Verification code has been sent to your Telegram account';
$string['summarycondition'] = 'has valid U2F setup';
$string['u2f:u2f_name'] = 'Name of the U2F device';
$string['error'] = 'Wrong token';