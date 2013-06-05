$(function() {
    $('span.jQtooltip').each(function() {
    	  var el = $(this);
    	  var title = el.attr('title');
    	  if (title && title != '') {
    	    el.attr('title', '').append('<div>' + title + '</div>');
    	    var width = el.find('div').width();
    	    var height = el.find('div').height();
    	    el.hover(
    	      function() {
    	        el.find('div')
    	          .clearQueue()
    	          .delay(200)
    	          .animate({width: width + 20, height: height + 20}, 200).show(200)
    	          .animate({width: width, height: height}, 200);
    	      },
    	      function() {
    	        el.find('div')
    	          .animate({width: width + 20, height: height + 20}, 150)
    	          .animate({width: 'hide', height: 'hide'}, 150);
    	      }
    	    ).mouseleave(function() {
    	      if (el.children().is(':hidden')) el.find('div').clearQueue();
    	    });
    	  }
    	})
  });