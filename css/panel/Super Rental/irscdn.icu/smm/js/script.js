$(document).ready(function () {
    var letCollapseWidth = false,
        paddingValue = 30,
        sumWidth = $('.navbar-right-block').width() + $('.navbar-left-block').width() + $('.navbar-brand').width() + paddingValue;

    $(window).on('resize', function () {
        navbarResizerFunc();
    });

    var navbarResizerFunc = function navbarResizerFunc() {
        if (sumWidth <= $(window).width()) {
            if (letCollapseWidth && letCollapseWidth <= $(window).width()) {
                $('#navbar').addClass('navbar-collapse');
                $('#navbar').removeClass('navbar-collapsed');
                $('nav').removeClass('navbar-collapsed-before');
                letCollapseWidth = false;
            }
        } else {
            $('#navbar').removeClass('navbar-collapse');
            $('#navbar').addClass('navbar-collapsed');
            $('nav').addClass('navbar-collapsed-before');
            letCollapseWidth = $(window).width();
        }
    };

    if ($(window).width() >= 768) {
        navbarResizerFunc();
    }
});

$(document).ready(function() { 

  $('#orderform-service option').each(function() {
    var text = $(this).text();
    $(this).text(text.replace('— $1.60', ''));
  });
  $('#orderform-service option').each(function() {
    var text = $(this).text();
    $(this).text(text.replace('— $8.75', '')); 
  });
  $('#orderform-service option').each(function() {
    var text = $(this).text();
    $(this).text(text.replace('— $5.75', '')); 
  });
  $('#orderform-service option').each(function() {
    var text = $(this).text();
    $(this).text(text.replace('— $5.75', '')); 
  });
   $('#orderform-service option').each(function() {
    var text = $(this).text();
    $(this).text(text.replace('— $0.30', '')); 
  }); 
  

  
  <!-- Add More Here Above This Comment^^^ -->
  
});