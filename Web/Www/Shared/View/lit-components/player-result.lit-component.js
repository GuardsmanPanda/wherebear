import { LitElement, css, html } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

class PlayerResult extends LitElement {
  static properties = {
    distanceMeters: { type: Number },
    iconPath: { type: String },
    name: { type: String },
    points: { type: Number },
    rank: { type: Number },
    title: { type: String }
  }

  static styles = css`${TailwindStyles}`;

  constructor() {
    super();

  }

  getRankTemplate() {
    if (this.rank > 3) {
      return html`
      <div class="flex justify-center items-center w-8 h-8 rounded-full bg-[#6074A9]">
        <span class="text-sm text-white">${this.rank}</span>
      </div>
      `;
    }

    const rankIconPath1 = '/static/img/icon/cup-gold.svg';
    const rankIconPath2 = '/static/img/icon/cup-silver.svg';
    const rankIconPath3 = '/static/img/icon/cup-bronze.svg';

    return html`
      <div class="flex items-center">
        <img src="${this.rank === 1 ? rankIconPath1 : this.rank === 2 ? rankIconPath2 : rankIconPath3}" alt="" class="h-8" />
      </div>  
    `;
  }

  get distanceAndUnit() {
    if (this.distanceMeters < 1000) {
      return {
        value: this.distanceMeters,
        unit: 'm'
      };
    }
    return {
      value: Math.round(this.distanceMeters / 1000),
      unit: 'km'
    }
  }

  getDistanceClasses() {
    if (this.distanceAndUnit.unit === 'm') {
      if (this.distanceAndUnit.value < 100) {
        return 'bg-[#FDE047] text-black'
      }
      return 'bg-[#FF8D1B] text-black';
    }
    return `bg-[#434E6C] text-white`;
  }

  render() {
    return html`
      <div class="flex items-center gap-2 p-1 bg-[#7F98DA] relative overflow-hidden rounded border border-[#3E4A6A] font-body">
        <div class="h-full z-10 shrink-0">${this.getRankTemplate()}</div>
        <lit-player-profile iconPath=${this.iconPath}></lit-player-profile>
        <div class="flex flex-col relative bottom-[3px] gap-0 grow z-10 truncate">
          <div class="text-base text-white font-medium truncate">${this.name} abcdef</div>
          <div class="text-xs text-white font-medium truncate">${this.title}</div>
        </div>
        <div class="flex flex-col gap-2 z-10 shrink-0">
          <div class="flex justify-center w-[72px] relative rounded border border-gray-800 bg-[#5F83E7]">
            <div class="w-5 aspect-auto absolute -top-[2px] left-0 transform -translate-x-1/2">
              <img src="/static/img/icon/star-gold.svg" />
            </div>
            <span class="text-xs text-white font-medium">${this.points}</span>
          </div>

          <div class="flex justify-center w-[72px] relative rounded border border-gray-800 ${this.getDistanceClasses()}">
            <div class="w-5 aspect-auto absolute -top-[2px] left-0 transform -translate-x-1/2">
              
            </div>
            <span class="text-xs font-medium">${this.distanceAndUnit.value}${this.distanceAndUnit.unit}</span>
          </div>
        </div>
        <div class="slanted-edge absolute top-0 right-0 w-[106px] h-full bg-[#6074A9]" style="clip-path: polygon(20px 0, 100% 0, 100% 100%, 0 100%); z-index: 0;"></div>
      </div>
    `;
  }


}

customElements.define('lit-player-result', PlayerResult);
