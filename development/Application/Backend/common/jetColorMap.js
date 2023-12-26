
// tried to make this work for both matrix and table types of maps but there were too many problems
// So this code is commented out for now to only allow color matrix types


//var tableType = $('#tableType').attr('name');
//console.log(tableType);
//console.log( typeof tableType );

//if (tableType == "matrix") {
//    var cells = $('table.graph-table input.ssInputData');
//} else if (tableType == "table") {
//    var cells = $("input[class*='multiInputData']");
//};



var cells = $('table.graph-table input.ssInputData');
$.each(cells, function () {

    var max = Number( $('#maxValue').text() );
    var min = Number( $('#minValue').text() );
    console.log("min: "+ min +" max: "+ max);

    var cellValue = Number($(this).val());

    var rgbData = jetColor(cellValue, min, max);

    var r = rgbData[0];
    var g = rgbData[1];
    var b = rgbData[2];

    $(this).css('background-color', 'rgba(' + r + ',' + g + ',' + b + ', .8)');
    $(this).css('border', '1px solid ' + 'rgba(' + r + ',' + g + ',' + b + ', 1)');

    if (Number(cellValue) < Number(.3*(max-min))) {
        $(this).css('color', 'white');
        $(this).css('font-weight', '300');
    } else {
        $(this).css('color', 'black');
    }

});
