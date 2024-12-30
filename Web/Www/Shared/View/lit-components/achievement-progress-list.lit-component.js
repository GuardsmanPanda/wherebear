import { css, html, LitElement } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * Represents a list of player's progress on achievements.
 */
class AchievementProgressList extends LitElement {
  /** 
   * The list of available filter types from achievements. 
   * The order will determine the order of the types in the list.
   */
  static FILTER_TYPES = ['LEVEL', 'COUNTRY'];

  static properties = {
    achievements: { type: Array },
    achievementTotalNumber: { type: Number },
    isAllFilterTypeSelected: { type: Boolean, state: true },
    selectedFilterTypes: { type: Array, state: true }
  };

  static styles = css`${TailwindStyles}`;

  constructor() {
    super();
    this.achievements = [];
    this.achievementTotalNumber = null;
    this.isAllFilterTypeSelected = true;
    this.selectedFilterTypes = [];
  }

  /**
   * Returns the percentage of completed achievements.
   * @returns {number} The percentage of completed achievements.
   */
  get completedAchievementsPercentage() {
    if (this.completedAchievements.length === 0)
      return 0;

    const percentage = Math.floor(this.completedAchievements.length / this.achievementTotalNumber * 100);
    return percentage === 0 ? 1 : percentage;
  }

  /**
   * Returns the completed achievements.
   * @returns {Array} The list of completed achievements.
   */
  get completedAchievements() {
    return this.achievements.filter(n => n.isCompleted);
  }

  /**
   * Returns filtered achievements based on selected filter types.
   * @returns {Map} A map of filtered achievements by type.
   */
  get filteredAchievements() {
    const filteredTypes = this.isAllFilterTypeSelected ? AchievementProgressList.FILTER_TYPES : this.selectedFilterTypes;

    let map = new Map();
    filteredTypes.map(selectedTypeFilter => {
      map.set(selectedTypeFilter, this.achievements.filter(n => n.type === selectedTypeFilter));
    });
    return map;
  }

  /**
   * Extracts unique countries from a list of achievements.
   * @param {Array} achievements - The list of achievements to extract countries from.
   * @returns {Array} The list of unique countries.
   */
  extractCountriesFromAchievements(achievements) {
    const countryMap = new Map();

    achievements.forEach(({ countryCca2, countryName }) => {
      if (!countryMap.has(countryCca2)) {
        countryMap.set(countryCca2, { cca2: countryCca2, name: countryName });
      }
    });

    return Array.from(countryMap.values()).sort((a, b) => a.name.localeCompare(b.name));
  }

  /**
   * Returns whether a filter type is selected.
   * @param {string} type - The filter type to check.
   * @returns {boolean} True if the filter type is selected, otherwise false.
   */
  isFilterTypeSelected(type) {
    if (this.isAllFilterTypeSelected) {
      return false;
    }
    return this.selectedFilterTypes.includes(type);
  }

  /**
   * Selects or deselects the 'ALL' filter type.
   * @param {boolean} isSelected - Whether to select or deselect the 'ALL' filter type.
   */
  selectAllFilterType(isSelected) {
    this.selectedFilterTypes = AchievementProgressList.FILTER_TYPES;
    this.isAllFilterTypeSelected = isSelected;
  }

  /**
   * Selects or deselects a specific filter type.
   * @param {string} type - The filter type to select or deselect.
   * @param {boolean} isSelected - Whether to select or deselect the filter type.
   */
  selectFilterType(type, isSelected) {
    if (this.isAllFilterTypeSelected) {
      this.isAllFilterTypeSelected = false;
      this.selectedFilterTypes = [type];
    } else {
      if (isSelected) {
        this.selectedFilterTypes = [...this.selectedFilterTypes, type];
      } else {
        this.selectedFilterTypes = this.selectedFilterTypes.filter(n => n !== type);
      }
    }
  }

  /**
   * Renders a list of achievements.
   * @param {Array} achievements - The list of achievements to render.
   */
  renderAchievementList(achievements) {
    return html`
      <div class="flex flex-col gap-2">
        ${achievements.map(achievement => html`
          <lit-achievement-progress
            name="${achievement.title}"
            description="${achievement.description}"
            .isCompleted="${achievement.isCompleted}"
            completedDate="${new Date().toISOString()}"
            .hasSteps="${achievement.hasSteps}"
            currentStep="${achievement.currentStep}"
            totalSteps="${achievement.totalSteps}"
          ></lit-achievement-progress>
        `)}
      </div>
    `;
  }

  render() {
    return html`
      <div class="flex flex-col w-full">
        <div class="flex flex-col gap-2 w-full p-2 rounded border-gray-700 bg-gray-600">
          <div class="flex justify-between w-full">
            <span class="text-sm font-medium text-gray-50">EARNED ACHIEVEMENTS: ${this.completedAchievements.length}/${this.achievementTotalNumber}</span>
            <span class="text-sm font-medium text-gray-50">(${this.completedAchievementsPercentage}%)</span>
          </div>
          <lit-progress-bar size="sm" percentage="${this.completedAchievementsPercentage}" innerBgColorClass="bg-pistachio-400"></lit-progress-bar>
        </div>

        <div class="flex justify-center gap-4 w-full mt-4">
          <lit-button-checkbox label="ALL" size="sm" class="w-full min-[500px]:w-28" .isDisabled="${this.isAllFilterTypeSelected}" .isSelected="${this.isAllFilterTypeSelected}" @clicked="${(e) => this.selectAllFilterType(e.detail.isSelected)}"></lit-button-checkbox>
          ${AchievementProgressList.FILTER_TYPES.map(filterType => html`
            <lit-button-checkbox label="${filterType}" size="sm" class="w-full min-[500px]:w-28" .isSelected="${this.isFilterTypeSelected(filterType)}" @clicked="${(e) => this.selectFilterType(filterType, e.detail.isSelected)}"></lit-button-checkbox>
          `)}
        </div>

        <div class="flex flex-col w-full">
          ${Array.from(this.filteredAchievements.entries())
        .sort(([typeA], [typeB]) => {
          const order = AchievementProgressList.FILTER_TYPES;
          return order.indexOf(typeA) - order.indexOf(typeB);
        })
        .map(([type, achievements]) => html`
          <div class="flex justify-between mt-2 ${type !== 'COUNTRY' ? 'mb-2' : ''} pb-1 border-b border-gray-700">
            <span class="text-sm text-gray-800 font-medium">${type}</span>
            <span class="text-sm text-gray-800 font-medium">${achievements.filter(n => n.isCompleted).length}/${achievements.length}</span>
          </div>

          ${type === 'COUNTRY' ? html`
            ${this.extractCountriesFromAchievements(achievements).map(country => html`
              <div class="flex justify-between mt-2 mb-1">
                <div class="flex items-center gap-2">
                  <lit-flag cca2="${country.cca2}" description="${country.name}" filePath="/static/flag/svg/${country.cca2}.svg" roundedClass="rounded-sm" class="h-4"></lit-flag>
                  <span class="text-sm font-medium text-gray-500">${country.name}</span>
                </div>
                <span class="text-sm font-medium text-gray-500">${achievements.filter(n => n.countryCca2 === country.cca2 && n.isCompleted).length}/${achievements.filter(n => n.countryCca2 === country.cca2).length}</span>
              </div>
              ${this.renderAchievementList(achievements.filter(n => n.countryCca2 === country.cca2))}
            `)}
          ` : html`
            ${this.renderAchievementList(achievements)}
          `}
        `)}
        </div>
      </div>
    `;
  }
}

customElements.define('lit-achievement-progress-list', AchievementProgressList);