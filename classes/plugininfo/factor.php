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
     * Finds all factors.
     * @return array of factor subplugins.
     */
    public static function get_factors() {
        $return = array();
        $factors = \core_plugin_manager::instance()->get_plugins_of_type('factor');

        foreach ($factors as $factor) {
            $classname = '\\factor_'.$factor->name.'\\factor';
            if (class_exists($classname)) {
                $return[] = new $classname($factor->name);
            }
        }
        return $return;
    }

    /**
     * Finds factor by its name.
     * @return factor object or false if factor not found.
     */
    public static function get_factor($name) {
        $factors = \core_plugin_manager::instance()->get_plugins_of_type('factor');

        foreach ($factors as $factor) {
            if ($name == $factor->name) {
                $classname = '\\factor_'.$factor->name.'\\factor';
                if (class_exists($classname)) {
                    return new $classname($factor->name);
                }
            }
        }

        return false;
    }

    /**
     * Finds enabled factors.
     * @return array of factor subplugins
     */
    public static function get_enabled_factors() {
        $return = array();
        $factors = self::get_factors();

        foreach ($factors as $factor) {
            if ($factor->is_enabled()) {
                $return[] = $factor;
            }
        }

        return $return;
    }

    /**
     * Returns the information about plugin availability
     *
     * True means that the plugin is enabled. False means that the plugin is
     * disabled. Null means that the information is not available, or the
     * plugin does not support configurable availability or the availability
     * can not be changed.
     *
     * @return null|bool
     */
    public function is_enabled() {
        if (!$this->rootdir) {
            // Plugin missing.
            return false;
        }

        $factor = $this->get_factor($this->name);

        if ($factor) {
            return $factor->is_enabled();
        }

        return false;
    }

    public function get_settings_section_name() {
        return $this->type . '_' . $this->name;
    }

    /**
     * Loads factor settings to the settings tree
     *
     * This function usually includes settings.php file in plugins folder.
     * Alternatively it can create a link to some settings page (instance of admin_externalpage)
     *
     * @param \part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig whether the current user has moodle/site:config capability
     */
    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig or !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $section = $this->get_settings_section_name();

        $settings = new \admin_settingpage($section, $this->displayname, 'moodle/site:config', $this->is_enabled() === false);

        if ($adminroot->fulltree) {
            include($this->full_path('settings.php'));
        }

        $adminroot->add($parentnodename, $settings);
    }
}