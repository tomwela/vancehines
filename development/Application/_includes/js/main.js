setTimeout(function(){

$(document).ready(function () {

	/*Custom Radio*/
	$('.custom-radio, .radio-list label').on('click', function(){
		$(this).parents('ul').find('.custom-radio').removeClass('checked');
		$(this).parents('li').find('.custom-radio').addClass('checked');
	});

    /*Custom Checkbox*/
	var count;
	$('.custom-checkbox:not(.custom-checkbox.select-all)').on('click', function(){


		if($(this).attr('class').search('checked') != -1){
			$(this).removeClass('checked');
			count--;
		}
		else{
			$(this).addClass('checked');
		}
		var count = $('.custom-checkbox.numeric.checked').length;
		if(count){
			$('.selected-counter').text(count + ' selected');
		}else{
			$('.selected-counter').text('');
		}
		if($('.custom-checkbox.numeric.checked').length == $('.numeric.custom-checkbox').length){
			 $('.custom-checkbox.select-all').addClass('checked')
		}
		else{
			$('.custom-checkbox.select-all').removeClass('checked')
		}


	});

	$('.custom-checkbox.select-all').on('click', function(){
		console.log('all');
		if($(this).hasClass('checked')){
			$('.numeric.custom-checkbox').each(function(){$(this).removeClass('checked')})
			$(this).removeClass('checked')
		}
		else{
			$('.numeric.custom-checkbox').each(function(){$(this).addClass('checked')})
			$(this).addClass('checked')
		}
		var count = $('.custom-checkbox.numeric.checked').length;
		if(count){
			$('.selected-counter').text(count + ' selected');
		}else{
			$('.selected-counter').text('');
		}		

	});

    /*Custom SelectBox*/
    var openSelectList = false;

    $(".custom-select .arrow, .custom-select .field").on('click', function(e){
        e.stopPropagation();
        $(this).parent().find('ul').css({'width':$(this).parent().find('.field').outerWidth()-2, 'display':'block'});
        openSelectList = true;
        $(this).parent().find('ul li').on('click', function(){
            $(this).parent().css({'display':'none'});
			 $(this).parents('.custom-select').find('.field').text($(this).text())
        })
    })

	$('body').on('click', function(){
		if(openSelectList==true){
			$('.custom-select ul').css({'display':'none'})
		}
	})

    /*Code for crossbrowser "Placeholder"*/
    $('input[type="text"], input[type="email"], input[type="password"], textarea').on('focusout', function () {
        if ($(this).val() == $(this).attr("title") || $(this).val() == "") {
            $(this).val($(this).attr("title"));
        }
    });

    $('input[type="text"], input[type="email"], input[type="password"], textarea').on('focus', function () {
        if ($(this).val() == $(this).attr("title")) {
            $(this).val("");
        }
    });

}); //End of ready
}, 1000);
