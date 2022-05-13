(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.snowplow = {
      attach: function (context, settings) {

        if (typeof context.location !== 'undefined') { // Only fire on document load.
            var action_event = "load";
            var count = 0;
            
        }
        
        
        
        function snowplow_tracker(quiz_type, step) {
            $(this, context).once('num_'+count).each(function() {
                window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_step/jsonschema/1-0-0",
                    "data": {
                        "category": "Career",
                        "quiz": quiz_type,
                        "step": step
                    }
                });
            });
        } 
    }
}
})(jQuery, Drupal, drupalSettings);
