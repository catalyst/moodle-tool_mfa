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
 * @package     factor_grace
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Grace period';
$string['info'] = 'Allows login without other factor for a specified period of time.';
$string['settings:graceperiod'] = 'Grace period';
$string['settings:graceperiod_help'] = 'Period of time when users can access Moodle without configured and enabled factors';
$string['setupfactors'] = 'You are currently in grace mode, and may not have enough factors setup to login once the grace period is over.
    Visit {$a->url} to check your authentication status, and setup more authentication factors. Your grace period expires in {$a->time}.';
$string['preferences'] = 'User Preferences';
$string['summarycondition'] = 'is within grace period';

$string['privacy:metadata'] = 'The Grace period factor plugin does not store any personal data';
