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
 * MFA management class.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa;

class manager {

    /**
     * Displays a debug table with current factor information.
     */
    public static function display_debug_notification() {
        global $OUTPUT;

        if (!get_config('tool_mfa', 'debugmode')) {
            return;
        }

        $output = $OUTPUT->heading(get_string('debugmode:heading', 'tool_mfa'), 3);

        $table = new \html_table();
        $table->head = array(
            get_string('factor', 'tool_mfa'),
            get_string('setup', 'tool_mfa'),
            get_string('status'),
            get_string('weight', 'tool_mfa'),
            get_string('achievedweight', 'tool_mfa'),
        );

        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $userfactors = \tool_mfa\plugininfo\factor::get_active_user_factor_types();
        $totalpossible = 0;

        foreach ($factors as $factor) {

            $namespace = 'factor_'.$factor->name;
            $name = get_string('pluginname', $namespace);

            $achieved = $factor->get_state() == \tool_mfa\plugininfo\factor::STATE_PASS ? $factor->get_weight() : 0;
            $achieved = '+'.$achieved;

            // Setup.
            if ($factor->has_setup()) {
                $found = false;
                foreach ($userfactors as $userfactor) {
                    if ($userfactor->name == $factor->name) {
                        $found = true;
                    }
                }
                $setup = $found ? get_string('yes') : get_string('no');
            } else {
                $setup = get_string('na', 'tool_mfa');
            }

            // Status.
            switch ($factor->get_state()) {
                case \tool_mfa\plugininfo\factor::STATE_PASS:
                    $state = get_string('state:pass', 'tool_mfa');
                    break;
                case \tool_mfa\plugininfo\factor::STATE_FAIL:
                    $state = get_string('state:fail', 'tool_mfa');
                    break;
                case \tool_mfa\plugininfo\factor::STATE_UNKNOWN:
                    $state = get_string('state:unknown', 'tool_mfa');
                    break;
                case \tool_mfa\plugininfo\factor::STATE_NEUTRAL:
                    $state = get_string('state:neutral', 'tool_mfa');
                    break;
            }

            $table->data[] = array($name, $setup, $state, $factor->get_weight(), $achieved);
            $totalpossible += $factor->get_weight();
        }

        $finalstate = tool_mfa_user_passed_enough_factors() ? get_string('state:pass', 'tool_mfa') : get_string('state:fail', 'tool_mfa');
        $table->data[] = array(get_string('overall', 'tool_mfa'), '-', $finalstate, $totalpossible, self::get_total_weight());

        echo \html_writer::table($table);
    }

    /**
     * Returns the total weight from all factors currently enabled for user.
     *
     * @return int
     */
    public static function get_total_weight() {
        $totalweight = 0;
        $factors = \tool_mfa\plugininfo\factor::get_active_user_factor_types();

        foreach ($factors as $factor) {
            if ($factor->get_state() == \tool_mfa\plugininfo\factor::STATE_PASS) {
                $totalweight += $factor->get_weight();
            }
        }
        return $totalweight;
    }

}

