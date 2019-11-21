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

$string['achievedweight'] = 'Achieved weight';
$string['combination'] = 'Combination';
$string['created'] = 'Created';
$string['createdfromip'] = 'Created from IP';
$string['debugmode:heading'] = 'Debug mode';
$string['debugmode:currentweight'] = 'Current weight: {$a}';
$string['devicename'] = 'Device';
$string['enablefactor'] = 'Enable factor';
$string['error:directaccess'] = 'This page shouldn\'t be accessed directly';
$string['error:factornotfound'] = 'MFA Factor \'{$a}\' not found';
$string['error:wrongfactorid'] = 'Factor id \'{$a}\' is incorrect';
$string['error:actionnotfound'] = 'Action \'{$a}\' not supported';
$string['error:setupfactor'] = 'Can not setup factor';
$string['error:revoke'] = 'Can not revoke factor';
$string['error:notenoughfactors'] = 'Not enough factors to authenticate';
$string['error:factornotenabled'] = 'MFA Factor \'{$a}\' not enabled';
$string['event:userpassedmfa'] = 'Verification passed';
$string['event:userrevokedfactor'] = 'Factor revocation';
$string['event:usersetupfactor'] = 'Factor setup';
$string['factor'] = 'Factor';
$string['graceperiod'] = 'Grace period';
$string['graceperiod_help'] = 'Period of time when users can access Moodle without configured and enabled factors';
$string['graceperiod:notconfigured'] = 'You haven\'t configured your MFA plugin factors';
$string['graceperiod:canaccess'] = 'You still can access Moodle until your grace period is expired';
$string['graceperiod:expires'] = 'Grace period expires \'{$a}\'';
$string['graceperiod:redirect'] = 'You will be redirected now to user preferences page to configure your factors';
$string['lastverified'] = 'Last verified';
$string['mfa'] = 'MFA';
$string['mfasettings'] = 'Manage MFA';
$string['na'] = 'n/a';
$string['overall'] = 'Overall';
$string['pluginname'] = 'Multi-factor authentication';
$string['preferences:header'] = 'Multi-factor authentication preferences';
$string['preferences:availablefactors'] = 'Available factors';
$string['preferences:activefactors'] = 'Active factors';
$string['privacy:metadata'] = 'Moodle MFA plugin does not store any personal data';
$string['revoke'] = 'Revoke';
$string['revokefactor'] = 'Revoke factor';
$string['settings:enabled'] = 'MFA plugin enabled';
$string['settings:enabled_help'] = '';
$string['settings:combinations'] = 'Summary of good conditions for login';
$string['settings:general'] = 'General MFA settings';
$string['settings:debugmode'] = 'Enable debug mode';
$string['settings:debugmode_help'] = 'Debug mode will display a small notification banner on MFA admin pages, as well as the user preferences page
         with information on the currently enabled factors.';
$string['setup'] = 'Setup';
$string['setupfactor'] = 'Setup factor';
$string['state:pass'] = 'Pass';
$string['state:fail'] = 'Fail';
$string['state:unknown'] = 'Unknown';
$string['state:neutral'] = 'Neutral';
$string['totalweight'] = 'Total weight';
$string['weight'] = 'Weight';
