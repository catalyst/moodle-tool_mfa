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
 * MFA Verification code element.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa\local\form;


defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/form/text.php');

class verification_field extends \MoodleQuickForm_text {

    /** @var bool $appendjs */
    private $appendjs;

    public function __construct($attributes=null, $submit = true) {
        global $PAGE;

        // Force attributes.
        if (empty($attributes)) {
            $attributes = [];
        }

        $attributes['autocomplete'] = 'one-time-code';
        $attributes['autofocus'] = 'autofocus';
        $attributes['inputmode'] = 'numeric';
        $attributes['pattern'] = '[0-9]*';
        $attributes['class'] = 'tool-mfa-verification-code';

        // Load JS for element.
        $this->appendjs = false;
        if ($submit) {
            if ($PAGE->pagelayout === 'secure') {
                $this->appendjs = true;
            } else {
                $PAGE->requires->js_call_amd('tool_mfa/autosubmit_verification_code', 'init', []);
            }
        }

        // Force element name to match JS.
        $elementname = 'verificationcode';
        $elementlabel = get_string('verificationcode', 'tool_mfa');

        return parent::__construct($elementname, $elementlabel, $attributes);
    }

    // @codingStandardsIgnoreStart
    public function toHtml() {
        // Empty the value after all attributes decided.
        $this->_attributes['value'] = '';
        $result = parent::toHtml();

        $submitjs = "<script>
            document.querySelector('#id_verificationcode').addEventListener('keyup', function() {
                if (this.value.length == 6) {
                    // Submits the closes form (parent).
                    this.closest('form').submit();
                }
            });
            </script>";

        if ($this->appendjs) {
            $result .= $submitjs;
        }
        return $result;
    }
    // @codingStandardsIgnoreEnd

    /**
     * Setup and return the script for autosubmission while inside the secure layout.
     *
     * @return string the JS to inline attach to the rendered object.
     */
    public function secure_js(): string {
        // Empty the value after all attributes decided.
        $this->_attributes['value'] = '';

        return "<script>
            document.querySelector('#id_verificationcode').addEventListener('keyup', function() {
                if (this.value.length == 6) {
                    // Submits the closes form (parent).
                    this.closest('form').submit();
                }
            });
        </script>";
    }
}
