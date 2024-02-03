function dashMenuToggle() {
  $('.app-container').toggleClass('sidebar-action');
}

function homeMenuToggle() {
  $('.head-menu').slideToggle(200);
}

function mainDropdown() {
  $('.main-dd').toggleClass('hidden');
}

$(function () {
  $('[data-toggle="tooltip"]').tooltip();

  if($("#dc-body").length) {
      $("#dc2-body").height($("#dc-body").height());
  }
})

$(document).ready(function() {
    setList(0);
    setList(1);

});

function ikon(opt) {
    var ikon = "";
    if (opt.indexOf("Instagram") >= 0) {
        ikon = "<span class=\"ico-ig\"><i class=\"fab fa-instagram\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("IGTV") >= 0) {
        ikon = "<span class=\"ico-ig\"><i class=\"fab fa-instagram\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Facebook") >= 0) {
        ikon = "<span class=\"ico-fb\"><i class=\"fab fa-facebook-square\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Trustpilot") >= 0) {
        ikon = "<span class=\"ico-trustpilot\"><i class=\"fas fa-solid fa-star\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Youtube") >= 0) {
        ikon = "<span class=\"ico-yt\"><i class=\"fab fa-youtube\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("YouTube") >= 0) {
        ikon = "<span class=\"ico-yt\"><i class=\"fab fa-youtube\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Twitter") >= 0) {
        ikon = "<span class=\"ico-tw\"><i class=\"fab fa-twitter\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Google Map") >= 0) {
        ikon = "<span class=\"ico-google\"><i class=\"fab fa-google\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Soundcloud") >= 0) {
        ikon = "<span class=\"ico-sc\"><i class=\"fab fa-soundcloud\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Spotify") >= 0) {
        ikon = "<span class=\"ico-sp\"><i class=\"fab fa-spotify\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Apple") >= 0) {
        ikon = "<span class=\"ico-apple\"><i class=\"fab fa-apple\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Telegram") >= 0) {
        ikon = "<span class=\"ico-tele\"><i class=\"fab fa-telegram-plane\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Pinterest") >= 0) {
        ikon = "<span class=\"ico-pt\"><i class=\"fab fa-pinterest-p\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Twitch") >= 0) {
        ikon = "<span class=\"ico-twc\"><i class=\"fab fa-twitch\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Tik") >= 0) {
        ikon = "<span class=\"ico-tic\"><i class=\"fab fa-tiktok\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Web") >= 0) {
        ikon = "<span class=\"ico-web\"><i class=\"fas fa-globe\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Diğer") >= 0) {
        ikon = "<span class=\"ico-dgr\"><i class=\"fas fa-stream\" aria-hidden=\"true\"></i> </span>";
          } else if (opt.indexOf("Behance") >= 0) {
        ikon = "<span class=\"fs-behance\"><i class=\"fab fa-behance\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Müzik") >= 0) {
        ikon = "<span class=\"fs-music\"><i class=\"fa fa-music\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Periscope") >= 0) {
        ikon = "<span class=\"fs-peri\"><i class=\"fab fa-periscope\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Discord") >= 0) {
        ikon = "<span class=\"fs-discord\"><i class=\"fab fa-discord\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Kwai") >= 0) {
        ikon = "<span class=\"fs-kwai\"><i class=\"fab fa-kickstarter-k\" aria-hidden=\"true\"></i> </span>";      
    } else if (opt.indexOf("Snapchat") >= 0) {
        ikon = "<span class=\"fs-snap\"><i class=\"fab fa-snapchat-ghost\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("TripAdvisor") >= 0) {
        ikon = "<span class=\"fs-TripAdvisor\"><i class=\"fas fa-plane\" aria-hidden=\"true\"></i> </span>";
      } else if (opt.indexOf("") >= 0) {
        ikon = "<span class=\"fs-\"><i class=\"fas fa-arrow-right\" aria-hidden=\"true\"></i> </span>";
  }
    return ikon;
}



function setList(val) {

    if (val == 0) {
        $("#orders-drop").empty();
        $("#orderform-service option").each(function() {
            if ($(this).attr('data-show') != 'hidden') {
                var ico = ikon($(this).text());
                $("#orders-drop").append('<button id="order-sItem" class="dropdown-item" type="button" onclick="selectOrder(' + $(this).val() + ')">' + ico + $(this).text() + '</button>');
            }
        });
        var e = document.getElementById("orderform-service");
        var selected = e.options[e.selectedIndex].text;
        var ico = ikon(selected);
        $("#order-services").html(ico + selected);


    } else if (val == 1) {

        $("#category-drop").empty();
        $("#orderform-category option").each(function() {
            if ($(this).attr('data-show') != 'hidden') {
                var ico = ikon($(this).text());
                $("#category-drop").append('<button id="order-cItem" class="dropdown-item" type="button" onclick="selectCategory(' + $(this).val() + ')">' + ico + $(this).text() + '</button>');
            }
        });

        var e = document.getElementById("orderform-category");
        var selected = e.options[e.selectedIndex].text;
        var ico = ikon(selected);
        $("#order-category").html(ico + selected);

    }
}

$(function(ready) {
    $("#orderform-service").change(function() {
        setList(0);
    });
    $("#orderform-category").change(function() {
        setList(1);
    });
});


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

$(document).ready(function() {
    $("#serv-inp").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".app-mtable tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});

$('.sss-tab').click(function() {
    if ($(this).hasClass('active')) {
        $(this).find('.ss-tab-content').slideToggle(200);
        $(this).toggleClass('active');
    } else {
        $('.sss-tab').removeClass('active');
        $('.sss-tab > .ss-tab-content').slideUp(200);
        $(this).find('.ss-tab-content').slideToggle(200);
        $(this).toggleClass('active');
    }
});

function change_mode() {

    var app = document.getElementsByTagName("BODY")[0];

    if (localStorage.lightMode == "dark") {
        localStorage.lightMode = "light";
        app.setAttribute("class", "light");
    } else {
        localStorage.lightMode = "dark";
        app.setAttribute("class", "dark");
    }
    console.log("lightMode = " + localStorage.lightMode);
}

function searching(name) {
                        let categoryGeted = [];
                        for (const element of categories) {
                        var elementitem = element.name.toLowerCase();
                        var elementId = element.id;
                        var resultBool = elementitem.includes(name);
                        if (resultBool == true) {
                            categoryGeted.push(elementId);
                        }
                        }
                        let result = [];
                        for (var i = 0; i < categories.length; i++) {
                        for (const elements of categoryGeted) {
                            if (elements == categories[i].id) {
                            result.push(categories[i]);
                            }
                        }
                        }
                        if (result.length == 0) {
                        result = categories;
                        }
                        let optionsResult = "";
                        for (const item of result) {
                        optionsResult =
                            optionsResult + `<option value="${item.id}">${item.name}</option>`;
                        }
                        return optionsResult;
                    }

                    function start(names) {
                        var result = searching(names);
                        let placeOfSelect = document.getElementById("orderform-category");
                        placeOfSelect.innerHTML = result;
                        setList();
                      $('#orderform-category').trigger('change');
                    }