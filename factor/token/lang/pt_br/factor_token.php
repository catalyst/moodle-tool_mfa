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
 * Language strings.
 *
 * @package     factor_token
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Confiar neste dispositivo';

$string['event:token_created'] = 'Token MFA criado.';
$string['form:trust'] = 'Confie neste dispositivo por {$a}.';
$string['privacy:metadata'] = 'O plug-in do fator de token não armazena nenhum dado pessoal.';
$string['settings:expiry'] = 'Duração da confiança';
$string['settings:expiry_help'] = 'A duração em que um dispositivo é confiável antes de exigir uma nova autenticação MFA..';
$string['settings:expireovernight'] = 'Expire a confiança durante a noite';
$string['settings:expireovernight_help'] = 'Isso força os tokens a expirarem durante a noite, evitando interrupções no meio do dia para os usuários. Em vez disso, eles serão solicitados a autenticar o MFA no início de um dia após a expiração.';
$string['summarycondition'] = ' o usuário já confiou neste dispositivo';
