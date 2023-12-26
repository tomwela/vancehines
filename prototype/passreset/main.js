$(function(){

//****************************************

$(".pass2, .pass1").keyup(function(){
	p1=$(".pass1").val();
	p2=$(".pass2").val();
     l=p1.length;
    if(l < 6 && p2!='') {
	val="Password lengh must be minimum 6";
}

	else if(p2===p1 && l > 5 && p2!='') val="Passwords match";
	else if(p2!='') val="Passwords do not match";
	$(".feed").html(val);
});


//***************SEND PASSWORDS*************************
	$('.reset').click(function(){
	
		var pass1= $('.pass1').val();
		var pass2 = $('.pass2').val();
	
		if(pass1===pass2 && pass2!='' && pass1!=''){

   var urlh="resetpass.php";
   var code=$(this).attr('rel');

$.post(urlh, {pass:pass2, code:code}, function(data){
	
        $(".feed").html(data);
    });
   // $(".feed").html("");
		}
if(pass1=='' || pass2=='') {
	$(".feed").html("Fields can not be empty");
}
$('.pass1, .pass2').val("");
		})


//****************************************
})