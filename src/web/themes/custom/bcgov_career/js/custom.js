(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.customJs = {
    attach(context, settings) {
      $(window).on("load resize", function () {
        const $window = $(this).width();
        if ($window > 767.98) {
          $(".work-value-quiz-carousel").removeClass("mbohide");
          $(".work-value-quiz-mobi-carousel").addClass("deskhide");
        }
 else {
          $(".work-value-quiz-carousel").addClass("mbohide");
          $(".work-value-quiz-mobi-carousel").removeClass("deskhide");
        }
      });

      if ($("body").hasClass("path-quiz")) {
        window.onorientationchange = function () {
          window.location.reload();
        };
      }

      $(".preview-quiz-form").parent().addClass("preview-block-wrapper");

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
          adaptiveHeight: true,
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
        console.log(getTabItemId);
        if (getTabItemId == "bottomcarousel") {
          $(this)
            .parents("#myResult")
            .find(".extradivs")
            .addClass("displayMobiPrint");
        }
 else {
          $(this)
            .parents("#myResult")
            .find(".extradivs")
            .removeClass("displayMobiPrint");
        }
        $(".carousel-inner-mobi").removeClass("active");
        $(`.carousel-inner-mobi[data-id=${getTabItemId}]`).addClass("active");
        $(".active > .carousel-slider-mobi-row").slick(
          "slickGoTo",
          parseInt(0),
          true
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
        // image-video iframe
        $(
          ".career-content-main-wrapper .career-content-item .image-video iframe"
        ).removeAttr("src");
        const src = $(
          ".career-content-main-wrapper .career-content-item.active .image-video iframe"
        ).attr("data-src");
        $(
          ".career-content-main-wrapper .career-content-item.active .image-video iframe"
        ).attr("src", src);
      });

      setTimeout(function () {
        $(".remove-link + .compare-carr > .career-chkk").prop("checked", true);
        const checkedLoadNum = $(
          ".remove-link + .compare-carr > .career-chkk:checked"
        ).length;
        if (checkedLoadNum >= 2) {
          $(".top-btn > a").removeClass("disable").attr("tabindex", "0");
        }
 else {
          $(".top-btn > a").addClass("disable").attr("tabindex", "-1");
        }
      }, 1500);

      $(window).on("load", function () {
        $(".career-checkbox").on("change", function (e) {
          const checkedNum = $(".career-checkbox:checked").length;
          $(this).parents("td").find(".use-ajax").trigger("click");
          console.log($(this).parents("td").find(".use-ajax"));
          console.log(`Compare count:- ${checkedNum}`);
          if (checkedNum >= 2) {
            $(".top-btn > a").removeClass("disable").attr("tabindex", "0");
          }
 else {
            $(".top-btn > a").addClass("disable").attr("tabindex", "-1");
          }

          if (checkedNum > 3) {
            $(".compare-popup-wrapper").addClass("active");
            $(this).prop("checked", false);
          }
        });

        $(".career-mobi-checkbox").change(function () {
          const checkedNum = $(".career-mobi-checkbox:checked").length;
          $(this).parent().prev().click();
          if (checkedNum >= 2) {
            $(".top-career-mobi-content .top-btn > a")
              .removeClass("disable")
              .attr("tabindex", "0");
          }
 else {
            $(".top-career-mobi-content .top-btn > a")
              .addClass("disable")
              .attr("tabindex", "-1");
          }

          if (checkedNum > 3) {
            $(".compare-popup-wrapper").addClass("active");
            $(this).prop("checked", false);
          }
        });

        $(".compare-popup-close, .close-compare-popup").on(
          "click",
          function () {
            $(".compare-popup-wrapper").removeClass("active");
          }
        );

        // $(".compare-career-email").on("click", function(){
        //   $(".email-popup-wrapper").addClass("active");
        // });

        $(".email-popup-close").on("click", function () {
          $(".email-popup-wrapper").removeClass("active");
        });

        $(document).on("keyup", function (e) {
          if (e.key == "Escape") {
            $(".email-popup-wrapper").removeClass("active");
            $(".compare-popup-wrapper").removeClass("active");
          }
        });
      });

      $(".tbody-main").each(function (i) {
        if (i % 5 === 0) {
          $(this)
            .nextAll()
            .addBack()
            .slice(0, 5)
            .wrapAll('<div class="slide-tbody-main"></div>');
        }
      });

      $(".careers-mobi-table-wrapper > .tbody").slick({
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        adaptiveHeight: true,
        dots: true,
        arrows: false,
      });
      $(
        ".path-quiz form .field--widget-double-reference-autocomplete-select"
      ).each(function (i) {
        $(this).addClass(i % 2 ? "work-bc-quiz-even" : "work-bc-quiz-odd");
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
      // $('.hideshow', context).once('workbc').on('click', function () {
      //   if ($('.hideshow').hasClass('hide')) {
      //     $(this).removeClass("hide");
      //     $('.hideshow span').text("+");
      //     $('.hideshow span.vaa').text("View All Aptitudes");

      //     $('.itm.hide').hide();
      //   } else {
      //     $(this).addClass("hide");
      //     $('.itm.hide').show();
      //     $('.hideshow span').text("-");
      //     $('.hideshow span.vaa').text("Show Top Aptitudes");
      //   }
      // });

      // $(".cancel-wrapper, .save-wrapper").wrapAll("<div class='save-cancel-wrapper'/>");

      $(".hideshow-workbc", context)
        .once("workbc")
        .on("click", function () {
          if ($(".hideshow-workbc").hasClass("hide")) {
            $(this).removeClass("hide");
            $(".hideshow-workbc span.vaa1").hide();
            $(".hideshow-workbc span.vaa").show();
            $(".result-heading h2.vaa1").hide();
            $(".result-heading h2.vaa").show();
          }
 else {
            $(this).addClass("hide");
            $(".hideshow-workbc span.vaa").hide();
            $(".hideshow-workbc span.vaa1").show();
            $(".result-heading h2.vaa").hide();
            $(".result-heading h2.vaa1").show();
          }
        });

      $(".hideshow", context)
        .once("workbc")
        .on("click", function () {
          if ($(".hideshow").hasClass("hide")) {
            $(this).removeClass("hide");
            $(".hideshow span.vaa1").hide();
            $(".hideshow span.vaa").show();
            $(".result-heading h2.vaa1").hide();
            $(".result-heading h2.vaa").show();
            $(".itm.hide").hide();
            $(".extradivs.hide").removeClass("show");
          } else {
            $(this).addClass("hide");
            $(".itm.hide").show();
            $(".hideshow span.vaa").hide();
            $(".hideshow span.vaa1").show();
            $(".result-heading h2.vaa").hide();
            $(".result-heading h2.vaa1").show();
            $(".extradivs.hide").addClass("show");
          }
        });

      $(".work-value-quiz-carousel > .row").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        dots: false,
        arrows: true,
        adaptiveHeight: true,
        nextArrow: $(".hideshow-workbc"),
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

      $(
        ".compare-career-print > .print-window, .quiz-node-print > .print-window"
      ).on("click", function () {
        window.print();
        // var w = window.open();
        // var html = $(".path-quiz").html();

        // $(w.document.body).html(html);
        // w.print();

        // var winPrint = window.open();
        // winPrint.document.write($(".path-quiz").html());
        // winPrint.document.close();
        // winPrint.focus();
        // winPrint.print();
        // winPrint.close();
      });

      $("#block-bcgov-career-content > form > div.form-wrapper").each(
        function () {
          if ($(this).find("> fieldset").hasClass("error")) {
            $(this).addClass("error-class");
          }
        }
      );

      $("#block-views-block-step-form-pagination-block-1")
        .prev("#block-bcgov-career-content")
        .addClass("no-border-block");
      $(".path-quiz .block-forms-steps")
        .prev("#block-bcgov-career-content")
        .addClass("no-border-block");

      if ($("#myResult").length) {
        $("#block-bcgov-career-content").addClass("results-main-block");
      }

      $(".path-quiz .form-actions.form-actions#edit-actions").each(function () {
        if ($(this).find("> #edit-previous").length) {
          $(this).addClass("hasPrev");
          $(this).parent().parent().addClass("has-prev-block");
        }
      });

      // move quiz prefix
      $(".field--type-work-bc-quiz").each(function () {
        $(this)
          .find("> .question-prefix")
          .prependTo($(this).find(" > fieldset > legend"));
      });

      $(".career-content-item-inner .image-video iframe")
        .parent()
        .addClass("video");

      $(
        ".careers-mobi-main-wrapper .careers-mobi-table-wrapper .tbody .tbody-main p:first-child"
      ).each(function () {
        if ($(this).text() == "Great") {
          $(this).parent().addClass("shift");
        }
      });
    },
  };
})(jQuery, Drupal, drupalSettings);
