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
 * AWS SNS SMS Gateway class
 *
 * @package     factor_sms
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_sms\local\smsgateway;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/aws/sdk/aws-autoloader.php');
use Aws\Sns\SnsClient;

class aws_sns implements gateway_interface {

    /**
     * Sends a message using the AWS SNS API
     *
     * {@inheritDoc}
     */
    public function send_sms_message(string $messagecontent, string $phonenumber) : bool {
        global $CFG, $SITE;

        $config = get_config('factor_sms');

        // Setup client params and instantiate client.
        $params = [
            'version' => 'latest',
            'region' => $config->api_region,
            'http' => ['proxy' => \local_aws\local\aws_helper::get_proxy_string()]
        ];
        if (!$config->usecredchain) {
            $params['credentials'] = [
                'key' => $config->api_key,
                'secret' => $config->api_secret
            ];
        }
        $client = new SnsClient($params);

        // Transform the phone number to international standard.
        $phonenumber = $this->format_number($phonenumber);

        // Setup the sender information.
        $senderid = $SITE->shortname;
        // Remove spaces from ID.
        $senderid = str_replace(' ', '', (trim($senderid)));
        // We have to truncate the senderID to 11 chars.
        $senderid = substr($senderid, 0, 11);

        // These messages need to be transactional.
        $client->SetSMSAttributes([
            'attributes' => [
                'DefaultSMSType' => 'Transactional',
                'DefaultSenderID' => $senderid,
            ],
        ]);

        // Actually send the message.
        try {
            $client->publish([
                'Message' => $messagecontent,
                'PhoneNumber' => $phonenumber,
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * This function internationalises a number to E.164 standard.
     * https://46elks.com/kb/e164
     *
     * @param string $phonenumber the phone number to format.
     * @return string the formatted phone number.
     */
    private function format_number(string $phonenumber) : string {
        // Remove all whitespace, dashes and brackets.
        $phonenumber = preg_replace('/[ \(\)-]/', '', $phonenumber);

        // Number is already in international format. Do nothing.
        if (strpos($phonenumber, '+') === 0) {
            return $phonenumber;
        }

        // Strip leading 0 if found.
        if (strpos($phonenumber, '0') === 0) {
            $phonenumber = substr($phonenumber, 1);
        }

        // Prepend country code.
        $countrycode = get_config('factor_sms', 'countrycode');
        $phonenumber = '+' . $countrycode . $phonenumber;

        return $phonenumber;
    }
}
