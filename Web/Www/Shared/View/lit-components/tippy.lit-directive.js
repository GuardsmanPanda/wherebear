import { Directive, directive } from 'lit/directive.js';
import tippy from 'tippy.js';

class TooltipDirective extends Directive {
  render() {
    return '';
  }

  update(part, [content]) {
    const element = part.element;
    tippy(element, {
      content: content,
    });
  }
}

export const tooltip = directive(TooltipDirective);
