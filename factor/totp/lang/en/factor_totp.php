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

$string['pluginname'] = 'TOTP Factor';

$string['settings:enable'] = 'Enable TOTP Factor';
$string['settings:enable_help'] = 'TOTP Factor will be available for users to setup';
$string['settings:weight'] = 'TOTP Factor weight';
$string['settings:weight_help'] = 'Factor weight determines the result factor combinations';
$string['settings:secretlength'] = 'TOTP secret key length';
$string['settings:secretlength_help'] = 'Generated TOTP secret key string length';
$string['verificationcode'] = 'Enter 6-digit verification code for confirmation';
$string['verificationcode_help'] = 'Code validation is necessary to confirm you stored your secret key with GA app';
$string['preferredname'] = 'Device label eg "Work iPhone"';
$string['preferredname_help'] = 'This is the device you have an authenticator app installed on. You can setup multiple devices so this label helps track which ones are being used. You should setup each device with it\'s own codes so they are easier to revoke.';

$string['addingfactor'] = 'Adding TOTP Factor';
$string['addingfactor:scan'] = 'Scan QR-Code or enter a secret key to your Google Authenticator';
$string['addingfactor:key'] = 'Secret key: ';
$string['error:wrongverification'] = 'Incorrect verification code';

$string['privacy:metadata'] = 'The TOTP Factor plugin does not store any personal data';

