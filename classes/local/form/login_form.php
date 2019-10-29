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
 * MFA login form
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_mfa\local\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/formslib.php");

class login_form extends \moodleform
{
    /**
     * {@inheritDoc}
     * @see moodleform::definition()
     */
    public function definition()
    {
        global $OUTPUT;
        $mform = $this->_form;

        // TODO: Get the list of active factors.

        // TOTP Factor.
        $mform->addElement('html', $OUTPUT->heading(get_string('totp:header', 'tool_mfa'), 5));
        $mform->addElement('text', 'totp_verification_code', get_string('totp:verification_code', 'tool_mfa'));
        $mform->addHelpButton('totp_verification_code', 'totp:verification_code', 'tool_mfa');
        $mform->setType("totp_verification_code", PARAM_ALPHANUM);

        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $code = $data['totp_verification_code'];


        // require_once(__DIR__.'/../../../factor/totp/class/factor_totp.php');


        // $test = new \tool_mfa\factor\totp\factor_totp;
        $test = new \factor_totp\factor;

        //        $plugins = \tool_mfa\plugininfo\factor::get_plugins_by_sortorder();
//        foreach ($plugins as $plugin) {
//            $plugintypeclass = \core_plugin_manager::instance()->resolve_plugininfo_class($plugin->type);
//
//            $classname = $plugintypeclass.'\\'.$plugin->name;
//
//            if (class_exists($classname)) {
//                $instance = new $classname;
//                $test = $instance->is_enabled();
//            }
//        }

//
//        foreach ($plugins as $plugin) {
//
//            $new = $plugin;
//
//            if ($new->is_installed_and_upgraded()) {
//
//                $path = "tool_mfa\\factor\\factor_$plugin->name";
//
//                $aaa = new $path;
//                // $dir = str_replace("/siteroot/admin/tool/mfa", )
//                // $factor = new \tool_mfa\factor\totp\totp_factor();
//                if (!$aaa->validate($code)) {
//                    $errors['totp_verification_code'] = get_string('totp:error:verification_code', 'tool_mfa');
//                }
//            }
//
//        }


        return $errors;
    }
}
