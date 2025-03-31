import { LitElement, css, html } from "lit"
import { customElement } from "lit/decorators.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"
import { Dialog } from "./dialog.lit-component"

/**
 * A login dialog that provides options for users to log in with Twitch or Google.
 */
@customElement("lit-login-dialog")
class LoginDialog extends LitElement {
  static styles = css`
    ${TailwindStyles}
  `

  private get litDialogElement(): Dialog | null {
    return this.renderRoot.querySelector("lit-dialog")
  }

  private getRedirectUrl() {
    const currentUrl = window.location.href
    return currentUrl.substring(currentUrl.indexOf("/"))
  }

  loginWithGoogle() {
    window.location.href = `/bear/auth/oauth2-client/730408173687-ad7cjtcq30kgm98mtndtot0dc5hv5fjn.apps.googleusercontent.com/redirect?redirect_path=${this.getRedirectUrl()}`
  }

  loginWithTwitch() {
    window.location.href = `/bear/auth/oauth2-client/q8q6jjiuc7f2ef04wmb7m653jd5ra8/redirect?redirect_path=${this.getRedirectUrl()}`
  }

  close() {
    this.litDialogElement?.close()
  }

  open() {
    this.litDialogElement?.open()
  }

  protected render() {
    return html`<lit-dialog label="Log in" modal x-on:closed="close">
      <div slot="content" class="flex flex-col gap-4">
        <lit-button
          label="Log in with Twitch"
          size="lg"
          bgColorClass="bg-[#9146FF]"
          imgPath="/static/img/icon/twitch-white.svg"
          @click="${this.loginWithTwitch}"
        ></lit-button>

        <lit-button
          label="Log in with Google"
          size="lg"
          bgColorClass="bg-gray-50"
          imgPath="/static/img/icon/google.svg"
          @click="${this.loginWithGoogle}"
        ></lit-button>

        <lit-button label="Continue as Guest" size="lg" bgColorClass="bg-gray-500" @click="${this.close}"></lit-button>
      </div>
    </lit-dialog>`
  }
}
