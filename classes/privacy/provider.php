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
 * Privacy provider.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa\privacy;

defined('MOODLE_INTERNAL') || die;

use core_privacy\local\metadata\collection;

/**
 * Class provider
 * @package tool_mfa\privacy
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\data_provider {

    /**
     * Returns metadata about this plugin's privacy policy.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'tool_mfa',
            [
                'id' => 'privacy:metadata:tool_mfa:id',
                'userid' => 'privacy:metadata:tool_mfa:userid',
                'factor' => 'privacy:metadata:tool_mfa:factor',
                'secret' => 'privacy:metadata:tool_mfa:secret',
                'label' => 'privacy:metadata:tool_mfa:label',
                'timecreated' => 'privacy:metadata:tool_mfa:timecreated',
                'createdfromip' => 'privacy:metadata:tool_mfa:createdfromip',
                'timemodified' => 'privacy:metadata:tool_mfa:timemodified',
                'lastverified' => 'privacy:metadata:tool_mfa:lastverified',
            ],
            'privacy:metadata:tool_mfa'
        );

        return $collection;
    }
}
