(function($) {
$(function() {

  $('ul.tabs').each(function() {
    $(this).find('li').each(function(i) {
      $(this).click(function(){
        $(this).addClass('current').siblings().removeClass('current')
          .parents('div.section').find('div.box_tab').eq(i).fadeIn(150).siblings('div.box_tab').hide();
      });
    });
  });

})
})(jQuery)