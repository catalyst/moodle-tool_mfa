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
$string['mfasettings'] = 'Manage MFA';
$string['factor'] = 'Factor';
$string['preferredname'] = 'Preferred name';
$string['enable'] = 'Enable';
$string['weight'] = 'Weight';
$string['settings'] = 'Settings';
$string['action'] = 'Action';
$string['edit'] = 'Edit';
$string['delete'] = 'Delete';
$string['created'] = 'Created';
$string['modified'] = 'Modified';
$string['addfactor'] = 'Add factor';
$string['deletefactor'] = 'Delete factor';
$string['editfactor'] = 'Edit factor';
$string['enablefactor'] = 'Enable factor';
$string['disablefactor'] = 'Disable factor';

$string['settings:enabled'] = 'MFA plugin enabled';
$string['settings:enabled_help'] = '';

$string['preferences:header'] = 'MFA preferences';
$string['preferences:availablefactors'] = 'Available factors';
$string['preferences:configuredfactors'] = 'Configured factors';

$string['graceperiod'] = 'Grace period';
$string['graceperiod_help'] = 'Period of time when users can access Moodle without configured and enabled factors';
$string['graceperiod:notconfigured'] = 'You haven\'t configured your MFA plugin factors';
$string['graceperiod:canaccess'] = 'You still can access Moodle until your grace period is expired';
$string['graceperiod:expires'] = 'Grace period expires \'{$a}\'';
$string['graceperiod:redirect'] = 'You will be redirected now to user preferences page to configure your factors';

$string['error:directaccess'] = 'This page shouldn\'t be accessed directly';
$string['error:factornotfound'] = 'MFA Factor \'{$a}\' not found';
$string['error:actionnotfound'] = 'Action \'{$a}\' not supported';
$string['error:addfactor'] = 'Can not add factor';

$string['event:userpassedmfa'] = 'Verification passed';

$string['privacy:metadata'] = 'Moodle MFA plugin does not store any personal data';