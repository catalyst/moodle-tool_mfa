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
 * @package     factor_grace
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Período de carência';
$string['info'] = 'Permite login sem outro fator por um período de tempo especificado.';
$string['settings:customwarning'] = 'Conteúdo do banner de aviso';
$string['settings:customwarning_help'] = 'Adicione conteúdo aqui para substituir a notificação de aviso de cortesia por conteúdo HTML personalizado. Adicionar {timeremaining} no texto irá substituí-lo pela duração de tolerância atual para o usuário, e {setuplink} substituirá pelo URL da página de configuração para o usuário.';
$string['settings:forcesetup'] = 'Configuração do fator de força';
$string['settings:forcesetup_help'] = 'Força um usuário a acessar a página de preferências para configurar o MFA quando o período do modo de carência expirar. Se desativado, os usuários não poderão se autenticar quando o período de cortesia expirar.';
$string['settings:graceperiod'] = 'Período de carência';
$string['settings:graceperiod_help'] = 'Período de tempo em que os usuários podem acessar o Moodle sem fatores configurados e habilitados';
$string['settings:ignorelist'] = 'Fatores ignorados';
$string['settings:ignorelist_help'] = 'O período de carência não dará pontos se houver outros fatores que os usuários possam usar para autenticar com o MFA. Qualquer fator aqui não será contado pelo período de carência ao decidir se deve dar pontos. Isso pode permitir que o período de carência permita a autenticação se outro fator, como e-mail, estiver sofrendo problemas de configuração ou sistema.';
$string['setupfactors'] = 'No momento, você está no modo de carência e pode não ter fatores suficientes configurados para fazer login depois que o período de tolerância terminar.
     Visite {$a->url} para verificar seu status de autenticação e configurar mais fatores de autenticação. Seu período de carência expira em {$a->time}.';
$string['preferences'] = 'Preferências de usuário';
$string['summarycondition'] = ' está dentro do período de carência';
$string['redirectsetup'] = 'Você deve concluir a configuração da autenticação multifator antes de continuar.';
$string['revokeexpiredfactors'] = 'Revogar fatores do modo de período de carência expirados';

$string['privacy:metadata'] = 'O plug-in do período de carência não armazena nenhum dado pessoal';
