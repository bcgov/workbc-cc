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

        if (
          $window < 768 &&
          !$(".compare-career-main-wrapper .career-content-compare").hasClass(
            "slick-initialized"
          )
        ) {
          $(".compare-career-main-wrapper .career-content-compare").slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: false,
            dots: true,
            arrows: true,
            nextArrow: $(".next-true"),
            prevArrow: $(".prev-true")
          });
        } else {
          $(".compare-career-main-wrapper .career-content-compare")
            .filter(".slick-initialized")
            .slick("unslick");
        }

        if (
          $window < 992 &&
          !$(".cari_quiz .carousel-inner > .career-item").hasClass(
            "slick-initialized"
          )
        ) {
          $(".cari_quiz .carousel-inner > .career-item").slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: false,
            dots: true,
            arrows: false
          });
        } else {
          $(".cari_quiz .carousel-inner > .career-item")
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

      setTimeout(function () {
        $(".remove-link + .compare-carr > .career-chkk").prop("checked", true);
        const checkedLoadNum = $(
          ".remove-link + .compare-carr > .career-chkk:checked"
        ).length;
        if (checkedLoadNum > 1) {
          $(".top-btn > a").removeClass("disable");
        }
 else {
          $(".top-btn > a").addClass("disable");
        }
      }, 1500);

      $(window).on("load", function () {
        $(".career-checkbox").on("change", function (e) {
          const checkedNum = $(".career-checkbox:checked").length;
          // $(this).parents("td").find(".use-ajax").trigger("click");
          console.log($(this).parents("td").find(".use-ajax"));
          if (checkedNum > 1) {
            $(".top-btn > a").removeClass("disable");
          }
 else {
            $(".top-btn > a").addClass("disable");
          }
        });
      });

      $(".clear-compare").click(function () {
        $(".remove-link").each(function () {
          $(this).next().find(".career-chkk").prop("checked", false);
          $(this).click();
        });
      });

      $(".career-mobi-checkbox").change(function () {
        const checkedNum = $(".career-mobi-checkbox:checked").length;
        $(this).parent().prev().click();
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
    },
  };
})(jQuery, Drupal, drupalSettings);
