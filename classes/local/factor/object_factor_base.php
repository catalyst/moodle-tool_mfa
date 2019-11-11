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
 * MFA factor abstract class.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa\local\factor;

defined('MOODLE_INTERNAL') || die();

abstract class object_factor_base implements object_factor {
    /**
     * Factor name.
     *
     * @var string
     */
    public $name;

    /**
     * Class constructor
     *
     * @param string factor name
     *
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Returns true if factor is enabled, otherwise false.
     *
     * Base class implementation.
     *
     * @return bool
     * @throws \dml_exception
     */
    public function is_enabled() {
        $status = get_config('factor_'.$this->name, 'enabled');
        if ($status == 1) {
            return true;
        }
        return false;
    }

    /**
     * Returns configured factor weight.
     *
     * Base class implementation.
     *
     * @return int
     * @throws \dml_exception
     */
    public function get_weight() {
        $weight = get_config('factor_'.$this->name, 'weight');
        if ($weight) {
            return (int)$weight;
        }
        return 0;
    }

    /**
     * Returns factor name from language string.
     *
     * Base class implementation.
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_display_name() {
        return get_string('pluginname', 'factor_'.$this->name);
    }

    /**
     * Returns factor help from language string.
     *
     * Base class implementation.
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_info() {
        return get_string('info', 'factor_'.$this->name);
    }

    /**
     * Defines add_factor form definition page for particular factor.
     *
     * Dummy implementation. Should be overridden in child class.
     *
     * @param $mform
     * @return object $mform
     */
    public function add_factor_form_definition($mform) {
        return $mform;
    }

    /**
     * Defines add_factor form definition page after form data has been set.
     *
     * Dummy implementation. Should be overridden in child class.
     *
     * @param $mform
     * @return object $mform
     */
    public function add_factor_form_definition_after_data($mform) {
        return $mform;
    }

    /**
     * Implements add_factor form validation for particular factor.
     * Returns an array of errors, where array key = field id and array value = error text.
     *
     * Dummy implementation. Should be overridden in child class.
     *
     * @param array $data
     * @return array
     */
    public function add_factor_form_validation($data) {
        return array();
    }

    /**
     * Adds given factor to user's configured factors list.
     * Returns true if factor has been successfully added, otherwise false.
     *
     * Dummy implementation. Should be overridden in child class.
     *
     * @param array $data
     * @return bool
     */
    public function add_user_factor($data) {
        return false;
    }

    /**
     * Returns an array of all user factors of given type (both enabled and disabled).
     *
     * Dummy implementation. Should be overridden in child class.
     *
     * @return array
     */
    public function get_all_user_factors() {
        return array();
    }

    /**
     * Returns an array of enabled user factors of given type.
     * Filters get_all_user_factors() output.
     *
     * @return array
     */
    public function get_enabled_user_factors() {
        $return = array();
        $factors = $this->get_all_user_factors();
        foreach ($factors as $factor) {
            if ($factor->disabled == 0) {
                $return[] = $factor;
            }
        }
        return $return;
    }

    /**
     * Defines login form definition page for particular factor.
     *
     * Dummy implementation. Should be overridden in child class.
     *
     * @param $mform
     * @return object $mform
     */
    public function login_form_definition($mform) {
        return $mform;
    }

    /**
     * Defines login form definition page after form data has been set.
     *
     * Dummy implementation. Should be overridden in child class.
     *
     * @param $mform
     * @return object $mform
     */
    public function login_form_definition_after_data($mform) {
        return $mform;
    }

    /**
     * Implements login form validation for particular factor.
     * Returns an array of errors, where array key = field id and array value = error text.
     *
     * Dummy implementation. Should be overridden in child class.
     *
     * @param array $data
     * @return array
     */
    public function login_form_validation($data) {
        return array();
    }
}
