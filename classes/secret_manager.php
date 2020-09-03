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

    const REVOKED = 'revoked';
    const VALID = 'valid';
    const NONVALID = 'nonvalid';

    private $factor;
    private $sessionvar;

    public function __construct(string $factor) {
        $this->factor = $factor;
        $this->sessionvar = "tool_mfa_secrets_{$this->factor}";
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

    private function add_secret_to_db(string $secret, int $expires) {
        global $DB, $USER;
        $expirytime = time() + $expires;

        $data = [
            'userid' => $USER,
            'factor' => $this->factor,
            'secret' => $secret,
            'timecreated' => time(),
            'expiry' => $expirytime,
            'revoked' => 0
        ];
        $DB->insert_record('tool_mfa_secrets', $data);
    }

    private function add_secret_to_session(string $secret, int $expires) {
        global $SESSION;
        $expirytime = time() + $expires;

        $data = [
            'secret' => $secret,
            'timecreated' => time(),
            'expiry' => $expirytime,
            'revoked' => 0
        ];
        $datastr = json_encode($data);

        $SESSION->$this->sessionvar = $datastr;
    }

    public function validate_secret(string $secret) {
        global $DB, $SESSION, $USER;

        // Check Session first, more likely and less overhead.
        $status = $this->check_secret_against_session($secret);
        if ($status !== self::NONVALID) {
            if ($status === self::VALID) {
                // Remove session token.
                unset($SESSION->$this->sessionvar);
            }
            return $status;
        }

        // Now DB.
        $status = $this->check_secret_against_db($secret);
        if ($status !== self::NONVALID) {
            if ($status === self::VALID) {
                // Cleanup DB $record.
                $DB->delete_records('tool_mfa_secrets', ['userid' => $USER->id, 'factor' => $this->factor]);
            }
            return $status;
        }

        //This is always nonvalid.
        return $status;
    }

    private function check_secret_against_db(string $secret) {
        global $DB;

        $sql = 'SELECT *
                  FROM {tool_mfa_secrets}
                 WHERE secret = :secret
                   AND expiry > :now';

        $record = $DB->get_record_sql($sql, ['secret' => $secret, 'now' => time()]);

        if (!empty($record)) {
            if ($record->revoked) {
                return self::REVOKED;
            }
            return self::VALID;
        }
        return self::NONVALID;
    }

    private function check_secret_against_session(string $secret) {
        global $SESSION;

       if (!empty($SESSION->this->sessionvar)) {
           $data = json_decode($SESSION->$this->sessionvar);
           if ($data->revoked) {
               return self::REVOKED;
           }
           return self::VALID;
       }
       return self::NONVALID;
    }

    public function revoke_secret(string $secret){}

    private function has_active_secret(){}
}