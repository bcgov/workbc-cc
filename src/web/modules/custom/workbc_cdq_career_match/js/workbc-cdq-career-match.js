
(function (Drupal, $, once, drupalSettings) {

  $(document).ready(function() {
    if( $('.views-table').length ){
      const queryString = window.location.search;
      if (queryString.includes("&sort=")) {
        $("html, body").animate({ scrollTop: $(".views-table").offset().top }, "slow");
      }
    }
  });


  Drupal.behaviors.CareerCompare = {
    attach: function (context, settings) {

      $(once('cdqcareermatch', '.careers-main-wrapper', context)).each(function () {

        $('.compare-career-checkbox').on('click', function() {
          let careerMatchId = $(this).data('career-match-id');
          selected = $(this).is(':checked');
          if (selected && totalSelected() > 3) {
            $(this).prop('checked', false);
            alert("You have reached the maximum number of careers you are able to add to the compare feature. Please deselect one of your selected careers to add this career.");
            // $(".popup, .popup-content").addClass("active");
          }
          else {
            $.ajax({
              url: Drupal.url('career-match/update-selected'),
              type: 'POST',
              dataType: 'json',
              data: { 'id' : careerMatchId, 'selected': selected},
              success: function (response) {
              }
            });
          }
          if (totalSelected() > 1) {
            $('.clear-compare').removeClass("disable");
            $('.compare-career').removeClass("disable");
          }
          else {
            $('.clear-compare').addClass("disable");
            $('.compare-career').addClass("disable");
          }
        });

        $('.clear-compare').on('click', function() {
          $('.compare-career-checkbox').each(function() {
            if ($(this).is(':checked')) {
              let careerMatchId = $(this).data('career-match-id');
              $.ajax({
                url: Drupal.url('career-match/update-selected'),
                type: 'POST',
                dataType: 'json',
                data: { 'id' : careerMatchId, 'selected': false},
                success: function (response) {
                }
              });
            }
          });
        });

        // when user selects a career profile from the table
        $('.views-field-title').on('click', function() {
          let current_item = $(this).closest('.career-table-row').data('id');
          $('.career-content-item').each(function() {
            $(this).removeClass("active");
          });
          $('.' + current_item).each(function() {
            $(this).addClass("active")
          });

          $('.career-table-row').each(function() {
            $(this).closest('.career-table-row').removeClass("active");
          });
          $(this).closest('.career-table-row').addClass("active");

          $('.cdq-results').each(function() {
            $(this).addClass("user-selected");
          });
        });

        // when user clicks back to quiz link ...
        $('a#back-to-quiz').on('click', function() {
          $('.cdq-results').each(function() {
            $(this).removeClass("user-selected");
          });

          $('.career-table-row').each(function() {
            $(this).closest('.career-table-row').removeClass("active");
          });
        });

        function totalSelected() {
          let total = 0;
          let target = ".careers-main-wrapper .compare-career-checkbox"; 

          if ($('#mobi-career-table').css('display') == "block") {
            target = ".careers-mobi-main-wrapper .compare-career-checkbox";
          }
          $(target).each(function() {
            if ($(this).is(':checked')) {
              total++;
            }
          });
          return total;
        }

        if (totalSelected() > 1) {
          $('.clear-compare').removeClass("disable");
          $('.compare-career').removeClass("disable");
        }
      });

      $(once('cdqworkvalues', '.quiz-work-values-wrapper', context)).each(function () {
        $('.work-values-toggle').on('click', function () {
          if ($(".important-values").hasClass("hide")) {
            $(this).removeClass("hide");
            $(".somewhat-values").addClass("hide");
            $(".important-values").removeClass("hide");
            $(".work-values-toggle span.vaa1").hide();
            $(".work-values-toggle span.vaa").show();
            $(".result-heading h3.vaa1").hide();
            $(".result-heading h3.vaa").show();
            $("#myResult").animate({scrollTop: 0}, "slow");
          } else {
            $(this).addClass("hide");
            $(".somewhat-values").removeClass("hide");
            $(".important-values").addClass("hide");
            $(".work-values-toggle span.vaa").hide();
            $(".work-values-toggle span.vaa1").show();
            $(".result-heading h3.vaa").hide();
            $(".result-heading h3.vaa1").show();
          }

        });
      });

      $(once('cdqworkvalues',".hideshow-workbc", context)).each(function () {
        $(this).on("click", function () {
          if ($(".hideshow-workbc").hasClass("hide")) {
            $(this).removeClass("hide");
            $(".hideshow-workbc span.vaa1").hide();
            $(".hideshow-workbc span.vaa").show();
            $(".result-heading h2.vaa1").hide();
            $(".result-heading h2.vaa").show();
            $("#myResult").animate({scrollTop: 0}, "slow");
          } else {
            $(this).addClass("hide");
            $(".hideshow-workbc span.vaa").hide();
            $(".hideshow-workbc span.vaa1").show();
            $(".result-heading h2.vaa").hide();
            $(".result-heading h2.vaa1").show();
          }
        });
      })

        //
        // $(".hideshow", context)
        //   .once("workbc")
        //   .on("click", function () {
        //     if ($(".hideshow").hasClass("hide")) {
        //       $(this).removeClass("hide");
        //       $(".hideshow span.vaa1").hide();
        //       $(".hideshow span.vaa").show();
        //       $(".result-heading h2.vaa1").hide();
        //       $(".result-heading h2.vaa").show();
        //       $(".itm.hide").hide();
        //       $(".extradivs.hide").removeClass("show");
        //       $("html, body").animate({scrollTop: 200}, "slow");
        //     } else {
        //       $(this).addClass("hide");
        //       $(".itm.hide").show();
        //       $(".hideshow span.vaa").hide();
        //       $(".hideshow span.vaa1").show();
        //       $(".result-heading h2.vaa").hide();
        //       $(".result-heading h2.vaa1").show();
        //       $(".extradivs.hide").addClass("show");
        //     }
        //   });

        $(".work-value-quiz-carousel > .row").slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
          dots: false,
          arrows: true,
          adaptiveHeight: true,
          nextArrow: $(".hideshow-workbc"),
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
          } else {
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


      $(once('cdqprint', '.career-top-right', context)).each(function () {
        $(".compare-career-print > .print-window, .quiz-node-print > .print-window").on("click", function () {
          window.print();
        });
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

      $(".tbody-main").each(function (i) {
        if (i % 5 === 0) {
          $(this)
            .nextAll()
            .addBack()
            .slice(0, 5)
            .wrapAll('<div class="slide-tbody-main"></div>');
        }
      });

      $("#mobi-career-table").each(function () {
        $(this).find(".tbody").slick({
          infinite: false,
          slidesToShow: 1,
          slidesToScroll: 1,
          adaptiveHeight: true,
          dots: true,
          arrows: false,
        });
      });

      $("#myResult").each(function () {
        $(this).find(".carousel-inner-mobi > .row").slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: false,
          dots: true,
          arrows: false
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

    }
  };

})(Drupal, jQuery, once, drupalSettings);
