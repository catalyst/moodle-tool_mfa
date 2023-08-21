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

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '../../../../../../iplookup/lib.php');

/**
 * Email renderer.
 *
 * @package     factor_email
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor_email_renderer extends plugin_renderer_base {

    /**
     * Generates an email
     *
     * @param   int $instanceid
     * @return  string|boolean
     */
    public function generate_email($instanceid) {
        global $DB;
        $instance = $DB->get_record('tool_mfa', ['id' => $instanceid]);
        $authurl = new \moodle_url('/admin/tool/mfa/factor/email/email.php',
            ['instance' => $instance->id, 'pass' => 1, 'secret' => $instance->secret]);
        $authurlstring = \html_writer::link($authurl, get_string('email:link', 'factor_email'));
        $messagestrings = ['secret' => $instance->secret, 'link' => $authurlstring];

        $blockurl = new \moodle_url('/admin/tool/mfa/factor/email/email.php',
            ['instance' => $instanceid]);
        $blockurlstring = \html_writer::link($blockurl, get_string('email:link', 'factor_email'));

        $templateinfo = [
            'title' => get_string('email:subject', 'factor_email'),
            'message' => get_string('email:message', 'factor_email', $messagestrings),
            'ipinformation' => get_string('email:ipinfo', 'factor_email'),
            'ip' => get_string('email:originatingip', 'factor_email', $instance->createdfromip),
            'geoinfo' => $this->get_ip_location_origin_string($instance->createdfromip ?: ''),
            'uadescription' => get_string('email:uadescription', 'factor_email'),
            'ua' => $instance->label,
            'linkstring' => get_string('email:revokelink', 'factor_email', $blockurlstring),
        ];
        return $this->render_from_template('factor_email/email', $templateinfo);
    }

    /**
     * Finds the location for the given IP address, handling errors.
     *
     * Returns a user readable string that explains where the request IP originated from.
     *
     * @param string $ipaddress
     * @return string String with IP location details, or a unknown location message if there was an error.
     */
    private function get_ip_location_origin_string(string $ipaddress): string {
        try {
            $geoinfo = iplookup_find_location($ipaddress);
            $city = $geoinfo['city'] ?: '';
            $country = $geoinfo['country'] ?: '';

            // It's possible for errors to be returned, or the geo lookup to simply be empty.
            // In these cases, we want to return the 'unknown' string.
            $iserror = !empty($geoinfo['error']);
            $islookupempty = empty($city) || empty($country);

            if ($iserror || $islookupempty) {
                return get_string('email:geoinfo:unknown', 'factor_email');
            }

            // Location info was found - return details.
            return get_string('email:geoinfo', 'factor_email', ['city' => $city, 'country' => $country]);

        } catch (Throwable $e) {
            // Some exception was thrown, so we cannot work out the location.
            return get_string('email:geoinfo:unknown', 'factor_email');
        }
    }
}
