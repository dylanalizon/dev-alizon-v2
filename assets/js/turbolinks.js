import Turbolinks from 'turbolinks';

Turbolinks.start();

document.addEventListener("turbolinks:load", function() {

  // Play initial animations on page load.
  setTimeout(() => {
    document.body.classList.remove('is-preload');
  }, 100);

  // On scroll => change navbar
  document.addEventListener('scroll', () => {
    const headerNav = document.querySelector('.header__nav');
    if (window.scrollY > 50) {
      headerNav.classList.add('header__affix');
    } else {
      headerNav.classList.remove('header__affix');
    }
  });

  // on Hamburger menu click
  const hamburgerMenu = document.querySelector('.header__navTrigger');
  if (hamburgerMenu) {
    hamburgerMenu.addEventListener('click', () => {
      hamburgerMenu.classList.toggle('active');
      const headerMainListDiv = document.getElementById('header__mainListDiv');
      if (headerMainListDiv) {
        headerMainListDiv.classList.toggle("header__show_list");
      }
    });
  }
});

Turbolinks.scroll = {};

document.addEventListener("turbolinks:load", () => {

  const elements = document.querySelectorAll("[data-turbolinks-scroll]");

  elements.forEach(function(element){

    element.addEventListener("click", ()=> {
      Turbolinks.scroll['top'] = document.scrollingElement.scrollTop;
    });

    element.addEventListener("submit", ()=> {
      Turbolinks.scroll['top'] = document.scrollingElement.scrollTop;
    });

  });

  if (Turbolinks.scroll['top']) {
    document.scrollingElement.scrollTo(0, Turbolinks.scroll['top']);
  }

  Turbolinks.scroll = {};
});
