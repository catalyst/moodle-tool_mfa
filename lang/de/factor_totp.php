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

$string['action:revoke'] = 'TOTP Authenticator zurückziehen';
$string['devicename'] = 'Geräte-Label';
$string['devicenameexample'] = 'z.B. "Privates iPhone 11"';
$string['devicename_help'] = 'Dies ist das Gerät, auf dem Sie eine Authentifizierungs-App installiert haben. Sie können mehrere Geräte einrichten, so dass diese Beschriftung dabei hilft, zu verfolgen, welche Geräte verwendet werden. Sie sollten jedes Gerät mit einem eigenen, eindeutigen Code einrichten, damit es separat widerrufen werden kann.';
$string['error:alreadyregistered'] = 'Dieses TOTP Secret wurde schon registriert';
$string['error:wrongverification'] = 'Falscher Verifizierungs-Code';
$string['error:codealreadyused'] = 'Dieser Code wurde bereits für die Authentifizierung verwendet. Bitte warten Sie, bis ein neuer Code generiert wird, und versuchen Sie es erneut.';
$string['error:oldcode'] = 'Dieser Code ist zu alt. Bitte überprüfen Sie, ob die Zeit auf Ihrem Authentifizierungsgerät korrekt ist und versuchen Sie es erneut. Die aktuelle Systemzeit ist {$a}.';
$string['error:futurecode'] = 'Dieser Code ist ungültig. Bitte überprüfen Sie, ob die Zeit auf Ihrem Authentifizierungsgerät korrekt ist und versuchen Sie es erneut. Die aktuelle Systemzeit ist {$a}.';
$string['info'] = '<p>Verwenden Sie eine beliebige TOTP-Authentifizierungs-App auf einem zweiten Gerät, um dort einen Verifizierungscode zu erhalten, auch wenn es offline ist.</p>
z.B. <ul><li><a href="https://freeotp.github.io/" target="_blank">FreeOTP für iOS und Android</a></li>
<li><a href="https://authy.com/download/" target="_blank">Twilio Authy</a></li>
<li><a href="https://www.microsoft.com/en-us/account/authenticator#getapp" target="_blank">Microsoft Authenticator</a></li>
<li>Google Authenticator für <a href="https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8" target="_blank">iOS</a> oder <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</a></li></ul>
<p>Hinweis: Bitte vergewissern Sie sich, dass Uhrzeit und Datum Ihres Geräts auf "Auto" oder "Netzwerk bereitgestellt" eingestellt sind.</p>';
$string['loginsubmit'] = 'Code prüfen';
$string['loginskip'] = 'Ich habe mein Gerät nicht verfügbar';
$string['pluginname'] = 'Authenticator App';
$string['privacy:metadata'] = 'Das TOTP-Faktor-Plugin speichert keine persönlichen Daten';
$string['settings:secretlength'] = 'Länge des TOTP Secret Keys';
$string['settings:secretlength_help'] = 'Länge des erzeugten TOTP Secret Keys';
$string['settings:totplink'] = 'Link zur Einrichtung der mobilen Anwendung anzeigen';
$string['settings:totplink_help'] = 'Wenn diese Option aktiviert ist, wird dem Benutzer eine dritte Einrichtungsoption mit einem direkten otpauth:// Link angezeigt';
$string['settings:window'] = 'TOTP Verifikations-Zeitfenster';
$string['settings:window_help'] = 'Wie lange jeder Code gültig ist. Sie können diesen Wert auf einen höheren Wert setzen, wenn die Geräteuhren Ihrer Benutzer oft leicht falsch gehen. Abgerundet auf die nächsten 30 Sekunden, d. h. die Zeit zwischen neu generierten Codes.';
$string['setupfactor'] = 'Einrichtung TOTP Authenticator';
$string['setupfactor:account'] = 'Account:';
$string['setupfactor:link'] = '<b> ODER </b> in mobiler App:';
$string['setupfactor:link_help'] = 'Wenn Sie ein mobiles Gerät verwenden und bereits eine Authentifizierungs-App installiert haben, funktioniert dieser Link möglicherweise. Beachten Sie, dass die Verwendung von TOTP auf demselben Gerät, auf dem Sie sich anmelden, die Vorteile von MFA schwächen kann.';
$string['setupfactor:linklabel'] = 'Bereits auf diesem Gerät installierte Anwendung öffnen';
$string['setupfactor:mode'] = 'Mode:';
$string['setupfactor:mode:timebased'] = 'Time-based';
$string['setupfactor:scan'] = 'QR-Code scannen:';
$string['setupfactor:scanfail'] = 'Sie können nicht scannen?';
$string['setupfactor:scanwithapp'] = 'Scannen Sie den QR-Code mit der von Ihnen gewählten Authenticator-Anwendung.';
$string['setupfactor:enter'] = 'Geben Sie die Daten manuell ein:';
$string['setupfactor:enter_help'] = 'Wenn Sie den Secret Key manuell hinzufügen, setzen Sie den Kontonamen in der App auf einen Wert, der der Plattform hilft, diesen Code zu identifizieren, z. B. den Namen der Website. Stellen Sie sicher, dass der ausgewählte Modus zeitbasiert ist.';
$string['setupfactor:key'] = 'Secret Key: ';
$string['verificationcode'] = 'Geben Sie Ihren 6-stelligen Verifizierungscode ein';
$string['verificationcode_help'] = 'Öffnen Sie Ihre Authenticator-App, z. B. FreeOTP, und suchen Sie den 6-stelligen Code, der zu dieser Website und dem Benutzernamen passt.';
$string['summarycondition'] = 'verwendet eine TOTP App';
$string['factorsetup'] = 'App einrichten';
$string['systimeformat'] = '%l:%M:%S %P %Z';
