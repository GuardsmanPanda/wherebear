import { LitElement, css, html, nothing } from 'lit';
import { styleMap } from 'lit/directives/style-map.js';
import tippy from 'tippy.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';
import { tooltip } from './tippy.lit-directive';

class CountryUsedList extends LitElement {

  static properties = {
    countries: { type: Array },
    currentRoundNumber: { type: Number },
    totalRounds: { type: Number },
    selectedRound: { type: Number },
    unplayedCountryTooltips: { type: Array, state: true }
  };

  static styles = css`${TailwindStyles}
    .texture {
background-image: url("https://www.transparenttextures.com/patterns/darth-stripe.png");
}`;

  constructor() {
    super();
    this.countries = [];
    this.currentRoundNumber = null;
    this.totalRounds = null;
    this.selectedRound = null;
    this.unplayedCountryTooltips = [
      "Mystery box!",
      "Guess who?",
      "Shhh...",
      "Top Secret!"
    ];
  }

  getUserRankIcon(userRank) {
    switch (userRank) {
      case 1:
        return 'ribbon-gold';
      case 2:
        return 'ribbon-silver';
      case 3:
        return 'ribbon-bronze';
      default:
        return null;
    }
  }

  getStyles(isPlaceHolder, cca2) {
    if (isPlaceHolder) {
      return {
        'box-shadow': 'inset 0 -4px 1px rgb(0 0 0 / 0.2)'
      }
    }
    return {
      'background-image': `url('/static/flag/svg/${cca2}.svg')`,
      'background-size': 'cover',
      'background-position': 'center',
      'box-shadow': 'inset 0 -4px 1px rgb(0 0 0 / 0.2)'
    }
  }

  getCountryUsedIconTemplate(args) {
    return html`
      <div
        class="flex flex-col w-[40px] h-[28px] rounded bg-gray-200 border-gray-900 border relative cursor-default"
        style="${styleMap(this.getStyles(args.isPlaceHolder, args.cca2))}">

        ${args.userRank <= 3 ? html`<img class="w-6 absolute -top-[3px] -right-[3px] " src="/static/img/icon/${this.getUserRankIcon(args.userRank)}.svg">` : nothing}
        
        ${args.isPlaceHolder
        ? html`
          <div class="flex justify-center items-center h-[24px]">
          <span class="text-xl font-medium text-gray-600">?</span>
          </div>`
        : ''
      }
        </div>
    `;
  }

  getRandomUnplayedCountryTooltip() {
    return this.unplayedCountryTooltips[Math.round(Math.random() * this.unplayedCountryTooltips.length)];
  }

  firstUpdated() {
    const tooltipTarget = this.shadowRoot.querySelector('#first');
    tippy(tooltipTarget, {
      content: "first tooltip",
    });

    const tooltipTarget2 = this.shadowRoot.querySelector('#second');
    tippy(tooltipTarget2, {
      content: "second tooltip",
    });
  }

  render() {
    console.log(this.countries)

    const countryTemplates = [];
    for (let i = 0; i < this.totalRounds; i++) {
      const country = this.countries[i];

      countryTemplates.push(html`
        <div class="relative">
          ${i === this.selectedRound - 1
          ? html`<img src="/static/img/icon/arrow.svg" class="absolute -top-[20px] left-[5px] z-10 w-[30px]" style="filter: drop-shadow(0 2px 2px rgba(0, 0, 0, 0.6));">`
          : nothing}
          ${i < this.countries.length
          ? html`
            <div ${tooltip(country.name)}>
            ${this.getCountryUsedIconTemplate({ isPlaceHolder: false, cca2: country.cca2, userRank: country.user_rank })}
            </div>`
          : html`
            <div ${tooltip(this.getRandomUnplayedCountryTooltip())}>
            ${this.getCountryUsedIconTemplate({ isPlaceHolder: true })}
            </div>`
        }
        </div>
      `);
    }

    return html`
      <div class="flex flex-wrap w-full gap-1  justify-center p-2">
        ${countryTemplates}
      </div>
    `;
  }
}

customElements.define('lit-country-used-list', CountryUsedList);

