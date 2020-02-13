import '@fortawesome/fontawesome-free/css/all.min.css';
import '@fortawesome/fontawesome-free/js/all.min';
import '../css/app.scss';

(function($) {

  let	$window = $(window),
    $body = $('body');

  // Play initial animations on page load.
  $window.on('load', function() {
    window.setTimeout(function() {
      $body.removeClass('is-preload');
    }, 100);
  });


  $window.scroll(function() {
    if ($(document).scrollTop() > 50) {
      $('.header__nav').addClass('header__affix');
    } else {
      $('.header__nav').removeClass('header__affix');
    }
  });

  $('.header__navTrigger').click(function () {
    $(this).toggleClass('active');
    $("#header__mainListDiv").toggleClass("header__show_list");
    $("#header__mainListDiv").fadeIn();
  });

})(jQuery);
