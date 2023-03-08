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
 * @package     factor_email
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['email:subject'] = 'Seu código de confirmação';
$string['email:message'] = 'Você está tentando entrar no Moodle. Seu código de confirmação é \'{$a->secret}\'.
      Como alternativa, você pode clicar neste {$a->link} no mesmo dispositivo para autorizar esta sessão.';
$string['email:ipinfo'] = 'Informações de IP';
$string['email:originatingip'] = 'Este pedido de login foi feito de \'{$a}\'';
$string['email:uadescription'] = 'Identidade do navegador para esta solicitação:';
$string['email:browseragent'] = 'Os detalhes do navegador para esta solicitação são: \'{$a}\'';
$string['email:revokelink'] = 'Se não foi você, siga {$a} para interromper esta tentativa de login.';
$string['email:geoinfo'] = 'Esta solicitação parece ter se originado aproximadamente em {$a->city}, {$a->country}.';
$string['email:link'] = 'link';
$string['email:revokesuccess'] = 'Este código foi revogado com sucesso. Todas as sessões de {$a} foram encerradas.
     O e-mail não poderá ser usado como um fator até que a segurança da conta seja verificada.';
$string['email:accident'] = 'Se você não solicitou este e-mail, clique em continuar para tentar invalidar a tentativa de login.
     Se você clicou neste link acidentalmente, clique em cancelar e nenhuma ação será tomada.';
$string['settings:duration'] = 'Duração da validade';
$string['settings:duration_help'] = 'O período de tempo em que o código é válido.';
$string['settings:suspend'] = 'Suspender contas não autorizadas';
$string['settings:suspend_help'] = 'Marque isso para suspender contas de usuário se uma verificação de e-mail não autorizada for recebida.';
$string['event:unauthemail'] = 'E-mail não autorizado recebido';
$string['unauthemail'] = 'E-mail não autorizado';
$string['error:wrongverification'] = 'Código de verificação incorreto';
$string['error:badcode'] = 'O código não foi encontrado. Este pode ser um link antigo, um novo código pode ter sido enviado por e-mail ou a tentativa de login com este código foi bem-sucedida.';
$string['error:parameters'] = 'Parâmetros de página incorretos.';
$string['loginsubmit'] = 'Verificar Código';
$string['loginskip'] = "Não recebi o código";
$string['info'] = '<p>Fator embutido. Usa o endereço de e-mail mencionado no perfil do usuário para enviar códigos de verificação</p>';
$string['pluginname'] = 'Verificação de E-mail';
$string['privacy:metadata'] = 'O plug-in de verificação de E-Mail não armazena nenhum dado pessoal';
$string['setupfactor'] = 'Configuração do verificação de e-mail';
$string['verificationcode'] = 'Digite o código de verificação para confirmação';
$string['verificationcode_help'] = 'O código de verificação foi enviado para o seu endereço de e-mail';
$string['summarycondition'] = ' tem configuração de e-mail válida';
