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
            infinite: false,
            dots: true,
            arrows: true,
            nextArrow: $(".next-true"),
            prevArrow: $(".prev-true")
          });
        } else {
          $(".tools-resource-items-wrapper > .field__items")
            .filter(".slick-initialized")
            .slick("unslick");
          $(".compare-career-main-wrapper .career-content-compare")
            .filter(".slick-initialized")
            .slick("unslick");
        }
      });

      players = new Array();

      function onYouTubeIframeAPIReady() {
        const temp = $("iframe.yt_players");
        for (let i = 0; i < temp.length; i++) {
          const t = new YT.Player($(temp[i]).attr("id"), {
            events: {
              onStateChange: onPlayerStateChange
            },
          });
          players.push(t);
        }
      }
      function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.PLAYING) {
          alert(players[0].getVideoUrl());
          // alert(players[0].getVideoUrl());
          const temp = event.target.getVideoUrl();
          const tempPlayers = $("iframe.yt_players");
          for (let i = 0; i < players.length; i++) {
            if (players[i].getVideoUrl() != temp) {
              players[i].stopVideo();
            }
          }
        }
      }
      setTimeout(function () {
        onYouTubeIframeAPIReady();
      }, 1500);

      equalHeight(false);

      window.onresize = function () {
        equalHeight(true);
      };

      function equalHeight(resize) {
        let elements = document.getElementsByClassName("equalHeight"),
          allHeights = [],
          i = 0;
        if (resize === true) {
          for (i = 0; i < elements.length; i++) {
            elements[i].style.height = "auto";
          }
        }
        for (i = 0; i < elements.length; i++) {
          const elementHeight = elements[i].clientHeight;
          allHeights.push(elementHeight);
        }
        for (i = 0; i < elements.length; i++) {
          elements[i].style.height = `${Math.max(...allHeights)}px`;
          if (resize === false) {
            elements[i].className = `${elements[i].className} show`;
          }
        }
      }

      $(".career-table-link > a").on("click", function () {
        const getCarDataId = $(this).parent().parent().data("id");
        $(".career-table-row").removeClass("active");
        $(this).parent().parent().addClass("active");
        $(".career-content-main-wrapper .career-content-item").removeClass(
          "active"
        );
        $(
          `.career-content-main-wrapper .career-content-item#${getCarDataId}`
        ).addClass("active");
      });

      $(".career-table-mobi-row-link").on("click", function () {
        // var getCarDataId = $(this).parent().parent().data("id");
      });

      setTimeout(function () {
        $(".remove-link + .compare-carr > .career-chkk").prop("checked", true);
        const checkedLoadNum = $(
          ".remove-link + .compare-carr > .career-chkk:checked"
        ).length;
        if (checkedLoadNum > 1) {
          $(".top-btn > a").removeClass("disable");
        }
 else {
          $(".top-btn > a").addClass("disable");
        }
      }, 1500);
      $(".career-checkbox").change(function (e) {
        const checkedNum = $(".career-checkbox:checked").length;
        $(this).parent().prev().click();
        if (checkedNum > 1) {
          $(".top-btn > a").removeClass("disable");
        }
 else {
          $(".top-btn > a").addClass("disable");
        }
      });
      $(".clear-compare").click(function () {
        $(".remove-link").each(function () {
          $(this).next().find(".career-chkk").prop("checked", false);
          $(this).click();
        });
      });

      $(".career-mobi-checkbox").change(function () {
        const checkedNum = $(".career-mobi-checkbox:checked").length;
        $(this).parent().prev().click();
        if (checkedNum > 1) {
          $(".top-career-mobi-content .top-btn > a").removeClass("disable");
        }
 else {
          $(".top-career-mobi-content .top-btn > a").addClass("disable");
        }
      });

      $(".tbody-main").each(function (i) {
        if (i % 5 == 0) {
          $(this)
            .nextAll()
            .addBack()
            .slice(0, 5)
            .wrapAll('<div class="slide-tbody-main"></div>');
        }
      });

      $(".careers-mobi-table-wrapper > .tbody").slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        arrows: false,
      });

      // $(".career-table-mobi-row-link").magnificPopup({
      //   type: 'inline',
      //   midClick: true,
      //   mainClass: 'mfp-fade'
      // });
    },
  };
})(jQuery, Drupal, drupalSettings);
