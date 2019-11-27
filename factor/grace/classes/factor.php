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
 * Grace period factor class.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_grace;

defined('MOODLE_INTERNAL') || die();

use tool_mfa\local\factor\object_factor_base;

class factor extends object_factor_base {

    /**
     * Grace Factor implementation.
     * This factor needs no user setup, return true.
     *
     * {@inheritDoc}
     */
    public function setup_user_factor($data) {
        return true;
    }

    /**
     * Grace Factor implementation.
     * This factor is a singleton, return single instance.
     *
     * {@inheritDoc}
     */
    public function get_all_user_factors() {

        $factor = (object) array(
            'id' => 1,
            'name' => $this->name,
            'devicename' => '-',
            'timecreated' => '-',
            'createdfromip' => '-',
            'lastverified' => '-',
            'revoked' => '-'
        );

        return [$factor];
    }

    /**
     * Grace Factor implementation.
     * Factor cannot be revoked, no extra filtering required.
     *
     * {@inheritDoc}
     */
    public function get_active_user_factors() {
        return $this->get_all_user_factors();
    }

    /**
     * Grace Factor implementation.
     * Factor has no input.
     *
     * {@inheritDoc}
     */
    public function has_input() {
        return false;
    }

    /**
     * Grace Factor implementation.
     * Checks the user login time against their first login after MFA activation.
     *
     * {@inheritDoc}
     */
    public function get_state() {
        global $USER;
        $starttime = get_user_preferences('factor_grace_first_login', null, $USER);

        // If no start time is recorded, status is unknown.
        if (empty($starttime)) {
            return \tool_mfa\plugininfo\factor::STATE_UNKNOWN;
        } else {
            $duration = get_config('factor_grace', 'graceperiod');

            return (time() <= $starttime + $duration)
            ? \tool_mfa\plugininfo\factor::STATE_PASS
            : \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        }
    }

    /**
     * Grace Factor implementation.
     * State cannot be set. Return true.
     *
     * {@inheritDoc}
     */
    public function set_state($state) {
        return true;
    }
}

