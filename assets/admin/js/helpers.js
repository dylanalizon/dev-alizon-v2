import Vue from "vue";
import { camelCase } from 'lodash-es';

export function loadVueComponent (tagName, func) {
  const elements = document.getElementsByTagName(tagName);
  if (elements.length) {
    func().then(loadedComponent => {
      do {
        const propsData = Array.from(elements[0].attributes).reduce((acc, prop) => {
          const name = prop.name
          const value = prop.value
          if (name.startsWith(':')) {
            acc[camelCase(name.substring(1))] = eval(value)
          } else {
            acc[camelCase(name)] = value
          }
          return acc
        }, {})
        const Component = Vue.extend(loadedComponent.default);
        new Component({ propsData }).$mount(elements[0]);
      } while (elements.length)
    });
  }
}
