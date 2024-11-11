import { Directive, directive } from 'lit/directive.js';
import tippy from 'tippy.js';

class TooltipDirective extends Directive {
  render() {
    return '';
  }

  update(part, [content]) {
    const element = part.element;

    // Destroy existing tooltip instance if it exists
    if (element._tippy) {
      element._tippy.destroy();
    }

    // Initialize a new tippy instance with updated content
    tippy(element, {
      content: content,
    });
  }
}

export const tooltip = directive(TooltipDirective);
