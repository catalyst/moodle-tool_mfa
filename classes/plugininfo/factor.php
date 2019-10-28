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
 * Subplugin info class.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa\plugininfo;

defined('MOODLE_INTERNAL') || die();

class factor extends \core\plugininfo\base {

    /**
     * Get enabled plugins, sorted by sortorder
     *
     * @return array Enabled plugins, sorted by sortorder
     */
    static public function get_plugins_by_sortorder() {

        $fileinfo = \core_plugin_manager::instance()->get_present_plugins('factor');
        $plugins = \core_plugin_manager::instance()->get_plugins_of_type('factor');

        foreach ($plugins as $name => $plugin) {
            if (isset($fileinfo[$name])) {
                $plugin->sortorder = $fileinfo[$name]->sortorder;
            }
        }
        usort($plugins, function($a, $b) {
            return $a->sortorder - $b->sortorder;
        });

        return $plugins;
    }
}