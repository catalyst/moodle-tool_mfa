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
 * Auth factor class.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_auth;

defined('MOODLE_INTERNAL') || die();

use tool_mfa\local\factor\object_factor_base;

class factor extends object_factor_base {

    /**
     * Auth Factor implementation.
     * Factor is a singleton, can only be one instance.
     *
     * {@inheritDoc}
     */
    public function get_all_user_factors() {
        global $DB, $USER;
        $records = $DB->get_records('tool_mfa', array('userid' => $USER->id, 'factor' => $this->name));

        if (!empty($records)) {
            return $records;
        }

        // Null records returned, build new record.
        $record = array(
            'userid' => $USER->id,
            'factor' => $this->name,
            'timecreated' => time(),
            'createdfromip' => $USER->lastip,
            'timemodified' => time(),
            'revoked' => 0,
        );
        $record['id'] = $DB->insert_record('tool_mfa', $record, true);
        return [(object) $record];
    }

    /**
     * Auth Factor implementation.
     * Factor does not have input.
     *
     * {@inheritDoc}
     */
    public function has_input() {
        return false;
    }

    /**
     * Auth Factor implementation.
     * State check is performed here, as there is no form to do it in.
     *
     * {@inheritDoc}
     */
    public function get_state() {
        global $USER;

        $safetypes = get_config('factor_auth', 'goodauth');
        if (strlen($safetypes) != 0) {
            $safetypes = explode(',', $safetypes);
            $authtypes = get_enabled_auth_plugins(true);

            // Check all safetypes against user auth.
            foreach ($safetypes as $type) {
                if ($authtypes[$type] == $USER->auth) {
                    return \tool_mfa\plugininfo\factor::STATE_PASS;
                }
            }
            return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        } else {
            return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        }
    }

    /**
     * Auth Factor implementation.
     * The state can never be set. Always return true.
     *
     * {@inheritDoc}
     */
    public function set_state($state) {
        return true;
    }

    /**
     * Auth factor implementation.
     * Return list of auth types that are safe.
     *
     * {@inheritDoc}
     */
    public function get_summary_condition() {
        $safetypes = get_config('factor_auth', 'goodauth');
        $authtypes = get_enabled_auth_plugins(true);
        $string = '';

        if (strlen($safetypes) > 0) {
            $safetypes = explode(',', $safetypes);
            foreach ($safetypes as $type) {
                $string .= $authtypes[$type];
            }
        }

        return get_string('summarycondition', 'factor_'.$this->name, $string);
    }
}
