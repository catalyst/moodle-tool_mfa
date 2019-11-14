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
 * MFA preferences form
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_mfa\local\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/formslib.php");

class preferences_form extends \moodleform
{
    /**
     * {@inheritDoc}
     * @see moodleform::definition()
     */
    public function definition() {
        $mform = $this->_form;
        $mform = $this->define_active_factors($mform);
        $mform = $this->define_available_factors($mform);

    }

    /**
     * Defines section with active user's factors.
     *
     * @param $mform
     * @return object $mform
     * @throws \coding_exception
     */
    public function define_active_factors($mform) {
        global $OUTPUT;

        $mform->addElement('html', $OUTPUT->heading(get_string('preferences:activefactors', 'tool_mfa'), 4));

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
        $table->attributes['class'] = 'generaltable';
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

            foreach ($userfactors as $userfactor) {
                if ($factor->has_revoke()) {
                    $revokeparams = array('action' => 'revoke', 'factor' => $factor->name, 'factorid' => $userfactor->id);
                    $revokeurl = new \moodle_url('action.php', $revokeparams);
                    $revokelink = \html_writer::link($revokeurl, $headers->revoke);
                } else {
                    $revokelink = "";
                }

                $timecreated = $userfactor->timecreated == '-' ? '-' : userdate($userfactor->timecreated, '%l:%M %p %d/%m/%Y');
                $lastverified = $userfactor->lastverified == '-' ? '-' : userdate($userfactor->lastverified, '%l:%M %p %d/%m/%Y');

                $row = new \html_table_row(array(
                    $factor->get_display_name(),
                    $userfactor->devicename,
                    $timecreated,
                    $userfactor->createdfromip,
                    $lastverified,
                    $revokelink,
                ));
                $table->data[] = $row;
            }
        }

        $return = $OUTPUT->box_start('generalbox');
        $return .= \html_writer::table($table);
        $return .= $OUTPUT->box_end();

        $mform->addElement('html', $return);

        return $mform;
    }

    /**
     * Defines section with available factors.
     *
     * @param $mform
     * @return object $mform
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function define_available_factors($mform) {
        global $OUTPUT;

        $mform->addElement('html', $OUTPUT->heading(get_string('preferences:availablefactors', 'tool_mfa'), 4));

        $headers = get_strings(array('factor', 'action'), 'tool_mfa');

        $table = new \html_table();
        $table->id = 'available_factors';
        $table->attributes['class'] = 'generaltable';
        $table->head  = array(
            $headers->factor,
            $headers->action,
        );
        $table->colclasses = array('leftalign', 'centeralign');
        $table->data  = array();

        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();

        foreach ($factors as $factor) {
            $actionparams = array('action' => 'setup', 'factor' => $factor->name);
            $actionurl = new \moodle_url('action.php', $actionparams);
            $actionlink = \html_writer::link($actionurl, get_string('setupfactor', 'tool_mfa'));

            $row = new \html_table_row(array(
                $OUTPUT->heading($factor->get_display_name(), 4) . $factor->get_info(),
                $actionlink,
            ));
            $table->data[] = $row;
        }

        $return = $OUTPUT->box_start('generalbox');
        $return .= \html_writer::table($table);
        $return .= $OUTPUT->box_end();

        $mform->addElement('html', $return);
        return $mform;
    }
}
