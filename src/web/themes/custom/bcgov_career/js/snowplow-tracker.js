(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.snowplow = {
      attach: function (context, settings) {

        if (typeof context.location !== 'undefined') { // Only fire on document load.
            var action_event = "load";
            var count = 0; 
            if($('body').hasClass('path-quiz') && !$('.region-content > div').hasClass('alert-error')){
                var path = window.location.pathname.split('/');
                if(path[4] == 'preview') {
                    var status = "modify";
                } else {
                    var status = "new";
                }
                
                var quiz_type = find_quiz_type(path);
                
                var current_step = path[3].substring(4, path[3].length);
                var step = parseInt(current_step);

               var category = find_quiz_category(quiz_type);

                if(step != 1) {
                    count++;
                    snowplow_tracker_quiz(category, quiz_type, step, status); 
                }
            }

            if($('body.path-node').length > 0) {
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
    
                var quiz_type = find_quiz_type_results();
                if(quiz_type == 'abilities' || quiz_type == 'work_preferences' || quiz_type == 'interests') {
                    var category = 'career';
                } else {
                    var category = 'personality';
                }
                $(this, context).once('num_'+count).each(function() {
                    snowplow_tracker_results(category, quiz_type, appt);
                });
            }
        }
        
        $('.views-field-field-quiz-link .quiz-link, .views-field-field-quiz-link .result-link').click(function () {
           
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
            count++;

            if($(this).hasClass('result-link')) {
                var status = "modify";
                step = 1;
                snowplow_tracker_quiz(category, quiz_type, step, status);
            } else if($(this).text() != 'View Your Results') {
                var status = "new";
                snowplow_tracker_quiz(category, quiz_type, step, status);
            }
        }); 
        
        function snowplow_tracker_quiz(category, quiz_type, step, status) {
            $(this, context).once('num_'+count).each(function() {
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

        //compare career
        $('.top-career-mobi-content .top-btn a').click(function() {
            count++;
            var noc_group = [];
            var compare_count = 1;
            var action = $(this).text().slice(0, -7).trim();

            $('.careers-mobi-table-wrapper .tbody-main').each(function(index) {
                index++;
                if($(this).find('.remove-link').length > 0 && compare_count <= 3) {
                    var noc_id =  $(this).find('.remove-link').parents('.tbody-main').find('.career-table-mobi-row-link .noc').text();
                    noc_id = noc_id.replace('(','').replace(')','').trim();
                    noc_id = noc_id.split(' ')[1];
                    if(noc_id != undefined) {
                        noc_group["noc_"+compare_count] = noc_id;
                        compare_count++;
                    }
                }
            });
            
            if($(this).text() == "Compare Careers") {
                $(this, context).once('num_'+count).each(function() {
                    snowplow_tracker_compare('compare', noc_group);
                });
            } else {
                $(this, context).once('num_'+count).each(function() {
                    snowplow_tracker_compare('clear', noc_group);
                });
            }
        });
        $('.top-career-content .top-btn a').click(function() {
            count++;
            var noc_group = [];
            var compare_count = 1;
            var action = $(this).text().slice(0, -7).trim();
            
            $('.career-table-row').each(function(index) {
               index++;
               if($(this).find('.remove-link').length > 0 && compare_count <= 3) {
                    var noc_id =  $(this).find('.remove-link').parents('.career-table-row').find('.career-table-link .noc').text();
                    noc_id = noc_id.replace('(','').replace(')','').trim();
                    noc_id = noc_id.split(' ')[1];
                    if(noc_id != undefined) {
                        noc_group["noc_"+compare_count] = noc_id;
                        compare_count++;
                    }
               }
               
            });
            
            if($(this).text() == "Compare Careers") {
                $(this, context).once('num_'+count).each(function() {
                    snowplow_tracker_compare('compare', noc_group);
                });
            } else {
                $(this, context).once('num_'+count).each(function() {
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
                if($(this).parents().hasClass('quiz-node-print')) {
                    var click_type = "print";
                } else if ($(this).parents().hasClass('quiz-node-email')) {
                    var click_type = "email";
                }
            } else {
                var source = "compare";
                if($(this).parents().hasClass('compare-career-print')) {
                    var click_type = "print";
                } else if ($(this).parents().hasClass('compare-career-email')) {
                    var click_type = "email";
                }
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
        $('#edit-submit[value="Submit"]').click(function() {
            var status = 'new';
            var step = null;

            var quiz_type = find_quiz_type();
            var category = find_quiz_category(quiz_type);
            snowplow_tracker_quiz(category, quiz_type, step, status); 
        });
        

        function snowplow_tracker_results(category, quiz_type, appt) {
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

            if($('body').hasClass('path-node')){
                var source = "search";
            } else {
                var source = "compare";
            }
            
            snowplow_preview_profile(click_type, source, noc_id);
        });

        function snowplow_preview_profile(click_type, source, text) {
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

            var path = window.location.pathname.split('/');
            var current_step = path[3].substring(4, path[3].length);
            var step = parseInt(current_step);

            snowplow_error_call(category, quiz_type, step);
        }

        function snowplow_error_call(category, quiz, step) {
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

        function find_quiz_type_results() {
            var quiz_type = $('.page-title span').text().split('-')[0].trim();
            var quiz_type = quiz_type.split(' ');
            if( quiz_type.length > 2 ) {
                quiz_type_s = quiz_type[0]+'_'+quiz_type[1];
            } else {
                quiz_type_s = quiz_type[0];
            }

            return quiz_type_s.toLowerCase();
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
})(jQuery, Drupal, drupalSettings);
