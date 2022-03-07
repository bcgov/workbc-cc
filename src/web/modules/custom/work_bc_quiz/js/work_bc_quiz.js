(function ($, Drupal) {
	Drupal.behaviors.workbc = { 
		attach: function (context, settings) {
			//alert($(window).width()+'-'+$('body').width());
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
			  if ( $(window).width() < 768 ) {
				  $('#block-views-block-career-quizzes-block-1 #myCarousel', context).once('workbc').carousel({
						pause: true,
						interval: false
					});
					$('#block-views-block-career-quizzes-block-2 #myCarousel1', context).once('workbc').carousel({
						pause: true,
						interval: false
					});
				  if($('#block-views-block-career-quizzes-block-1 #myCarousel .career').parent().hasClass("row")){
					$('#block-views-block-career-quizzes-block-1 #myCarousel .career').unwrap().addClass('carousel-item');  
					$('#block-views-block-career-quizzes-block-1 #myCarousel .career:first').addClass('active');
				  }
				  if($('#block-views-block-career-quizzes-block-2 #myCarousel1 .personality').parent().hasClass("row")){
					$('#block-views-block-career-quizzes-block-2 #myCarousel1 .personality').unwrap().addClass('carousel-item');  
					$('#block-views-block-career-quizzes-block-2 #myCarousel1 .personality:first').addClass('active');
				  }
				   
				   
				}
				 if($('body').width()<768)
				{
				  $('.dropdown-inner ul li').click(function(){
					$('.dropdown-inner ul li').removeClass('active');
					$(this).addClass('active 0');
				  });
				  $('.dropdown-inner ul li#items_03').click(function(){
					$('body').removeClass('overlay');
					$('.carousel-item.itm').removeClass("active");
					$('#myResult .carousel-inner>.itm:first').addClass('active');
					$('.carousel-indicators li').removeClass("active");
					$('.carousel-indicators li:first').addClass("active");
					$(".itm").slice(3,9).removeClass('carousel-item');
					
					$('#myResult .carousel-inner>.itm:first').addClass('active 1')
					$(".itm").slice(3,9).wrapAll("<div class='row extradivs'></div>");
					$(".carousel-indicators li").slice(3,9).wrapAll("<div class='extradots'></div>");
				  });
				  $('.dropdown-inner ul li#items_09').click(function(){
					$('body').removeClass('overlay');
					
					$('.carousel-item.itm').removeClass("active");
					$('#myResult .carousel-inner>.itm:first').addClass('active');
					$('.carousel-indicators li').removeClass("active");
					$('.carousel-indicators li:first').addClass("active");
					if($('.carousel-indicators li').parent().hasClass("extradots")){
					  $('.carousel-indicators .extradots li').unwrap();
					}
					if($('#myResult .itm').parent().hasClass("extradivs")){
					  $('#myResult .extradivs .itm').unwrap();
					  $('#myResult .itm').addClass('carousel-item'); 
					}
				  });
				 
				  $('.mobile-view svg').click(function(){
					$('body').addClass('overlay');
				  });
				  $('.cross-button span').click(function(){
					$('body').removeClass('overlay');
				  });
				   
					$('#myResult', context).once('workbc').carousel({
						pause: true,
						interval: false
					});
				  if($('#myResult .itm').parent().hasClass("row")){
					$('#myResult .carousel-item>.itm').unwrap();
					$('#myResult .carousel-inner>.itm').addClass('carousel-item');  
					$('#myResult .carousel-inner>.itm:first').addClass('active');
				}
			  }
				
			
		}
	}
})(jQuery, Drupal);