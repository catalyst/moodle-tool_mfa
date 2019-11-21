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
 * @package     factor_email
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['email:subject'] = 'Your confirmation code';
$string['email:message'] = 'You are tying to log in to Moodle. Your confirmation code is \'{$a}\'';
$string['error:wrongverification'] = 'Incorrect verification code';
$string['info'] = '<p>Built-in factor. Uses e-mail address mentioned in user profile for sending verification codes</p>';
$string['pluginname'] = 'E-Mail Factor';
$string['privacy:metadata'] = 'The E-Mail Factor plugin does not store any personal data';
$string['settings:enable'] = 'Enable E-Mail Factor';
$string['settings:enable_help'] = 'E-Mail Factor will be available for users to setup';
$string['settings:weight'] = 'E-Mail Factor weight';
$string['settings:weight_help'] = 'Factor weight determines the result factor combinations';
$string['setupfactor'] = 'E-Mail Factor setup';
$string['verificationcode'] = 'Enter verification code for confirmation';
$string['verificationcode_help'] = 'Verification code has been sent to your email address';
