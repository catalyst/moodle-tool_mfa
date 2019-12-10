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

defined('MOODLE_INTERNAL') || die();

class manager {

    const REDIRECT = 1;
    const NO_REDIRECT = 0;
    const REDIRECT_EXCEPTION = -1;

    /**
     * Displays a debug table with current factor information.
     */
    public static function display_debug_notification() {
        global $OUTPUT, $PAGE;

        if (!get_config('tool_mfa', 'debugmode')) {
            return;
        }

        $output = $OUTPUT->heading(get_string('debugmode:heading', 'tool_mfa'), 3);

        $table = new \html_table();
        $table->head = array(
            get_string('weight', 'tool_mfa'),
            get_string('factor', 'tool_mfa'),
            get_string('setup', 'tool_mfa'),
            get_string('achievedweight', 'tool_mfa'),
            get_string('status'),
        );
        $table->attributes['class'] = 'admintable generaltable';
        $table->colclasses = array(
            'text-right',
            '',
            '',
            'text-right',
            'text-center',
        );
        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $userfactors = \tool_mfa\plugininfo\factor::get_active_user_factor_types();

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
            $OUTPUT = $PAGE->get_renderer('tool_mfa');
            $state = $OUTPUT->get_state_badge($factor->get_state());

            $table->data[] = array(
                $factor->get_weight(),
                $name,
                $setup,
                $achieved,
                $state,
            );
        }

        $finalstate = self::get_status();
        $table->data[] = array(
            '',
            '',
            '<b>' . get_string('overall', 'tool_mfa') . '</b>',
            self::get_total_weight(),
            $OUTPUT->get_state_badge($finalstate),
        );

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

    /**
     * Checks that provided factorid exists and belongs to current user.
     *
     * @param string $factorname
     * @param int $factorid
     * @param object $user
     * @return bool
     * @throws \dml_exception
     */
    public static function is_factorid_valid($factorid, $user) {
        global $DB;
        return $DB->record_exists('tool_mfa', array('userid' => $user->id, 'id' => $factorid));
    }

    /**
     * Function to display to the user that they cannot login, then log them out.
     *
     * @return void
     */
    public static function cannot_login() {
        self::mfa_logout();
        print_error('error:notenoughfactors', 'tool_mfa', new \moodle_url('/'));
    }

    /**
     * Logout user.
     *
     * @return void
     */
    public static function mfa_logout() {
        $authsequence = get_enabled_auth_plugins();
        foreach ($authsequence as $authname) {
            $authplugin = get_auth_plugin($authname);
            $authplugin->logoutpage_hook();
        }
        require_logout();
    }

    /**
     * Function to get the overall status of a user's authentication.
     *
     * @return mixed a STATE variable from plugininfo
     */
    public static function get_status() {
        global $SESSION;

        // Check for any instant fail states.
        $factors = \tool_mfa\plugininfo\factor::get_active_user_factor_types();
        foreach ($factors as $factor) {
            if ($factor->get_state() == \tool_mfa\plugininfo\factor::STATE_FAIL) {
                return \tool_mfa\plugininfo\factor::STATE_FAIL;
            }
        }

        // Check for passing state. If found, ensure that session var is set.
        if (isset($SESSION->tool_mfa_authenticated) && $SESSION->tool_mfa_authenticated) {
            return \tool_mfa\plugininfo\factor::STATE_PASS;
        } else if (self::passed_enough_factors()) {
            return \tool_mfa\plugininfo\factor::STATE_PASS;
        }

        // Check next factor for instant fail (fallback).
        if (\tool_mfa\plugininfo\factor::get_next_user_factor()->get_state() ==
            \tool_mfa\plugininfo\factor::STATE_FAIL) {

            return \tool_mfa\plugininfo\factor::STATE_FAIL;
        }
        // Else return neutral state.
        return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
    }

    /**
     * Function to check the overall status of a users authentication,
     * and perform any required actions.
     *
     * @param bool $shouldreload whether the function should reload (used for auth.php).
     * @return void
     */
    public static function check_status($shouldreload = false) {
        global $SESSION;

        if (empty($SESSION->wantsurl)) {
            $wantsurl = '/';
        } else {
            $wantsurl = $SESSION->wantsurl;
        }

        $state = self::get_status();
        if ($state == \tool_mfa\plugininfo\factor::STATE_PASS) {
            self::set_pass_state();
            unset($SESSION->wantsurl);
            redirect(new \moodle_url($wantsurl));
        } else if ($state == \tool_mfa\plugininfo\factor::STATE_FAIL) {
            self::cannot_login();
        } else if ($shouldreload) {
            redirect(new \moodle_url('/admin/tool/mfa/auth.php'));
        }
    }

