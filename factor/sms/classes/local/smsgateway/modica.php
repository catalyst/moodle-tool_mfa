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
 * Modica Group Gateway class
 *
 * @package     factor_sms
 * @author      Alex Morris <alex.morris@catalyst.net.nz>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_sms\local\smsgateway;

defined('MOODLE_INTERNAL') || die();

class modica implements gateway_interface {

    /**
     * Sends a message using the Modica Group Mobile Gateway API
     *
     * {@inheritDoc}
     */
    public function send_sms_message(string $messagecontent, string $phonenumber): bool {
        global $CFG, $SITE;

        $config = get_config('factor_sms');

        $params = [
            'http' => isset($config->modica_url) ? 'https://api.modicagroup.com/rest/gateway' : $config->modica_url,
            'username' => $config->modica_application,
            'password' => $config->modica_password
        ];

        // Transform the phone number to international standard.
        $phonenumber = \factor_sms\helper::format_number($phonenumber);

        // https://confluence.modicagroup.com/display/DC/Mobile+Gateway+REST+API#MobileGatewayRESTAPI-Sendingtoasingledestination.
        $json = json_encode(
            [
                'destination' => $phonenumber,
                'content' => $messagecontent
            ]
        );

        $curl = new \curl();
        $curl->setHeader(array('Accept: application/json', 'Expect:'));
        $curl->post($params['http'] . '/messages', $json, [
            'CURLOPT_USERPWD' => $params['username'] . ':' . $params['password'],
        ]);
        $info = $curl->get_info();

        return !empty($info['http_code']) && $info['http_code'] == 201;
    }

    public function add_settings($settings) {
        $settings->add(new \admin_setting_configtext('factor_sms/modica_url',
            get_string('settings:modica:url', 'factor_sms'),
            get_string('settings:modica:url_help', 'factor_sms'),
            'https://api.modicagroup.com/rest/gateway'));

        $settings->add(new \admin_setting_configtext('factor_sms/modica_application',
            get_string('settings:modica:application', 'factor_sms'),
            get_string('settings:modica:application_help', 'factor_sms'), ''));

        $settings->add(new \admin_setting_configpasswordunmask('factor_sms/modica_password',
            get_string('settings:modica:password', 'factor_sms'),
            get_string('settings:modica:password_help', 'factor_sms'), ''));
    }

    public function is_gateway_enabled(): bool {
        return true;
    }
}
