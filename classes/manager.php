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
        $html = $OUTPUT->heading(get_string('debugmode:heading', 'tool_mfa'), 3);

        $table = new \html_table();
        $table->head = array(
            get_string('weight', 'tool_mfa'),
            get_string('factor', 'tool_mfa'),
            get_string('setup', 'tool_mfa'),
            get_string('achievedweight', 'tool_mfa'),
            get_string('status'),
        );
        $table->attributes['class'] = 'admintable generaltable table table-bordered';
        $table->colclasses = array(
            'text-right',
            '',
            '',
            'text-right',
            'text-center',
        );
        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $userfactors = \tool_mfa\plugininfo\factor::get_active_user_factor_types();
        $runningtotal = 0;
        $weighttoggle = false;

        foreach ($factors as $factor) {

            $namespace = 'factor_'.$factor->name;
            $name = get_string('pluginname', $namespace);

            // If factor is unknown, pending from here.
            if ($factor->get_state() == \tool_mfa\plugininfo\factor::STATE_UNKNOWN) {
                $weighttoggle = true;
            }

            // Stop adding weight if 100 achieved.
            if (!$weighttoggle) {
                $achieved = $factor->get_state() == \tool_mfa\plugininfo\factor::STATE_PASS ? $factor->get_weight() : 0;
                $achieved = '+'.$achieved;
                $runningtotal += $achieved;

            } else {
                $achieved = '';
            }

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
            // If toggle has been flipped, fall to default pending badge.
            if ($weighttoggle) {
                $state = $OUTPUT->get_state_badge('');
            } else {
                $state = $OUTPUT->get_state_badge($factor->get_state());
            }

            $table->data[] = array(
                $factor->get_weight(),
                $name,
                $setup,
                $achieved,
                $state,
            );

            // If we just hit 100, flip toggle.
            if ($runningtotal >= 100) {
                $weighttoggle = true;
            }
        }

        $finalstate = self::get_status();
        $table->data[] = array(
            '',
            '',
            '<b>' . get_string('overall', 'tool_mfa') . '</b>',
            self::get_cumulative_weight(),
            $OUTPUT->get_state_badge($finalstate),
        );

        $html .= \html_writer::table($table);
        echo $html;
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
        global $OUTPUT;
        echo $OUTPUT->header();
        echo $OUTPUT->not_enough_factors();
        echo $OUTPUT->footer();
        self::mfa_logout();
        die;
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

        $totalweight = self::get_cumulative_weight();
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

            // Unset user preferences during mfa auth.
            unset_user_preference('mfa_sleep_duration', $USER);

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

        // Dont redirect if already on auth.php
        $authurl = new \moodle_url('/admin/tool/mfa/auth.php');
        if ($url->compare($authurl)) {
            return self::NO_REDIRECT;
        }

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
        if (isset($USER->policyagreed) && !$USER->policyagreed) {
            $manager = new \core_privacy\local\sitepolicy\manager();
            $policyurl = $manager->get_redirect_url(false);
            if (!empty($policyurl) && $url->compare($policyurl)) {
                return self::NO_REDIRECT;
            }
        }

        // WS/AJAX check.
        if (WS_SERVER || AJAX_SCRIPT) {
            if (isset($SESSION->mfa_pending) && !empty($SESSION->mfa_pending)) {
                // Allow AJAX and WS, but never from auth.php
                return self::NO_REDIRECT;
            }
            return self::REDIRECT_EXCEPTION;
        }

        // Check factor defined safe urls.
        $factorurls = self::get_factor_no_redirect_urls();
        foreach ($factorurls as $factorurl) {
            if ($factorurl->compare($url)) {
                return self::NO_REDIRECT;
            }
        }

        // Circular checks.
        if (isset($SESSION->mfa_redir_referer)
            && $SESSION->mfa_redir_referer != $authurl) {
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

        return self::REDIRECT;
    }

    /**
     * Gets all defined factor urls that should not redirect.
     *
     * @return array
     */
    public static function get_factor_no_redirect_urls() {
        $factors = \tool_mfa\plugininfo\factor::get_factors();
        $urls = array();
        foreach ($factors as $factor) {
            $urls = array_merge($urls, $factor->get_no_redirect_urls());
        }
        return $urls;
    }

    /**
     * Sleeps for an increasing period of time.
     */
    public static function sleep_timer() {
        global $USER;

        $currentduration = get_user_preferences('mfa_sleep_duration', null, $USER);
        if (!empty($currentduration)) {
            // Double current time.
            set_user_preference('mfa_sleep_duration', $currentduration * 2, $USER);
        } else {
            // No duration set.
            $currentduration = 0.05;
            set_user_preference('mfa_sleep_duration', $currentduration, $USER);
        }

        sleep($currentduration);
    }

    /*
     * If MFA Plugin is ready check tool_mfa_authenticated USER property and
     * start MFA authentication if it's not set or false.
     *
     * @return void
     */
    public static function require_auth($courseorid = null, $autologinguest = null, $cm = null,
    $setwantsurltome = null, $preventredirect = null) {
        global $PAGE, $SESSION, $FULLME;

        if (!self::is_ready()) {
            // Set session var so if MFA becomes ready, you dont get locked from session.
            $SESSION->tool_mfa_authenticated = true;
            return;
        }

        if (empty($SESSION->tool_mfa_authenticated) || !$SESSION->tool_mfa_authenticated) {
            if ($PAGE->has_set_url()) {
                $cleanurl = $PAGE->url;
            } else {
                // Use $FULLME instead.
                $cleanurl = new \moodle_url($FULLME);
            }
            $authurl = new \moodle_url('/admin/tool/mfa/auth.php');

            $redir = self::should_require_mfa($cleanurl, $preventredirect);

            if ($redir == self::NO_REDIRECT && !$cleanurl->compare($authurl)) {
                // A non-MFA page that should take precedence.
                // This check is for any pages, such as site policy, that must occur before MFA.
                // This check allows AJAX and WS requests to fire on these pages without throwing an exception.
                $SESSION->mfa_pending = true;
            }

            if ($redir == self::REDIRECT) {
                if (empty($SESSION->wantsurl)) {
                    !empty($setwantsurltome)
                        ? $SESSION->wantsurl = qualified_me()
                        : $SESSION->wantsurl = new \moodle_url('/');

                    $SESSION->tool_mfa_setwantsurl = true;
                }
                // Remove pending status.
                // We must now auth with MFA, now that pending statuses are resolved.
                unset($SESSION->mfa_pending);
                redirect($authurl);
            } else if ($redir == self::REDIRECT_EXCEPTION) {
                if (!empty($SESSION->mfa_redir_referer)) {
                    throw new \moodle_exception('redirecterrordetected', 'tool_mfa', $SESSION->mfa_redir_referer);
                } else {
                    throw new \moodle_exception('redirecterrordetected', 'error');
                }
            }
        }
    }

    /**
     * Sets config variable for given factor.
     *
     * @param array $data
     * @param string $factor
     *
     * @return bool true or exception
     * @throws dml_exception
     */
    public static function set_factor_config($data, $factor) {
        $factorconf = get_config($factor);
        foreach ($data as $key => $newvalue) {
            if (empty($factorconf->$key)) {
                add_to_config_log($key, null, $newvalue, $factor);
                set_config($key, $newvalue, $factor);

            } else if ($factorconf->$key != $newvalue) {
                add_to_config_log($key, $factorconf->$key, $newvalue, $factor);
                set_config($key, $newvalue, $factor);
            }
        }
        return true;
    }

    /**
     * Checks if MFA Plugin is enabled and has enabled factor.
     * If plugin is disabled or there is no enabled factors,
     * it means there is nothing to do from user side.
     * Thus, login flow shouldn't be extended with MFA.
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function is_ready() {
        global $CFG, $USER;

        // Check if user can interact with MFA.
        $usercontext = \context_user::instance($USER->id);
        if (!has_capability('tool/mfa:mfaaccess', $usercontext, $USER)) {
            return false;
        }

        if (!empty($CFG->upgraderunning)) {
            return false;
        }

        $pluginenabled = get_config('tool_mfa', 'enabled');
        if (empty($pluginenabled)) {
            return false;
        }

        $enabledfactors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        if (count($enabledfactors) == 0) {
            return false;
        }

        return true;
    }

    /**
     * Performs factor actions for given factor.
     * Change factor order and enable/disable.
     *
     * @param string $factorname
     * @param string $action
     *
     * @return void
     * @throws dml_exception
     */
    public static function do_factor_action($factorname, $action) {
        $order = explode(',', get_config('tool_mfa', 'factor_order'));
        $key = array_search($factorname, $order);

        switch ($action) {
            case 'up':
                if ($key >= 1) {
                    $fsave = $order[$key];
                    $order[$key] = $order[$key - 1];
                    $order[$key - 1] = $fsave;
                }
                break;

            case 'down':
                if ($key < (count($order) - 1)) {
                    $fsave = $order[$key];
                    $order[$key] = $order[$key + 1];
                    $order[$key + 1] = $fsave;
                }
                break;

            case 'enable':
                if (!$key) {
                    $order[] = $factorname;
                }
                break;

            case 'disable':
                if ($key) {
                    unset($order[$key]);
                }
                break;

            default:
                break;
        }
        self::set_factor_config(array('factor_order' => implode(',', $order)), 'tool_mfa');
    }

    /*
     * If it is possible for a user to setup a factor that could put them into a pass state, returns true.
     *
     * @return bool
     */
    public static function possible_factor_setup() {
        global $USER;

        // Get all active factors.
        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();

        // If a factor has setup, and can pass, return true.
        foreach ($factors as $factor) {
            if ($factor->has_setup()) {
                if (in_array(\tool_mfa\plugininfo\factor::STATE_PASS, $factor->possible_states($USER))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Gets current user weight, up until first unknown factor.
     */
    public static function get_cumulative_weight() {
        $factors = \tool_mfa\plugininfo\factor::get_active_user_factor_types();
        $totalweight = 0;
        foreach ($factors as $factor) {
            if ($factor->get_state() == \tool_mfa\plugininfo\factor::STATE_PASS) {
                $totalweight += $factor->get_weight();
                // If over 100, break. Dont care about >100.
                if ($totalweight >= 100) {
                    break;
                }
            } else if ($factor->get_state() == \tool_mfa\plugininfo\factor::STATE_UNKNOWN) {
                break;
            }
        }
        return $totalweight;
    }
}
