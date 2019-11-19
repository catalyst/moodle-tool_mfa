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

namespace tool_mfa\tests;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/tool_mfa_testcase.php');

class admin_setting_managemfa_testcase extends tool_mfa_testcase {

    public function test_get_factor_combinations_default() {
        $namagemfa = new \tool_mfa\local\admin_setting_managemfa();
        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $combinations = $namagemfa->get_factor_combinations($factors, 0, count($factors) - 1);
        $this->assertEquals(0, count($factors));
        $this->assertEquals(0, count($combinations));
    }

    public function test_get_factor_combinations_provider() {
        $provider = array();

        $factors = array();
        $provider[] = array($factors, 0);

        $factors = array();
        $factors[] = array('name' => 'totp', 'enabled' => 1, 'weight' => 90);
        $provider[] = array($factors, 0);

        $factors = array();
        $factors[] = array('name' => 'email', 'enabled' => 1, 'weight' => 100);
        $provider[] = array($factors, 1);

        $factors = array();
        $factors[] = array('name' => 'iprange', 'enabled' => 1, 'weight' => 150);
        $provider[] = array($factors, 1);

        $factors = array();
        $factors[] = array('name' => 'iprange', 'enabled' => 1, 'weight' => 40);
        $factors[] = array('name' => 'email', 'enabled' => 1, 'weight' => 40);
        $provider[] = array($factors, 0);

        $factors = array();
        $factors[] = array('name' => 'email', 'enabled' => 1, 'weight' => 90);
        $factors[] = array('name' => 'totp', 'enabled' => 1, 'weight' => 40);
        $provider[] = array($factors, 1);

        $factors = array();
        $factors[] = array('name' => 'totp', 'enabled' => 1, 'weight' => 100);
        $factors[] = array('name' => 'email', 'enabled' => 1, 'weight' => 100);
        $provider[] = array($factors, 2);

        $factors = array();
        $factors[] = array('name' => 'totp', 'enabled' => 1, 'weight' => 100);
        $factors[] = array('name' => 'email', 'enabled' => 1, 'weight' => 100);
        $factors[] = array('name' => 'iprange', 'enabled' => 1, 'weight' => 100);
        $provider[] = array($factors, 3);

        $factors = array();
        $factors[] = array('name' => 'totp', 'enabled' => 1, 'weight' => 90);
        $factors[] = array('name' => 'email', 'enabled' => 1, 'weight' => 30);
        $factors[] = array('name' => 'iprange', 'enabled' => 1, 'weight' => 40);
        $provider[] = array($factors, 2);

        $factors = array();
        $factors[] = array('name' => 'email', 'enabled' => 1, 'weight' => 30);
        $factors[] = array('name' => 'iprange', 'enabled' => 1, 'weight' => 40);
        $factors[] = array('name' => 'totp', 'enabled' => 1, 'weight' => 90);
        $provider[] = array($factors, 3);

        $factors = array();
        $factors[] = array('name' => 'email', 'enabled' => 1, 'weight' => 30);
        $factors[] = array('name' => 'iprange', 'enabled' => 1, 'weight' => 40);
        $factors[] = array('name' => 'totp', 'enabled' => 1, 'weight' => 90);
        $factors[] = array('name' => 'auth', 'enabled' => 1, 'weight' => 90);
        $provider[] = array($factors, 7);

        $factors = array();
        $factors[] = array('name' => 'email', 'enabled' => 1, 'weight' => 50);
        $factors[] = array('name' => 'iprange', 'enabled' => 1, 'weight' => 50);
        $factors[] = array('name' => 'totp', 'enabled' => 1, 'weight' => 50);
        $factors[] = array('name' => 'auth', 'enabled' => 1, 'weight' => 50);
        $provider[] = array($factors, 6);

        $factors = array();
        $factors[] = array('name' => 'email', 'enabled' => 0, 'weight' => 50);
        $factors[] = array('name' => 'iprange', 'enabled' => 1, 'weight' => 50);
        $factors[] = array('name' => 'totp', 'enabled' => 0, 'weight' => 50);
        $factors[] = array('name' => 'auth', 'enabled' => 1, 'weight' => 50);
        $provider[] = array($factors, 1);

        $factors = array();
        $factors[] = array('name' => 'email', 'enabled' => 0, 'weight' => 50);
        $factors[] = array('name' => 'iprange', 'enabled' => 1, 'weight' => 50);
        $factors[] = array('name' => 'totp', 'enabled' => 1, 'weight' => 50);
        $factors[] = array('name' => 'auth', 'enabled' => 1, 'weight' => 50);
        $provider[] = array($factors, 3);

        return $provider;
    }

    /**
     * @dataProvider test_get_factor_combinations_provider
     *
     * @param array $factorset configured factors
     * @param int $combinationscount expected count of available combinations
     */
    public function test_get_factor_combinations_with_data_provider($factorset, $combinationscount) {
        $this->resetAfterTest();
        $enabledcount = 0;

        foreach ($factorset as $factor) {
            $this->set_factor_state($factor['name'], $factor['enabled'], $factor['weight']);
            if ($factor['enabled'] == 1) {
                $enabledcount++;
            }
        }

        $namagemfa = new \tool_mfa\local\admin_setting_managemfa();
        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $combinations = $namagemfa->get_factor_combinations($factors, 0, count($factors) - 1);

        foreach ($combinations as $combination) {
            $this->assertGreaterThanOrEqual(100, $combination['totalweight']);
            $this->assertLessThan(200, $combination['totalweight']);
            $this->assertGreaterThanOrEqual(1, count($combination['combination']));
            foreach ($combination['combination'] as $conbinationfactor) {
                $this->assertInstanceOf('\tool_mfa\local\factor\object_factor', $conbinationfactor);
            }
        }

        $this->assertEquals($enabledcount, count($factors));
        $this->assertEquals($combinationscount, count($combinations));
    }
}
