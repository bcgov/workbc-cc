(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.customJs = {
    attach(context, settings) {
      $(window).on("load resize", function () {
        const $window = $(this).width();
        if (
          $window < 768 &&
          !$(".tools-resource-items-wrapper > .field__items").hasClass(
            "slick-initialized"
          )
        ) {
          $(".tools-resource-items-wrapper > .field__items").slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: true,
            dots: true,
          });

          $(".compare-career-main-wrapper .career-content-compare").slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: true,
            dots: true,
            arrows: false
          });
        } else {
          $(".tools-resource-items-wrapper > .field__items").filter('.slick-initialized').slick('unslick');
          $(".compare-career-main-wrapper .career-content-compare").filter('.slick-initialized').slick('unslick');
        }
      });

      $(".career-table-link > a").on("click", function(){
        var getCarDataId = $(this).parent().parent().data("id");
        $(".career-table-row").removeClass("active");
        $(this).parent().parent().addClass("active");
        $(".career-content-main-wrapper .career-content-item").removeClass("active");
        $(".career-content-main-wrapper .career-content-item#"+getCarDataId).addClass("active");
      });

      $(".career-table-mobi-row-link").on("click", function(){
        //var getCarDataId = $(this).parent().parent().data("id");
      });

      setTimeout(function(){
        $(".remove-link + .compare-carr > .career-chkk").prop('checked', true);
        var checkedLoadNum = $(".remove-link + .compare-carr > .career-chkk:checked").length;
        if(checkedLoadNum > 1){
          $(".top-btn > a").removeClass("disable");
        }
        else{
          $(".top-btn > a").addClass("disable");
        }
      }, 1500);
      $('.career-checkbox').change(function(e) {
        var checkedNum = $(".career-checkbox:checked").length;
        $(this).parent().prev().click();
        if(checkedNum > 1){
          $(".top-btn > a").removeClass("disable");
        }
        else{
          $(".top-btn > a").addClass("disable");
        }
      });
      $(".clear-compare").click(function(){
        $(".remove-link + .compare-carr > .career-chkk").click();
      });

      $(".career-mobi-checkbox").change(function() {
        var checkedNum = $(".career-mobi-checkbox:checked").length;
        $(this).parent().prev().click();
        if(checkedNum > 1){
          $(".top-career-mobi-content .top-btn > a").removeClass("disable");
        }
        else{
          $(".top-career-mobi-content .top-btn > a").addClass("disable");
        }
      });

      $('.tbody-main').each(function(i) {
        if( i % 5 == 0 ) {
            $(this).nextAll().addBack().slice(0,5).wrapAll('<div class="slide-tbody-main"></div>');
        }
      });

      $('.careers-mobi-table-wrapper > .tbody').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        arrows:false
      });

      // $(".career-table-mobi-row-link").magnificPopup({
      //   type: 'inline',
      //   midClick: true,
      //   mainClass: 'mfp-fade'
      // });
    },
  };
})(jQuery, Drupal, drupalSettings);
