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
//
/**
 * Strings for component 'tool_mfa', language 'en'.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['mfa'] = 'MFA';
$string['pluginname'] = 'Moodle MFA plugin';
$string['settings'] = 'MFA settings';
$string['settings:error:weight'] = 'Factor weight should be between 0 and 100';
$string['settings:error:totalweight'] = 'Weight sum of all enabled factors should be more than 100';
//$string['header'] = 'You don\'t have your 2FA configured. Please, scan this QR code and enter the code below for confirmation';

$string['totp:testpage'] = 'TOTP Test page';
$string['totp:header'] = 'TOTP Check';
$string['totp:verification_code'] = 'Enter verification code';
$string['totp:verification_code_help'] = 'Enter verification code for confirmation';
$string['totp:error:verification_code'] = 'Verification code is wrong';


$string['privacy:metadata'] = 'Moodle MFA plugin does not store any personal data';