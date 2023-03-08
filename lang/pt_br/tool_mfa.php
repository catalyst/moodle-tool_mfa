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
//
/**
 * Strings for component 'tool_mfa', language 'en'.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['achievedweight'] = 'Atingiu o peso';
$string['areyousure'] = 'Tem certeza de que deseja revogar o fator?';
$string['combination'] = 'Combinação';
$string['created'] = 'Criado';
$string['createdfromip'] = 'Criado a partir do IP';
$string['debugmode:heading'] = 'Modo Debug';
$string['debugmode:currentweight'] = 'Peso Atal: {$a}';
$string['devicename'] = 'Dispositivo';
$string['enablefactor'] = 'Habilitar fator';
$string['error:directaccess'] = 'Esta página não deve ser acessada diretamente';
$string['error:factornotfound'] = 'Fator MFA \'{$a}\' não encontrado';
$string['error:wrongfactorid'] = 'O id do fator \'{$a}\' está incorreto';
$string['error:actionnotfound'] = 'Ação \'{$a}\' não suportada';
$string['error:setupfactor'] = 'Não é possível configurar o fator';
$string['error:revoke'] = 'Não é possível revogar o fator';
$string['error:notenoughfactors'] = 'Incapaz de autenticar';
$string['error:reauth'] = 'Não foi possível confirmar sua identidade o suficiente para atender à política de segurança de autenticação deste site.<br>Isso pode ser devido a: <br> 1) Etapas bloqueadas - aguarde alguns minutos e tente novamente.
      <br> 2) Falha nas etapas - verifique os detalhes de cada etapa. <br> 3) As etapas foram ignoradas - recarregue esta página ou tente fazer login novamente.';
$string['error:support'] = 'Se você ainda não conseguir fazer login ou acredita que está vendo isso por engano,
      envie um e-mail para o seguinte endereço para suporte:';
$string['error:home'] = 'Clique aqui para voltar a tela inicial.';
$string['error:factornotenabled'] = 'Fator MFA \'{$a}\' não ativado';
$string['email:subject'] = 'Não foi possível fazer login em {$a}';
$string['event:userpassedmfa'] = 'Verificação aprovada';
$string['event:userrevokedfactor'] = 'Revogação do fator';
$string['event:usersetupfactor'] = 'Configuração de fator';
$string['event:userdeletedfactor'] = 'Fator excluído';
$string['event:userfailedmfa'] = 'Falha do usuário na autenticação MFA';
$string['event:faillockout'] = 'A autenticação MFA falhou devido a muitas tentativas.';
$string['event:failnotenoughfactors'] = 'A autenticação MFA falhou devido a fatores insuficientes satisfeitos.';
$string['event:failfactor'] = 'A autenticação MFA falhou devido a um fator de falha.';
$string['factor'] = 'Fator';
$string['fallback'] = 'Fator de fallback';
$string['fallback_info'] = 'Esse fator é um fallback se nenhum outro fator for configurado. Este fator sempre falhará.';
$string['gotourl'] = 'Ir para URL original: ';
$string['guidance'] = 'Guia do usuário MFA';
$string['inputrequired'] = 'Input do usuário';
$string['ipatcreation'] = 'Endereço IP quando o fator foi criado';
$string['lastverified'] = 'Última verificação';
$string['lockedusersforfactor'] = 'Usuários bloqueados: {$a}';
$string['lockedusersforallfactors'] = 'Usuários bloqueados: todos os fatores';
$string['mfa'] = 'MFA';
$string['mfasettings'] = 'Gerenciar MFA';
$string['na'] = 'n/a';
$string['needhelp'] = 'Precisa de Ajuda?';
$string['nologinusers'] = 'Não logado';
$string['nonauthusers'] = 'MFA pendente';
$string['overall'] = 'Total';
$string['pluginname'] = 'Autenticação de Multiplo Fator';
$string['preferences:header'] = 'Preferências de autenticação multifator';
$string['preferences:availablefactors'] = 'Fatores disponíveis';
$string['preferences:activefactors'] = 'Fatores ativos';
$string['privacy:metadata:tool_mfa'] = 'Dados com fatores MFA configurados';
$string['privacy:metadata:tool_mfa:id'] = 'ID do Registro';
$string['privacy:metadata:tool_mfa:userid'] = 'O ID do usuário ao qual o fator pertence';
$string['privacy:metadata:tool_mfa:factor'] = 'Tipo de Fator';
$string['privacy:metadata:tool_mfa:secret'] = 'Quaisquer dados secretos para o fator';
$string['privacy:metadata:tool_mfa:label'] = 'Label para instância de fator, por exemplo, dispositivo ou e-mail';
$string['privacy:metadata:tool_mfa:timecreated'] = 'Hora em que a instância do fator foi configurada';
$string['privacy:metadata:tool_mfa:createdfromip'] = 'IP de onde o fator foi configurado';
$string['privacy:metadata:tool_mfa:timemodified'] = 'Hora em que a instância do fator foi modificado';
$string['privacy:metadata:tool_mfa:lastverified'] = 'A hora em que o usuário foi verificado pela última vez com este fator';
$string['privacy:metadata:tool_mfa_secrets'] = 'Esta tabela de banco de dados armazena secrets temporários para autenticação do usuário.';
$string['privacy:metadata:tool_mfa_secrets:userid'] = 'O usuário ao qual este secret está associado.';
$string['privacy:metadata:tool_mfa_secrets:factor'] = 'O fator ao qual este secret está associado.';
$string['privacy:metadata:tool_mfa_secrets:secret'] = 'O código de segurança secreto.';
$string['privacy:metadata:tool_mfa_secrets:sessionid'] = 'O ID da sessão ao qual esse secret está associado.';
$string['privacy:metadata:tool_mfa_auth'] = 'Esta tabela de banco de dados armazena a última vez que uma autenticação MFA bem-sucedida foi registrada para um ID de usuário.';
$string['privacy:metadata:tool_mfa_auth:userid'] = 'O usuário ao qual essa data/hora está associado.';
$string['privacy:metadata:tool_mfa_auth:lastverified'] = 'Hora em que o usuário foi autenticado pela última vez com';
$string['revoke'] = 'Revogar';
$string['revokefactor'] = 'Revogar Fator';
$string['settings:enabled'] = 'Plug-in MFA ativado';
$string['settings:enabled_help'] = '';
$string['settings:combinations'] = 'Resumo das boas condições para login';
$string['settings:general'] = 'Configurações gerais do MFA';
$string['settings:debugmode'] = 'Habilitar modo debug';
$string['settings:debugmode_help'] = 'O modo debug exibirá um pequeno banner de notificação nas páginas de administração do MFA, bem como na página de preferências do usuário
          com informações sobre os fatores atualmente habilitados.';
$string['settings:duration'] = 'Validade do secret';
$string['settings:duration_help'] = 'A duração que os secrets gerados são válidos.';
$string['settings:enablefactor'] = 'Habilitar Fator';
$string['settings:enablefactor_help'] = 'Marque o campo acima para permitir que o fator seja usado para autenticação MFA.';
$string['settings:guidancecheck'] = 'Use a página de orientação';
$string['settings:guidancecheck_help'] = 'adicione um link para a página de orientação nas páginas de autenticação MFA e na página de preferências MFA.';
$string['settings:guidancefiles'] = 'Arquivos de página de orientação';
$string['settings:guidancefiles_help'] = 'Adicione quaisquer arquivos aqui para usar na página de orientação e incorpore-os na página usando {{filename}} (caminho resolvido) ou {{{filename}}} (link html) no editor';
$string['settings:guidancepage'] = 'Conteúdo da página de orientação';
$string['settings:guidancepage_help'] = 'HTML aqui será exibido na página de orientação. Digite os nomes de arquivo da área de arquivo para incorporar o arquivo com o caminho resolvido {{filename}} ou como um link html usando {{{filename}}}.';
$string['settings:lockout'] = 'Limite de bloqueio';
$string['settings:lockout_help'] = 'Quantidade de tentativas que um usuário tem para responder os fatores antes de perder a permissão de realizar login';
$string['settings:redir_exclusions'] = 'URLs que não devem redirecionar a verificação de MFA';
$string['settings:redir_exclusions_help'] = 'Cada nova linha é um URL relativo do siteroot para o qual a verificação MFA não redirecionará, por exemplo. /admin/tool/securityquestions/set_responses.php';
$string['settings:weight'] = 'Peso do Fator';
$string['settings:weight_help'] = 'O peso deste fator se passado. Um usuário precisa de pelo menos 100 pontos para fazer o login.';
$string['setup'] = 'Configurar';
$string['setuprequired'] = 'Configuração do usuário';
$string['setupfactor'] = 'Configuração do fator';
$string['state:pass'] = 'Passou';
$string['state:fail'] = 'Falhou';
$string['state:unknown'] = 'Desconhecido';
$string['state:neutral'] = 'Neutro';
$string['state:locked'] = 'Bloqueado';
$string['totalweight'] = 'Peso total';
$string['weight'] = 'Peso';
$string['mfareports'] = 'Relatórios MFA';
$string['factorreport'] = 'Relatório de todos os fatores';
$string['lockoutnotification'] = 'Você tem {$a} tentativas de verificação restantes para este fator.';
$string['mfa:mfaaccess'] = 'Interação com o MFA';
$string['factorsetup'] = 'Fator \'{$a}\' configurado com sucesso.';
$string['factorrevoked'] = 'Fator \'{$a}\' revogado com sucesso.';
$string['factorlocked'] = 'O fator \'{$a}\' foi bloqueado devido a tentativas excedidas.';
$string['factorreset'] = 'Seu MFA \'{$a->fator}\' foi redefinido por um administrador do site. Pode ser necessário configurar esse fator novamente. {$a->url}';
$string['factorresetall'] = 'Todos os seus fatores MFA foram redefinidos por um administrador do site. Pode ser necessário configurar esses fatores novamente. {$a}';
$string['preferenceslink'] = 'Clique aqui para acessar as preferências do usuário.';
$string['connector'] = 'E';
$string['pending'] = 'Pendente';
$string['performbulk'] = 'Ação em massa';
$string['redirecterrordetected'] = 'Redirecionamento incompatível detectado, execução do script encerrada. Ocorreu um erro de redirecionamento entre MFA e {$a}.';
$string['resetfactor'] = 'Redefinir fatores de autenticação do usuário';
$string['resetfactorconfirm'] = 'Tem certeza de que deseja redefinir este fator para {$a}?';
$string['resetuser'] = 'Usuário:';
$string['resetsuccess'] = 'Fator \'{$a->factor}\' redefinido com sucesso para o usuário \'{$a->username}\'.';
$string['resetsuccessbulk'] = 'FFator \'{$a}\' redefinido com sucesso para os usuários fornecidos.';
$string['selectfactor'] = 'Selecione o fator para redefinir:';
$string['resetfactorplaceholder'] = 'Nome de usuário ou email';
$string['userempty'] = 'O usuário não pode estar vazio.';
$string['resetconfirm'] = 'Redefinir fator de usuário';
$string['usernotfound'] = 'Não foi possível localizar o usuário.';
$string['totalusers'] = 'Total de Usuários';
$string['usersauthedinperiod'] = 'Logado';
$string['lookbackperiod'] = 'Mostrando informações de MFA de {$a} em diante.';
$string['alltime'] = 'Tempo todo';
$string['selectperiod'] = 'Selecione um período de lookback para o relatório:';
$string['userlogs'] = 'Logs de Usuários';
$string['verificationcode'] = 'Digite o código de verificação para confirmação';
$string['verificationcode_help'] = 'O código de verificação fornecido pelo fator de autenticação atual.';
$string['viewlockedusers'] = 'Ver usuários bloqueados';
