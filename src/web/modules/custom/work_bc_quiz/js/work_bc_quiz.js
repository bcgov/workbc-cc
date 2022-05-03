(function ($, Drupal) {
    Drupal.behaviors.workbc = { 
        attach: function (context, settings) {
            $(".wvq .hideshow .vaa").click(function(){

             $(".extradivs1").animate({marginLeft: "-100%"}, 1500);
            });
            $(".wvq .hideshow .vaa1").click(function(){
             $(".extradivs1").animate({marginLeft: "-0"}, 1500);

            });
            
        }
    }
})(jQuery, Drupal);