(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.snowplow = {
      attach: function (context, settings) {

        if (typeof context.location !== 'undefined') { // Only fire on document load.
            var action_event = "load";
            var count = 0; 
        }
        
        $('.views-field-field-quiz-link.quiz_link').click(function () {
           
            var quiz_type = $(this).parents(".quiz-list").find('.title-field h4').text();
            if(quiz_type == 'abilities' || quiz_type == 'Work Preferences' || quiz_type == 'Interests') {
                var category = 'Career';
            } else {
                var category = 'personality'
            }
            var step = 1;
            count++;
            snowplow_tracker_quiz(category, quiz_type, step);
        }); 

        if($('body').hasClass('path-quiz')){
            var path = window.location.pathname.split('/');
            count++;
            var current_step = path[3].slice(path[3].length - 1);
            var step = parseInt(current_step);
            quiz_type = path[2].split('-')[0];
            if(quiz_type == 'abilities' || quiz_type == 'Work Preferences' || quiz_type == 'Interests') {
                var category = 'Career';
            } else {
                var category = 'personality';
            }
            if(step != 1) {
                snowplow_tracker_quiz(category, quiz_type, step); 
            }
        }
        // $('input#edit-submit[value="Next"]').click(function () {
            
        // });
        
        function snowplow_tracker_quiz(category, quiz_type, step) {
            $(this, context).once('num_'+count).each(function() {
                window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_step/jsonschema/1-0-0",
                    "data": {
                        "category": category,
                        "quiz": quiz_type,
                        "step": step
                    }
                });
            });
        } 

        //compare career
        var noc_group = {};
        $('.top-career-content .top-btn a').click(function() {
            count++;
            var action = $(this).text().slice(0, -7).trim();
            $('.career-table-row').each(function(index) {
               index++;
               var noc_id =  $(this).find('.remove-link').parents('.career-table-row').find('.career-table-link .noc').text();
               noc_id = noc_id.replace('(','').replace(')','').trim();
               noc_id = noc_id.split(' ')[1];
               if(noc_id != '') {
                noc_group["noc_"+index] = noc_id;
               }else if(index <= 3){
                noc_group["noc_"+index] = null;
               }
               
            });
            console.log(action);
            snowplow_tracker_compare(action, noc_group);
        });

        function snowplow_tracker_compare(action, noc_group) {
            $(this, context).once('num_'+count).each(function() {
                window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_compare/jsonschema/1-0-0",
                "data": {
                    "action": action,
                    noc_group
                }
              });
            });
        }

        //Career clicks
        $('.btn.btn-find-job').click(function() {
            count++;
            if($('body').hasClass('path-node')){
                var source = "search";
            } else {
                var source = "compare";
            }
            var click_type = "find_jobs";
            var url = $(this).attr('href');
            var text = $(this).attr('href').split('?');
            var noc_id = text[text.length - 1];
            noc_id = noc_id.split('=')[1];

            snowplow_tracker_target(click_type, source, noc_id, url);
        });

        

        //profile click
        $('.btn.btn-career-profile').click(function() {
            count++
            if($('body').hasClass('path-node')){
                var source = "search";
            } else {
                var source = "compare";
            }
            var click_type = "career_profile";
            var url = $(this).attr('href');
            var text = $(this).attr('href').split('/');
            var noc_id = text[text.length - 1];

            snowplow_tracker_target(click_type, source, noc_id, url);
        });

        function snowplow_tracker_target(click_type, source, text, url) {
            $(this, context).once('num_'+count).each(function() {
                window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_search_click/jsonschema/1-0-0",
                    "data": {
                        "click_type": click_type,
                        "source": source,
                        "text": text,
                        "url" : url
                    }
                });
            });
        }

        //Print and email click
        $('.career-top-right .icon').click(function() {
            count++;
            if($('body').hasClass('path-node')){
                var source = "search";
            } else {
                var source = "compare";
            }

            if($(this).parent().hasClass('compare-career-print')) {
                var click_type = "print";
            } else if ($(this).parent().hasClass('compare-career-email')) {
                var click_type = "email";
            }
            snowplow_tracker_print_email(click_type, source);
        });

        function snowplow_tracker_print_email(click_type, source, text, url) {
            $(this, context).once('num_'+count).each(function() {
                window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_search_click/jsonschema/1-0-0",
                    "data": {
                        "click_type": click_type,
                        "source": source,
                    }
                });
            });
        }

        //Sort matches
        $('.careers-table-main-wrapper th a').click(function() {
            count++;
            var click_type = "sort";
            var source = "search";
            var sort_type = $(this).attr('href').split('#')[0].split('&')[0].split('=')[1];
            var text = $(this).attr('href').split('#')[0].split('&')[1].split('=')[1];
            var sort_text = sort_type+' - '+text;
            console.log(sort_text);
            snowplow_tracker_sort(click_type, source, sort_text);

        })

        function snowplow_tracker_sort(click_type, source, text) {
            $(this, context).once('num_'+count).each(function() {
                window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_search_click/jsonschema/1-0-0",
                    "data": {
                        "click_type": click_type,
                        "source": source,
                        "text": text
                    }
                });
            });
        }

        function snowplow_tracker_target(click_type, source, text, url) {
            $(this, context).once('num_'+count).each(function() {
                window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_search_click/jsonschema/1-0-0",
                    "data": {
                        "click_type": click_type,
                        "source": source,
                        "text": text,
                        "url" : url
                    }
                });
            });
        }

        //Results page
        if($('body.path-node').length > 0) {
            var appt = {};
            var appt_set = {};
            count++;
            var i = 0;
            $('.carousel-inner-device .itm').each(function(index) {
                i++;
                var appt_score = $(this).find('.field-match').text();
                var appt_key_name = "Apptitude_"+i+"_name";
                var appt_name = $(this).find('.title-field').text();
                var appt_key_score = "Apptitude_"+i+"_score";
                appt[appt_key_name] = appt_name.trim();
                appt[appt_key_score] = appt_score.trim().slice(0, -5).trim().slice(0, -1);
            });

            var quiz_type = $('.page-title span').text().split(' ')[0];
            if(quiz_type == 'abilities' || quiz_type == 'Work Preferences' || quiz_type == 'Interests') {
                var category = 'career';
            } else {
                var category = 'personality';
            }
            snowplow_tracker_results(category, quiz_type, appt);
        }

        function snowplow_tracker_results(category, quiz_type, appt) {
            $(this, context).once('num_'+count).each(function() {
                window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_result/jsonschema/1-0-0",
                "data": {
                    "category": category,
                    "quiz": quiz_type,
                    appt
                }
              });
            });
        } 

        //Preview profile
        $('.career-table-row-link').click(function () {
            count++;
            var noc_id  = $(this).find('.noc').text();
            noc_id = noc_id.replace('(','').replace(')','').trim();
            noc_id = noc_id.split(' ')[1];

            var click_type = 'preview';

            if($('body').hasClass('path-node')){
                var source = "search";
            } else {
                var source = "compare";
            }
            
            snowplow_preview_profile(click_type, source, noc_id);
        });

        function snowplow_preview_profile(click_type, source, text) {
            $(this, context).once('num_'+count).each(function() {
                window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_search_click/jsonschema/1-0-0",                "data": {
                    "click_type": click_type,
                    "source": source,
                    "text": text
                }
              });
            });
        } 

        //Error calls
        if($('.region-content > div').hasClass('alert-error')) {
            count++;
            var quiz_type = $('.page-title span').text().split(' ')[0];
            if(quiz_type == 'abilities' || quiz_type == 'Work Preferences' || quiz_type == 'Interests') {
                var category = 'career';
            } else {
                var category = 'personality';
            }

            var path = window.location.pathname.split('/');
            var current_step = path[3].slice(path[3].length - 1);
            var step = parseInt(current_step);
            quiz_type = path[2].split('-')[0];

            snowplow_error_call(category, quiz_type, step);
        }

        function snowplow_error_call(category, quiz, step) {
            $(this, context).once('num_'+count).each(function() {
                window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_error/jsonschema/1-0-0",
                    "data": {
                        "error_message": "Please answer all questions before proceeding.",
                        "category": category,
                        "quiz": "Abilities",
                        "step": step
                    }
                }); 
            });
        }
    }
}
})(jQuery, Drupal, drupalSettings);
