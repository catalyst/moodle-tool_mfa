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
 * @package     factor_totp
 * @subpackage  tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:revoke'] = 'Revoke TOTP Factor';
$string['devicename'] = 'Device label';
$string['devicenameexample'] = 'eg "Work iPhone 11"';
$string['devicename_help'] = 'This is the device you have an authenticator app installed on. You can setup multiple devices so this label helps track which ones are being used. You should setup each device with their own unique code so they can be revoked separately.';
$string['error:wrongverification'] = 'Incorrect verification code';
$string['info'] = '<p>Use any TOTP Authenticator app to get a verification code on your phone even when it is offline.</p>
<p>eg Google Authenticator for <a href="https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8" target="_blank">iPhone</a> or <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</a></p>';
$string['loginsubmit'] = 'Verify code';
$string['loginskip'] = 'I don\'t have my device';
$string['pluginname'] = 'Authenticator app';
$string['privacy:metadata'] = 'The TOTP Factor plugin does not store any personal data';
$string['settings:secretlength'] = 'TOTP secret key length';
$string['settings:secretlength_help'] = 'Generated TOTP secret key string length';
$string['setupfactor'] = 'TOTP Factor Setup';
$string['setupfactor:scan'] = 'Enter secret or scan QR code';
$string['setupfactor:key'] = 'Secret key: ';
$string['verificationcode'] = 'Enter your 6 digit verification code';
$string['verificationcode_help'] = 'Open your Authenticator app such as Google Authenticator and look for the 6 digit code which matches this site and username';
$string['summarycondition'] = 'using a TOTP app';
$string['factorsetup'] = 'Setup App';
