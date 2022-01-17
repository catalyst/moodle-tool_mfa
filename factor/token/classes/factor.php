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
 * Token factor class.
 *
 * @package     factor_token
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_token;

use tool_mfa\local\factor\object_factor_base;

class factor extends object_factor_base {

    /**
     * token implementation.
     * This factor is a singleton, return single instance.
     *
     * {@inheritDoc}
     */
    public function get_all_user_factors($user) {
        global $DB;
        $records = $DB->get_records('tool_mfa', array('userid' => $user->id, 'factor' => $this->name));

        if (!empty($records)) {
            return $records;
        }

        // Null records returned, build new record.
        $record = array(
            'userid' => $user->id,
            'factor' => $this->name,
            'timecreated' => time(),
            'createdfromip' => $user->lastip,
            'timemodified' => time(),
            'revoked' => 0,
        );
        $record['id'] = $DB->insert_record('tool_mfa', $record, true);
        return [(object) $record];
    }

    /**
     * Role implementation.
     * Factor has no input
     *
     * {@inheritDoc}
     */
    public function has_input() {
        // TODO
        return false;
    }

    /**
     * Role implementation.
     * Checks whether the user has selected roles in any context.
     *
     * {@inheritDoc}
     */
    public function get_state() {
        // TODO

        // If we got here, no roles matched, allow access.
        return \tool_mfa\plugininfo\factor::STATE_PASS;
    }

    /**
     * Role implementation.
     * Cannot set state, return true.
     *
     * {@inheritDoc}
     */
    public function set_state($state) {
        // TODO
        return true;
    }

    /**
     * Role implementation.
     * User can not influence. Result is whatever current state is.
     *
     * {@inheritDoc}
     */
    public function possible_states($user) {
        // TODO
        return [$this->get_state()];
    }
}
