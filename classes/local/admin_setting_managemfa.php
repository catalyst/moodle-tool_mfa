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
 * Admin setting for MFA.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa\local;

defined('MOODLE_INTERNAL') || die();

/// Add libraries
require_once($CFG->libdir.'/ddllib.php');
require_once($CFG->libdir.'/xmlize.php');
require_once($CFG->libdir.'/messagelib.php');

class admin_setting_managemfa extends \admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('authsui', get_string('authsettings', 'admin'), '', '');
    }

    /**
     * Always returns true
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns '' and doesn't write anything
     *
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // do not write any setting
        return '';
    }

    /**
     * Return XHTML to display control
     *
     * @param mixed $data Unused
     * @param string $query
     * @return string highlight
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        // display strings
        $txt = get_strings(array('name', 'enable', 'weight', 'settings'), 'tool_mfa');

        $table = new \html_table();
        $table->id = 'managemfatable';
        $table->attributes['class'] = 'admintable generaltable';
        $table->head  = array($txt->name, $txt->enable, $txt->weight, $txt->settings);
        $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign');
        $table->data  = array();

        $factors = \tool_mfa\plugininfo\factor::get_factors();

        foreach ($factors as $factor) {
            $settings = "<a href=\"settings.php?section=factor_$factor->name\">$txt->settings</a>";
            $url = "tool\\mfa\\index.php?sesskey=" . sesskey();

            // hide/show link
            if ($factor->is_enabled()) {
                $hideshow = "<a href=\"$url&amp;action=disable&amp;factor=$factor->name\">";
                $hideshow .= $OUTPUT->pix_icon('t/hide', get_string('disable')) . '</a>';
                $class = '';
            }
            else {
                $hideshow = "<a href=\"$url&amp;action=enable&amp;factor=$factor->name\">";
                $hideshow .= $OUTPUT->pix_icon('t/show', get_string('enable')) . '</a>';
                $class = 'dimmed_text';
            }

            // Add a row to the table.
            $row = new \html_table_row(array($factor->get_display_name(), $hideshow, $factor->get_weight(), $settings));
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;
        }

        $return = $OUTPUT->box_start('generalbox');
        $return .= \html_writer::table($table);
        $return .= $OUTPUT->box_end();

        return highlight($query, $return);
    }
}