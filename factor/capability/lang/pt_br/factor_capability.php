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
 * @package     factor_capability
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Capacidade do usuário';
$string['privacy:metadata'] = 'O plug-in do fator de capacidade do usuário não armazena nenhum dado pessoal';
$string['settings:adminpasses'] = 'Os administradores do site podem passar por esse fator';
$string['settings:adminpasses_help'] = 'Por padrão, os administradores passam em todas as verificações de capacidade, incluindo esta que usa \'factor/capability:cannotpassfactor\', o que significa que eles falharão neste fator.
     Se marcado, todos os administradores do site passarão por esse fator se não tiverem esse recurso de outra função.
     Se os administradores do site não forem verificados, esse fator será reprovado.';
$string['summarycondition'] = ' NÃO tem o recurso factor/capability:cannotpassfactor em nenhuma função, incluindo administrador do site.';
$string['capability:cannotpassfactor'] = 'IMPEDEM que uma função seja aprovada no fator de capacidade do usuário MFA.';
