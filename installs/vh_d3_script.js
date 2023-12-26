
// Vance & Hines d3.js scripts
var margin = {top: 25, right: 0, bottom: 100, left: 45},
    width = 1100 - margin.left - margin.right,
    height = 640 - margin.top - margin.bottom;


var tip = d3.tip()
    .attr('class', 'd3-tip')
    .offset([-15, 0])
    .html(function (d) {
        return "<strong>Installs:</strong> <span style='color:white'>" + d.frequency + "</span><br /><center><span>" + d.letter + "<center></span>";
    })

var svg = d3.select("#charts")
    .append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
    //             .style("background-color","rgba(255, 255, 0, 0.03)")
    .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

// instantiate the tool tip
svg.call(tip);


// specify date format date.
var format = d3.time.format("%m/%d/%Y");

// formatted date.  Today's Date plus 1 for MySQL Date calculations
var today = format(d3.time.day.offset(new Date(), +1));

// get the date from 30 days ago and format it
var before = format(d3.time.day.offset(new Date(), -30));
//console.log( today +" "+ before);


updateData("1", '03/01/2014', today, mode="all");



// ###########################
//  Functions  Below this line
function updateData(id, date1, date2, mode) {


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


    d3.json("php/installs.php?type=" + reportType + "&d1=" + date1 + "&d2=" + date2 +"&mode=" + mode, function (error, response) {

        var comma = d3.format(",");
        var total = comma(response.total);
        // Update Page Title
        d3.select("#totalInstalls").text(total);

        if(total==0) {
            // if there are no results remove the graph & data
            svg.select(".x.axis").remove();
            svg.select(".y.axis").remove();

            svg.selectAll("rect").remove();

            d3.select("#max").text('');
            d3.select("#min").text('');
            d3.select("#mean").text('');
            d3.select("#median").text('');

            return;
        }


        var data = response.results;
        data.forEach(function (d) {
            //d.letter = parseDate(d.letter);
            d.frequency = +d.frequency;
        });


        var x = d3.scale.ordinal()
            .domain(data.map(function (d) {
                return d.letter;
            }))
            .rangeRoundBands([0, width], .1);

        var y = d3.scale.linear()
            .domain([0, d3.max(data, function (d) {
                return d.frequency * 1.2;
            })])
            .range([height, 0]);


        svg.select(".x.axis").remove();
        svg.select(".y.axis").remove();


        var xAxis = d3.svg.axis()
            .scale(x)
            .tickValues(x.domain().filter(function (d, i) {
                if (data.length > 50) {
                    return !(i % 4);
                } else {
                    return !(i % 1);
                }
            }))
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
            .attr("dx", "-.8em")
            .attr("dy", ".15em")
            .attr("transform", function (d) {
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


        var bar = svg.selectAll("rect")
            .data(data, function (d) {
                return d.letter;
            });


        // remove data
        if (bar) {
            bar.exit().remove();
        }

        // add data:
        bar
            .enter()
            .append("rect")
            .attr("class", "bar")
            .attr("x", function (d) {
                return x(d.letter);
            })
            .attr("y", function (d) {
                return height;  // start at 0 for transitions on page load to build up to actual value
            })
            .attr("width", x.rangeBand())
            .attr("height", 0)  // start at 0 for transitions on page load to build up to actual value
            .on('mouseover', tip.show)
            .on('mouseout', tip.hide);


        bar.classed("highlight", function (d) {
            if (d > 20) {
                return true;
            } else {
                return false;
            }
        });
        //.attr("class", "highlight");


        // update data:
        bar.transition()
            //.delay(function(d,i){
            //    return i*5;
            //})
            .duration(300)
            .ease("cubic-in-out")
            .attr("x", function (d) {
                return x(d.letter);
            })
            .attr("y", function (d) {
                return y(d.frequency);
            })
            .attr("width", x.rangeBand())
            .attr("height", function (d) {
                return height - y(d.frequency);
            });


        var max = d3.max(data, function (d) {
            return d.frequency;
        });
        var min = d3.min(data, function (d) {
            return d.frequency;
        });
        var mean = d3.mean(data, function (d) {
            return d.frequency;
        });
        var median = d3.median(data, function (d) {
            return d.frequency;
        });
        var total = d3.sum(data, function (d) {
            return d.frequency;
        });

        d3.select("#max").text(max);
        d3.select("#min").text(min);
        d3.select("#mean").text(d3.round(mean, 1));
        d3.select("#median").text(median);

        console.log(" ");
        console.log("max: " + max);
        console.log("min: " + min);
        console.log("mean: " + d3.round(mean, 1));
        console.log("median: " + median);
        console.log("Total: " + total);
        console.log("number of Bars: " + data.length);
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

