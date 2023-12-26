<?php
timezone_open("America/Los_Angeles");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="FP3 Installations">
    <meta name="author" content="Vance & Hines">
    <!--     <link rel="icon" href="../../favicon.ico"> -->

    <title>FP3 Installs</title>

    <link rel="stylesheet"
          href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css"/>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="installs.min.css">

</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-xs-6">
            <h3 id="Title"><span id="totalInstalls"></span> Total FP3 Installs</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div id="reportButtons">
                <ul class="list-inline">
                    <li>
                        <button type="button" class="btn btn-default btn-primary active" id="1"
                                onclick="event.preventDefault(event);vhEng.changeData(this.id)">By Week
                        </button>
                    </li>
                    <li>
                        <button type="button" class="btn btn-default" id="2"
                                onclick="event.preventDefault(event);vhEng.changeData(this.id)">By Month
                        </button>
                    </li>
                    <li>
                        <div class="btn-group" role="group" aria-label="...">
                            <button type="button" class="btn btn-default" id="3" onclick="event.preventDefault(event);vhEng.changeData(this.id)">
                                Last 30 Days
                            </button>
                            <button type="button" class="btn btn-default" id="4" onclick="event.preventDefault(event);vhEng.changeData(this.id)">
                                Last 60 Days
                            </button>
                            <button type="button" class="btn btn-default" id="5" onclick="event.preventDefault(event);vhEng.changeData(this.id)">
                                Last 90 Days
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-xs-6">
            

            <!--<div id="mode" class="dropdown">-->
            <!--    <button class="btn btn-default dropdown-toggle" type="button" id="modeButton" data-toggle="dropdown"-->
            <!--            aria-haspopup="true" aria-expanded="true">All <span class="caret"></span>-->
            <!--    </button>-->
            <!--    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">-->
            <!--        <li><a href="#">All</a></li>-->
            <!--        <li><a href="#">66007</a></li>-->
            <!--        <li><a href="#">66005</a></li>-->
            <!--    </ul>-->
            <!--</div>-->


        <div id="modeControl" class="btn-group" role="group" aria-label="...">
            <button type="button" class="btn btn-default btn-primary active" id="all" onclick="event.preventDefault(event);">
                All
            </button>
            <button type="button" class="btn btn-default" id="66007" onclick="event.preventDefault(event);">
                66007
            </button>
            <button type="button" class="btn btn-default" id="66005" onclick="event.preventDefault(event);">
                66005
            </button>
        </div>


        </div>
    </div>
    <hr>
</div>

<div class="container">

    <div id="chartArea" class="row">
        <table class="table table-striped table-condensed" width="200">
            <tr>
                <th class="statsTable">Min</th>
                <th class="statsTable">Max</th>
                <th class="statsTable">Mean</th>
                <th class="statsTable">Median</th>
            </tr>
            <tr>
                <td><span id="min"></span></td>
                <td><span id="max"></span></td>
                <td><span id="mean"></span></td>
                <td><span id="median"></span></td>
            </tr>
        </table>

        <div id="charts"></div>
    </div>


    <div class="row">
        <div id="dateRange">
            <!--  jquery datepicker -->
            <label for="from">From</label>
            <input type="text" id="from" name="from" class="form-control" value="03/01/2014">

            <label for="to">to</label>
            <input type="text" id="to" name="to" class="form-control" value="<?php echo date("m/d/Y"); ?>">

            <br/>
            <button type="button" class="btn btn-default" id="resetDatePicker">Reset Dates</button>
        </div>
    </div>


</div><!-- -->

<footer class="footer">
    <div class="container">
        <p id="footerText" class="text-muted">&copy; 2013 - <?php echo date("Y"); ?>. Vance &amp; Hines. All right
            reserved.</p>
        <a href="http://vanceandhines.com/"><img id="logo" class="pull-right" src="vhlogo-filloutline.png"></a>
    </div>
