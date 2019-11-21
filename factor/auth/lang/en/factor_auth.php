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

$string['pluginname'] = 'Authentication type';
$string['privacy:metadata'] = 'The Auth Factor plugin does not store any personal data';
$string['settings:enable'] = 'Enable Auth type Factor';
$string['settings:enable_help'] = 'The auth type factor allows an easily bypass MFA if the user auth type is say saml2 or oidc and where they may have already passed through an MFA process at the IdP level.';
$string['settings:weight'] = 'Auth Factor weight';
$string['settings:weight_help'] = 'If set to 100 then this is effectively a bypass of MFA';
