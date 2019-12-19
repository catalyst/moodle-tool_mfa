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
        $issuer = $SITE->fullname.' '.$domain;
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
        $image = $qrcode->getBarcodePngData(7, 7);
        $html = \html_writer::img('data:image/png;base64,' . base64_encode($image), '');
        return $html;
    }

    /**
     * TOTP state
     *
     * {@inheritDoc}
     */
    public function get_state() {

        $userfactors = $this->get_active_user_factors();

        // If no codes are setup then we must be neutral not unknown.
        if (count($userfactors) == 0) {
            return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        }

        return parent::get_state();
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function setup_factor_form_definition($mform) {
        $secret = $this->generate_secret_code();
        $mform->addElement('hidden', 'secret', $secret);
        $mform->setType('secret', PARAM_ALPHANUM);

        return $mform;
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function setup_factor_form_definition_after_data($mform) {
        global $OUTPUT;

        $mform->addElement('html', $OUTPUT->heading(get_string('setupfactor', 'factor_totp'), 3));

        $mform->addElement('text', 'devicename', get_string('devicename', 'factor_totp'),
            array('placeholder' => get_string('devicenameexample', 'factor_totp')));
        $mform->addHelpButton('devicename', 'devicename', 'factor_totp');
        $mform->setType("devicename", PARAM_TEXT);
        $mform->addRule('devicename', get_string('required'), 'required', null, 'client');

        $secretfield = $mform->getElement('secret');
        $secret = $secretfield->getValue();
        $qrcode = $this->generate_qrcode($secret);

        $secret = wordwrap($secret, 4, ' ', true) . '</code>';
        $secret = \html_writer::tag('code', $secret);

        $html = '';
        $html .= \html_writer::tag('p', get_string('setupfactor:key', 'factor_totp').$secret);
        $html .= $qrcode;

        $mform->addElement('static', 'description', get_string('setupfactor:scan', 'factor_totp'), $html);

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
    public function setup_factor_form_validation($data) {
        $errors = array();

        $totp = TOTP::create($data['secret']);
        if (!$totp->verify($data['verificationcode'], time(), 1)) {
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

        $mform->addElement('text', 'verificationcode', get_string('verificationcode', 'factor_totp'),
            array('autofocus' => 'autofocus', 'pattern' => '[0-9]*'));
        $mform->setType("verificationcode", PARAM_ALPHANUM);
        $mform->addHelpButton('verificationcode', 'verificationcode', 'factor_totp');

        return $mform;
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function login_form_validation($data) {
        $factors = $this->get_active_user_factors();
        $result = array('verificationcode' => get_string('error:wrongverification', 'factor_totp'));

        foreach ($factors as $factor) {
            $totp = TOTP::create($factor->secret);
            if ($totp->verify($data['verificationcode'], time(), 1)) {
                $result = array();
                $this->update_lastverified($factor->id);
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
    public function setup_user_factor($data) {
        global $DB, $USER;

        if (!empty($data->secret)) {
            $row = new \stdClass();
            $row->userid = $USER->id;
            $row->factor = $this->name;
            $row->secret = $data->secret;
            $row->label = $data->devicename;
            $row->timecreated = time();
            $row->createdfromip = $USER->lastip;
            $row->timemodified = time();
            $row->lastverified = time();
            $row->revoked = 0;

            $id = $DB->insert_record('tool_mfa', $row);
            $record = $DB->get_record('tool_mfa', array('id' => $id));
            $this->create_event_after_factor_setup($USER);

            return $record;
        }

        return null;
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function get_all_user_factors() {
        global $DB, $USER;
        return $DB->get_records('tool_mfa', array('userid' => $USER->id, 'factor' => $this->name));
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_revoke() {
        return true;
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_setup() {
        return true;
    }

    /**
     * TOTP Factor implementation.
     * Empty override of parent.
     *
     * {@inheritDoc}
     */
    public function post_pass_state() {
        return;
    }

    /**
     * TOTP Factor implementation.
     * TOTP cannot return fail state.
     *
     * {@inheritDoc}
     */
    public function possible_states($user) {
        return array(
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL,
            \tool_mfa\plugininfo\factor::STATE_UNKNOWN,
        );
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function get_setup_string() {
        return get_string('factorsetup', 'factor_totp');
    }
}
