
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
            $(".compare-popup-wrapper").addClass("active");
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


        $('.close-compare-popup, .compare-popup-close').on('click', function() {
          console.log('close compare popup');
            $(".compare-popup-wrapper").removeClass("active");
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
          $('.bottom-links').hide()
          $("html, body").animate({ scrollTop: $(".path-quiz").offset().top }, "slow");
        });

        // when user clicks back to quiz link ...
        $('a#back-to-quiz').on('click', function() {
          $('.cdq-results').each(function() {
            $(this).removeClass("user-selected");
          });

          $('.career-content-item').each(function() {
            $(this).removeClass("active");
          });
          $('.bottom-links').show()
          $("html, body").animate({ scrollTop: $(".path-quiz").offset().top }, "slow");
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

      $(once('main-content', '.category-results-toggle', context)).each(function () {
        $(this).on('click', function () {
          let expanded = $("#categoryToggle").attr('aria-expanded');
          if (expanded === "false") {
            $(".result-heading h3.vaa1").hide();
            $(".result-heading h3.vaa").show();
          } else {
            $(".result-heading h3.vaa").hide();
            $(".result-heading h3.vaa1").show();
          }
        })
      });

      $(once('main-content', '.path-quiz', context)).each(function () {
        $(".hideshow-workbc").on('click', function () {
          let expanded = $("#categoryToggle").attr('aria-expanded');
          if (expanded === "false") {
            $(".result-heading h3.vaa1").hide();
            $(".result-heading h3.vaa").show();
          } else {
            $(".result-heading h3.vaa").hide();
            $(".result-heading h3.vaa1").show();
          }
        })
      });

      $(once('main-content', '.mobi-tools-resource', context)).each(function () {
        $(this).find("> .field__items").slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
          dots: true,
        });
      });

      $(once('mobi-career-table', '.careers-mobi-table-wrapper', context)).each(function () {

        $(".tbody-main").each(function (i) {
          if (i % 5 === 0) {
            $(this)
              .nextAll()
              .addBack()
              .slice(0, 5)
              .wrapAll('<div class="slide-tbody-main"></div>');
          }
        });

        $(this).find("> .tbody").slick({
          infinite: false,
          slidesToShow: 1,
          slidesToScroll: 1,
          adaptiveHeight: true,
          dots: true,
          arrows: false,
        });
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