    /**
     * Checks whether user has passed enough factors to be allowed in.
     *
     * @return bool true if user has passed enough factors.
     */
    public static function passed_enough_factors() {

        // Check for any instant fail states.
        $factors = \tool_mfa\plugininfo\factor::get_active_user_factor_types();
        foreach ($factors as $factor) {
            if ($factor->get_state() == \tool_mfa\plugininfo\factor::STATE_FAIL) {
                self::mfa_logout();
            }
        }

        $totalweight = self::get_total_weight();
        if ($totalweight >= 100) {
            return true;
        }

        return false;
    }

    /**
     * Sets the session variable for pass_state, if not already set.
     *
     * @return void
     */
    public static function set_pass_state() {
        global $SESSION, $USER;
        if (!isset($SESSION->tool_mfa_authenticated)) {
            $SESSION->tool_mfa_authenticated = true;
            $event = \tool_mfa\event\user_passed_mfa::user_passed_mfa_event($USER);
            $event->trigger();

            // Unset session vars during mfa auth.
            unset($SESSION->mfa_redir_referer);
            unset($SESSION->mfa_redir_count);

            // Fire post pass state factor actions.
            $factors = \tool_mfa\plugininfo\factor::get_active_user_factor_types();
            foreach ($factors as $factor) {
                $factor->post_pass_state();
            }
        }
    }

    /**
     * Checks whether the user should be redirected from the provided url.
     *
     * @return int
     */
    public static function should_require_mfa($url, $preventredirect) {
        global $CFG, $USER, $SESSION;
        // Remove all params before comparison.
        $url->remove_all_params();

        // Soft maintenance mode.
        if (!empty($CFG->maintenance_enabled)) {
            return self::NO_REDIRECT;
        }

        // Admin not setup.
        if (!empty($CFG->adminsetuppending)) {
            return self::NO_REDIRECT;
        }

        // Initial installation.
        // We get this for free from get_plugins_with_function.

        // Upgrade check.
        // We get this for free from get_plugins_with_function.

        // Honor prevent_redirect.
        if ($preventredirect) {
            return self::NO_REDIRECT;
        }

        // User not properly setup.
        if (user_not_fully_set_up($USER)) {
            return self::NO_REDIRECT;
        }

        // Enrolment.
        $enrol = new \moodle_url('/enrol/index.php');
        if ($enrol->compare($url)) {
            return self::NO_REDIRECT;
        }

        // Guest access.
        if (isguestuser()) {
            return self::NO_REDIRECT;
        }

        // Forced password changes.
        if (get_user_preferences('auth_forcepasswordchange')) {
            return self::NO_REDIRECT;
        }

        // Login as.
        if (\core\session\manager::is_loggedinas()) {
            return self::NO_REDIRECT;
        }

        // Site policy.
        if (isset($USER->policyagreed) && !$USER->policyagreed
        && defined('NO_SITEPOLICY_CHECK') && !NO_SITEPOLICY_CHECK) {
            $manager = new \core_privacy\local\sitepolicy\manager();
            $policyurl = $manager->get_redirect_url(false);
            if (!empty($policyurl)) {
                return self::NO_REDIRECT;
            }
        }

        // WS/AJAX check.
        if (WS_SERVER || AJAX_SCRIPT) {
            return self::REDIRECT_EXCEPTION;
        }

        // Circular checks.
        $authurl = new \moodle_url('/admin/tool/mfa/auth.php');
        if (isset($SESSION->mfa_redir_referer) &&
            $SESSION->mfa_redir_referer != $authurl) {
            if ($SESSION->mfa_redir_referer == get_local_referer(true)) {
                // Possible redirect loop.
                if (!isset($SESSION->mfa_redir_count)) {
                    $SESSION->mfa_redir_count = 1;
                } else {
                    $SESSION->mfa_redir_count++;
                }
                if ($SESSION->mfa_redir_count > 5) {
                    return self::REDIRECT_EXCEPTION;
                }
            } else {
                // If not a match, reset counter.
                $SESSION->mfa_redir_count = 0;
            }
        }
        // Set referer after checks.
        $SESSION->mfa_redir_referer = get_local_referer(true);

        $safe = new \moodle_url('/admin/tool/mfa/auth.php');
        if ($safe->compare($url)) {
            return self::NO_REDIRECT;
        }
        return self::REDIRECT;
    }
}
