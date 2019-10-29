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
     * Finds all enabled plugins, the result may include missing plugins.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        $return = array();
        $plugins = \core_plugin_manager::instance()->get_plugins_of_type('factor');

        foreach ($plugins as $plugin) {
            $classname = '\\factor_'.$plugin->name.'\\factor';
            if (class_exists($classname)) {
                $instance = new $classname;
                if ($instance->is_enabled()) {
                    $return[] = $instance;
                }
            }
        }

        return $return;
    }
}