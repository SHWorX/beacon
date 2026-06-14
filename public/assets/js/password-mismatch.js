/*
 * Project:     Beacon
 * File:        password-mismatch.js
 * Date:        2026-06-14
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

let passwordField = document.getElementById('password');
let confirmationField = document.getElementById('confirm_password');
let unmatchedError = document.getElementById('password_mismatch');

let check = function() {
    if (passwordField.value !== '' && confirmationField.value !== '') {
        if (passwordField.value === confirmationField.value) {
            unmatchedError.style.display = 'none';
        } else {
            unmatchedError.style.display = 'block';
        }
    }
}

passwordField.addEventListener('keyup', () => check());
confirmationField.addEventListener('keyup', () => check());
