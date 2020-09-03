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
 * MFA secret management class.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa;

defined('MOODLE_INTERNAL') || die();

class secret_manager {

    private $factor;

    public function __construct(string $factor) {
        $this->factor = $factor;
    }

    public function create_secret(int $expires, bool $session, string $secret = null){
        // Setup a secret if not provided.
        if (empty($secret)) {
            $secret = random_int(100000, 999999);
        }

        // Check if there already an active secret.
        if ($this->has_active_secret()) {
            return;
        }

        // Now pass the code where it needs to go.
        if ($session) {
            $this->add_secret_to_session($secret, $expires);
        } else {
            $this->add_secret_to_db($secret, $expires);
        }
    }
    private function add_secret_to_db(string $secret, $expires){}
    private function add_secret_to_session(){}

    public function validate_secret(string $secret){}
    private function check_secret_against_db(){}
    private function check_secret_against_session(){}

    public function revoke_secret(string $secret){}

    private function has_active_secret(){}
}