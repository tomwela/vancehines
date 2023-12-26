<?php
	session_start();
	if(isset($_SESSION['username'])) {

	}
	else {
    	header('Location: index.php');
}
 
 
require_once('php/connectdb.php');
?>

<html>
<head>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<style>
    body {
        background-color: #FAF0E6;
        color:#3366CC;
        font-size: 20px;
        font-family: Georgia, serif;
    }
    #logo{
        margin-bottom:20px;
    }

    #logout{
    	margin-right: 20px;
    	color:#3366CC;
    	font-size:20px;
    }

    td {
		background-color: #D8832D;
		}
    td:hover {
	color: #ff0000;

	}
	a:active {
    background-color: yellow;
     color: #ff00ff; 
	} 
</style>

</head>
<body> 


<script>


// Set the number of snowflakes (more than 30 - 40 not recommended)
var snowmax=40

// Set the colors for the snow. Add as many colors as you like
// var snowcolor=new Array("#aaaacc","#ddddFF","#ccccDD")
var snowcolor=new Array("#FFFFFF","#F41313","#3A9F00")
// Set the fonts, that create the snowflakes. Add as many fonts as you like
var snowtype=new Array("Arial Black","Arial Narrow","Times","Comic Sans MS")

// Set the letter that creates your snowflake (recommended:*)
var snowletter="*"
//var snowletter="M-XMAS"

// Set the speed of sinking (recommended values range from 0.3 to 2)
var sinkspeed=0.6

// Set the maximal-size of your snowflaxes
var snowmaxsize=22

// Set the minimal-size of your snowflaxes
var snowminsize=8

// Set the snowing-zone
// Set 1 for all-over-snowing, set 2 for left-side-snowing 
// Set 3 for center-snowing, set 4 for right-side-snowing
var snowingzone=1

///////////////////////////////////////////////////////////////////////////
// CONFIGURATION ENDS HERE
///////////////////////////////////////////////////////////////////////////


// Do not edit below this line
var snow=new Array()
var marginbottom
var marginright
var timer
var i_snow=0
var x_mv=new Array();
var crds=new Array();
var lftrght=new Array();
var browserinfos=navigator.userAgent 
var ie5=document.all&&document.getElementById&&!browserinfos.match(/Opera/)
var ns6=document.getElementById&&!document.all
var opera=browserinfos.match(/Opera/)  
var browserok=ie5||ns6||opera

function randommaker(range) {		
	rand=Math.floor(range*Math.random())
    return rand
}

function initsnow() {
	if (ie5 || opera) {
		marginbottom = document.body.clientHeight
		marginright = document.body.clientWidth
	}
	else if (ns6) {
		marginbottom = window.innerHeight
		marginright = window.innerWidth
	}
	var snowsizerange=snowmaxsize-snowminsize
	for (i=0;i<=snowmax;i++) {
		crds[i] = 0;                      
    	lftrght[i] = Math.random()*15;         
    	x_mv[i] = 0.03 + Math.random()/10;
		snow[i]=document.getElementById("s"+i)
		snow[i].style.fontFamily=snowtype[randommaker(snowtype.length)]
		snow[i].size=randommaker(snowsizerange)+snowminsize
		snow[i].style.fontSize=snow[i].size
		snow[i].style.color=snowcolor[randommaker(snowcolor.length)]
		snow[i].sink=sinkspeed*snow[i].size/5
		if (snowingzone==1) {snow[i].posx=randommaker(marginright-snow[i].size)}
		if (snowingzone==2) {snow[i].posx=randommaker(marginright/2-snow[i].size)}
		if (snowingzone==3) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/4}
		if (snowingzone==4) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/2}
		snow[i].posy=randommaker(6*marginbottom-marginbottom-6*snow[i].size)
		snow[i].style.left=snow[i].posx
		snow[i].style.top=snow[i].posy
	}
	movesnow()
}

function movesnow() {
	for (i=0;i<=snowmax;i++) {
		crds[i] += x_mv[i];
		snow[i].posy+=snow[i].sink
		snow[i].style.left=snow[i].posx+lftrght[i]*Math.sin(crds[i]);
		snow[i].style.top=snow[i].posy
		
		if (snow[i].posy>=marginbottom-6*snow[i].size || parseInt(snow[i].style.left)>(marginright-3*lftrght[i])){
			if (snowingzone==1) {snow[i].posx=randommaker(marginright-snow[i].size)}
			if (snowingzone==2) {snow[i].posx=randommaker(marginright/2-snow[i].size)}
			if (snowingzone==3) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/4}
			if (snowingzone==4) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/2}
			snow[i].posy=0
		}
	}
	var timer=setTimeout("movesnow()",50)
}

for (i=0;i<=snowmax;i++) {
	document.write("<span id='s"+i+"' style='position:absolute;top:-"+snowmaxsize+"'>"+snowletter+"</span>")
}
if (browserok) {
	window.onload=initsnow
}
</script>  







<form method="get">
<div class ="container">
<h3 align="center">Vance and Hines Top Stories database</h3>
<img id="logo" src="images/image-logo.png"/> 
<div style='float: right;'><a href="story.php" id="logout"><b>New Story</b></a><a href="logout.php" id="logout"><b>Logout</b></a></div> 


<table class= "table table-hover">
<?php
	

	$result = mysqli_query($connection, "SELECT * FROM topstories order by id DESC");
    echo "<thead> <tr> <th>ID</th> <th>Image</th> <th>Headlines</th> <th>Date</th> <th>Web(url)</th> <th></th> <th></th> </tr> </thead>";		
	     //----display data in table------
	    //----loop through results of database query and display them in the table-----
	while($row = mysqli_fetch_array( $result, MYSQLI_BOTH )) {
		
			//-----echo out the contents of each row into a table------
	echo "<tbody>";
	echo "<tr>";
	echo '<td>' . $row['id'] . '</td>';
	if($row['imgurl']== "")
		{
			echo '<td><img class="img-rounded" src="http://www.dev.vhfp3.com/prototype/stories/Noimage.png" height="200" width="300"></td>';
		}
	else
		{
			echo '<td><img class="img-rounded" src="' . $row['imgurl'] . '" alt="" height="200" width="300"></td>';	
		}


	
	echo '<td>' . $row['headlines'] . '</td>';
	echo '<td>' . $row['date']. '</td>';
	echo '<td>' . $row['description']. '</td>';
	echo '<td><a href="edit.php?id=' . $row['id'] . '"> Edit </a>  </td>';
   	echo '<td><a href="delete.php?id=' . $row['id'] . '"> Delete </a> </td>';
   	echo "</tr>"; 
   	echo "</tbody>";
	} 
			//-------close table-------
	echo "</table>";

	
        
?>
</div> 
</form>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>	