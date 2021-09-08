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
 * Helper class for shared sms gateway functions
 *
 * @package     factor_sms
 * @author      Alex Morris <alex.morris@catalyst.net.nz>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_sms;


defined('MOODLE_INTERNAL') || die();

class helper {
    /**
     * This function internationalises a number to E.164 standard.
     * https://46elks.com/kb/e164
     *
     * @param string $phonenumber the phone number to format.
     * @return string the formatted phone number.
     */
    public static function format_number(string $phonenumber): string {
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
