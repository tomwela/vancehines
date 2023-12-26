
var margin = {top: 25, right: 20, bottom: 100, left: 50},
    width = 1100 - margin.left - margin.right,
    height = 640 - margin.top - margin.bottom;


var tip = d3.tip()
  .attr('class', 'd3-tip')
  .offset([-15, 0])
  .html(function(d) {
    return "<strong>Installs:</strong> <span style='color:white'>" + d.frequency + "</span><br /><center><span>"+ d.letter +"<center></span>";
  })


//creat SVG element and call it svg - bad practice
var svg = d3.select("#charts")
            .append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
//             .style("background-color","rgba(255, 255, 0, 0.03)")
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");


svg.call(tip);


// specify format format date.
var format = d3.time.format("%m/%d/%Y");

// formatted date.  Today's Date plus 1 for MySQL Date calculations
var today = format( d3.time.day.offset(new Date(), +1) );

// get the date from 30 days ago and format it
var before = format( d3.time.day.offset(new Date(), -30) );
//console.log( today +" "+ before);


d3.json("php/installs.php?type=byWeek&d1=03/01/2014&d2="+ today +'"', function(error, response) {

  	var comma = d3.format(",");
    var total = comma(response.total);
    // Update Page Title
    d3.select("#totalInstalls").text(total);


    var data = response.results;
    data.forEach(function(d) {
        //d.letter = parseDate(d.letter);
        d.frequency = +d.frequency;  // type cast to a number
    });

    var x = d3.scale.ordinal()
              .domain(data.map(function(d) { return d.letter; }))
              .rangeRoundBands([0, width], .1);

    var y = d3.scale.linear()
              .domain([0, d3.max(data, function(d) { return d.frequency * 1.2; })])
              .range([height, 0]);

    // initialize the axes ???
    var xAxis = d3.svg.axis()
        .scale(x)
        .orient("bottom");

    var yAxis = d3.svg.axis()
        .scale(y)
        .orient("left");
        //.tickFormat(formatPercent);


    // display x-axis labels angled for readability
    svg.append("g")
        .attr("class", "x axis")
        .attr("transform", "translate(0," + height + ")")
        .call(xAxis)
        .selectAll("text")
        		.style("text-anchor", "end")
        		.attr("dx","-.8em")
        		.attr("dy",".15em")
        		.attr("transform", function(d) {
        			return "rotate(-65)"
        		});


    // display Y-axis labels & Title
    svg.append("g")
        .attr("class", "y axis")
        .call(yAxis)
        .append("text")
          .attr("transform", "rotate(-90)")
          .attr("y", 6)
          .attr("dy", ".71em");

          //.style("text-anchor", "end");
          //.text(total +" Total Installs");

    //create Bars per datum
    //svg.selectAll(".bar")
    svg.selectAll("rect")
        .data(data)
        .enter()
        .append("rect")
          .attr("class", "bar")
          .attr("x", function(d) { return x(d.letter); })
          .attr("y", function(d) { return y(d.frequency); })
          .attr("width", x.rangeBand())
          .attr("height", function(d) { return height - y(d.frequency); })
          .on('mouseover', tip.show)
          .on('mouseout', tip.hide);


	svg.selectAll("text")
	   .data(data)
	   .enter()
	   .append("text")
			.text(function(d) { return d.frequency; })
			//.attr("text-anchor", "middle")
        .attr("x", function(d) { return x(d.letter) + 0; })
        .attr("y", function(d) { return y(d.frequency) + 10; })
        .attr("width", x.rangeBand())
        .attr("height", function(d) { return height - y(d.frequency); })
        .attr("font-family", "sans-serif")
        .attr("font-size", "9px")
        .attr("fill", "black");




		var max    = d3.max(data, function(d) { return d.frequency; });
		var min    = d3.min(data, function(d) { return d.frequency; });
		var mean   = d3.mean(data, function(d) { return d.frequency; });
		var median = d3.median(data, function(d) { return d.frequency; });


		d3.select("#max").text( max );
		d3.select("#min").text( min );
		d3.select("#mean").text( d3.round(mean,1) );
		d3.select("#median").text( median );

		console.log(" ");
    console.log("max: "+ max );
    console.log("min: "+ min );
    console.log("mean: "+ d3.round(mean,1) );
    console.log("median: "+ median );

});

