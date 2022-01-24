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

namespace factor_token\tests;

/**
 * Tests for MFA manager class.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor_token_test extends \advanced_testcase {

    public function test_calculate_expiry_time() {
        $this->resetAfterTest();

        $timestamp = 1642213800; // 1230 UTC.

        set_config('expireovernight', 0, 'factor_token');
        $factor = new \factor_token\factor('token');
        $method = new \ReflectionMethod($factor, 'calculate_expiry_time');
        $method->setAccessible(true);

        // Test that non-overnight timestamps are just exactly as configured.
        // We don't need to care about 0 or negative ints, they will just make the cookie expire immediately.
        $expiry = $method->invoke($factor, $timestamp);
        $this->assertEquals($expiry[1], DAYSECS);

        set_config('expiry', HOURSECS, 'factor_token');
        $expiry = $method->invoke($factor, $timestamp);
        $this->assertGreaterThan(HOURSECS - 30, $expiry[1]);
        $this->assertLessThan(HOURSECS + 30, $expiry[1]);

        set_config('expireovernight', 1, 'factor_token');
        // Manually calculate the next reset time.
        $reset = strtotime('tomorrow 0200', $timestamp);
        $resetdelta = $reset - $timestamp;
        // Confirm that a timestamp that doesnt reach reset time.
        if ($timestamp + HOURSECS < $reset) {
            $expiry = $method->invoke($factor, $timestamp);
            $this->assertGreaterThan(HOURSECS - 30, $expiry[1]);
            $this->assertLessThan(HOURSECS + 30, $expiry[1]);
        }

        set_config('expiry', 2 * DAYSECS, 'factor_token');
        // Now confirm that the returned expiry is less than the absolute amount.
        $expiry = $method->invoke($factor, $timestamp);
        $this->assertGreaterThan(DAYSECS, $expiry[1]);
        $this->assertLessThan(2 * DAYSECS, $expiry[1]);
        $this->assertGreaterThan($resetdelta + DAYSECS - 30, $expiry[1]);
        $this->assertLessThan($resetdelta + DAYSECS + 30, $expiry[1]);
    }
}
