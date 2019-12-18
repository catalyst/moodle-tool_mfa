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
 * Security Question factor class.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_secq;

defined('MOODLE_INTERNAL') || die();

use tool_mfa\local\factor\object_factor_base;

class factor extends object_factor_base {

    /**
     * Security Questions Factor implementation.
     *
     * {@inheritDoc}
     */
    public function setup_user_factor($data) {
        // SETUP FUNCTIONALITY HERE.
        return true;
    }

    /**
     * Security Questions Factor implementation.
     *
     * {@inheritDoc}
     */
    public function get_all_user_factors() {
        // FACTOR FUNCTIONALITY HERE.
        return array();
    }

    /**
     * Security Questions Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_input() {
        // CHANGE TO TRUE WHEN IMPLEMENTED.
        return false;
    }

    /**
     * Security Questions Factor implementation.
     * RETURN CORRECT STATE WHEN IMPLEMENTED.
     *
     * {@inheritDoc}
     */
    public function get_state() {
        return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
    }

    /**
     * Security Questions Factor implementation.
     *
     * {@inheritDoc}
     */
    public function set_state($state) {
        // SET STATE CORRECTLY HERE.
        return true;
    }

    /**
     * Security Questions Factor Implementation.
     */
    public function get_no_redirect_urls() {
        return array(
            new \moodle_url('/admin/tool/securityquestions/set_responses.php')
        );
    }
}
