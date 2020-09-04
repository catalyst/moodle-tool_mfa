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
 * Tests for MFA secret manager class.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa\tests;

defined('MOODLE_INTERNAL') || die();

class tool_mfa_secret_manager_testcase extends \advanced_testcase {
    public function test_create_secret() {
        global $DB, $SESSION;

        $this->resetAfterTest(true);
        $this->setUser($this->getDataGenerator()->create_user());

        // Test adding secret to DB.
        $secman = new \tool_mfa\secret_manager('mock');

        $sec1 = $secman->create_secret(1800, false);
        $count1 = $DB->count_records('tool_mfa_secrets', ['factor' => 'mock']);
        $this->assertEquals(1 ,$count1);
        $this->assertNotEquals('', $sec1);
        $sec2 = $secman->create_secret(1800, false);
        $count2 = $DB->count_records('tool_mfa_secrets', ['factor' => 'mock']);
        $this->assertEquals(1, $count2);
        $this->assertEquals('', $sec2);
        $DB->delete_records('tool_mfa_secrets', []);

        // Now adding secret to session.
        $this->assertTrue(empty($SESSION->tool_mfa_secrets_mock));
        $sec1 = $secman->create_secret(1800, true);
        $this->assertTrue(!empty($SESSION->tool_mfa_secrets_mock));
        $this->assertNotEquals('', $sec1);
        $sec2 = $secman->create_secret(1800, true);
        $this->assertTrue(!empty($SESSION->tool_mfa_secrets_mock));
        $this->assertEquals('', $sec2);
        unset($SESSION->tool_mfa_secrets_mock);

        // Now test adding a forced code.
        $sec1 = $secman->create_secret(1800, false);
        $count1 = $DB->count_records('tool_mfa_secrets', ['factor' => 'mock']);
        $this->assertEquals(1 ,$count1);
        $this->assertNotEquals('', $sec1);
        $sec2 = $secman->create_secret(1800, false, 'code');
        $count2 = $DB->count_records('tool_mfa_secrets', ['factor' => 'mock']);
        $this->assertEquals(2, $count2);
        $this->assertEquals('code', $sec2);
        $DB->delete_records('tool_mfa_secrets', []);
    }

    public function test_add_secret_to_db() {
        global $DB, $USER;

        $this->resetAfterTest(true);
        $secman = new \tool_mfa\secret_manager('mock');
        $this->setUser($this->getDataGenerator()->create_user());

        // Let's make stuff public using reflection.
        $reflectedscanner = new \ReflectionClass($secman);
        $reflectedmethod = $reflectedscanner->getMethod('add_secret_to_db');
        $reflectedmethod->setAccessible(true);

        //Now add a secret and confirm it creates the correct record.
        $reflectedmethod->invoke($secman, 'code', 1800);
        $record = $DB->get_record('tool_mfa_secrets', []);
        $this->assertEquals('code', $record->secret);
        $this->assertEquals($USER->id, $record->userid);
        $this->assertEquals('mock', $record->factor);
        $this->assertGreaterThanOrEqual(time(), (int) $record->expiry);
        $this->assertEquals(0, $record->revoked);
    }

    public function test_add_secret_to_session() {
        global $SESSION;

        $this->resetAfterTest(true);
        $secman = new \tool_mfa\secret_manager('mock');
        $this->setUser($this->getDataGenerator()->create_user());

        // Let's make stuff public using reflection.
        $reflectedscanner = new \ReflectionClass($secman);
        $reflectedmethod = $reflectedscanner->getMethod('add_secret_to_session');
        $reflectedmethod->setAccessible(true);

        //Now add a secret and confirm it creates the correct record.
        $reflectedmethod->invoke($secman, 'code', 1800);
        $parent = json_decode($SESSION->tool_mfa_secrets_mock, true);
        $data = json_decode($parent['code']);
        $this->assertEquals('code', $data->secret);
        $this->assertGreaterThanOrEqual(time(), (int) $data->expiry);
        $this->assertEquals(0, $data->revoked);
    }

