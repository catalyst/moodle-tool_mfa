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
 * @package     factor_totp
 * @subpackage  tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:revoke'] = 'Revogar autenticador TOTP';
$string['devicename'] = 'Rótulo do dispositivo';
$string['devicenameexample'] = 'por exemplo, "Funcionar iPhone 11"';
$string['devicename_help'] = 'Este é o dispositivo no qual você tem um aplicativo autenticador instalado. Você pode configurar vários dispositivos para que esta etiqueta ajude a rastrear quais estão sendo usados. Você deve configurar cada dispositivo com seu próprio código exclusivo para que possam ser revogados separadamente.';
$string['error:alreadyregistered'] = 'Este secret TOTP já foi registrado';
$string['error:wrongverification'] = 'Código de verificação incorreto';
$string['error:codealreadyused'] = 'Este código já foi usado para autenticação. Aguarde a geração de um novo código e tente novamente.';
$string['error:oldcode'] = 'Este código é muito antigo. Verifique se a hora em seu dispositivo autenticador está correta e tente novamente.
     A hora atual do sistema é {$a}.';
$string['error:futurecode'] = 'Este código é inválido. Verifique se a hora em seu dispositivo autenticador está correta e tente novamente.
     A hora atual do sistema é {$a}.';
$string['info'] = '<p>Use qualquer aplicativo autenticador TOTP para obter um código de verificação em seu telefone, mesmo quando estiver off-line.</p>
por exemplo. <ul><li><a href="https://authy.com/download/">Twilio Authy</a></li>
<li><a href="https://www.microsoft.com/en-us/account/authenticator#getapp">Microsoft Authenticator</a></li>
<li>Google Authenticator para <a href="https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8" target="_blank">iOS</a> ou <a href ="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</a></li></ul>
<p>Observação: verifique se a hora e a data do seu dispositivo foram definidas como "Automático" ou "Fornecido pela rede".</p>';
$string['loginsubmit'] = 'Código de verificação';
$string['loginskip'] = 'Eu não tenho meu dispositivo';
$string['pluginname'] = 'Aplicativo Autenticador';
$string['privacy:metadata'] = 'O plug-in do fator TOTP não armazena nenhum dado pessoal';
$string['settings:secretlength'] = 'Comprimento da chave secreta TOTP';
$string['settings:secretlength_help'] = 'Comprimento gerado da string da chave secreta TOTP';
$string['settings:totplink'] = 'Mostrar link de configuração do aplicativo móvel';
$string['settings:totplink_help'] = 'Se ativado, o usuário verá uma terceira opção de configuração com um link otpauth:// direto';
$string['settings:window'] = 'Janela de verificação TOTP';
$string['settings:window_help'] = 'Por quanto tempo cada código é válido. Você pode definir isso para um valor mais alto como uma solução alternativa se os relógios do dispositivo de seus usuários estiverem um pouco errados.
     Arredondado para os 30 segundos mais próximos, que é o tempo entre os novos códigos gerados.';
$string['setupfactor'] = 'Configuração do autenticador TOTP';
$string['setupfactor:account'] = 'Conta:';
$string['setupfactor:link'] = '<b> OU </b> abra o aplicativo:';
$string['setupfactor:link_help'] = 'Se você estiver em um dispositivo móvel e já tiver um aplicativo autenticador instalado, este link pode funcionar. Observe que usar o TOTP no mesmo dispositivo em que você faz login pode enfraquecer os benefícios do MFA.';
$string['setupfactor:linklabel'] = 'Aplicativo aberto já instalado neste dispositivo';
$string['setupfactor:mode'] = 'Modo:';
$string['setupfactor:mode:timebased'] = 'Baseado no tempo';
$string['setupfactor:scan'] = 'Escanear QR code:';
$string['setupfactor:scanfail'] = 'Não pode digitalizar?';
$string['setupfactor:scanwithapp'] = 'Escanear QR code com o aplicativo autenticador escolhido.';
$string['setupfactor:enter'] = 'Insira os detalhes manualmente:';
$string['setupfactor:enter_help'] = 'Ao adicionar manualmente o código secreto, defina o nome da conta no aplicativo para algo que ajude a identificar esse código para a plataforma, como o nome do site. Certifique-se de que o modo selecionado seja baseado em tempo.';
$string['setupfactor:key'] = 'Chave Secreta: ';
$string['verificationcode'] = 'Digite seu código de verificação de 6 dígitos';
$string['verificationcode_help'] = 'Abra seu aplicativo autenticador, como o Google Authenticator, e procure o código de 6 dígitos que corresponde a este site e nome de usuário';
$string['summarycondition'] = ' Usando um aplicativo TOTP';
$string['factorsetup'] = 'Configurar Aplicativo';
$string['systimeformat'] = '%l:%M:%S %P %Z';
