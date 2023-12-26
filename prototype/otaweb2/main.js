 $(function(){
	$('.dbTable tr').hover(function(){
		id=$(this).attr('hoverdID');
		$('#'+id+'_id').toggle();
		$('#'+id+'_id2').toggle();
	})




 })



