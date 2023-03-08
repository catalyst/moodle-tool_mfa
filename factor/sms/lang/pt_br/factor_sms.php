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
 * @package     factor_sms
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Fator de SMS';
$string['awssdkrequired'] = 'O plug-in local_aws que utiliza o AWS SDK é necessário para usar esse fator. Instale o plug-in local_aws.';
$string['action:revoke'] = 'Revogar número de celular';
$string['addnumber'] = 'Digite o número do celular';
$string['info'] = '<p>Configure um número de celular para receber códigos de segurança únicos por SMS.</p>';
$string['loginsubmit'] = 'Código de verificação';
$string['loginskip'] = "não recebi código";
$string['setupfactor'] = 'Configurar número de celular';
$string['settings:gateway'] = 'Gateway de SMS';
$string['settings:gateway_help'] = 'O provedor de SMS pelo qual você deseja enviar mensagens';
$string['settings:aws'] = 'AWS SNS';
$string['settings:aws:usecredchain'] = 'Use a cadeia de provedores de credenciais padrão para encontrar credenciais da AWS';
$string['settings:aws:key'] = 'Chave';
$string['settings:aws:key_help'] = 'Chave da API da Amazon';
$string['settings:aws:secret'] = 'Secret';
$string['settings:aws:secret_help'] = 'Secret da API da Amazon.';
$string['settings:aws:region'] = 'Região';
$string['settings:aws:region_help'] = 'Região da API gateway da Amazon.';
$string['settings:modica'] = 'Modica Mobile Gateway';
$string['settings:modica:url'] = 'URL da API';
$string['settings:modica:url_help'] = 'Can be left blank in most cases';
$string['settings:modica:application'] = 'Nome do aplicativo de descanso';
$string['settings:modica:application_help'] = 'Nome do aplicativo Mobile Gateway (API)';
$string['settings:modica:password'] = 'Senha';
$string['settings:modica:password_help'] = 'Mobile Gateway (API) Senha';
$string['settings:duration'] = 'Duração da validade';
$string['settings:duration_help'] = 'O período de tempo em que o código é válido.';
$string['settings:countrycode'] = 'Código do número do país';
$string['settings:countrycode_help'] = 'O código de chamada sem o + inicial como padrão se os usuários não inserirem um número internacional com o prefixo +.

Veja este link para uma lista de códigos de chamada: {$a}';
$string['smssent'] = 'Uma mensagem SMS contendo seu código de verificação foi enviada para {$a}.';
$string['smsstring'] = '{$a->code} é o seu código de segurança único {$a->fullname}.

@{$a->url} #{$a->código}';
$string['summarycondition'] = ' Usando um código de segurança SMS único';
$string['phoneplaceholder'] = '04xx xxx xxx ou +61 4xx xxx xxx';
$string['phonehelp'] = 'Digite seu número de celular local ou um número de telefone internacional começando com \'+\'.';
$string['privacy:metadata'] = 'O plug-in do fator de SMS do celular não armazena nenhum dado pessoal';
$string['wrongcode'] = 'Código de segurança invaludo';
$string['event:smssent'] = 'Mensagem de SMS enviada.';
