(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.customJs = {
    attach(context, settings) {
      $(window).on("load resize", function () {
        const $window = $(this).width();
        if (
          $window < 768 &&
          !$(".tools-resource-items-wrapper > .field__items").hasClass(
            "slick-initialized"
          )
        ) {
          $(".tools-resource-items-wrapper > .field__items").slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: true,
            dots: true,
          });
        } else {
          $(".tools-resource-items-wrapper > .field__items")
            .filter(".slick-initialized")
            .slick("unslick");
        }
      });
    },
  };
})(jQuery, Drupal, drupalSettings);
