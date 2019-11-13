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
        $mform = $this->define_configured_factors($mform);
        $mform = $this->define_available_factors($mform);

    }

    /**
     * Defines section with configured user's factors.
     *
     * @param $mform
     * @return object $mform
     * @throws \coding_exception
     */
    public function define_configured_factors($mform) {
        global $OUTPUT;

        $mform->addElement('html', $OUTPUT->heading(get_string('preferences:configuredfactors', 'tool_mfa'), 4));

        $headers = get_strings(array(
            'factor',
            'devicename',
            'created',
            'createdfromip',
            'modified',
            'lastlogon',
            'enable',
            'edit',
            'revoke',
        ), 'tool_mfa');

        $table = new \html_table();
        $table->id = 'configured_factors';
        $table->attributes['class'] = 'generaltable';
        $table->head  = array(
            $headers->factor,
            $headers->devicename,
            $headers->created,
            $headers->createdfromip,
            $headers->modified,
            $headers->lastlogon,
            $headers->edit,
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
            'centeralign',
        );
        $table->data  = array();

        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();

        foreach ($factors as $factor) {

            $userfactors = $factor->get_all_user_factors();

            foreach ($userfactors as $userfactor) {
                $url = "action.php?sesskey=" . sesskey();
                $edit = "<a href=\"action.php?sesskey=".sesskey()
                    ."&amp;action=edit&amp;factor=$factor->name&amp;factorid=$userfactor->id\">$headers->edit</a>";
                $revoke = "<a href=\"action.php?sesskey=".sesskey()
                    ."&amp;action=revoke&amp;factor=$factor->name&amp;factorid=$userfactor->id\">$headers->revoke</a>";

                $timecreated = $userfactor->timecreated == '-' ? '-' : userdate($userfactor->timecreated, '%l:%M %p %d/%m/%Y');
                $timemodified = $userfactor->timemodified == '-' ? '-' : userdate($userfactor->timemodified, '%l:%M %p %d/%m/%Y');

                $row = new \html_table_row(array(
                    $factor->get_display_name(),
                    $userfactor->devicename,
                    $timecreated,
                    $userfactor->createdfromip,
                    $timemodified,
                    $userfactor->lastlogon,
                    $edit,
                    $revoke,
                ));
                $row->attributes['class'] = $class;
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
            $url = "action.php?sesskey=" . sesskey();

            $action = "<a href=\"$url&amp;action=add&amp;factor=$factor->name\">";
            $action .= get_string('addfactor', 'tool_mfa') . '</a>';

            $row = new \html_table_row(array(
                $OUTPUT->heading($factor->get_display_name(), 4) . $factor->get_info(),
                $action,
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
