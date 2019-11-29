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
 * IP Range factor class.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_iprange;

defined('MOODLE_INTERNAL') || die();

use tool_mfa\local\factor\object_factor_base;

class factor extends object_factor_base {

    /**
     * IP Range Factor implementation.
     * This factor needs no user setup, return true.
     *
     * {@inheritDoc}
     */
    public function setup_user_factor($data) {
        return true;
    }

    /**
     * IP Range Factor implementation.
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
     * IP Range Factor implementation.
     * Factor has no input
     *
     * {@inheritDoc}
     */
    public function has_input() {
        return false;
    }

    /**
     * IP Range Factor implementation.
     * Checks a users current IP against allowed and disallowed ranges.
     *
     * {@inheritDoc}
     */
    public function get_state() {
        $safeips = get_config('factor_iprange', 'safeips');

        // TODO: Check for failures here.

        if (!empty($safeips)) {
            if (remoteip_in_list($safeips)) {
                return \tool_mfa\plugininfo\factor::STATE_PASS;
            }
        }

        return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
    }

    /**
     * IP Range Factor implementation.
     * Cannot set state, return true.
     *
     * {@inheritDoc}
     */
    public function set_state($state) {
        return true;
    }
}
