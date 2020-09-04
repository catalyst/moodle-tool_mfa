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

    /**
     * This function creates or takes a secret, and stores it in the database or session.
     *
     * @param integer $expires the length of time the secret is valid. e.g. 1 min = 60
     * @param boolean $session whether this secret should be stored in the session.
     * @param string $secret an optional provided secret
     * @return string the secret code, or 0 if no new code created.
     */
    public function create_secret(int $expires, bool $session, string $secret = null) : string {

        // Check if there already an active secret, unless we are forcibly given a code.
        if ($this->has_active_secret() && empty($secret)) {
            return '';
        }

        // Setup a secret if not provided.
        if (empty($secret)) {
            $secret = random_int(100000, 999999);
        }

        // Now pass the code where it needs to go.
        if ($session) {
            $this->add_secret_to_session($secret, $expires);
        } else {
            $this->add_secret_to_db($secret, $expires);
        }

        return $secret;
    }

    /**
     * Inserts the provided secret into the database with a given expiry duration.
     *
     * @param string $secret the secret to store
     * @param integer $expires expiry duration in seconds
     * @return void
     */
    private function add_secret_to_db(string $secret, int $expires) : void {
        global $DB, $USER;
        $expirytime = time() + $expires;

        $data = [
            'userid' => $USER->id,
            'factor' => $this->factor,
            'secret' => $secret,
            'timecreated' => time(),
            'expiry' => $expirytime,
            'revoked' => 0
        ];
        $DB->insert_record('tool_mfa_secrets', $data);
    }

    /**
     * Inserts the provided secret into the session with a given expiry duration.
     *
     * @param string $secret the secret to store
     * @param integer $expires expiry duration in seconds
     * @return void
     */
    private function add_secret_to_session(string $secret, int $expires) : void {
        global $SESSION;

        $expirytime = time() + $expires;
        $data = [
            'secret' => $secret,
            'timecreated' => time(),
            'expiry' => $expirytime,
            'revoked' => 0
        ];
        $datastr = json_encode($data);

        // Determine if there is already session data.
        $field = $this->sessionvar;
        if (!empty($SESSION->$field)) {
            $parentarr = json_decode($SESSION->$field, true);
        } else {
            $parentarr = [];
        }

        // If there is ever secret collision (from forcing) 1 record is still fine.
        $parentarr[$secret] = $datastr;

        $SESSION->$field = json_encode($parentarr);
    }

    /**
     * Validates whether the provided secret is currently valid.
     *
     * @param string $secret the secret to check
     * @return string a secret manager state constant
     */
    public function validate_secret(string $secret) : string {
        global $DB, $SESSION, $USER;

        // Check Session first, more likely and less overhead.
        $status = $this->check_secret_against_session($secret);
        if ($status !== self::NONVALID) {
            if ($status === self::VALID) {
                // Remove session token.
                $field = $this->sessionvar;
                $data = json_decode($SESSION->$field, true);
                unset($data[$secret]);
                $SESSION->$field = json_encode($data);
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

    /**
     * Checks if a given secret is valid from the Database.
     *
     * @param string $secret the secret to check.
     * @return string a secret manager state constant.
     */
    private function check_secret_against_db(string $secret) : string {
        global $DB, $USER;

        $sql = "SELECT *
                  FROM {tool_mfa_secrets}
                 WHERE secret = :secret
                   AND expiry > :now
                   AND userid = :userid
                   AND factor = :factor";

        $record = $DB->get_record_sql($sql, ['secret' => $secret, 'now' => time(), 'userid' => $USER->id, 'factor' => $this->factor]);

        if (!empty($record)) {
            if ($record->revoked) {
                return self::REVOKED;
            }
            return self::VALID;
        }
        return self::NONVALID;
    }

    /**
     * Checks whether a given secret is valid in the session.
     *
     * @param string $secret the secret to check
     * @return string a secret manager state constant
     */
    private function check_secret_against_session(string $secret) : string {
        global $SESSION;

        $field = $this->sessionvar;
        if (!empty($SESSION->$field)) {
            $parentarr = json_decode($SESSION->$field, true);
            if (!empty($parentarr[$secret])) {
                $data = json_decode($parentarr[$secret]);
                if ($data->secret !== $secret || $data->expiry < time()) {
                    return self::NONVALID;
                } else if ($data->revoked) {
                    return self::REVOKED;
                }
                return self::VALID;
            }
            return self::NONVALID;
        }
        return self::NONVALID;
    }

    /**
     * Revokes the provided secret code for the user.
     *
     * @param string $secret the secret to revoke.
     * @return void
     */
    public function revoke_secret(string $secret) : void {
        // Check session first. Faster and more likely.
        $status = $this->check_secret_against_session($secret);
        if ($status === self::VALID) {
            // We only need to do something if this is a valid secret.
            $this->revoke_session_secret($secret);
        }

        // Now DB.
        $status = $this->check_secret_against_db($secret);
        if ($status === self::VALID) {
            $this->revoke_db_secret($secret);
        }
    }

    /**
     * Revokes the current session level secret.
     *
     * @param string $secret the secret to revoke.
     * @return void
     */
    private function revoke_session_secret(string $secret) : void {
        global $SESSION;

        $field = $this->sessionvar;
        // We know at this point this is a valid session secret.
        $parentarr = json_decode($SESSION->$field, true);
        $data = json_decode($parentarr[$secret]);
        $data->revoked = 1;
        $parentarr[$secret] = json_encode($data);
        // Now write it back into the session.
        $SESSION->$field = json_encode($parentarr);
    }

    /**
     * Revokes a DB secret.
     *
     * @param string $secret
     * @return void
     */
    private function revoke_db_secret(string $secret) : void {
        global $DB, $USER;
        // We know this secret is valid, so we don't need to check expiry.
        $DB->set_field('tool_mfa_secrets', 'revoked', 1, ['userid' => $USER->id, 'factor' => $this->factor, 'secret' => $secret]);
    }

    /**
     * Checks whether this factor currently has an active secret, and should not add another.
     *
     * @return boolean
     */
    private function has_active_secret() : bool {
        global $DB, $SESSION, $USER;

        $field = $this->sessionvar;
        // Check for session first.
        if (isset($SESSION->$field)) {
            $parentarr = json_decode($SESSION->$field, true);
            foreach ($parentarr as $dataenc) {
                $data = json_decode($dataenc);
                if ($data->expiry > time() && !$data->revoked) {
                    return true;
                }
            }
        }

        // Now DB.
        $sql = "SELECT *
                  FROM {tool_mfa_secrets}
                 WHERE expiry > :now
                   AND userid = :userid
                   AND factor = :factor
                   AND revoked = 0";
        if ($DB->record_exists_sql($sql, ['now' => time(), 'userid' => $USER->id, 'factor' => $this->factor])) {
            return true;
        }

        return false;
    }
}