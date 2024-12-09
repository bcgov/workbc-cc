(function (Drupal, $, once, drupalSettings) {
    Drupal.behaviors.snowplow = {
      attach: function (context, settings) {

        var daCount = 0;

        if (typeof context.location !== 'undefined') { // Only fire on document load.
            var action_event = "load";
            var count = 0; 
            if($('body').hasClass('path-webform') && !$('.region-content > div').hasClass('alert-error')){
                if($('form').hasClass('webform-submission-edit-form')) {
                    var status = "modify";
                } else {
                    var status = "new";
                }
                var path = window.location.pathname.split('/');
                var quiz_type = find_quiz_type(path);

                var current_step = context.querySelector(".is-active").getAttribute("data-webform-page");
                var step = parseInt(current_step.replace("page_", ""));

                var category = find_quiz_category(quiz_type);

                if(step != 1) {
                    count++;
                    snowplow_tracker_quiz(category, quiz_type, step, status);    
                }
            }

            if($('main > div').hasClass("cdq-results")) {
                var appt = {};
                var appt_set = {};
                count++;
                var i = 0;
                $('.carousel-inner-device .itm').each(function(index) {
                    i++;
                    var appt_score = $(this).find('.field-match').text();
                    var appt_key_name = "Apptitude_"+i+"_name";
                    var appt_name = $(this).find('.title-field').text().toLowerCase();
                    var appt_key_score = "Apptitude_"+i+"_score";
                    appt[appt_key_name] = appt_name.trim();
                    appt[appt_key_score] = parseInt(appt_score.trim().slice(0, -5).trim().slice(0, -1));
                });
    
                var path = window.location.pathname.split('/');
                var quiz_type = find_quiz_type(path);

                if(quiz_type == 'abilities' || quiz_type == 'work_preferences' || quiz_type == 'interests') {
                    var category = 'career';
                } else {
                    var category = 'personality';
                }
                $(once('num_'+count, 'html', context)).each(function() {
                    snowplow_tracker_results(category, quiz_type, appt);
                });
            }
        }
        
        $('.quiz-new-link, .quiz-modify-link').click(function () {
            var quiz_type = $(this).parent().parent().find('.title-field h4').text();
            var lastIndex = quiz_type.lastIndexOf(" ");

            quiz_type = quiz_type.substring(0, lastIndex).trim();
            quiz_type = quiz_type.replaceAll(' ', '_').toLowerCase();

            if(quiz_type == 'abilities' || quiz_type == 'work_preferences' || quiz_type == 'interests') {
                var category = 'career';
            } else {
                var category = 'personality'
            }
            var step = 1;
            var status = "new";
            count++;

            if($(this).hasClass('quiz-modify-link')) {
                status = "modify";
            }
            snowplow_tracker_quiz(category, quiz_type, step, status);
            
        }); 
    

        function snowplow_tracker_quiz(category, quiz_type, step, status) {
            $(once('num_'+count, 'html', context)).each(function() {
                console.log("<<<snowplow_tracker_quiz>>>");
                console.log("  - " + category);
                console.log("  - " + quiz_type);
                console.log("  - " + step);
                console.log("  - " + status);
                console.log("<<<end>>>");
                window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_step/jsonschema/1-0-0",
                    "data": {
                        "category": category,
                        "quiz": quiz_type,
                        "step": step,
                        "status": status
                    }
                });
            });
        } 

        //compare careers
        $('.top-career-content .top-btn a').click(function() {
            var count = 0;
            var noc_group = [];
            var compare_count = 1;

            count++;
            
            $('.careers-main-wrapper .career-table-row .compare-career-checkbox').each(function(index) {
                if ($(this).is(':checked')) {
                  noc_group["noc_"+compare_count] = $(this).data('career-match-noc');
                  compare_count++;
                }
               index++;
            });

            if($(this).text() == "Compare Careers") {
                $(once('num_'+count, context)).each(function() {
                    snowplow_tracker_compare('compare', noc_group);
                });
            } else {
                $(once('num_'+count, context)).each(function() {
                    snowplow_tracker_compare('clear', noc_group);
                });
            }
        });


        $('.top-career-mobi-content .top-btn a').click(function() {
            var count = 0;
            var noc_group = [];
            var compare_count = 1;

            count++;

            $('.careers-mobi-main-wrapper .career-table-row .compare-career-checkbox').each(function(index) {
                if ($(this).is(':checked')) {
                  noc_group["noc_"+compare_count] = $(this).data('career-match-noc');
                  compare_count++;
                }
               index++;
            });

            if($(this).text() == "Compare Careers") {
                $(once('num_'+count, context)).each(function() {
                    snowplow_tracker_compare('compare', noc_group);
                });
            } else {
                $(once('num_'+count, context)).each(function() {
                    snowplow_tracker_compare('clear', noc_group);
                });
            }
        });

        function snowplow_tracker_compare(action, noc_group) {
            noc_1 = null;
            noc_2 = null;
            noc_3 = null;

            if(noc_group['noc_1'] != '') {
                noc_1 = noc_group['noc_1'];
            }
            if(noc_group['noc_2'] != '') {
                noc_2 = noc_group['noc_2'];
            }
            if(noc_group['noc_3'] != '') {
                noc_3 = noc_group['noc_3'];
            }

            console.log("<<<snowplow_tracker_compare>>>");
            console.log("  - " + action);
            console.log("  - " + noc_1);
            console.log("  - " + noc_2);
            console.log("  - " + noc_3);
            console.log("<<<end>>>")

            window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_compare/jsonschema/1-0-0",
                "data": {
                    "action": action,
                    "noc_1": noc_1,
                    "noc_2": noc_2,
                    "noc_3": noc_3
                }
            });
        }

        //career clicks
        $('.btn.btn-find-job').click(function() {
            count++;
            if($('main > div').hasClass("cdq-results")) {
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
           if($('main > div').hasClass("cdq-results")) {
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

        //Print and email click
        $('.career-top-right .quiz-node-print .icon').click(function() {
            count++;

            var click_type = "print";
            if($('main > div').hasClass("cdq-results")) {                
                var source = "search";
            } else {
                var source = "compare";
            }
            snowplow_tracker_print_email(click_type, source);            
        });

        $('.career-top-right .quiz-node-email .icon').click(function() {
            count++;

            var click_type = "email";
            if($('main > div').hasClass("cdq-results")) {                
                var source = "search";
            } else {
                var source = "compare";
            }
            snowplow_tracker_print_email(click_type, source);            
        });

        function snowplow_tracker_print_email(click_type, source) {
            $(once('num_'+count, 'html', context)).each(function() {
                console.log("<<<snowplow_tracker_print_email>>>");
                console.log("  - "+click_type);
                console.log("  - "+source);
                console.log("<<<end>>>");                
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
            var sort_field = $(this).attr('href').split('#')[0].split('&')[1].split('=')[1];
            var sort_direction = $(this).attr('href').split('#')[0].split('&')[2].split('=')[1];
            var sort_text = sort_field + ' - ' + sort_direction;
            snowplow_tracker_sort(click_type, source, sort_text);
        })

        function snowplow_tracker_sort(click_type, source, text) {
            console.log("<<<snowplow_tracker_sort>>>");
            console.log("  - " + click_type);
            console.log("  - " + source);
            console.log("  - " + text);
            console.log("<end>");
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
            $(once('num_'+count, 'html', context)).each(function() {
                console.log("<<<snowplow_tracker_target>>>");
                console.log("  - "+click_type);
                console.log("  - "+source);
                console.log("  - "+text);
                console.log("  - "+url);
                console.log("<<<end>>>");                
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
        $('#edit-submit[value="Submit"]').click(function() {
            var status = 'new';
            var step = null;

            var quiz_type = find_quiz_type();
            var category = find_quiz_category(quiz_type);
            snowplow_tracker_quiz(category, quiz_type, step, status); 
        });
        

        function snowplow_tracker_results(category, quiz_type, appt) {
            console.log("<<<snowplow_tracker_results>>>");
            console.log("  - " + category);
            console.log("  - " + quiz_type);
            console.log("  - " + appt);
            console.log("<<<end>>>");
            window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_result/jsonschema/1-0-0",
                "data": {
                    "category": category,
                    "quiz": quiz_type,
                    appt
                }
            });
        } 

        //Preview profile
        $('.career-table-row-link').click(function () {
            count++;
            var noc_id  = $(this).find('.noc').text();
            noc_id = noc_id.replace('(','').replace(')','').trim();
            noc_id = noc_id.split(' ')[1];

            var click_type = 'preview';

            if($('main > div').hasClass("cdq-results")) {
                var source = "search";
            } else {
                var source = "compare";
            }
            
            snowplow_preview_profile(click_type, source, noc_id);
        });

        function snowplow_preview_profile(click_type, source, text) {
            console.log("<<<snowplow_preview_profile>>>");
            console.log("  - " + click_type);
            console.log("  - " + source);
            console.log("  - " + text);
            console.log("<end>");
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

        //Error calls
        if($('.region-content > div').hasClass('alert-error')) {
            count++;
            quiz_type = find_quiz_type();

            if(quiz_type == 'abilities' || quiz_type == 'work_preferences' || quiz_type == 'interests') {
                var category = 'career';
            } else {
                var category = 'personality';
            }
            var current_step = context.querySelector(".is-active").getAttribute("data-webform-page");
            var step = parseInt(current_step.replace("page_", ""));

            snowplow_error_call(category, quiz_type, step);
        }

        function snowplow_error_call(category, quiz, step) {
            console.log("<<<snowplow_error_call>>>");
            console.log("  - " + category);
            console.log("  - " + quiz);
            console.log("  - " + step);
            console.log("<end>");
            $(this, context).once('num_'+count).each(function() {
                window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/career_quiz_error/jsonschema/1-0-0",
                    "data": {
                        "error_message": "Please answer all questions before proceeding.",
                        "category": category,
                        "quiz": quiz,
                        "step": step
                    }
                }); 
            });
        }

        function find_quiz_type() {
            var path = window.location.pathname.split('/');
            var quiz_type = '';
            if( path[2].lastIndexOf("-") > 0 ) {
                var lastIndex = path[2].lastIndexOf("-");
                quiz_type = path[2].substring(0, lastIndex).trim();
                quiz_type = quiz_type.replaceAll('-', '_').toLowerCase();
            } else {
                quiz_type = path[2].slice(0,-5).trim();
            }

            return quiz_type;
        }

        function find_quiz_category(quiz_type) {
            if(quiz_type == 'abilities' || quiz_type == 'work_preferences' || quiz_type == 'interests') {
                var category = 'career';
            } else {
                var category = 'personality';
            }
            return category;
        }
    }
}
})(Drupal, jQuery, once, drupalSettings);
