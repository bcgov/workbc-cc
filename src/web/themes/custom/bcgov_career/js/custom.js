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

      $(".career-table-link > a").on("click", function () {
        const getCarDataId = $(this).parent().parent().data("id");
        $(".career-table-row").removeClass("active");
        $(this).parent().parent().addClass("active");
        $(".career-content-main-wrapper .career-content-item").removeClass(
          "active"
        );
        $(
          `.career-content-main-wrapper .career-content-item#${getCarDataId}`
        ).addClass("active");
      });

      $(".career-table-mobi-row-link").on("click", function () {
        // var getCarDataId = $(this).parent().parent().data("id");
      });

      $(".career-checkbox").change(function () {
        const checkedNum = $(".career-checkbox:checked").length;
        if (checkedNum > 1) {
          $(".top-btn > a").removeClass("disable");
        }
 else {
          $(".top-btn > a").addClass("disable");
        }
      });

      $(".career-mobi-checkbox").change(function () {
        const checkedNum = $(".career-mobi-checkbox:checked").length;
        if (checkedNum > 1) {
          $(".top-career-mobi-content .top-btn > a").removeClass("disable");
        }
 else {
          $(".top-career-mobi-content .top-btn > a").addClass("disable");
        }
      });

      $(".tbody-main").each(function (i) {
        if (i % 5 == 0) {
          $(this)
            .nextAll()
            .addBack()
            .slice(0, 5)
            .wrapAll('<div class="slide-tbody-main"></div>');
        }
      });

      $(".careers-mobi-table-wrapper > .tbody").slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        arrows: false,
      });

      // $(".career-table-mobi-row-link").magnificPopup({
      //   type: 'inline',
      //   midClick: true,
      //   mainClass: 'mfp-fade'
      // });
    },
  };
})(jQuery, Drupal, drupalSettings);
