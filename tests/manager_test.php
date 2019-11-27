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
 * Tests for MFA manager class.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa\tests;
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/tool_mfa_testcase.php');

class tool_mfa_manager_testcase extends tool_mfa_testcase {

    public function test_get_total_weight() {
        $this->resetAfterTest(true);

        // Create and login a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // First get weight with no active factors.
        $this->assertEquals(0, \tool_mfa\manager::get_total_weight());

        // Now setup a couple of input based factors
        $this->set_factor_state('totp', 1, 100);
        
        $this->set_factor_state('email', 1, 100);

        // Check weight is still 0 with no passes.
        $this->assertEquals(0, \tool_mfa\manager::get_total_weight());

        // Manually pass 1 factor
        $factor = \tool_mfa\plugininfo\factor::get_factor('totp');
        $totpdata = [
            'secret' => 'fakekey',
            'devicename' => 'fakedevice'
        ];
        $this->assertTrue($factor->setup_user_factor((object) $totpdata));
        $factor->set_state(\tool_mfa\plugininfo\factor::STATE_PASS);
        $this->assertEquals(100, \tool_mfa\manager::get_total_weight());

        // Now both.
        $factor2 = \tool_mfa\plugininfo\factor::get_factor('email');
        $factor2->set_state(\tool_mfa\plugininfo\factor::STATE_PASS);
        $this->assertEquals(200, \tool_mfa\manager::get_total_weight());

        // Now setup a no input factor, and check that weight is automatically added without input.
        $this->set_factor_state('auth', 1, 100);
        set_config('goodauth', '0', 'factor_auth');

        $this->assertEquals(300, \tool_mfa\manager::get_total_weight());
    }



}