    public function test_validate_secret() {
        global $DB, $SESSION;


        // Test adding a code and getting it returned, then validated.
        $this->resetAfterTest(true);
        $this->setUser($this->getDataGenerator()->create_user());
        $secman = new \tool_mfa\secret_manager('mock');

        $secret = $secman->create_secret(1800, true);
        $this->assertEquals(\tool_mfa\secret_manager::VALID, $secman->validate_secret($secret));
        unset($SESSION->tool_mfa_secrets_mock);
        $secret = $secman->create_secret(1800, false);
        $this->assertEquals(\tool_mfa\secret_manager::VALID, $secman->validate_secret($secret));
        $DB->delete_records('tool_mfa_secrets', []);

        // Test a manual forced code.
        $secret = $secman->create_secret(1800, true, 'code');
        $this->assertEquals(\tool_mfa\secret_manager::VALID, $secman->validate_secret($secret));
        unset($SESSION->tool_mfa_secrets_mock);
        $secret = $secman->create_secret(1800, false);
        $this->assertEquals(\tool_mfa\secret_manager::VALID, $secman->validate_secret($secret));
        $DB->delete_records('tool_mfa_secrets', []);

        // Test bad codes.
        $secret = $secman->create_secret(1800, true);
        $this->assertEquals(\tool_mfa\secret_manager::NONVALID, $secman->validate_secret('nonvalid'));
        unset($SESSION->tool_mfa_secrets_mock);
        $secret = $secman->create_secret(1800, false);
        $this->assertEquals(\tool_mfa\secret_manager::NONVALID, $secman->validate_secret('nonvalid'));
        $DB->delete_records('tool_mfa_secrets', []);

        // Test validate when no secrets present.
        $this->assertEquals(\tool_mfa\secret_manager::NONVALID, $secman->validate_secret('nonvalid'));

        // Test revoked secrets.
        $secret = $secman->create_secret(1800, false);
        $DB->set_field('tool_mfa_secrets', 'revoked', 1, []);
        $this->assertEquals(\tool_mfa\secret_manager::REVOKED, $secman->validate_secret($secret));
        $DB->delete_records('tool_mfa_secrets', []);

        // Test expired secrets.
        $secret = $secman->create_secret(-1, true);
        $this->assertEquals(\tool_mfa\secret_manager::NONVALID, $secman->validate_secret($secret));
        unset($SESSION->tool_mfa_secrets_mock);
        $secret = $secman->create_secret(-1, false);
        $this->assertEquals(\tool_mfa\secret_manager::NONVALID, $secman->validate_secret($secret));
        $DB->delete_records('tool_mfa_secrets', []);
        unset($SESSION->tool_mfa_secrets_mock);
    }

    public function test_check_secret_against_db() {
        global $DB;

        $this->resetAfterTest(true);
        $secman = new \tool_mfa\secret_manager('mock');
        $this->setUser($this->getDataGenerator()->create_user());

        // Let's make stuff public using reflection.
        $reflectedscanner = new \ReflectionClass($secman);
        $reflectedmethod = $reflectedscanner->getMethod('check_secret_against_db');
        $reflectedmethod->setAccessible(true);

        // Good code.
        $secret = $secman->create_secret(1800, false);
        $result = $reflectedmethod->invoke($secman, $secret);
        $this->assertEquals(\tool_mfa\secret_manager::VALID, $result);

        // Bad code.
        $result = $reflectedmethod->invoke($secman, 'nonvalid');
        $this->assertEquals(\tool_mfa\secret_manager::NONVALID, $result);

        // Revoked code.
        $DB->set_field('tool_mfa_secrets', 'revoked', 1, []);
        $result = $reflectedmethod->invoke($secman, $secret);
        $this->assertEquals(\tool_mfa\secret_manager::REVOKED, $result);
        $DB->set_field('tool_mfa_secrets', 'revoked', 0, []);

        // Expired code.
        $DB->set_field('tool_mfa_secrets', 'expiry', time() - 1, []);
        $result = $reflectedmethod->invoke($secman, $secret);
        $this->assertEquals(\tool_mfa\secret_manager::NONVALID, $result);

        // Code in session not DB.
        $secret = $secman->create_secret(1800, true);
        $result = $reflectedmethod->invoke($secman, $secret);
        $this->assertEquals(\tool_mfa\secret_manager::NONVALID, $result);
    }

    public function test_check_secret_against_session() {
        global $SESSION;

        $this->resetAfterTest(true);
        $secman = new \tool_mfa\secret_manager('mock');
        $this->setUser($this->getDataGenerator()->create_user());

        // Let's make stuff public using reflection.
        $reflectedscanner = new \ReflectionClass($secman);
        $reflectedmethod = $reflectedscanner->getMethod('check_secret_against_session');
        $reflectedmethod->setAccessible(true);

        // Good code.
        $secret = $secman->create_secret(1800, true);
        $result = $reflectedmethod->invoke($secman, $secret);
        $this->assertEquals(\tool_mfa\secret_manager::VALID, $result);

        // Bad code.
        $result = $reflectedmethod->invoke($secman, 'nonvalid');
        $this->assertEquals(\tool_mfa\secret_manager::NONVALID, $result);

        // Revoked code.
        $secman->revoke_secret($secret);
        $result = $reflectedmethod->invoke($secman, $secret);
        $this->assertEquals(\tool_mfa\secret_manager::REVOKED, $result);
        unset($SESSION->tool_mfa_secrets_mock);

        // Expired code.
        $secret = $secman->create_secret(-1, true);
        $result = $reflectedmethod->invoke($secman, $secret);
        $this->assertEquals(\tool_mfa\secret_manager::NONVALID, $result);

        // Code in DB not session.
        $secret = $secman->create_secret(1800, false);
        $result = $reflectedmethod->invoke($secman, $secret);
        $this->assertEquals(\tool_mfa\secret_manager::NONVALID, $result);
    }

