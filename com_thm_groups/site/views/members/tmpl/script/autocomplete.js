var suggest_count = 0;
var input_initial_value = '';
var suggest_selected = 0;

$(window).load(function(){
	$('#search_box').click(function(){
		$('#search_box').val("");
	});
	// читаем ввод с клавиатуры
	$("#search_box").keyup(function(I){
		// определяем какие действия нужно делать при нажатии на клавиатуру
		switch(I.keyCode) {
		// игнорируем нажатия на эти клавишы
		case 13:  // enter
		case 27:  // escape
		case 38:  // стрелка вверх
		case 40:  // стрелка вниз
			break;

		default:
			// производим поиск только при вводе более 2х символов
			if($(this).val().length>2){

				input_initial_value = $(this).val();
			
				$.ajax({
					type : 'GET',

					url:'index.php?option=com_thm_groups&format=raw&task=members.getLol',

					data: 'query='+$(this).val(),
					
					success: function(data){
						$("#search_advice_wrapper").html("").show();
						$('#search_advice_wrapper').append(data);
						// Fehler?
						suggest_count = $(".advice_variant").size();
						console.log(suggest_count);
					}
				});
			}
		break;
		}
	});

	//считываем нажатие клавишь, уже после вывода подсказки
	$("#search_box").keydown(function(I){
		
		switch(I.keyCode) {
		// по нажатию клавишь прячем подсказку
		case 13: // enter
		case 27: // escape
			$('#search_advice_wrapper').hide();
			return false;
			break;
			// делаем переход по подсказке стрелочками клавиатуры
		case 38: // стрелка вверх
		case 40: // стрелка вниз
			console.log("catwooman");
			I.preventDefault();
			if(suggest_count){
				//делаем выделение пунктов в слое, переход по стрелочкам
				key_activate( I.keyCode-39 );
			}
			break;
		}
	});

	// делаем обработку клика по подсказке
	$(document).on("click",".advice_variant",function(){
		// ставим текст в input поиска
		$('#search_box').val($(this).text());
		$('#userID').val($(this).attr('title'));
		// прячем слой подсказки
		$('#search_advice_wrapper').fadeOut(350).html('');
	});

	// если кликаем в любом месте сайта, нужно спрятать подсказку
	$('html').click(function(){
		$('#search_advice_wrapper').hide();
	});
	// если кликаем на поле input и есть пункты подсказки, то показываем скрытый слой
	$('#search_box').click(function(event){
		//alert(suggest_count);
		if(suggest_count)
			$('#search_advice_wrapper').show();
		event.stopPropagation();
	});
});

function key_activate(n){
	$('#search_advice_wrapper div').eq(suggest_selected-1).removeClass('active');

	if(n == 1 && suggest_selected < suggest_count){
		suggest_selected++;
//		$('#userID').val($('#search_advice_wrapper div').attr('title'));
	}else if(n == -1 && suggest_selected > 0){
		suggest_selected--;
	}

	if( suggest_selected > 0){
		$('#search_advice_wrapper div').eq(suggest_selected-1).addClass('active');
		$("#search_box").val( $('#search_advice_wrapper div').eq(suggest_selected-1).text() );
//		$('#userID').val($('#search_advice_wrapper div').attr('title'));
	} else {
		$("#search_box").val( input_initial_value );
	}
}