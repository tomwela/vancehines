$(document).ready(function () {

	/*Custom Radio*/
	$('.custom-radio, .radio-list label').bind('click', function(){
		$(this).parents('ul').find('.custom-radio').removeClass('checked');
		$(this).parents('li').find('.custom-radio').addClass('checked');
	});

    /*Custom Checkbox*/
	var numCheckArray = $('.numeric.custom-checkbox');
	var count;
	$('.custom-checkbox:not(.custom-checkbox.select-all)').bind('click', function(){


		if($(this).attr('class').search('checked') != -1){
			$(this).removeClass('checked');
			count--;
		}
		else{
			$(this).addClass('checked');
		}

		if($('.custom-checkbox.numeric.checked').length == numCheckArray.length){
			 $('.custom-checkbox.select_all').addClass('checked')
		}
		else{
			$('.custom-checkbox.select-all').removeClass('checked')
		}

	});

	$('.custom-checkbox.select-all').bind('click', function(){

		if($(this).hasClass('checked')){
			numCheckArray.each(function(){$(this).removeClass('checked')})
			$(this).removeClass('checked')
		}
		else{
			numCheckArray.each(function(){$(this).addClass('checked')})
			$(this).addClass('checked')
		}

	});

    /*Custom SelectBox*/
    var openSelectList = false;

    $(".custom-select .arrow, .custom-select .field").bind('click', function(e){
        e.stopPropagation();
        $(this).parent().find('ul').css({'width':$(this).parent().find('.field').outerWidth()-2, 'display':'block'});
        openSelectList = true;
        $(this).parent().find('ul li').bind('click', function(){
            $(this).parent().css({'display':'none'});
			 $(this).parents('.custom-select').find('.field').text($(this).text())
        })
    })

	$('body').bind('click', function(){
		if(openSelectList==true){
			$('.custom-select ul').css({'display':'none'})
		}
	})

    /*Code for crossbrowser "Placeholder"*/
    $('input[type="text"], input[type="email"], input[type="password"], textarea').focusout(function () {
        if ($(this).val() == $(this).attr("title") || $(this).val() == "") {
            $(this).val($(this).attr("title"));
        }
    });

    $('input[type="text"], input[type="email"], input[type="password"], textarea').focus(function () {
        if ($(this).val() == $(this).attr("title")) {
            $(this).val("");
        }
    });

}); //End of ready