import '@fortawesome/fontawesome-free/css/all.min.css';
import '@fortawesome/fontawesome-free/js/all.min';
import '../css/app.scss';
import Turbolinks from 'turbolinks';

Turbolinks.start();

document.addEventListener("turbolinks:load", function() {
  let	$window = $(window);

  // Play initial animations on page load.
  setTimeout(function() {
    $('body').removeClass('is-preload');
  }, 100);

  // On scroll => change navbar
  $window.scroll(function() {
    if ($(document).scrollTop() > 50) {
      $('.header__nav').addClass('header__affix');
    } else {
      $('.header__nav').removeClass('header__affix');
    }
  });

  // on Hambergur menu click
  $('.header__navTrigger').click(function () {
    $(this).toggleClass('active');
    let $headerMainListDiv = $("#header__mainListDiv");
    $headerMainListDiv.toggleClass("header__show_list");
    $headerMainListDiv.fadeIn();
  });
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
