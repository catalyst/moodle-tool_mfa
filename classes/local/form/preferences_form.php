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
            'weight',
            'created',
            'modified',
            'enable',
            'edit',
            'delete',
        ), 'tool_mfa');

        $table = new \html_table();
        $table->id = 'configured_factors';
        $table->attributes['class'] = 'generaltable';
        $table->head  = array(
            $headers->factor,
            $headers->devicename,
            $headers->weight,
            $headers->created,
            $headers->modified,
            $headers->enable,
            $headers->edit,
            $headers->delete,
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

            $userfactors = $factor->get_all_user_factors();

            foreach ($userfactors as $userfactor) {
                $url = "action.php?sesskey=" . sesskey();
                $edit = "<a href=\"action.php?sesskey=".sesskey()
                    ."&amp;action=edit&amp;factor=$factor->name&amp;factorid=$userfactor->id\">$headers->edit</a>";
                $delete = "<a href=\"action.php?sesskey=".sesskey()
                    ."&amp;action=delete&amp;factor=$factor->name&amp;factorid=$userfactor->id\">$headers->delete</a>";

                if ($userfactor->disabled == 1) {
                    $hideshow = "<a href=\"$url&amp;action=enable&amp;factor=$factor->name&amp;factorid=$userfactor->id\">";
                    $hideshow .= $OUTPUT->pix_icon('t/show', get_string('enable')) . '</a>';
                    $class = 'dimmed_text';
                } else {
                    $hideshow = "<a href=\"$url&amp;action=disable&amp;factor=$factor->name&amp;factorid=$userfactor->id\">";
                    $hideshow .= $OUTPUT->pix_icon('t/hide', get_string('disable')) . '</a>';
                    $class = '';
                }

                $timecreated = empty($userfactor->timecreated) ? '' : userdate($userfactor->timecreated, '%l:%M %p %d/%m/%Y');
                $timemodified = empty($userfactor->timemodified) ? '' : userdate($userfactor->timemodified, '%l:%M %p %d/%m/%Y');

                $row = new \html_table_row(array(
                    $factor->get_display_name(),
                    $userfactor->devicename,
                    $factor->get_weight(),
                    $timecreated,
                    $timemodified,
                    $hideshow,
                    $edit,
                    $delete,
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

        $headers = get_strings(array('factor', 'weight', 'action'), 'tool_mfa');

        $table = new \html_table();
        $table->id = 'available_factors';
        $table->attributes['class'] = 'generaltable';
        $table->head  = array($headers->factor, $headers->weight, $headers->action);
        $table->colclasses = array('leftalign', 'centeralign', 'centeralign');
        $table->data  = array();

        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();

        foreach ($factors as $factor) {
            $url = "action.php?sesskey=" . sesskey();

            $action = "<a href=\"$url&amp;action=add&amp;factor=$factor->name\">";
            $action .= get_string('addfactor', 'tool_mfa') . '</a>';

            $row = new \html_table_row(array($factor->get_display_name(), $factor->get_weight(), $action));
            $table->data[] = $row;
        }

        $return = $OUTPUT->box_start('generalbox');
        $return .= \html_writer::table($table);
        $return .= $OUTPUT->box_end();

        $mform->addElement('html', $return);
        return $mform;
    }
}
