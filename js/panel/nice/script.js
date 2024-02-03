$(document).ready(function() {
    $("#searchService").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".service-item").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
          $.post("ajax_data",
      {action:"favorite_kontrol"},
      function(e){
        if(e['field']!= 0){
      e['field'].forEach(favori_isaretle);
}
  });
});
function favori_isaretle(item, index) {
  console.log(item['services_id']);
  var id = item['services_id'];
  $('#favori_ekle_smmspot[data-id="'+id+'"]').children("i").toggleClass('far fas');
}

$(document).on('click','#favori_ekle_smmspot',function(){
      $.post("ajax_data",
      {action:"favorite_ekle",favori:$(this).data('id')},
      function(e){
          if(e['message'] == "Failed"){
              console.log('mert');
           $('#favori_ekle_smmspot[data-id="'+e['id']+'"]').children("i").toggleClass('fas far');  
           }else{
           $('#favori_ekle_smmspot[data-id="'+e['id']+'"]').children("i").toggleClass('far fas');  
             
          }
      console.log(e);
  });
});
function sortArray(array) {
    clearTimeout(delay);
    var delay = setTimeout(function(){
        var firstElem = array.shift();
        array.push(firstElem);
        return startAnim(array); 
    },5000)
}

const copyToClipboard = str => {
  const el = document.createElement('textarea');
  el.value = str;
  el.setAttribute('readonly', '');
  el.style.position = 'absolute';
  el.style.left = '-9999px';
  document.body.appendChild(el);
  el.select();
  document.execCommand('copy');
  document.body.removeChild(el);
  makeToast('Panoya kopyalandı')
};

var toastTime;

function makeToast(text = null, timeOut=4000) {
  $('.toast-text').html(text)
  $('.bs-toast').fadeIn(300);

  toastTime = setTimeout(() => {
    $('.bs-toast').fadeOut(300);
  }, timeOut);
}

function removeToast() {
  $('.bs-toast').fadeOut(300);
  clearTimeout(toastTime);
}

$('.orderToggle').click(function() {
  $(this).parents('.card-order').find('.co-hidden').slideToggle(200);
})

var docScrollTop = $(document).scrollTop()

$(document).scroll(function() {
  docScrollTop = $(document).scrollTop()
  headerScroll(docScrollTop)
})

function headerScroll(docScrollTop) {
  if(docScrollTop > 20) {
    $('#smmspot-na-header').addClass('sticky')
  } else {
    $('#smmspot-na-header').removeClass('sticky')
  }
}

$('.noAuthMenuBtn').click(function() {
  $('.col-menu').slideToggle(200);
})

function rightMenuToggle() {
  $('.app-rightbar').toggleClass('active')
}

function sidebarToggle() {
  $('.app-sidebar').toggleClass('active');
}
/*
var appHeader = document.getElementsByClassName('app-header');

const appHeaderScroll = (x) => {
  if(x > 10) {
    if( !appHeader.classList.contains('active') ) {
      appHeader.classList.add('active');
    }
  }  else {
    if( appHeader.classList.contains('active') ) {
      appHeader.classList.remove('active');
    }
  }
}

if(appHeader.length) {
  appHeader = appHeader[0];

  window.addEventListener('scroll', () => {
    appHeaderScroll(window.pageYOffset);
  })
}*/

$('.home-ss-tab').click(function() {
    if ($(this).hasClass('active')) {
        $(this).find('.ss-tab-content').slideToggle(200);
        $(this).toggleClass('active');
    } else {
        $('.home-ss-tab').removeClass('active');
        $('.home-ss-tab > .ss-tab-content').slideUp(200);
        $(this).find('.ss-tab-content').slideToggle(200);
        $(this).toggleClass('active');
    }
});

$("#orderform-service").change(function () {
    service_id = $(this).val();
    $("#s_id").text(service_id);

    description = window.modules.siteOrder.services[service_id].description
    $("#s_desc").html(description);

    name = window.modules.siteOrder.services[service_id].name
    $("#s_name").html(name);
})




/*$(document).ready(function() {
    $("#serv-inp").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".pnd-mtable tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $("#serv-cat").on("keyup", function(e) {
        var value = $(e.relatedTarget).data('data-filter-category-id');
        $(".pnd-mtable tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    $('select').select2();
    
});


function dashMenuToggle() {
    $('.app-sidebar').toggleClass('sidebar-inact');
    $('.app-header').toggleClass('sidebar-inact');
    $('.app-content').toggleClass('sidebar-inact');
    $('body').toggleClass('body-pause');
}

$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});

$('.services-list-filter').click(function(){

	var dataFilter=$(this).data("services-filter");
    console.log(dataFilter);
    var value = dataFilter;
    
    $(".pnd-mtable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });

});*/