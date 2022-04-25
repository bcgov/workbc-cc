(function ($, Drupal) {
    Drupal.behaviors.workbc = { 
        attach: function (context, settings) {
            //alert($(window).width()+'-'+$('body').width());
            $(".wvq .hideshow .vaa").click(function(){
              $('.wvq .col-print-item.itm').hide();
            });
            $(".wvq .hideshow .vaa1").click(function(){
              $('.wvq .col-print-item.itm').show();
            });
            
        }
    }
})(jQuery, Drupal);