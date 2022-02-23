(function ($, Drupal) {
	Drupal.behaviors.workbc = { 
		attach: function (context, settings) {
			$('.hideshow', context).once('workbc').on('click', function (){
				if($('.hideshow').hasClass('hide')){
					$(this).removeClass("hide");
					$('.hideshow span').text("+");
					$('.hideshow span.vaa').text("View All Aptitudes");
					
					$('.itm.hide').hide();
				}else {
					$(this).addClass("hide");
					$('.itm.hide').show();
					$('.hideshow span').text("-");
					$('.hideshow span.vaa').text("Show Top Aptitudes");
				}
			});
			  if ( $(window).width() < 640 ) {
				  $('#block-views-block-career-quizzes-block-1 #myCarousel', context).once('workbc').carousel({
						pause: true,
						interval: false
					});
					$('#block-views-block-career-quizzes-block-2 #myCarousel', context).once('workbc').carousel({
						pause: true,
						interval: false
					});
					$('#myResult', context).once('workbc').carousel({
						pause: true,
						interval: false
					});
				  if($('#block-views-block-career-quizzes-block-1 #myCarousel .career').parent().hasClass("row")){
					$('#block-views-block-career-quizzes-block-1 #myCarousel .career').unwrap().addClass('carousel-item');  
					$('#block-views-block-career-quizzes-block-1 #myCarousel .career:first').addClass('active');
				  }
				  if($('#block-views-block-career-quizzes-block-2 #myCarousel .personality').parent().hasClass("row")){
					$('#block-views-block-career-quizzes-block-2 #myCarousel .personality').unwrap().addClass('carousel-item');  
					$('#block-views-block-career-quizzes-block-2 #myCarousel .personality:first').addClass('active');
				  }
				  if($('#myResult .itm').parent().hasClass("row")){
					$('#myResult .itm').unwrap().addClass('carousel-item');  
					$('#myResult .itm:first').addClass('active');
				  }
				}
			
		}
	}
})(jQuery, Drupal);