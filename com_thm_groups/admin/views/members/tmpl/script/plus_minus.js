$(function() {
	$('td').each(function() {
		
		var asd = $(this);
		
		asd.find('a.minus').click(function() {
			var data = asd.find('input').val();
			if(data > 0) {
				asd.find('input').val(parseInt(data) - 1);
			}
			return false
		});
		
		asd.find('a.plus').click(function() {
			var data = asd.find('input').val();
			asd.find('input').val(parseInt(data) + 1);
			return false
		});
		
	});
});

