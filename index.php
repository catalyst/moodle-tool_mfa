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
 * MFA configuration page.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->libdir.'/adminlib.php');

use tool_mfa\local\form\settings_form;

admin_externalpage_setup('tool_mfa_settings');

$output = $PAGE->get_renderer('tool_mfa');

// $config = get_mfa_config();

// $form = new settings_form(null, array('config' => $config));
$form = new settings_form();

//if ($data = $form->get_data()) {
//    redirect(new moodle_url('/admin/tool/mfa/index.php'));
//}

echo $output->header();
echo $output->heading(get_string('pluginname', 'tool_mfa'));
$form->display();
echo $output->footer();