</footer>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
<script src="http://labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
<script src="vh_d3_script.min.js"></script>
<script>
    var vhEng = vhEng || {};
    (function () {

        //jquery date range picker
        $(function () {
            $("#from").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 1,
                onClose: function (selectedDate) {
                    $("#to").datepicker("option", "minDate", selectedDate);
                }
            });
            $("#to").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 1,
                onClose: function (selectedDate) {
                    $("#from").datepicker("option", "maxDate", selectedDate);
                }
            });
        });

        $("#resetDatePicker").click(function () {
            var format = d3.time.format("%m/%d/%Y");
            var today = format(new Date());

            $('#from').datepicker("setDate", '03/01/2014');
            $('#to').datepicker("setDate", today);
        });

        $("#reportButtons button").click(function () {

            $("#reportButtons button").removeClass("btn-primary active");
            $(this).addClass("btn-primary active");

            //// don't show or allow the date picker for some reports
            //if (this.id == 2 || this.id == 3 || this.id == 4 || this.id == 5) {
            //    $("#dateRange").css("display","none")
            //} else {
            //    $("#dateRange").css("display","block");
            //}

        });

        $("#modeControl button").click(function() {

            $("#modeControl button").removeClass("btn-primary active");
            $(this).addClass("btn-primary active");

            var report = $("#reportButtons button.active").attr('id');
            //console.log(report);

            vhEng.changeData(report);

        });


        //$(document).ready(function () {
        //    $('#mode.dropdown > ul > li > a').click(function () {
        //        event.preventDefault(event);
        //
        //        // use selection from the dropdown
        //        //var selection = $(this).html();
        //
        //        //Update the dropdown text to reflect the user selection
        //        //var modeButton = $("#modeControl  button.active").attr('id');
        //
        //        // find the button that is active to determine the report type
        //        var report = $("#reportButtons button.active").attr('id');
        //        console.log(report);
        //
        //
        //        vhEng.changeData(report);
        //
        //        // write to the console
        //        switch (selection) {
        //            case 'All':
        //                console.log(selection);
        //                break;
        //            case '66005':
        //                console.log(selection);
        //                break;
        //            case '66007':
        //                console.log(selection);
        //                break;
        //            default:
        //                alert('no operation was selected');
        //        }
        //    });
        //});


        this.changeData = function (id) {

            var format = d3.time.format("%m/%d/%Y");
            var today = format(d3.time.day.offset(new Date(), +1));

            var dateObj1 = $("#from").datepicker("getDate");
            var fromDate = dateObj1.getMonth() + 1 + "/" + dateObj1.getDate() + "/" + dateObj1.getFullYear();


            var dateObj2 = $("#to").datepicker("getDate");
            var do2 = dateObj2.getMonth() + 1 + "/" + dateObj2.getDate() + "/" + dateObj2.getFullYear();
            var toDate = format(d3.time.day.offset(new Date(do2), +1));


            var last30 = format(d3.time.day.offset(new Date(), -30));
            var last60 = format(d3.time.day.offset(new Date(), -60));
            var last90 = format(d3.time.day.offset(new Date(), -90));

            //console.log("today: "+ today )
            //console.log("fromDate: "+ fromDate )
            //console.log("toDate: "+ toDate )
            //
            //console.log("last30: "+ last30 )
            //console.log("last60: "+ last60 )
            //console.log("last90: "+ last90 )


            var mode = $("#modeControl button.active").attr('id');

            switch (id) {
                case "1":
                    updateData(id, fromDate, toDate, mode);
                    break;

                case "2":
                    updateData(id, fromDate, toDate, mode);
                    break;

                case "3":
                    updateData("3", last30, today, mode);
                    break;

                case "4":
                    updateData("3", last60, today, mode);
                    break;

                case "5":
                    updateData("3", last90, today, mode);
                    break;

                default:
                    updateData("1", fromDate, toDate, mode);
            }
        }

    }).apply(vhEng);

</script>
</body>
</html>