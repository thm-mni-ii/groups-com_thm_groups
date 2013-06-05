$(function(){

	$("select#groups").change(function(){
		var uid=document.getElementById("groups").value;
		$.ajax({

			type: 'POST',

			url:'index.php?option=com_thm_groups&format=raw&task=members.getUsersOfGroup',

			data: 'uid='+uid,

			success: function(data){

				$('#selectFrom').html(data);

			}

		})

		

	})
	
	$('#add').click(function() {
		var bool = "false";
		var transferValue = "";
		var transferName = "";
		$('#selectTo option').each(function(){
			if($(this).val() == $('#selectFrom option:selected').val()){
				bool = "true";
				return false;
			}else{
				bool = "false";
			}

		});
		if(bool == "true"){}
		else{
			transferValue = $('#selectFrom option:selected').val();
			transferName = $('#selectFrom option:selected').text();
			$('#selectTo').append($('<option>',{value:transferValue}).text(transferName));
			bool = "false";
		}
//		return !$('#selectFrom option:selected').appendTo('#selectTo');
	});
	$('#remove').click(function() {
		return !$('#selectTo option:selected').remove();
	});




});