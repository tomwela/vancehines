<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FP3 Installs</title>

    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
	
<div class="container">
<div class="row">
  
		<h3 id="Title"><span id="totalInstalls"></span> Total FP3 Installs</h3>
    
    <div class="well">     
              <div id="reportButtons">
            		<ul>
              		  <li><button type="button" class="btn btn-default btn-primary active" id="1" onclick="event.preventDefault(event);changeData(this.id)" >By Week</button></li>
              		  <li><button type="button" class="btn btn-default" id="2" onclick="event.preventDefault(event);changeData(this.id)" >By Month</button></li>
              		  <li><button type="button" class="btn btn-default" id="3" onclick="event.preventDefault(event);changeData(this.id)" >Last 30 Days</button></li>
              		  <li><button type="button" class="btn btn-default" id="4" onclick="event.preventDefault(event);changeData(this.id)" >Last 60 Days</button></li>
              		  <li><button type="button" class="btn btn-default" id="5" onclick="event.preventDefault(event);changeData(this.id)" >Last 90 Days</button></li>
            		</ul>
<!--             		<img id="fp3logo" class="pull-right" src="fuelpakfp3.png" > -->
              </div>
    </div><!--  well -->

      		<div id="charts"></div>
      		
</div><!-- row -->
  <div class="row">
    <div class="col-md-2">  
    
      <div id="dateRange">
          <!--  jquery datepicker -->
          <label for="from">From</label>
          <input type="text" id="from" name="from" class="form-control" value="03/01/2014">
          
          <label for="to">to</label>
          <input type="text" id="to" name="to" class="form-control" value="<?php echo date("m/d/Y"); ?>">
          
          <br />
          <button type="button" class="btn btn-default" id="resetDatePicker">Reset Dates</button>
      </div>
    
    </div><!-- col-md-2 -->
  </div><!-- row -->
</div><!-- container -->

<footer class="footer">
  <div class="container">
    <p id="footerText" class="text-muted">&copy; 2013 - <?php echo date("Y"); ?>. Vance &amp; Hines. All right reserved.</p>
    <img id="logo" class="pull-right" src="vhlogo-filloutline.png" >
  </div>
</footer>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
<script src="http://labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
<script src="script.js"></script>
<script>
  
$("#reportButtons button").click(function() {

	$( "#reportButtons button" ).removeClass("btn-primary active");
	$( this ).addClass("btn-primary active");
	
	
	// don't allow the date picker for days
/*
	if (this.id == 3 || this.id == 4 || this.id == 5) {
		$("#dateRange").css("display","none")
	} else {
		$("#dateRange").css("display","inherit");
	}
*/

});


$("#resetDatePicker").click(function() {

    var format = d3.time.format("%m/%d/%Y");
    var today  = format( new Date() );
    
    $('#from').datepicker("setDate", '03/01/2014');
    $('#to').datepicker("setDate", today);
    
});


function changeData(id) {
  
    var format = d3.time.format("%m/%d/%Y");

    var today  = format( d3.time.day.offset(new Date(), +1) );

  	var dateObj1 = $("#from").datepicker("getDate");
  	var fromDate = dateObj1.getMonth()+1 +"/"+ dateObj1.getDate() +"/"+ dateObj1.getFullYear();

  	
  	var dateObj2 = $("#to").datepicker("getDate");
  	var do2      = dateObj2.getMonth()+1 +"/"+ dateObj2.getDate() +"/"+ dateObj2.getFullYear();
  	var toDate   = format( d3.time.day.offset(new Date(do2), +1));
  	
  	
  	
  	console.log("do2: "+ do2);
  	console.log("From: "+ fromDate +" To: "+ toDate);

    switch (id) {
      case "1":
          
          updateData(id, fromDate, toDate);
          break;
          
      case "2":
    
          updateData(id, fromDate, toDate);
          break;
          
      case "3":
          var last30 = format( d3.time.day.offset(new Date(), -30) );
              
          updateData("3", last30, today);
          break;
          
      case "4":
          var last60 = format( d3.time.day.offset(new Date(), -60) );
          
          $('#report_label').text('Last 60 Days');
    
          updateData("3", last60, today);
          break;
          
      case "5":
          var last90 = format( d3.time.day.offset(new Date(), -90) );
              
          updateData("3", last90, today);
          break;
 
      default:
          console.log('default');
    }
}
  
  
//jquery date range picker
$(function() {
  $( "#from" ).datepicker({
    defaultDate: "+1w",
    changeMonth: true,
    numberOfMonths: 1,
    onClose: function( selectedDate ) {
      $( "#to" ).datepicker( "option", "minDate", selectedDate );
    }
  });
  $( "#to" ).datepicker({
    defaultDate: "+1w",
    changeMonth: true,
    numberOfMonths: 1,
    onClose: function( selectedDate ) {
      $( "#from" ).datepicker( "option", "maxDate", selectedDate );
    }
  });
});

</script>
</body>
</html>
