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
 * @package     tool_mfa
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

use tool_mfa\local\factor\object_factor_base;
use OTPHP\TOTP;

class factor extends object_factor_base {

    public function validate($code) {
        // TODO: Get the secret from db for given user.
//        $secretcode = 'JBSWY3DPEHPK3PXP';
//
//        $hotp = TOTP::create($secretcode);
//        $otp = $hotp->now();
//
//        if ($code !== $otp) {
//            return false;
//        }

        return true;
    }

    public function draw_qrcode($secretcode) {
        $code = 'otpauth://totp/Example:alice@google.com?secret='.$secretcode.'&issuer=Example&algorithm=SHA1&&period=30';
        $barcode = new \TCPDF2DBarcode($code, 'QRCODE');
        $image = $barcode->getBarcodePngData(10, 10);
        $qr = \html_writer::img('data:image/png;base64,' . base64_encode($image),'');
        return $qr;
    }

    public function define_add_factor_form_definition($mform) {
        global $OUTPUT;

        $mform->addElement('html', $OUTPUT->heading('Adding TOTP Factor', 3));
        //$mform->addElement('html', $OUTPUT->box(''));

        $secret = $this->generate_secret_code();
        $mform->addElement('hidden', 'secret', $secret);
        $mform->setType('secret', PARAM_ALPHANUM);

        $mform->addElement('text', 'verificationcode', get_string('addfactor:verificationcode', 'factor_totp'));
        $mform->addHelpButton('verificationcode', 'addfactor:verificationcode', 'factor_totp');
        $mform->setType("verificationcode", PARAM_INT);
        $mform->addRule('verificationcode', get_string('required'), 'required', null, 'client');



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
        $length = $this->get_secret_length();
        $hotp = TOTP::create();
        $secret = substr($hotp->getSecret(), 0, $length);


        return $secret;
    }

    public function validation($data) {
        $errors = array();

        if (empty($data['verificationcode'])) {
            $errors['verificationcode'] = get_string('error:wrongverification', 'factor_totp');
            return $errors;
        }

        $totp = TOTP::create($data['secret']);
        if ($data['verificationcode'] != $totp->now()) {
            $errors['verificationcode'] = get_string('error:wrongverification', 'factor_totp');
        }

        return $errors;
    }

    public function add_user_factor($data) {

        // TODO: add factor data to db here and return true.
        return true;
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
