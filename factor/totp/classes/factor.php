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
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/EncoderInterface.php');
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/Binary.php');
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/Base32.php');

use gradereport_singleview\local\ui\empty_element;
use tool_mfa\local\factor\object_factor_base;
use OTPHP\TOTP;

class factor extends object_factor_base {

    public function verify($data) {
        global $USER;
        $factors = $this->get_enabled_user_factors($USER->id);

        foreach ($factors as $factor) {
            if ($factor) {
                $secret = $factor->secret;
                $hotp = TOTP::create($secret);
                $otp = $hotp->now();

                if ($data['verificationcode'] !== $otp) {
                    return array('verificationcode' => 'Wrong verification code');
                }
            }
        }
        return array();
    }

    public function draw_qrcode($secretcode) {
        global $USER;
        $code = 'otpauth://totp/'.$USER->username.'_2:'.$USER->email.'?secret='.$secretcode.'&issuer=Moodle&algorithm=SHA1&&period=30';
        $barcode = new \TCPDF2DBarcode($code, 'QRCODE');
        $image = $barcode->getBarcodePngData(10, 10);
        $qr = \html_writer::img('data:image/png;base64,' . base64_encode($image),'');
        return $qr;
    }

    public function define_add_factor_form_definition($mform) {
        global $OUTPUT;

        $mform->addElement('html', $OUTPUT->heading(get_string('addingfactor', 'factor_totp'), 3));

        $secret = $this->generate_secret_code();
        $mform->addElement('hidden', 'secret', $secret);
        $mform->setType('secret', PARAM_ALPHANUM);

        $mform->addElement('text', 'verificationcode', get_string('verificationcode', 'factor_totp'));
        $mform->addHelpButton('verificationcode', 'verificationcode', 'factor_totp');
        $mform->setType("verificationcode", PARAM_INT);
        $mform->addRule('verificationcode', get_string('required'), 'required', null, 'client');

        return $mform;
    }

    public function define_login_form($mform) {
        global $USER;
        $userfactors = $this->get_enabled_user_factors($USER->id);

        if (count($userfactors) > 0) {
            $mform->addElement('text', 'verificationcode', get_string('verificationcode', 'factor_totp'));
            $mform->addRule('verificationcode', get_string('required'), 'required', null, 'client');
            $mform->setType("verificationcode", PARAM_ALPHANUM);
        }

        return $mform;
    }

    public function get_secret_length() {
        $length = get_config('factor_totp', 'secret_length');
        if ($length) {
            return (int)$length;
        }
        return 8;
    }

    public function generate_secret_code() {
        $hotp = TOTP::create();
        $length = $this->get_secret_length();
        return substr($hotp->getSecret(), 0, $length);
    }

    public function validation($data) {
        $errors = array();

        $totp = TOTP::create($data['secret']);
        if ($data['verificationcode'] != $totp->now()) {
            $errors['verificationcode'] = get_string('error:wrongverification', 'factor_totp');
        }

        return $errors;
    }

    public function add_user_factor($data) {
        global $DB, $USER;

        if (!empty($data->secret)) {
            $row = new \stdClass();
            $row->userid = $USER->id;
            $row->secret = $data->secret;
            $row->timecreated = time();
            $row->timemodified = time();
            $row->disabled = 0;

            $DB->insert_record('tool_mfa_factor_totp', $row);
            return true;
        }

        return false;
    }

    public function get_all_user_factors($user) {
        global $DB;
        $sql = "SELECT id, 'totp' AS name, secret, timecreated, timemodified, disabled
                  FROM {tool_mfa_factor_totp}
                 WHERE userid = ?
              ORDER BY disabled, timemodified";

        $return = $DB->get_records_sql($sql, array($user));
        return $return;
    }

    public function define_add_factor_form_definition_after_data($mform) {
        global $OUTPUT;
        $secretfield = $mform->getElement('secret');

        if (!empty($secretfield)) {
            $secret = $secretfield->getValue();
            $qrcode = $this->draw_qrcode($secret);

            $mform->addElement('html', $OUTPUT->heading('Scan QR-Code or enter a key to your Google Authenticator:', 5));
            $mform->addElement('html', $OUTPUT->heading($this->get_secret_length().'-digit key: '.$secret, 5));
            $mform->addElement('html', $qrcode);
            $mform->addElement('html', $OUTPUT->box(''));

            $hotp = TOTP::create($secret);
            $otp = $hotp->now();
            $mform->addElement('html', $OUTPUT->heading('HINT! Verification code: '.$otp, 5));
        }

        return $mform;
    }
}
