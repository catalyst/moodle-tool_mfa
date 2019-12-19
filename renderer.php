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
 * MFA renderer.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class tool_mfa_renderer extends plugin_renderer_base {

    /**
     * Returns the state of the factor as a badge
     *
     * @return html
     */
    public function get_state_badge($state) {

        switch ($state) {
            case \tool_mfa\plugininfo\factor::STATE_PASS:
                return \html_writer::tag('span', get_string('state:pass', 'tool_mfa'), array('class' => 'badge badge-success'));

            case \tool_mfa\plugininfo\factor::STATE_FAIL:
                return \html_writer::tag('span', get_string('state:fail', 'tool_mfa'), array('class' => 'badge badge-danger'));

            case \tool_mfa\plugininfo\factor::STATE_NEUTRAL:
                return \html_writer::tag('span', get_string('state:neutral', 'tool_mfa'), array('class' => 'badge badge-warning'));

            case \tool_mfa\plugininfo\factor::STATE_UNKNOWN:
                return \html_writer::tag('span', get_string('state:unknown', 'tool_mfa'),
                        array('class' => 'badge badge-secondary'));

            default:
                return \html_writer::tag('span', get_string('pending', 'tool_mfa'), array('class' => 'badge badge-secondary'));
        }
    }

    /**
     * Returns a list of factors which a user can add
     *
     * @return html
     */
    public function available_factors() {
        global $OUTPUT;

        $html = $OUTPUT->heading(get_string('preferences:availablefactors', 'tool_mfa'), 4);

        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        foreach ($factors as $factor) {

            // TODO is_configured / is_ready.
            if (!$factor->has_setup()) {
                continue;
            }
            $html .= $this->setup_factor($factor);
        }

        return $html;
    }

    public function setup_factor($factor) {
        global $OUTPUT;

        $html = '';

        $html .= html_writer::start_tag('div', array('class' => 'card'));
        $html .= html_writer::tag('div', $factor->get_display_name(), array('class' => 'card-header'));
        $html .= html_writer::start_tag('div', array('class' => 'card-body'));
        $html .= $factor->get_info();

        $setupparams = array('action' => 'setup', 'factor' => $factor->name, 'sesskey' => sesskey());
        $setupurl = new \moodle_url('action.php', $setupparams);
        $html .= $OUTPUT->single_button($setupurl, $factor->get_setup_string());
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('div');
        $html .= '<br>';

        return $html;
    }

    /**
     * Defines section with active user's factors.
     *
     * @return string $html
     * @throws \coding_exception
     */
    public function active_factors() {
        global $OUTPUT;

        $html = $OUTPUT->heading(get_string('preferences:activefactors', 'tool_mfa'), 4);

        $headers = get_strings(array(
            'factor',
            'devicename',
            'created',
            'createdfromip',
            'lastverified',
            'revoke',
        ), 'tool_mfa');

        $table = new \html_table();
        $table->id = 'active_factors';
        $table->attributes['class'] = 'generaltable table table-bordered';
        $table->head  = array(
            $headers->factor,
            $headers->devicename,
            $headers->created,
            $headers->createdfromip,
            $headers->lastverified,
            $headers->revoke,
        );
        $table->colclasses = array(
            'leftalign',
            'leftalign',
            'centeralign',
            'centeralign',
            'centeralign',
            'centeralign',
            'centeralign',
            'centeralign',
        );
        $table->data  = array();

        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();

        foreach ($factors as $factor) {

            $userfactors = $factor->get_active_user_factors();

            if (!$factor->has_setup()) {
                continue;
            }

            foreach ($userfactors as $userfactor) {
                if ($factor->has_revoke()) {
                    $revokeparams = array('action' => 'revoke', 'factor' => $factor->name,
                        'factorid' => $userfactor->id, 'sesskey' => sesskey());
                    $revokeurl = new \moodle_url('action.php', $revokeparams);
                    $revokelink = \html_writer::link($revokeurl, $headers->revoke);
                } else {
                    $revokelink = "";
                }

                $timecreated = $userfactor->timecreated == '-' ? '-' : userdate($userfactor->timecreated, '%l:%M %p %d/%m/%Y');
                $lastverified = $userfactor->lastverified == '-' ? '-' : userdate($userfactor->lastverified, '%l:%M %p %d/%m/%Y');

                $row = new \html_table_row(array(
                    $factor->get_display_name(),
                    $userfactor->label,
                    $timecreated,
                    $userfactor->createdfromip,
                    $lastverified,
                    $revokelink,
                ));
                $table->data[] = $row;
            }
        }
        // If table has no data, don't output.
        if (count($table->data) == 0) {
            return '';
        }
        $html .= \html_writer::table($table);
        $html .= '<br>';

        return $html;
    }

    /**
     * Generates notification text for display when user cannot login.
     *
     * @return string $notification
     */
    public function not_enough_factors() {
        global $CFG, $OUTPUT, $SITE;

        $notification = \html_writer::tag('h4', get_string('error:notenoughfactors', 'tool_mfa'));
        $notification .= \html_writer::tag('p', get_string('error:reauth', 'tool_mfa'));

        // Support link.
        $supportemail = $CFG->supportemail;
        if (!empty($supportemail)) {
            $subject = get_string('email:subject', 'tool_mfa', $SITE->fullname);
            $maillink = \html_writer::link("mailto:$supportemail?Subject=$subject", $supportemail);
            $notification .= get_string('error:support', 'tool_mfa');
            $notification .= \html_writer::tag('p', $maillink);
        }

        // Support page link.
        $supportpage = $CFG->supportpage;
        if (!empty($supportpage)) {
            $linktext = \html_writer::link($supportpage, get_string('error:supportpage', 'tool_mfa'));
            $notification .= $linktext;
        }

        // Home link.
        $url = new \moodle_url('/');
        $link = \html_writer::link($url, get_string('error:home', 'tool_mfa'));
        $notification .= \html_writer::tag('p', $link);

        return $OUTPUT->notification($notification, 'notifyerror');
    }
}
