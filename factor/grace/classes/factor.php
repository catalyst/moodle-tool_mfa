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
        global $DB, $USER;

        if (!$DB->record_exists('factor_grace', array('userid' => $USER->id))) {
            $DB->insert_record('factor_grace', array(
                'userid' => $USER->id,
                'ip' => $USER->lastip,
                'timecreated' => time(),
            ));
        }
        $factorrecord = $DB->get_record('factor_grace', array('userid' => $USER->id));

        $factor = (object) array(
            'id' => 1,
            'name' => $this->name,
            'devicename' => '-',
            'timecreated' => $factorrecord->timecreated,
            'createdfromip' => $factorrecord->timecreated,
            'lastverified' => '-',
            'revoked' => '-'
        );

        return [$factor];
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
        // Check if user already has a record, if not, create one at current time.
        global $USER, $DB;

        if (!$DB->record_exists('factor_grace', array('userid' => $USER->id))) {
            $DB->insert_record('factor_grace', array(
                'userid' => $USER->id,
                'ip' => $USER->lastip,
                'timecreated' => time(),
            ));
        }
        $record = $DB->get_record('factor_grace', array('userid' => $USER->id));

        $starttime = $record->timecreated;

        // If no start time is recorded, status is unknown.
        if (empty($starttime)) {
            return \tool_mfa\plugininfo\factor::STATE_UNKNOWN;
        } else {
            $duration = get_config('factor_grace', 'graceperiod');

            if (!empty($duration)) {
                return (time() <= $starttime + $duration)
                ? \tool_mfa\plugininfo\factor::STATE_PASS
                : \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
            } else {
                return \tool_mfa\plugininfo\factor::STATE_UNKNOWN;
            }
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

    public function post_pass_state() {
        global $SESSION, $USER;

        if (isset($SESSION->grace_message_shown) && $SESSION->grace_message_shown) {
            return;
        }

        // Ensure grace factor passed before displaying notification.
        $grace = \tool_mfa\plugininfo\factor::get_factor('grace');
        if ($grace->get_state() == \tool_mfa\plugininfo\factor::STATE_PASS) {
            $SESSION->grace_message_shown = true;

            $url = new \moodle_url('/admin/tool/mfa/user_preferences.php');
            $link = \html_writer::link($url, get_string('preferences', 'factor_grace'));

            // Can never be null here, STATE_PASS above.
            $starttime = get_user_preferences('factor_grace_first_login', null, $USER);
            $timeremaining = ($starttime + get_config('factor_grace', 'graceperiod')) - time();
            $time = format_time($timeremaining);

            $data = array('url' => $link, 'time' => $time);
            $message = get_string('setupfactors', 'factor_grace', $data);
            \core\notification::info($message);
        }
    }
}

