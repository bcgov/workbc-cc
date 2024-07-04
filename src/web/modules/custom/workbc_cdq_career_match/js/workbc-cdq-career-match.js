
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
                console.log('success');
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
          console.log('clear compare');
          $('.compare-career-checkbox').each(function() {
            if ($(this).is(':checked')) {
              let careerMatchId = $(this).data('career-match-id');
              $.ajax({
                url: Drupal.url('career-match/update-selected'),
                type: 'POST',
                dataType: 'json',
                data: { 'id' : careerMatchId, 'selected': false},
                success: function (response) {
                  console.log('success');
                }
              });              
              console.log(careerMatchId);
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

        console.log("Total Selected: " + totalSelected()); 
        if (totalSelected() > 1) {
          console.log("gonna remove disable");
          $('.clear-compare').removeClass("disable");
          $('.compare-career').removeClass("disable");
        }
        else {
          console.log("TEST ->");
        }
      });

    }
  };

})(Drupal, jQuery, once, drupalSettings);