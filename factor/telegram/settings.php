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
 * Settings
 *
 * @package     factor_telegram
 * @subpackage  tool_mfa
 * @author      Jan Dageförde, Laura Troost
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configcheckbox('factor_telegram/enabled',
    new lang_string('settings:enablefactor', 'tool_mfa'),
    new lang_string('settings:enablefactor_help', 'tool_mfa'), 0));

$settings->add(new admin_setting_configtext('factor_telegram/weight',
    new lang_string('settings:weight', 'tool_mfa'),
    new lang_string('settings:weight_help', 'tool_mfa'), 100, PARAM_INT));

$settings->add(new admin_setting_configduration('factor_telegram/duration',
    get_string('settings:duration', 'factor_telegram'),
    get_string('settings:duration_help', 'factor_telegram'), 30 * MINSECS, MINSECS));

$settings->add(new admin_setting_configcheckbox('factor_telegram/suspend',
    get_string('settings:suspend', 'factor_telegram'),
    get_string('settings:suspend_help', 'factor_telegram'), 0));