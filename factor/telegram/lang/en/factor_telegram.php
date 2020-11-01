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
 * @package     factor_telegram
 * @author      Jan Dageförde, Laura Troost
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Telegram One-Time Code';
$string['action:revoke'] = 'Revoke Telegram ID';
$string['addtelegramid'] = 'Enter Telegram username or ID';
$string['info'] = '<p>Setup your Telegram ID so that one-time security codes can be sent to you.</p>';
$string['loginsubmit'] = 'Verify code';
$string['loginskip'] = "I didn't receive a code";
$string['setupfactor'] = 'Setup Telegram ID';
$string['settings:duration'] = 'Validity duration';
$string['settings:duration_help'] = 'The period of time that the code is valid.';
$string['telegramhelp'] = 'Enter your Telegram user name or, alternatively, your Telegram user ID.';
$string['telegramsent'] = 'A Telegram message containing your verification code was sent to you ({$a}).';
$string['telegramstring'] = '{$a->code} is your {$a->fullname} one-time security code.

@{$a->url} #{$a->code}';
$string['summarycondition'] = 'Using a one-time security code sent via Telegram';
$string['privacy:metadata'] = 'The Telegram factor plugin does not store any personal data';
$string['wrongcode'] = 'Invalid security code.';
