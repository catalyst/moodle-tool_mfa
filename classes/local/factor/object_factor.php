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
 * MFA factor interface.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa\local\factor;

defined('MOODLE_INTERNAL') || die();

interface object_factor {
    /**
     * Returns true if factor is enabled, otherwise false.
     *
     * @return bool
     * @throws \dml_exception
     */
    public function is_enabled();

    /**
     * Returns configured factor weight.
     *
     * @return int
     * @throws \dml_exception
     */
    public function get_weight();

    /**
     * Returns factor name from language string.
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_display_name();

    /**
     * Returns factor info from language string.
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_info();

    /**
     * Defines add_factor form definition page for particular factor.
     *
     * @param $mform
     * @return object $mform
     * @throws \coding_exception
     */
    public function add_factor_form_definition($mform);

    /**
     * Defines add_factor form definition page after form data has been set.
     *
     * @param $mform
     * @return object $mform
     * @throws \coding_exception
     */
    public function add_factor_form_definition_after_data($mform);

    /**
     * Implements add_factor form validation for particular factor.
     * Returns an array of errors, where array key = field id and array value = error text.
     *
     * @param array $data
     * @return array
     */
    public function add_factor_form_validation($data);

    /**
     * Defines login form definition page for particular factor.
     *
     * @param $mform
     * @return object $mform
     * @throws \coding_exception
     */
    public function login_form_definition($mform);

    /**
     * Defines login form definition page after form data has been set.
     *
     * @param $mform
     * @return object $mform
     * @throws \coding_exception
     */
    public function login_form_definition_after_data($mform);

    /**
     * Implements login form validation for particular factor.
     * Returns an array of errors, where array key = field id and array value = error text.
     *
     * @param array $data
     * @return array
     */
    public function login_form_validation($data);

    /**
     * Adds given factor to user's configured factors list.
     * Returns true if factor has been successfully added, otherwise false.
     *
     * @param array $data
     * @return bool
     */
    public function add_user_factor($data);

    /**
     * Returns an array of all user factors of given type (both enabled and disabled).
     *
     * @return array
     */
    public function get_all_user_factors();

    /**
     * Returns an array of enabled user factors of given type.
     * Filters get_all_user_factors() output.
     *
     * @return array
     */
    public function get_enabled_user_factors();

    /**
     * Returns true if factor class has factor records that might be deleted.
     * It means that user can delete factor record from their profile.
     *
     * @return bool
     */
    public function has_delete();
}
