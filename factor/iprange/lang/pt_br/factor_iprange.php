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
 * @package     factor_iprange
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['allowedipsempty'] = 'Ninguém vai passar desse fator! Você pode adicionar seu próprio endereço IP (<i>{$a->ip}</i>)';
$string['allowedipshasmyip'] = 'Seu IP (<i>{$a->ip}</i>) está na lista e você passará por este fator.';
$string['allowedipshasntmyip'] = 'Seu IP (<i>{$a->ip}</i>) não está na lista e você não passará por este fator.';
$string['pluginname'] = 'Fator de intervalo de IP';
$string['privacy:metadata'] = 'O plug-in Fator de intervalo de IP não armazena nenhum dado pessoal';
$string['settings:safeips'] = 'Intervalos de IP seguros';
$string['settings:safeips_help'] = 'Insira uma lista de endereços IP ou sub-redes a serem contados como um fator de passagem. Se estiver vazio, ninguém passará por este fator. {$a->info} {$a->sintaxe}';
$string['summarycondition'] = ' está em uma rede segura';
