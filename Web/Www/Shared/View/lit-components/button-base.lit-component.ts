import { LitElement, css } from "lit"
import { property, state } from "lit/decorators.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"

type IconPosition = "left" | "right"

type Size = "xs" | "sm" | "md" | "lg" | "xl"

/**
 * Base class for buttons that provides basic functionality for handling button states such as
 * size and mouse interaction events.
 */
export class ButtonBase extends LitElement {
  @property({ type: String }) iconPosition: IconPosition = "left"
  @property({ type: Boolean }) isDisabled = false
  /** Whether the button is full rounded. */
  @property({ type: Boolean }) isPill = false
  /** Determines if the button is selectable (the button can stay pressed down) */
  @property({ type: Boolean }) isSelectable = false
  @property({ type: Boolean }) isSelected = false
  @property({ type: String }) size: Size = "sm"

  /** Tracks whether the mouse has left the button after it was clicked. Used for not applying hover css before the mouse has left the button. */
  @state() hasMouseLeftAfterClicked = true

  static styles = [
    css`
      ${TailwindStyles}
    `,
    css`
      .inner-shadow {
        box-shadow:
          inset 0 1px 1px rgba(255, 255, 255, 0.6),
          inset 0 -2px 0 rgba(0, 0, 0, 0.6);
      }
      .inner-shadow:active {
        box-shadow:
          inset 0 1px 0 rgba(255, 255, 255, 0.6),
          inset 0 -1px 0 rgba(0, 0, 0, 0.6);
      }

      .inner-shadow-selected {
        box-shadow:
          inset 0 1px 1px rgba(255, 255, 255, 0.6),
          inset 0 -2px 0 rgba(0, 0, 0, 0.6);
      }
      .inner-shadow-selected:active {
        box-shadow:
          inset 0 1px 1px rgba(255, 255, 255, 0.6),
          inset 0 -1px 0 rgba(0, 0, 0, 0.6);
      }
    `,
  ]

  /** Defines a mapping between size and their corresponding height classes in Tailwind CSS syntax. */
  private heightClasses: { [key in Size]: string } = {
    xs: "h-6",
    sm: "h-8",
    md: "h-10",
    lg: "h-12",
    xl: "h-14",
  }

  /** The height class corresponding to the current button size. */
  protected get heightClass(): string {
    return this.heightClasses[this.size]
  }

  /** Event handler for when the button is clicked. */
  protected onClick(): void {
    if (this.isSelectable) {
      this.isSelected = !this.isSelected
    }

    this.hasMouseLeftAfterClicked = false

    this.dispatchEvent(
      new CustomEvent("clicked", {
        detail: { isSelected: this.isSelected },
        bubbles: true,
        composed: true,
      }),
    )
  }

  /** Event handler for when the mouse enters the button. */
  protected onMouseEnter(): void {
    if (this.isSelected) {
      this.hasMouseLeftAfterClicked = true
    }
  }

  /** Event handler for when the mouse leaves the button. */
  protected onMouseLeave(): void {
    this.hasMouseLeftAfterClicked = true
  }
}
