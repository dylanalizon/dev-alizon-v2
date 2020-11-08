const input_selector = [
  'input[type=text]',
  'input[type=email]',
  'input[type=password]',
  'input[type=url]',
  'input[type=tel]',
  'input[type=number]',
  'input[type=search]',
  'input[type=date]',
  'input[type=time]',
  'textarea'
]

const updateTextFields = () => {
  const inputs = document.querySelectorAll(String(input_selector));
  inputs.forEach((input) => {
    const label = input.parentNode.querySelector('label');
    if (!label) {
      return;
    }
    if (input.value.length || document.activeElement === input || input.autofocus || input.placeholder) {
      label.classList.add('active');
      return;
    }
    label.classList.remove('active');
  })
}

updateTextFields();

/**
 * Add active when element has focus
 */
document.addEventListener(
  'focus',
  e => {
    const target = e.target;
    if (target.matches(String(input_selector))) {
      const label = target.parentNode.querySelector('label');
      if (label) {
        label.classList.add('active');
      }
    }
  },
  true
);

/**
 * Remove active when element is blurred
 */
document.addEventListener(
  'blur',
  e => {
    const target = e.target;
    if (target.matches(String(input_selector))) {
      if (target.value.length === 0 && !target.placeholder) {
        const label = target.parentNode.querySelector('label');
        if (label) {
          label.classList.remove('active');
        }
      }
    }
  },
  true
);
