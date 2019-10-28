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
 * MFA login form
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_mfa\local\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/formslib.php");
require_once($CFG->libdir.'/tcpdf/tcpdf_barcodes_2d.php');
require_once(__DIR__.'/../../../factors/totp/extlib/OTPHP/OTPInterface.php');
require_once(__DIR__.'/../../../factors/totp/extlib/OTPHP/TOTPInterface.php');
require_once(__DIR__.'/../../../factors/totp/extlib/OTPHP/ParameterTrait.php');
require_once(__DIR__.'/../../../factors/totp/extlib/OTPHP/OTP.php');
require_once(__DIR__.'/../../../factors/totp/extlib/OTPHP/TOTP.php');

require_once(__DIR__.'/../../../factors/totp/extlib/Assert/Assertion.php');
require_once(__DIR__.'/../../../factors/totp/extlib/ParagonIE/ConstantTime/EncoderInterface.php');
require_once(__DIR__.'/../../../factors/totp/extlib/ParagonIE/ConstantTime/Binary.php');
require_once(__DIR__.'/../../../factors/totp/extlib/ParagonIE/ConstantTime/Base32.php');
use OTPHP\TOTP;


class login_form extends \moodleform
{
    /**
     * {@inheritDoc}
     * @see moodleform::definition()
     */
    public function definition()
    {
        global $OUTPUT;
        $mform = $this->_form;

//        $secretcode = 'JBSWY3DPEHPK3PXP';
//        $code = 'otpauth://totp/Example:alice@google.com?secret='.$secretcode.'&issuer=Example';
//        $barcode = new \TCPDF2DBarcode($code, 'QRCODE');
//        $image = $barcode->getBarcodePngData(10, 10);
//        $qr = \html_writer::img('data:image/png;base64,' . base64_encode($image),'');
//        $mform->addElement('html', $qr);

        // TODO: Get the list of active factors.

        // TOTP Factor.
        $mform->addElement('html', $OUTPUT->heading(get_string('totp:header', 'tool_mfa'), 5));
        $mform->addElement('text', 'totp_verification_code', get_string('totp:verification_code', 'tool_mfa'));
        $mform->addHelpButton('totp_verification_code', 'totp:verification_code', 'tool_mfa');
        $mform->setType("totp_verification_code", PARAM_ALPHANUM);

        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $code = $data['totp_verification_code'];

        // TODO: Get the secret from db for given user.
        $secretcode = 'JBSWY3DPEHPK3PXP';

        $hotp = TOTP::create($secretcode);
        $otp = $hotp->now();

        if ($code !== $otp) {
            $errors['totp_verification_code'] = get_string('totp:error:verification_code', 'tool_mfa');
        }

        return $errors;
    }
}
