import Pusher from 'pusher-js';
import {Notyf} from 'notyf';
import tippy from 'tippy.js';

require('pannellum/build/pannellum.js');
import L from "leaflet";
require('leaflet.markercluster');

window.htmx = require('htmx.org/dist/htmx.cjs.js');
window.pusher = Pusher
window.tippy = tippy;

const tippyFunction = function (el) {
    tippy(el, {
        content: el.getAttribute('tippy'),
        appendTo: () => document.body,
        duration: [250, 250],
        hideOnClick: false,
        inertia: true,
    });
}


htmx.config.historyCacheSize = 0;
window.onload = () => {
    document.querySelectorAll('[tippy]').forEach(tippyFunction);

    // ----------------------------------------------------------------------------------
    // Add event listener to open and close the dialog element.
    // ----------------------------------------------------------------------------------
    document.body.addEventListener('dialog:open', function (evt) {
        const title = document.getElementById('dialog-title');
        title.innerHTML = decodeURIComponent(evt.detail.value);
        const el = document.getElementById('dialog');
        if (!el.hasAttribute('open')) {
            el.showModal();
        }
    });


    // ----------------------------------------------------------------------------------
    // Check to see if an element with id 'dialog' doesn't exist, and inject it if it doesn't.
    // ----------------------------------------------------------------------------------
    if (!document.getElementById('dialog')) {
        document.body.insertAdjacentHTML('beforeend', `
        <dialog id="dialog" class="shadow-xl" style="padding: 0; border-radius: 0.125rem;">
            <div class="shadow-sm" style="display: grid; grid-template-columns: auto 3rem; align-items: center; height: 3rem; padding-left: 1rem; border-bottom-width: 2px; gap: 1rem; color: rgb(31 41 55); font-weight: 700; text-transform: capitalize; font-size: 1.125rem;">
                <h3 id="dialog-title">Dialog</h3>
                <form method="dialog">
                    <button class="hover:bg-red-500 hover:text-red-100" style="height: 3rem; width: 3rem; vertical-align: middle; color: rgb(185 28 28); transition-duration: 75ms; transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, -webkit-text-decoration-color;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </form>
            </div>
            <div id="dialog-content" style="padding: 0.75rem 1.25rem 0.75rem 1.25rem;"></div>
        </dialog>
    `);
    }

    window.notify = new Notyf({
        duration: 4000,
        ripple: true,
        position: {x: 'right', y: 'top'},
        dismissible: true,
        types: [
            {type: 'success', background: 'rgb(16 185 129)'},
            {type: 'error', background: 'rgb(182,40,40)'},
            {type: 'info', background: 'rgb(31 41 55)'},
            {type: 'warning', background: 'rgb(251 191 36)'},
        ]
    });
}


htmx.on("htmx:afterRequest", event => {
    if (event.detail.successful) {
        if (event.detail.elt.hasAttribute('hx-dialog-close')) {
            document.getElementById('dialog').close();
        }
    } else {
        toast('error', 'Something went wrong');
    }
});

htmx.on('htmx:afterProcessNode', event => {
    event.target.querySelectorAll('[tippy]').forEach(tippyFunction);
});
