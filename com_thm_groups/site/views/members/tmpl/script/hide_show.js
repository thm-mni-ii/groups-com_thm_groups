$(document).ready(function(){

	$(".button").hover(function(){
		$(this).addClass("hover");
	}, function(){
		$(this).removeClass("hover");
	});	



	$('#Group_Select').children().hide();
	$("#button_single_user h3").click(function(){
		$("#button_single_user").toggleClass("selected");
		$('#Group_Select').children().slideToggle();


		// If many users of group selected, single_user = true
		var single_user = $('#single_user').val();

		if( single_user == 'false')
			$('#single_user').val('true');
		if( single_user == 'true')
			$('#single_user').val('false');

	});



});