    public function test_revoke_secret() {
        global $DB, $SESSION;

        $this->resetAfterTest(true);
        $secman = new \tool_mfa\secret_manager('mock');
        $this->setUser($this->getDataGenerator()->create_user());

        // Session secrets.
        $secret = $secman->create_secret(1800, true);
        $secman->revoke_secret($secret);
        $this->assertEquals(\tool_mfa\secret_manager::REVOKED, $secman->validate_secret($secret));
        unset($SESSION->tool_mfa_secrets_mock);

        // DB secrets.
        $secret = $secman->create_secret(1800, false);
        $secman->revoke_secret($secret);
        $this->assertEquals(\tool_mfa\secret_manager::REVOKED, $secman->validate_secret($secret));
        $DB->delete_records('tool_mfa_secrets', []);

        // Revoke a non-valid secret.
        $secret = $secman->create_secret(1800, false);
        $secman->revoke_secret('nonvalid');
        $this->assertEquals(\tool_mfa\secret_manager::NONVALID, $secman->validate_secret('nonvalid'));
    }

    public function test_revoke_session_secret() {
        global $SESSION;

        $this->resetAfterTest(true);
        $secman = new \tool_mfa\secret_manager('mock');
        $this->setUser($this->getDataGenerator()->create_user());

        // Let's make stuff public using reflection.
        $reflectedscanner = new \ReflectionClass($secman);

        $reflectedmethod = $reflectedscanner->getMethod('revoke_session_secret');
        $reflectedmethod->setAccessible(true);

        // We do not need to test bad data, as it is validated before this method call.
        // Test revoking a good secret.
        $secret = $secman->create_secret(1800, true);
        $parentarr = json_decode($SESSION->tool_mfa_secrets_mock, true);
        $data = json_decode($parentarr[$secret]);
        $this->assertEquals(0, $data->revoked);

        $reflectedmethod->invoke($secman, $secret);
        $parentarr = json_decode($SESSION->tool_mfa_secrets_mock, true);
        $data = json_decode($parentarr[$secret]);
        $this->assertEquals(1, $data->revoked);
    }

    public function test_revoke_db_secret() {
        global $DB;

        $this->resetAfterTest(true);
        $secman = new \tool_mfa\secret_manager('mock');
        $this->setUser($this->getDataGenerator()->create_user());

        // Let's make stuff public using reflection.
        $reflectedscanner = new \ReflectionClass($secman);

        $reflectedmethod = $reflectedscanner->getMethod('revoke_db_secret');
        $reflectedmethod->setAccessible(true);

        // We do not need to test bad data, as it is validated before this method call.
        // Test revoking a good secret.
        $secret = $secman->create_secret(1800, false);
        $record = $DB->get_record('tool_mfa_secrets', []);
        $this->assertEquals(0, $record->revoked);

        $reflectedmethod->invoke($secman, $secret);
        $record = $DB->get_record('tool_mfa_secrets', []);
        $this->assertEquals(1, $record->revoked);
    }

    public function test_has_active_secret() {
        global $DB, $SESSION;

        $this->resetAfterTest(true);
        $secman = new \tool_mfa\secret_manager('mock');
        $this->setUser($this->getDataGenerator()->create_user());

        // Let's make stuff public using reflection.
        $reflectedscanner = new \ReflectionClass($secman);

        $reflectedmethod = $reflectedscanner->getMethod('has_active_secret');
        $reflectedmethod->setAccessible(true);

        // Session secrets.
        $this->assertFalse($reflectedmethod->invoke($secman));
        $secman->create_secret(1800, true);
        $this->assertTrue($reflectedmethod->invoke($secman));
        unset($SESSION->tool_mfa_secrets_mock);
        $secman->create_secret(-1, true);
        $this->assertFalse($reflectedmethod->invoke($secman));
        unset($SESSION->tool_mfa_secrets_mock);
        $secret = $secman->create_secret(1800, true);
        $secman->revoke_secret($secret);
        $this->assertFalse($reflectedmethod->invoke($secman));

        // DB secrets.
        $this->assertFalse($reflectedmethod->invoke($secman));
        $secman->create_secret(1800, false);
        $this->assertTrue($reflectedmethod->invoke($secman));
        $DB->delete_records('tool_mfa_secrets', []);
        $secman->create_secret(-1, false);
        $this->assertFalse($reflectedmethod->invoke($secman));
        $DB->delete_records('tool_mfa_secrets', []);
        $secret = $secman->create_secret(1800, false);
        $secman->revoke_secret($secret);
        $this->assertFalse($reflectedmethod->invoke($secman));
    }
}