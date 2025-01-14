
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
              });
            }
          });
        });


        $('.close-compare-popup, .compare-popup-close').on('click', function() {
          $(".compare-popup-wrapper").removeClass("active");
        });


        // when user selects a career profile from the table (DESKTOP only)
        $('.careers-table-main-wrapper .views-field-title').on('click', function() {
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

        // when user selects a career profile from the table (MOBILE only)
        $('.careers-mobi-main-wrapper .views-field-title').on('click', function() {
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
          $('#modifyNextLinks').hide()
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

      // work values results - toggle sub-heading text
      $(once('main-content', '.path-quiz', context)).each(function () {
        $(".hideshow-workbc").on('click', function () {
          let expanded = $(".result-heading h3.vaa").is(':hidden');
          if (expanded) {
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

      $(once('main-content', '.work-value-quiz-carousel', context)).each(function () {
        $(this).find("> .row").slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
          dots: false,
          arrows: true,
          adaptiveHeight: true,
          nextArrow: $(".hideshow-workbc"),
        });
      });

      $(once('main-content', '.carousel-slider-mobi-row', context)).each(function () {
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

      $(once('main-content', '.mobi_cari_quiz', context)).each(function () {
        $(this).find(".carousel-inner > .career-item").slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: false,
          dots: true,
          arrows: false
        });
      });

      $(once('main-content', '.mobi-career-content-compare', context)).each(function () {
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

      // Bootstrap tooltip initialize
      $(once('mobi-career-table', '.matches-wrap', context)).each(function () {
        let toolTipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        let toolTipList  = toolTipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl, {
            placement: "right",
            customClass: "shadow p-3 mb-5 bg-body rounded"
          })
        });
      });

    }
  };


  Drupal.behaviors.CareerQuizzes = {
    attach: function (context, settings) {

      $("#block-workbc-cdq-content > form > div.form-wrapper").each(
        function () {
          if ($(this).find("> fieldset").hasClass("error")) {
              const actions = document.getElementById("edit-actions");
              const errors = document.getElementsByClassName("cdq-validation-error");
              if (errors.length == 0) {
                actions.insertAdjacentHTML("beforeend", '<div class="cdq-validation-error">Please answer all questions before proceeding.</div>');
              }
              const content = document.getElementById("block-workbc-cdq-content");
              content.scrollIntoView({ behavior: "smooth", block: "end" });

            }
          }
      );


    }
  };

})(Drupal, jQuery, once, drupalSettings);



