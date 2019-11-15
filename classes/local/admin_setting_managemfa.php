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

require_once($CFG->libdir.'/ddllib.php');
require_once($CFG->libdir.'/xmlize.php');
require_once($CFG->libdir.'/messagelib.php');

class admin_setting_managemfa extends \admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('mfaui', get_string('mfasettings', 'tool_mfa'), '', '');
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

        $txt = get_strings(array('factor', 'enable', 'order', 'weight', 'settings'), 'tool_mfa');

        $table = new \html_table();
        $table->id = 'managemfatable';
        $table->attributes['class'] = 'admintable generaltable';
        $table->head  = array($txt->factor, $txt->enable, $txt->order, $txt->weight, $txt->settings);
        $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign');
        $table->data  = array();

        $factors = \tool_mfa\plugininfo\factor::get_factors();
        $enabledfactors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $order = 1;

        foreach ($factors as $factor) {
            $settingsparams = array('section' => 'factor_'.$factor->name);
            $settingsurl = new \moodle_url('settings.php', $settingsparams);
            $settingslink = \html_writer::link($settingsurl, $txt->settings);

            if ($factor->is_enabled()) {
                $hideshowparams = array('action' => 'disable', 'factor' => $factor->name);
                $hideshowurl = new \moodle_url('tool/mfa/index.php', $hideshowparams);
                $hideshowlink = \html_writer::link($hideshowurl, $OUTPUT->pix_icon('t/hide', get_string('disable')));
                $class = '';

                if ($order > 1) {
                    $upparams = array('action' => 'up', 'factor' => $factor->name);
                    $upurl = new \moodle_url('tool/mfa/index.php', $upparams);
                    $uplink = \html_writer::link($upurl, $OUTPUT->pix_icon('t/up', get_string('moveup')));
                } else {
                    $uplink = \html_writer::link('', $uplink = $OUTPUT->spacer(array('style' => 'margin-right: .5rem')));
                }

                if ($order < count($enabledfactors)) {
                    $downparams = array('action' => 'down', 'factor' => $factor->name);
                    $downurl = new \moodle_url('tool/mfa/index.php', $downparams);
                    $downlink = \html_writer::link($downurl, $OUTPUT->pix_icon('t/down', get_string('movedown')));
                } else {
                    $downlink = '';
                }
                $updownlink = $uplink.$downlink;
                $order++;
            } else {
                $hideshowparams = array('action' => 'enable', 'factor' => $factor->name);
                $hideshowurl = new \moodle_url('tool/mfa/index.php', $hideshowparams);
                $hideshowlink = \html_writer::link($hideshowurl, $OUTPUT->pix_icon('t/show', get_string('disable')));
                $class = 'dimmed_text';
                $updownlink = '';
            }

            $rowarray = array($factor->get_display_name(), $hideshowlink, $updownlink, $factor->get_weight(), $settingslink);
            $row = new \html_table_row($rowarray);
            $row->attributes['class'] = $class;

            $table->data[] = $row;
        }

        $return = $OUTPUT->box_start('generalbox');
        $return .= \html_writer::table($table);
        $return .= $OUTPUT->box_end();

        return highlight($query, $return);
    }
}
