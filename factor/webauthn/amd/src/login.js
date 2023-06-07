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
 * For collecting WebAuthn authenticator details on login
 *
 * @module     factor_webauthn/login
 * @copyright  Catalyst IT
 * @author     Alex Morris <alex.morris@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as utils from './utils';

const getAttestationResponse = (cred) => {
    const response = {
        id: cred?.rawId,
        clientDataJSON: cred.response?.clientDataJSON,
        authenticatorData: cred.response?.authenticatorData,
        signature: cred.response?.signature,
        userHandle: cred.response?.userHandle,
    };

    Object.entries(response).forEach(([key, value]) => {
        if (value) {
            response[key] = utils.arrayBufferToBase64(value);
        }
    });

    return response;
};

export const init = (initialArgs) => {
    document.addEventListener('click', async(e) => {
        if (!e.target.closest('#id_submitbutton')) {
            return;
        }

        if (!navigator.credentials || !navigator.credentials.create) {
            throw new Error('This browser does not support webauthn.');
        }

        const getArgs = JSON.parse(initialArgs);
        if (getArgs.success === false) {
            throw new Error(getArgs.msg || 'unknown error occured');
        }

        e.preventDefault();

        utils.recursiveBase64StrToArrayBuffer(getArgs);

        const cred = await navigator.credentials.get(getArgs);
        const authenticatorAttestationResponse = getAttestationResponse(cred);

        document.getElementById('id_response_input').value = JSON.stringify(authenticatorAttestationResponse);
        document.getElementById('id_response_input').form.submit();
    });
};
