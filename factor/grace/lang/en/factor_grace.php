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
 * @package     factor_auth
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Grace Period';
$string['info'] = 'Allows login without other factor for a specified period of time.';

$string['settings:enable'] = 'Enable grace period factor';
$string['settings:enable_help'] = 'The grace period';
$string['settings:weight'] = 'Auth Factor weight';
$string['settings:weight_help'] = 'If set to 100 then this is effectively a bypass of MFA';
$string['settings:graceperiod'] = 'Grace period';
$string['settings:graceperiod_help'] = 'Period of time when users can access Moodle without configured and enabled factors';

$string['privacy:metadata'] = 'The Auth Factor plugin does not store any personal data';
