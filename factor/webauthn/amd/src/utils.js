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
 * WebAuthn utility functions, for handling array buffers.
 *
 * @module     factor_webauthn/utils
 * @copyright  Catalyst IT
 * @author     Alex Morris <alex.morris@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const prefix = '=?BINARY?B?';
const suffix = '?=';

export const recursiveBase64StrToArrayBuffer = (obj) => {
    if (typeof obj !== 'object') {
        return;
    }
    for (let key in obj) {
        if (typeof obj[key] === 'string') {
            let str = obj[key];
            if (str.substring(0, prefix.length) === prefix && str.substring(str.length - suffix.length) === suffix) {
                str = str.substring(prefix.length, str.length - suffix.length);

                const binaryString = window.atob(str);
                const len = binaryString.length;
                const bytes = new Uint8Array(len);
                for (let i = 0; i < len; i++) {
                    bytes[i] = binaryString.charCodeAt(i);
                }
                obj[key] = bytes.buffer;
            }
        } else {
            recursiveBase64StrToArrayBuffer(obj[key]);
        }
    }
};

export const arrayBufferToBase64 = (buffer) => {
    let binary = '';
    const bytes = new Uint8Array(buffer);
    const len = bytes.byteLength;
    for (let i = 0; i < len; i++) {
        binary += String.fromCharCode(bytes[i]);
    }
    return window.btoa(binary);
};
