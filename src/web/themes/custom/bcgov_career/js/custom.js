(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.customJs = {
    attach(context, settings) {
      $(window).on("load resize", function () {
        const $window = $(this).width();
      });

      $(".mobi_cari_quiz").each(function () {
        $(this).find(".carousel-inner > .career-item").slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: false,
          dots: true,
          arrows: false
        });
      });

      $(".mobi-tools-resource").each(function () {
        $(this).find("> .field__items").slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
          dots: true,
        });
      });

      $(".mobi-career-content-compare").each(function () {
        $(this).slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: false,
          dots: true,
          arrows: true,
          nextArrow: $(".next-true"),
          prevArrow: $(".prev-true")
        });
      });

      $(".carousel-slider-mobi-row").each(function () {
        $(this).slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: false,
          dots: true,
          arrows: false,
        });
      });

      $(".carousel-mobi-tabs-trigger").on("click", function () {
        $(this).next().addClass("active");
        $("body").addClass("overlayBg");
      });
      $(".carousel-mobi-tab-close").on("click", function () {
        $(this).parent().removeClass("active");
        $("body").removeClass("overlayBg");
      });
      $(".carousel-mobi-tab-item").on("click", function () {
        $(".carousel-mobi-tab-item").removeClass("active");
        $(this).addClass("active");

        const getTabItemId = $(this).data("href");
        $(".carousel-inner-mobi").removeClass("active");
        $(`.carousel-inner-mobi[data-id=${getTabItemId}]`).addClass("active");
        $(".active > .carousel-slider-mobi-row").slick(
          "slickGoTo",
          parseInt(0),
          false
        );

        $(this).parent().parent().removeClass("active");
        $("body").removeClass("overlayBg");

        $(".carousel-mobi-tabs-trigger > span.text").text($(this).data("text"));
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
          $(this).parents("td").find(".use-ajax").trigger("click");
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

      $(
        ".path-quiz form .field--widget-double-reference-autocomplete-select"
      ).each(function (i) {
        if (!$(this).find(".form-type-radio > .radio").length) {
          $(this)
            .find(".form-type-radio")
            .append("<span class='radio'></span>");
        }
        if (i === 0) {
          $(this).addClass("first-item");
        }
      });

      // Quiz
      $(".hideshow", context)
        .once("workbc")
        .on("click", function () {
          if ($(".hideshow").hasClass("hide")) {
            $(this).removeClass("hide");
            $(".hideshow span").text("+");
            $(".hideshow span.vaa").text("View All Aptitudes");

            $(".itm.hide").hide();
          } else {
            $(this).addClass("hide");
            $(".itm.hide").show();
            $(".hideshow span").text("-");
            $(".hideshow span.vaa").text("Show Top Aptitudes");
          }
        });
      // if ($(window).width() < 768) {
      //   $('#block-views-block-career-quizzes-block-1 #myCarousel', context).once('workbc').carousel({
      //     pause: true,
      //     interval: false
      //   });
      //   $('#block-views-block-career-quizzes-block-2 #myCarousel1', context).once('workbc').carousel({
      //     pause: true,
      //     interval: false
      //   });
      //   if ($('#block-views-block-career-quizzes-block-1 #myCarousel .career').parent().hasClass("row")) {
      //     $('#block-views-block-career-quizzes-block-1 #myCarousel .career').unwrap().addClass('carousel-item');
      //     $('#block-views-block-career-quizzes-block-1 #myCarousel .career:first').addClass('active');
      //   }
      //   if ($('#block-views-block-career-quizzes-block-2 #myCarousel1 .personality').parent().hasClass("row")) {
      //     $('#block-views-block-career-quizzes-block-2 #myCarousel1 .personality').unwrap().addClass('carousel-item');
      //     $('#block-views-block-career-quizzes-block-2 #myCarousel1 .personality:first').addClass('active');
      //   }

      // }
      if ($("body").width() < 768) {
        $("#myResult", context).once("workbc").carousel({
          pause: true,
          interval: false
        });
        if ($("#myResult .itm").parent().hasClass("row")) {
          $("#myResult .carousel-item>.itm").unwrap();
          $("#myResult .carousel-inner>.itm").addClass("carousel-item");
          $("#myResult .carousel-inner>.itm:first").addClass("active");
        }
      }
    },
  };
})(jQuery, Drupal, drupalSettings);
