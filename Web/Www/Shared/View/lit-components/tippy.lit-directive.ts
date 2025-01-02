import { noChange } from "lit"
import { Directive, directive, PartInfo, PartType } from "lit/directive.js"
import tippy, { Instance as TippyInstance } from "tippy.js"

class TooltipDirective extends Directive {
  private tippyInstance: TippyInstance | null = null

  constructor(partInfo: PartInfo) {
    super(partInfo)
    if (partInfo.type !== PartType.ELEMENT) {
      throw new Error("TooltipDirective can only be used on elements")
    }
  }

  render(content: string): string {
    return content
  }

  update(part: any, [content]: [string]): typeof noChange {
    const element = part.element as HTMLElement

    // Destroy existing tooltip instance if it exists
    if (this.tippyInstance) {
      this.tippyInstance.destroy()
    }

    // Initialize a new tippy instance with updated content
    this.tippyInstance = tippy(element, {
      content: content,
    })

    return noChange
  }
}

export const tooltip = directive(TooltipDirective)
