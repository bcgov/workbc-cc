$(document).ready(function(){
  $(window).on("load resize", function(){
    var $window = $(this).width();
    if($window < 768 && !$('.tools-resource-items-wrapper > .field__items').hasClass("slick-initialized")){
      $('.tools-resource-items-wrapper > .field__items').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        dots: true
      });
    }
    else{
      $('.tools-resource-items-wrapper > .field__items').slick('unslick');
    }
  });
});