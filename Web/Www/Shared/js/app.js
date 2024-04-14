window.htmx = require('htmx.org/dist/htmx.cjs.js');
import tippy from 'tippy.js';

window.tippy = tippy;

const styles = `
.tippy-box[data-animation=fade][data-state=hidden]{opacity:0}[data-tippy-root]{max-width:calc(100vw - 10px)}.tippy-box{position:relative;background-color:#333;color:#fff;border-radius:4px;font-size:14px;line-height:1.4;white-space:normal;outline:0;transition-property:transform,visibility,opacity}.tippy-box[data-placement^=top]>.tippy-arrow{bottom:0}.tippy-box[data-placement^=top]>.tippy-arrow:before{bottom:-7px;left:0;border-width:8px 8px 0;border-top-color:initial;transform-origin:center top}.tippy-box[data-placement^=bottom]>.tippy-arrow{top:0}.tippy-box[data-placement^=bottom]>.tippy-arrow:before{top:-7px;left:0;border-width:0 8px 8px;border-bottom-color:initial;transform-origin:center bottom}.tippy-box[data-placement^=left]>.tippy-arrow{right:0}.tippy-box[data-placement^=left]>.tippy-arrow:before{border-width:8px 0 8px 8px;border-left-color:initial;right:-7px;transform-origin:center left}.tippy-box[data-placement^=right]>.tippy-arrow{left:0}.tippy-box[data-placement^=right]>.tippy-arrow:before{left:-7px;border-width:8px 8px 8px 0;border-right-color:initial;transform-origin:center right}.tippy-box[data-inertia][data-state=visible]{transition-timing-function:cubic-bezier(.54,1.5,.38,1.11)}.tippy-arrow{width:16px;height:16px;color:#333}.tippy-arrow:before{content:"";position:absolute;border-color:transparent;border-style:solid}.tippy-content{position:relative;padding:5px 9px;z-index:1}
.tippy-box[data-theme~=material]{background-color:#505355;font-weight:600}.tippy-box[data-theme~=material][data-placement^=top]>.tippy-arrow:before{border-top-color:#505355}.tippy-box[data-theme~=material][data-placement^=bottom]>.tippy-arrow:before{border-bottom-color:#505355}.tippy-box[data-theme~=material][data-placement^=left]>.tippy-arrow:before{border-left-color:#505355}.tippy-box[data-theme~=material][data-placement^=right]>.tippy-arrow:before{border-right-color:#505355}.tippy-box[data-theme~=material]>.tippy-backdrop{background-color:#505355}.tippy-box[data-theme~=material]>.tippy-svg-arrow{fill:#505355}
`
const styleSheet = document.createElement("style")
styleSheet.innerText = styles
document.head.appendChild(styleSheet)

const tippyFunction = function (el) {
    tippy(el, {
        content: el.getAttribute('tippy'),
        appendTo: 'parent',
        duration: [250, 250],
        hideOnClick: false,
        inertia: true,
        theme: 'material',
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
