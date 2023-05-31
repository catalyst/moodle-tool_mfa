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
 * @package     factor_webauthn
 * @author      Alex Morris <alex.morris@catalyst.net.nz>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'WebAuthn';
$string['summarycondition'] = 'using a WebAuthn supported authenticator';
$string['setupfactor'] = 'Setup authenticator';
$string['info'] = '<p>Use a WebAuthn supported authenticator</p>';
$string['settings:authenticatortypes'] = 'Types of Authenticator';
$string['settings:authenticatortypes_help'] = 'Toggle certain types of authenticators';
$string['authenticator:usb'] = 'USB';
$string['authenticator:nfc'] = 'NFC';
$string['authenticator:ble'] = 'BLE';
$string['authenticator:hybrid'] = 'Hybrid';
$string['authenticator:internal'] = 'Internal';
$string['settings:userverification'] = 'User Verification';
$string['settings:userverification_help'] = 'Serves to ensure the person authenticating is in fact who they say they are. User verification can take various forms, such as password, PIN, fingerprint, etc.';
$string['userverification:required'] = 'User verification is required (e.g. by pin). Authentication fails if key does not have user verification';
$string['userverification:preferred'] = 'User verification is preferred, authentication will not fail if user verification is missing';
$string['userverification:discouraged'] = 'User verification should not be employed, for example to minimize user interaction';
$string['error'] = 'Failed to authenticate';
$string['loginsubmit'] = 'Verify authenticator';
$string['loginskip'] = 'I don\'t have my authenticator';
$string['register'] = 'Register authenticator';
$string['action:revoke'] = 'Revoke authenticator';
$string['setupfactor'] = 'Setup authenticator';
$string['privacy:metadata'] = 'The WebAuthn factor plugin does not store any personal data';
