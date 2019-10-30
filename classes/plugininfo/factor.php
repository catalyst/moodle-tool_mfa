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
     * @return array of factor subplugins
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
//        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
//        $ADMIN = $adminroot; // May be used in settings.php.
//        $plugininfo = $this; // Also can be used inside settings.php.

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

        $adminroot->add('tools', $settings);
    }
}