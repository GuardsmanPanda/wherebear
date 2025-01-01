import { css, html, LitElement, nothing } from 'lit';
import { customElement, property } from 'lit/decorators.js';
import { classMap } from 'lit/directives/class-map.js';

// @ts-ignore
import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';
import { tooltip } from './tippy.lit-directive';

/** 
 * A custom map marker used to indicate the position of a player on a map.
 * Displays player rank, name, guessed country and distance, with custom styling for rank-based colors.
 */
@customElement('lit-map-marker')
class MapMarker extends LitElement {
  @property({ type: Number }) distanceMeters?: number;
  @property({ type: String }) flagCca2?: string;
  @property({ type: String }) flagDescription?: string;
  @property({ type: String }) flagFilePath?: string;
  @property({ type: String }) iconFilePath!: string;
  @property({ type: String }) playerName?: string;
  @property({ type: Number }) rank?: number;

  static styles = [css`${TailwindStyles}`];

  get distanceClasses(): Record<string, boolean> {
    let classes: Record<string, boolean> = {};

    if (this.distanceMeters) {
      if (this.distanceMeters <= 100) {
        classes = {
          'bg-[#FDE047]': true,
          'text-gray-800': true,
        };
      } else if (this.distanceMeters > 100 && this.distanceMeters < 1000) {
        classes = {
          'bg-[#FF8D1B]': true,
          'text-gray-800': true,
        };
      } else if (this.distanceMeters >= 1000) {
        classes = {
          'bg-gray-600': true,
          'text-gray-0': true,
        };
      }
    }

    return classes;
  }

  get nameClasses(): Record<string, boolean> {
    return {
      'bg-rank-first-lighter': this.rank === 1,
      'bg-rank-second-lighter': this.rank === 2,
      'bg-rank-third-lighter': this.rank === 3,
      'bg-gray-50': this.rank != null && this.rank > 3,
    };
  }

  get rankClasses(): Record<string, boolean> {
    return {
      'bg-rank-first-dark': this.rank === 1,
      'bg-rank-second-dark': this.rank === 2,
      'bg-rank-third-dark': this.rank === 3,
      'bg-gray-100': this.rank != null && this.rank > 3,
    };
  }

  get rightCornerHexaColor(): string {
    if (this.rank === 1) {
      return '#F5D83A';
    } else if (this.rank === 2) {
      return '#B1D2EB';
    } else if (this.rank === 3) {
      return '#F3A965';
    } else {
      return '#E0E2E9';
    }
  }

  /**
   * Returns the distance value and appropriate unit (meters or kilometers).
   * Converts values greater than 1000 meters into kilometers.
   */
  private getDistanceWithUnit(): { value: number; unit: string } {
    const meters = this.distanceMeters;

    if (meters) {
      if (meters < 1000) {
        return {
          value: Math.round(meters),
          unit: 'm',
        };
      } else {
        return {
          value: Math.round(meters / 1000),
          unit: 'km',
        };
      }
    }

    return { value: 0, unit: 'm' };
  }

  protected render() {
    let distance = null;
    if (this.distanceMeters !== null) {
      distance = this.getDistanceWithUnit();
    }

    return html`
      <div class="flex flex-col items-center font-body">
        <div class="flex flex-col items-center">
          ${this.flagFilePath ? html`
          <div class="flex items-center relative top-[4px] z-20">
            <lit-flag cca2="FR" cca2="${this.flagCca2}" description="${this.flagDescription}" filePath="${this.flagFilePath}" class="h-[18px] bg-gray-50 cursor-auto" roundedClass="rounded-sm"></lit-flag>
            
            <div class="flex justify-center items-center min-w-10 h-4 px-1 pt-0.5 rounded-r border border-l-0 border-gray-700 ${classMap(this.distanceClasses)}">
              <span class="relative bottom-[1px] text-xs font-medium">${distance?.value}${distance?.unit}</span>
            </div>
          </div>
          ` : nothing}
          
          ${this.playerName ? html`
          <div class="w-3 h-10 absolute top-[40px] bg-gray-100 border border-gray-700" style="box-shadow: inset 1px 0 2px rgba(0, 0, 0, 0.3);"></div>

          <div class="flex items-center min-w-[120px] h-6 overflow-hidden z-10 rounded border border-b-2 border-gray-700 cursor-auto ${classMap(this.nameClasses)}">
            <div class="flex shrink-0 justify-center items-center w-6 h-full border-r border-gray-700 rounded-l ${classMap(this.rankClasses)}">
              <span class="font-heading text-base text-gray-800 font-medium">${this.rank}</span>
            </div>
            <div class="flex justify-center items-center flex-grow w-full max-w-32 h-full rounded-r">
              <span class="relative top-[1px] ml-2 font-heading font-medium text-base text-gray-800 truncate" ${tooltip(this.playerName)}>${this.playerName}</span>
            </div>
            <svg class="shrink-0 relative bottom-px left-px" width="24" height="24" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M15.2353 0.5H14.9094L14.7778 0.79818L1.54254 30.7982L1.23291 31.5H2H28C30.4853 31.5 32.5 29.4853 32.5 27V5C32.5 2.51472 30.4853 0.5 28 0.5H15.2353Z" fill="${this.rightCornerHexaColor}" stroke="#333847"/>
            </svg>
          </div>
          ` : nothing}
          
        </div>
        <div class="flex justify-center items-end w-16 h-12 relative top-[4px] overflow-visible">
          <img src="${this.iconFilePath}" class="z-10" style="height: auto; max-height: 48px;" />
        </div>
      </div>   
    `;
  }
}