// function updateData(err, data) {
function updateData(id, date1, date2) {


  switch (id) {
      case "1":
          var reportType = "byWeek";
          break;
      case "2":
          var reportType = "byMonth";
          break;
      case "3":
          var reportType = "byDay";
          break;
      default:
          console.log('default');
  }


  d3.json("php/installs.php?type="+reportType+"&d1="+date1+"&d2="+date2+'"', function(error, response) {

  		var comma = d3.format(",");
    	var total = comma(response.total);
      // Update Page Title
      d3.select("#totalInstalls").text(total);


      var data = response.results;
      data.forEach(function(d) {
        //d.letter = parseDate(d.letter);
        d.frequency = +d.frequency;
      });


      var x = d3.scale.ordinal()
                .domain(data.map(function(d) { return d.letter; }))
                .rangeRoundBands([0, width], .1);

      var y = d3.scale.linear()
                .domain([0, d3.max(data, function(d) { return d.frequency * 1.2; })])
                .range([height, 0]);


      var xAxis = d3.svg.axis()
          .scale(x)
          .orient("bottom");

      var yAxis = d3.svg.axis()
          .scale(y)
          .orient("left");
          //.tickFormat(formatPercent);


      svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")")
            .call(xAxis)
            .selectAll("text")
            		.style("text-anchor", "end")
            		.attr("dx","-.8em")
            		.attr("dy",".15em")
            		.attr("transform", function(d) {
            			return "rotate(-65)"
            		});


      svg.append("g")
            .attr("class", "y axis")
            .call(yAxis)
            .append("text")
              .attr("transform", "rotate(-90)")
              .attr("y", 6)
              .attr("dy", ".71em");
              //.style("text-anchor", "end");
              //.text(total +" FP3 Installs");


      svg.select(".x.axis").remove();
      svg.select(".y.axis").remove();


      //var bar = svg.selectAll(".bar")
      var bar = svg.selectAll("rect")
              .data(data, function(d) { return d.letter; });

      // new data:
      bar
        .enter()
        .append("rect")
          .attr("class", "bar")
          .attr("x", function(d) { return x(d.letter); })
          .attr("y", function(d) { return y(d.frequency); })
          .attr("width", x.rangeBand())
          .attr("height", function(d) { return height - y(d.frequency); })
          .on('mouseover', tip.show)
          .on('mouseout', tip.hide);

      // removed data:
      bar.exit().remove();

      // updated data:
      bar.transition()
         .duration(750)
        .attr("x", function(d) { return x(d.letter); })
        .attr("y", function(d) { return y(d.frequency); })
        .attr("width", x.rangeBand())
        .attr("height", function(d) { return height - y(d.frequency); });


/*
      //var t = svg.selectAll("text")
      var t = bar.selectAll("text")
              .data(data, function(d) { return d.letter; });

  		t
  		.enter()
  		.append("text")
  		.text(function(d) { return d.frequency; })
  			//.attr("text-anchor", "middle")
  			.attr("x", function(d) {
            if (reportType == "byWeek" || reportType == "byDay" ) {
            	return x(d.letter) + 0;
            } else {
            	return x(d.letter) + (width/d.letter.length + .1*d.letter.length)/4 - 7.1*1.75;
            }
  			})
  		  .attr("y", function(d) {
            if (reportType == "byWeek" || reportType == "byDay" ) {
              return y(d.frequency) + 10;
            } else {
              return y(d.frequency) + 20;
            }
  			})
  		  .attr("width", x.rangeBand())
  		  .attr("height", function(d) { return height - y(d.frequency); })
  			.attr("font-family", "sans-serif")
  			.style("font-size", function(d) {
            if (reportType == "byWeek" || reportType == "byDay" ) {
              return "9px";
            } else {
              return "14px";
            }
  			})
  		  .attr("fill", "black");


        // removed data:
        t.exit().remove();
*/

        svg.call(tip);


		var max    = d3.max(data, function(d) { return d.frequency; });
		var min    = d3.min(data, function(d) { return d.frequency; });
		var mean   = d3.mean(data, function(d) { return d.frequency; });
		var median = d3.median(data, function(d) { return d.frequency; });

		d3.select("#max").text( max );
		d3.select("#min").text( min );
		d3.select("#mean").text( d3.round(mean,1) );
		d3.select("#median").text( median );



		console.log(" ");
    console.log("max: "+ max );
    console.log("min: "+ min );
    console.log("mean: "+ d3.round(mean,1) );
    console.log("median: "+ median );

  });

};

// coerce data to numbers
// use with tsv data above
/*
function type(d) {
  d.frequency = +d.frequency;
  return d;
}
*/

