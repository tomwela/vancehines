$(document).ready(function () {
    $('table.graph-table input.ssInputData').click(function (event) {
        if (event.shiftKey) {
            console.log('');

            var operation = $('.btn-group :button.active').val();
            console.log("function: " + operation);

            var fInput = $("input[name='shiftClickInput']").val().trim();
            console.log("Input Box: " + fInput);

            if (fInput == 0 || fInput == '') {
                console.log('Please enter a number in the input box')
                return;
            }

            var cell = $(this).val();
            console.log('Cell: ' + cell);

            switch (operation) {
                case 'add':
                    var selected = parseFloat(Number(cell) + Number(fInput));
                    break;
                case 'subtract':
                    var selected = parseFloat(Number(cell) - Number(fInput));
                    break;
                case 'multiply':
                    var selected = parseFloat(Number(cell) * Number(fInput));
                    break;
                case 'divide':
                    var selected = parseFloat(Number(cell) / Number(fInput));
                    break;
                case 'equal':
                    var selected = parseFloat(Number(fInput));
                    break;
                default:
                    alert('no operation was selected');
            }

            console.log("Value: " + selected);
            $(this).val(selected.toFixed(2));
            $(this).css('border', '1px solid lime');
            $(this).css('background-color', '');
            $(this).css('color', 'black');
        }
    });

});



