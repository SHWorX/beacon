/*
 * Project:     beacon
 * File:        neumorphism-common.js
 * Date:        2026-06-19
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */
function removeBadge(button)
{
    button.parentElement.remove();
}

/* MODALS */
class Modal
{
    static open(id)
    {
        const modal = document.getElementById(id);

        if (!modal) {
            return;
        }

        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');

        document.body.classList.add('modal-open');

        this.#active = modal;
        this.#previousFocus = document.activeElement;

        // Focus first focusable element
        const focusable = modal.querySelector(
            "button, a, input, select, textarea, [tabindex]:not([tabindex='-1'])"
        );

        if (focusable) {
            focusable.focus();
        }
    }

    static close(id)
    {
        const modal = document.getElementById(id);

        if (!modal) {
            return;
        }

        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');

        document.body.classList.remove('modal-open');

        if (this.#previousFocus) {
            this.#previousFocus.focus();
        }

        this.#active = null;
    }

    static toggle(id)
    {
        const modal = document.getElementById(id);

        if (!modal) {
            return;
        }

        if (modal.classList.contains('show')) {
            this.close(id);
        } else {
            this.open(id);
        }
    }

    static closeAll()
    {
        document.querySelectorAll('.modal.show').forEach(modal => this.close(modal.id));
    }

    static #active = null;
    static #previousFocus = null;
}

document.addEventListener('click', e => {
    const open = e.target.closest('[data-modal-open]');
    if (open) {
        Modal.open(open.dataset.modalOpen);
        return;
    }

    const close = e.target.closest('[data-modal-close]');
    if (close) {
        Modal.close(close.closest('.modal').id);
    }
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        Modal.closeAll();
    }
});
/* \MODALS */
