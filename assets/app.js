import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('hamburger-btn');
    const nav = document.getElementById('mobile-nav');
    const iconOpen = document.getElementById('icon-open');
    const iconClose = document.getElementById('icon-close');

    if (!btn || !nav) return;

    btn.addEventListener('click', () => {
        const isOpen = nav.style.maxHeight && nav.style.maxHeight !== '0px';

        nav.style.maxHeight = isOpen ? '0px' : nav.scrollHeight + 'px';
        btn.setAttribute('aria-expanded', String(!isOpen));
        iconOpen.style.display = isOpen ? 'block' : 'none';
        iconClose.style.display = isOpen ? 'none' : 'block';
    });
});