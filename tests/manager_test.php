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

        // Now setup a couple of input based factors.
        $this->set_factor_state('totp', 1, 100);

        $this->set_factor_state('email', 1, 100);

        // Check weight is still 0 with no passes.
        $this->assertEquals(0, \tool_mfa\manager::get_total_weight());

        // Manually pass 1 .
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

    public function test_get_status() {
        $this->resetAfterTest(true);

        // Create and login a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Check for fail status with no factors.
        $this->assertEquals(\tool_mfa\manager::get_status(), \tool_mfa\plugininfo\factor::STATE_FAIL);

        // Now add a no input factor.
        $this->set_factor_state('auth', 1, 100);
        set_config('goodauth', '0', 'factor_auth');

        // Check state is now passing.
        $this->assertEquals(\tool_mfa\manager::get_status(), \tool_mfa\plugininfo\factor::STATE_PASS);

        // Now add a failure state factor, and ensure that fail takes precedent.
        $this->set_factor_state('email', 1, 100);
        $factoremail = \tool_mfa\plugininfo\factor::get_factor('email');
        $factoremail->set_state(\tool_mfa\plugininfo\factor::STATE_FAIL);

        $this->assertEquals(\tool_mfa\manager::get_status(), \tool_mfa\plugininfo\factor::STATE_FAIL);

        // Remove no input factor, and remove fail state from email. Simulates no data entered yet.
        $this->set_factor_state('auth', 0, 100);
        $factoremail->set_state(\tool_mfa\plugininfo\factor::STATE_UNKNOWN);

        $this->assertEquals(\tool_mfa\manager::get_status(), \tool_mfa\plugininfo\factor::STATE_NEUTRAL);
    }

    public function test_passed_enough_factors() {
        $this->resetAfterTest(true);

        // Create and login a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Check when no factors are setup.
        $this->assertEquals(\tool_mfa\manager::passed_enough_factors(), false);

        // Setup a no input factor.
        $this->set_factor_state('auth', 1, 100);
        set_config('goodauth', '0', 'factor_auth');

        // Check that is enough to pass.
        $this->assertEquals(\tool_mfa\manager::passed_enough_factors(), true);

        // Lower the weight of the factor.
        $this->set_factor_state('auth', 1, 75);
        $this->assertEquals(\tool_mfa\manager::passed_enough_factors(), false);

        // Add another factor to get enough weight to pass, but dont set pass state yet.
        $this->set_factor_state('email', 1, 100);
        $factoremail = \tool_mfa\plugininfo\factor::get_factor('email');
        $this->assertEquals(\tool_mfa\manager::passed_enough_factors(), false);

        // Now pass the factor and check weight.
        $factoremail->set_state(\tool_mfa\plugininfo\factor::STATE_PASS);
        $this->assertEquals(\tool_mfa\manager::passed_enough_factors(), true);
    }
}

