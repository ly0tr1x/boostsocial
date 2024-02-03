

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