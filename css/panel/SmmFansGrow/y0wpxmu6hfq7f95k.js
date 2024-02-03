$('.panel-menu-btn').on('click', function () {
  $('.panel-page-side').toggleClass('show')
})

$('.hamburger-btn').on('click', function () {
  $('.mobile-menu-bg').addClass('show')
  $('.mobile-menu').addClass('show')
})
$('.close-menu').on('click', function () {
  $('.mobile-menu-bg').removeClass('show')
  $('.mobile-menu').removeClass('show')
})
$('.mobile-menu-bg').on('click', function () {
  $('.mobile-menu-bg').removeClass('show')
  $('.mobile-menu').removeClass('show')
})

$(document).ready(function(){
  $("#serv-inp").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#serv-table tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
}); 

$('.logos-list a').click(function(){

	var dataFilter=$(this).data("services-filter");
      console.log(dataFilter);
        var value = dataFilter;
        $(".app-mtable tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
   });



function ikon(opt) {
      var ikon = "";
    if (opt.indexOf("Instagram") >= 0) {
        ikon = "<span class=\"fs-ig\"><i class=\"fab fa-instagram\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("YouTube") >= 0) {
        ikon = "<span class=\"fs-yt\"><i class=\"fab fa-youtube\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Facebook") >= 0) {
        ikon = "<span class=\"fs-fb\"><i class=\"fab fa-facebook-square\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Youtube") >= 0) {
        ikon = "<span class=\"fs-yt\"><i class=\"fab fa-youtube\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Twitter") >= 0) {
        ikon = "<span class=\"fs-tw\"><i class=\"fab fa-twitter\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Google") >= 0) {
        ikon = "<span class=\"fs-gp\"><i class=\"fab fa-google-plus\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Swarm") >= 0) {
        ikon = "<span class=\"fs-fsq\"><i class=\"fab fa-forumbee\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Dailymotion") >= 0) {
        ikon = "<span class=\"fs-dm\"><i class=\"fab fa-hospital-o\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Periscope") >= 0) {
        ikon = "<span class=\"fs-pc\"><i class=\"fab fa-map-marker\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Soundcloud") >= 0) {
        ikon = "<span class=\"fs-sc\"><i class=\"fab fa-soundcloud\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Vine") >= 0) {
        ikon = "<span class=\"fs-vn\"><i class=\"fab fa-vine\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Spotify") >= 0) {
        ikon = "<span class=\"fs-sp\"><i class=\"fab fa-spotify\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Snapchat") >= 0) {
        ikon = "<span class=\"fs-snap\"><i class=\"fab fa-snapchat-square\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Pinterest") >= 0) {
        ikon = "<span class=\"fs-pt\"><i class=\"fab fa-pinterest-p\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("iTunes") >= 0) {
        ikon = "<span class=\"fs-apple\"><i class=\"fab fa-apple\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Müzik") >= 0) {
        ikon = "<span class=\"fs-music\"><i class=\"fab fa-music\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Vimeo") >= 0) {
        ikon = "<span class=\"fs-videmo\"><i class=\"fab fa-vimeo\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Ekşi") >= 0) {
        ikon = "<span class=\"fs-eksi\"><i class=\"fab fa-tint\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Telegram") >= 0) {
        ikon = "<span class=\"fs-telegram\"><i class=\"fab fa-telegram\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Twitch") >= 0) {
        ikon = "<span class=\"fs-twc\"><i class=\"fab fa-twitch\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Clubhouse") >= 0) {
        ikon = "<span class=\"fs-club\"><i class=\"fas fa-hand-sparkles\" aria-hidden=\"true\"></i> </span>";
	} else if (opt.indexOf("Amazon") >= 0) {
        ikon = "<span class=\"fs-amaz\"><i class=\"fab fa-amazon\" aria-hidden=\"true\"></i> </span>";
	} else if (opt.indexOf("Tumblr") >= 0) {
        ikon = "<span class=\"fs-tumb\"><i class=\"fab fa-tumblr-square\" aria-hidden=\"true\"></i> </span>";
	} else if (opt.indexOf("Yandex") >= 0) {
        ikon = "<span class=\"fs-yndx\"><i class=\"fab fa-yoast\" aria-hidden=\"true\"></i> </span>";
	} else if (opt.indexOf("Linkedin") >= 0) {
        ikon = "<span class=\"fs-lnk\"><i class=\"fab fa-linkedin\" aria-hidden=\"true\"></i> </span>";
	} else if (opt.indexOf("Discord") >= 0) {
        ikon = "<span class=\"fs-dc\"><i class=\"fab fa-discord\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Tiktok") >= 0) {
        ikon = "<span class=\"fs-tiktok\"><i class=\"fab fa-tiktok\" aria-hidden=\"true\"></i> </span>";
    } else {
        ikon = "<span class=\"\"><i class=\"far fa-star\" aria-hidden=\"true\"></i> </span>  ";
    }
    return ikon;
   }



function selectOrder(val) {
    $('#orderform-service').val(val);
    $("#orderform-service").trigger("change");
	var ico = ikon($("#orderform-service option[value='" + val + "']").text());
    $("#order-services").html(ico + $("#orderform-service option[value='" + val + "']").text());
}
$("#order-sItem").click(function() {
    $("#order-services").html($(this).html());
});

function selectCategory(val) {
    $('#orderform-category').val(val);
    $("#orderform-category").trigger("change");
  	var ico = ikon($("#orderform-category option[value='" + val + "']").text());
    $("#order-category").html(ico + $("#orderform-category option[value='" + val + "']").text());
}

function selectCategory(val) {
    $('#orderform-category').val(val);
    $("#orderform-category").trigger("change");
  	var ico = ikon($("#orderform-category option[value='" + val + "']").text());
    $("#order-category").html(ico + $("#orderform-category option[value='" + val + "']").text());
}

$('.notify').hide()
jQuery('.notify-btn').on('click',function(){
  jQuery('.notify').toggle();
})