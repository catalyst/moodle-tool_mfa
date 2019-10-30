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

    public function is_enabled() {
        return true;
    }

    public function define_factor_settings($mform) {

        $mform->addElement('header', 'totpheader', get_string('settings:header', 'factor_totp'));
        $mform->setExpanded('totpheader');

        $mform->addElement('text', 'totpweight', get_string('settings:weight', 'factor_totp'));
        $mform->addHelpButton('totpweight', 'settings:weight', 'factor_totp');
        $mform->setType("totpweight", PARAM_INT);

        $mform->addElement('advcheckbox', 'totpenable', get_string('settings:enable', 'factor_totp'));
        $mform->addHelpButton('totpenable', 'settings:enable', 'factor_totp');
        $mform->setType("totpenable", PARAM_INT);

        return $mform;
    }

    public function validate($code) {
        // TODO: Get the secret from db for given user.
        $secretcode = 'JBSWY3DPEHPK3PXP';

        $hotp = TOTP::create($secretcode);
        $otp = $hotp->now();

        if ($code !== $otp) {
            return false;
        }

        return true;
    }

    public function draw_qrcode() {
        $secretcode = 'JBSWY3DPEHPK3PXP';
        $code = 'otpauth://totp/Example:alice@google.com?secret='.$secretcode.'&issuer=Example';
        $barcode = new \TCPDF2DBarcode($code, 'QRCODE');
        $image = $barcode->getBarcodePngData(10, 10);
        $qr = \html_writer::img('data:image/png;base64,' . base64_encode($image),'');
        return $qr;
    }
}