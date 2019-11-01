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
$string['name'] = 'Factor name';
$string['enable'] = 'Enable';
$string['weight'] = 'Weight';
$string['settings'] = 'Settings';
$string['action'] = 'Action';
$string['addfactor'] = 'Add factor';
$string['removefactor'] = 'Remove factor';
$string['enablefactor'] = 'Enable factor';
$string['disablefactor'] = 'Disable factor';

$string['preferences:header'] = 'MFA preferences';
$string['preferences:availablefactors'] = 'Available factors';
$string['preferences:configuredfactors'] = 'Configured factors';

$string['graceperiod'] = 'Grace period';
$string['graceperiod_help'] = 'Period of time when users can access Moodle without configured and enabled factors';

$string['error:directaccess'] = 'This page shouldn\'t be accessed directly';
$string['error:factornotfound'] = 'MFA Factor \'{$a}\' not found';
$string['error:actionnotfound'] = 'Action \'{$a}\' not supported';
$string['error:addfactor'] = 'Can not add factor';

$string['privacy:metadata'] = 'Moodle MFA plugin does not store any personal data';