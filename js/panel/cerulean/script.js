
$(document).ready(function(){
  $("#serv-inp").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#serv-table tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
}); 


$(window).on("load", function () {
  if ($("#g-recaptcha-response") !== null) {
    $("#g-recaptcha-response").attr("required", true);
  }
});
$(".icon-details").click(function () {
  $("#detailsModalLabel").text(
    $(this).parent().parent().find("td:eq(0)").text()
  );
  $("#detailsModal .modal-body").html($(this).data("details"));
  $("#detailsModal").modal("show");
});
$.expr[":"].icontains = function (a, i, m) {
  return $(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};

var fontFamily = getComputedStyle(document.documentElement).getPropertyValue('--primary-font-family').trim().replace(/"/g, '');
if(fontFamily == 'Euclid Circular B') {
    $('#fontFamily').attr('href', 'css/font.css');
}
else {
    $('#fontFamily').attr('href', 'https://fonts.googleapis.com/css2?family='+encodeURIComponent(fontFamily)+'&display=swap');
}
$('body').addClass('d-flex');

$('#menu-toggle').click(function(e) {
  e.preventDefault();
  $('#dashboard-wrapper').toggleClass('toggled');
});

function dashMenuToggle() {
    $('.app-sidebar').toggleClass('sidebar-inact');
    $('.app-header').toggleClass('sidebar-inact');
    $('.app-content').toggleClass('sidebar-inact');
}

$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});



$(document).ready(function () {
    setList(0);
    setList(1);    
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
    } else if (opt.indexOf("Zomato") >= 0) {
        ikon = "<span class=\"fs-zom\"><i class=\"fab fa-cutlery\" aria-hidden=\"true\"></i> </span>";
	} else if (opt.indexOf("Amazon") >= 0) {
        ikon = "<span class=\"fs-amaz\"><i class=\"fab fa-amazon\" aria-hidden=\"true\"></i> </span>";
	} else if (opt.indexOf("Tumblr") >= 0) {
        ikon = "<span class=\"fs-tumb\"><i class=\"fab fa-tumblr-square\" aria-hidden=\"true\"></i> </span>";
	} else if (opt.indexOf("Yandex") >= 0) {
        ikon = "<span class=\"fs-yndx\"><i class=\"fab fa-yoast\" aria-hidden=\"true\"></i> </span>";
	} else if (opt.indexOf("Linkedin") >= 0) {
        ikon = "<span class=\"fs-lnk\"><i class=\"fab fa-linkedin\" aria-hidden=\"true\"></i> </span>";
	} else if (opt.indexOf("Yahoo") >= 0) {
        ikon = "<span class=\"fs-yahoo\"><i class=\"fab fa-yahoo\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("TikTok") >= 0) {
        ikon = "<span class=\"fs-tiktok\"><i class=\"fa fa-music\" aria-hidden=\"true\"></i> </span>";
    } else {
        ikon = "<span class=\"\"><i class=\"far fa-star\" aria-hidden=\"true\"></i> </span>  ";
    }
    return ikon;
   }

function setList(val) {
    /* orders */
    if (val == 0) {
        $("#orders-drop").empty();
        $("#neworder_services option").each(function () {
            var ico = ikon($(this).text());
            $("#orders-drop").append('<button id="serviceItem" class="dropdown-item" type="button" onclick="selectOrder(' + $(this).val() + ')">' + ico + $(this).text() + "</button>");
        });
        /*if(this.selected) {*/
        var e = document.getElementById("neworder_services");
        var selected = $( "#neworder_services option:selected" ).text();
        var ico = ikon(selected);
        $("#serviceTitle").html(ico + selected);
        /*}else {
       var ico = ikon($("#neworder_services option:nth-child(1)").text());              
    	$("#serviceTitle").html(ico + $("#neworder_services option:nth-child(1)").text());         
                  }            */
    } else if (val == 1) {
        /* SERVICES */

        $("#category-drop").empty();
        $("#neworder_category option").each(function () {
            var ico = ikon($(this).text());
            $("#category-drop").append('<button id="categoryItem" class="dropdown-item" type="button" onclick="selectCategory(' + $(this).val() + ')">' + ico + $(this).text() + "</button>");
        });

        /* if(this.selected) {*/
        var e = document.getElementById("neworder_category");
        var selected = e.options[e.selectedIndex].text;
        var ico = ikon(selected);
        $("#categoryTitle").html(ico + selected);
        /*}else {      
        var ico = ikon($("#neworder_category option:nth-child(1)").text());              
        $("#categoryTitle").html(ico + $("#neworder_category option:nth-child(1)").text());
                 } */
    }
}
$(function (ready) {
    $("#neworder_services").change(function () {
        setList(0);
    });
    $("#neworder_category").change(function () {
        setList(1);
    });
});

function selectOrder(val) {
    $("#neworder_services").val(val);
    $("#neworder_services").trigger("change");
    var ico = ikon($("#neworder_services option[value='" + val + "']").text());
    $("#serviceTitle").html(ico + $("#neworder_services option[value='" + val + "']").text());
}
$("#serviceItem").click(function () {
    $("#serviceTitle").html($(this).html());
});

function selectCategory(val) {
    $("#neworder_category").val(val);
    $("#neworder_category").trigger("change");
    var ico = ikon($("#neworder_category option[value='" + val + "']").text());
    $("#categoryTitle").html(ico + $("#neworder_category option[value='" + val + "']").text());
}


function copywalletid(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
}