/*
 * Project:     Beacon
 * File:        popover.js
 * Date:        2026-06-10
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

const popover = document.getElementById('popover');
const offset = 12;

function getBestPosition(x, y) {
    const spaceTop = y;
    const spaceBottom = window.innerHeight - y;
    const spaceLeft = x;
    const spaceRight = window.innerWidth - x;

    // pick direction with most space
    const max = Math.max(spaceTop, spaceBottom, spaceLeft, spaceRight);

    if (max === spaceTop) return 'top';
    if (max === spaceBottom) return 'bottom';
    if (max === spaceLeft) return 'left';
    return 'right';
}

document.querySelectorAll('.has-popover').forEach(el => {
    el.addEventListener('mousemove', (e) => {
        const text = el.dataset.popover;

        if (text !== '') {
            popover.innerHTML = text;
        }
        popover.classList.add('show');

        const rect = popover.getBoundingClientRect();

        const x = e.clientX;
        const y = e.clientY;

        const requestedPosition = el.dataset.position;
        const position = requestedPosition || getBestPosition(x, y);

        let finalX = x;
        let finalY = y;

        switch (position) {
            case 'top':
                finalX -= rect.width / 2;
                finalY -= rect.height + offset;
                break;

            case 'bottom':
                finalX -= rect.width / 2;
                finalY += offset;
                break;

            case 'left':
                finalX -= rect.width + offset;
                finalY -= rect.height / 2;
                break;

            case 'right':
                finalX += offset;
                finalY -= rect.height / 2;
                break;
        }

        // clamp to viewport (important)
        finalX = Math.max(8, Math.min(finalX, window.innerWidth - rect.width - 8));
        finalY = Math.max(8, Math.min(finalY, window.innerHeight - rect.height - 8));

        popover.style.left = finalX + 'px';
        popover.style.top = finalY + 'px';
    });

    el.addEventListener('mouseleave', () => {
        popover.classList.remove('show');
    });
});
