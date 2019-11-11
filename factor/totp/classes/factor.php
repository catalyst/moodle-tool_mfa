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
 * TOTP factor class.
 *
 * @package     factor_totp
 * @subpackage  tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_totp;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tcpdf/tcpdf_barcodes_2d.php');
require_once(__DIR__.'/../extlib/OTPHP/OTPInterface.php');
require_once(__DIR__.'/../extlib/OTPHP/TOTPInterface.php');
require_once(__DIR__.'/../extlib/OTPHP/ParameterTrait.php');
require_once(__DIR__.'/../extlib/OTPHP/OTP.php');
require_once(__DIR__.'/../extlib/OTPHP/TOTP.php');

require_once(__DIR__.'/../extlib/Assert/Assertion.php');
require_once(__DIR__.'/../extlib/Assert/AssertionFailedException.php');
require_once(__DIR__.'/../extlib/Assert/InvalidArgumentException.php');
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/EncoderInterface.php');
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/Binary.php');
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/Base32.php');

use tool_mfa\local\factor\object_factor_base;
use OTPHP\TOTP;

class factor extends object_factor_base {
    /**
     * Generates TOTP URI for given secret key.
     * Uses site name, domain and user name to make GA account look like:
     * "Sitename domain (username)"
     *
     * @param string $secret
     * @return string
     */
    public function generate_totp_uri($secret) {
        global $USER, $SITE, $CFG;
        $domain = str_replace('http://', '', str_replace('https://', '', $CFG->wwwroot));
        $issuer = $SITE->shortname.' '.$domain;
        $totp = TOTP::create($secret);
        $totp->setLabel($USER->username);
        $totp->setIssuer($issuer);
        return $totp->getProvisioningUri();
    }

    /**
     * Generates HTML sting with QR code for given secret key.
     *
     * @param string $secret
     * @return string
     */
    public function generate_qrcode($secret) {
        $uri = $this->generate_totp_uri($secret);
        $qrcode = new \TCPDF2DBarcode($uri, 'QRCODE');
        $image = $qrcode->getBarcodePngData(10, 10);
        $html = '<br>'.\html_writer::img('data:image/png;base64,' . base64_encode($image),'');
        return $html;
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function add_factor_form_definition($mform) {
        global $OUTPUT;

        $mform->addElement('html', $OUTPUT->heading(get_string('addingfactor', 'factor_totp'), 3));

        $secret = $this->generate_secret_code();
        $mform->addElement('hidden', 'secret', $secret);
        $mform->setType('secret', PARAM_ALPHANUM);

        $mform->addElement('text', 'preferredname', get_string('preferredname', 'factor_totp'));
        $mform->addHelpButton('preferredname', 'preferredname', 'factor_totp');
        $mform->setType("preferredname", PARAM_TEXT);
        $mform->addRule('preferredname', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'verificationcode', get_string('verificationcode', 'factor_totp'));
        $mform->addHelpButton('verificationcode', 'verificationcode', 'factor_totp');
        $mform->setType("verificationcode", PARAM_INT);
        $mform->addRule('verificationcode', get_string('required'), 'required', null, 'client');

        return $mform;
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function add_factor_form_definition_after_data($mform) {
        global $OUTPUT;
        $secretfield = $mform->getElement('secret');

        $secret = $secretfield->getValue();
        $qrcode = $this->generate_qrcode($secret);

        $mform->addElement('html', $OUTPUT->heading(get_string('addingfactor:scan', 'factor_totp'), 5));
        $mform->addElement('html', $qrcode);
        $mform->addElement('html', $OUTPUT->heading(get_string('addingfactor:key', 'factor_totp').$secret, 5));

        return $mform;
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function add_factor_form_validation($data) {
        $errors = array();

        $totp = TOTP::create($data['secret']);
        if (!$totp->verify($data['verificationcode'], time(), 2)) {
            $errors['verificationcode'] = get_string('error:wrongverification', 'factor_totp');
        }

        return $errors;
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function login_form_definition($mform) {
        $userfactors = $this->get_enabled_user_factors();

        if (count($userfactors) > 0) {
            $mform->addElement('text', 'verificationcode', get_string('verificationcode', 'factor_totp'));
            $mform->addRule('verificationcode', get_string('required'), 'required', null, 'client');
            $mform->setType("verificationcode", PARAM_ALPHANUM);
        }

        return $mform;
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function login_form_validation($data) {
        $factors = $this->get_enabled_user_factors();
        $result = array('verificationcode' => 'Wrong verification code');

        foreach ($factors as $factor) {
            $totp = TOTP::create($factor->secret);
            if ($totp->verify($data['verificationcode'], time(), 2)) {
                $result = array();
            }
        }
        return $result;
    }

    /**
     * Generates cryptographically secure pseudo-random 16-digit secret code.
     *
     * @return string
     */
    public function generate_secret_code() {
        $totp = TOTP::create();
        return substr($totp->getSecret(), 0, 16);
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function add_user_factor($data) {
        global $DB, $USER;

        if (!empty($data->secret)) {
            $row = new \stdClass();
            $row->userid = $USER->id;
            $row->secret = $data->secret;
            $row->preferredname = $data->preferredname;
            $row->timecreated = time();
            $row->timemodified = time();
            $row->disabled = 0;

            $DB->insert_record('factor_totp', $row);
            return true;
        }

        return false;
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function get_all_user_factors() {
        global $DB, $USER;
        $sql = "SELECT id, 'totp' AS name, preferredname, secret, timecreated, timemodified, disabled
                  FROM {factor_totp}
                 WHERE userid = ?
              ORDER BY disabled, timemodified";

        $return = $DB->get_records_sql($sql, array($USER->id));
        return $return;
    }
}
