
(function (Drupal, $, once, drupalSettings) {
  Drupal.behaviors.CareerCompare = {
    attach: function (context, settings) {

      $(once('cdqcareermatch', '.careers-main-wrapper', context)).each(function () {

        $('.compare-career-checkbox').on('click', function() {
          let careerMatchId = $(this).data('career-match-id');
          selected = $(this).is(':checked');
          if (selected && totalSelected() > 3) {
            $(this).prop('checked', false);
            console.log("3 Max");
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


        $('.views-field-title').on('click', function() {
            let current_item = $(this).closest('.career-table-row').data('id');
            $('.career-content-item').each(function() {
              $(this).removeClass("active");
            });
            $('#' + current_item).addClass("active");

            $('.career-table-row').each(function() {
              $(this).closest('.career-table-row').removeClass("active");
            });
            $(this).closest('.career-table-row').addClass("active");
        });

        function totalSelected() {
          let total = 0;
          $('.compare-career-checkbox').each(function() {
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
        $('.work-values-toggle').on('click', function() {
          if ($(".important-values").hasClass("hide")) {
            $(this).removeClass("hide");
            $(".somewhat-values").addClass("hide");
            $(".important-values").removeClass("hide");
            $(".work-values-toggle span.vaa1").hide();
            $(".work-values-toggle span.vaa").show();
            $(".result-heading h3.vaa1").hide();
            $(".result-heading h3.vaa").show();
            $("#myResult").animate({scrollTop: 0}, "slow");
          }
          else {
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
