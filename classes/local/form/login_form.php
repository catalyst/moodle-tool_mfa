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
        $mform->addElement('html', $OUTPUT->heading(get_string('header', 'tool_mfa'), 5));

        $code = 'otpauth://totp/Example:alice@google.com?secret=JBSWY3DPEHPK3PXP&issuer=Example';
        $barcode = new \TCPDF2DBarcode($code, 'QRCODE');
        $image = $barcode->getBarcodePngData(10, 10);
        $qr = \html_writer::img('data:image/png;base64,' . base64_encode($image),'');
        $mform->addElement('html', $qr);

        $mform->addElement('text', 'verification_code', get_string('verification_code', 'tool_mfa'));
        $mform->addHelpButton('verification_code', 'verification_code', 'tool_mfa');
        $mform->setType("verification_code", PARAM_ALPHANUM);

        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $code = $data['verification_code'];

        $errors['verification_code'] = get_string('error:verification_code', 'tool_mfa');

        return $errors;
    }
}
