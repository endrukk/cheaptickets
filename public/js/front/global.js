/**
 * Created by endre on 2018. 02. 06..
 */
$(document).ready(function () {
    $('#ajaxSearchFlights,#ajaxAddCheapTicket').on('click',function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        //todo: Ajax form submit, in laravel we need a token to submit the form
        $.ajax({
            url: form.attr('action'),
            dataType: 'text/json',
            type: 'POST',
            data: {
                format: 'json',
                data: form.serializeArray()
            },
            error: function() {
               console.log('error');
            },
            success: function(data) {
               console.log(data);
            }
        });
    });

    $('#filghtsArrival, #flightsDeparture').on('input',function () {
        searchAirports($(this));
    });

    $('#ajaxSearchFlights').on('click',function () {
        searchFlights();
    });

    $(document).on('click','.destination-result',function () {
        if($(this).parent().prev('input[type=text]')[0] == $('#filghtsArrival')[0]){
            $('#filghtsArrival').val($(this).attr('data-dest-airport'));
            $('#destinationID').val($(this).attr('data-dest-code'));
        }else if($(this).parent().prev('input[type=text]')[0] == $('#flightsDeparture')[0]){
            $('#flightsDeparture').val($(this).attr('data-dest-airport'));
            $('#deartureID').val($(this).attr('data-dest-code'));
        }
        $('.ajax-select').hide();
    });

});


function searchAirports(element){

    var airport = element.val();
    var token = $('#fareFinderForm').find('input[name="_token"]').val();
    var resultContainer = element.next('.ajax-select');
    if(airport.length >= 3) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': token
            }
        });
        $.ajax({
            url: '/ajax/tickets/airport-finder',
            dataType: 'json',
            type: 'POST',
            data: {
                airport: airport
            },
            success: function (data) {
                var html = "";
                for(var i = 0; i < data.length; i++){
                    html +=
                        '<div class="destination-result" data-dest-airport="' + data[i]['airport'] + ' - ' + data[i]['city']
                        + '" data-dest-code="' + data[i]['id'] + '">'
                        +'<p>'
                        + '<strong>' + data[i]['airport'] + '</strong> - '
                        + data[i]['code']
                        + '<br /><span>' + data[i]['city']+ ', ' + data[i]['country'] + '</span>'
                        + '</p>'
                        +'</div>';
                }
                resultContainer.html(html).show();
            },
            error: function () {
                console.log('error');
            }
        });
    }
}

function searchFlights(){
    var destinationCode = $('#destinationID').val();
    var token = $('#fareFinderForm').find('input[name="_token"]').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token
        }
    });
    $.ajax({
        url: '/ajax/tickets/fare-finder',
        dataType: 'json',
        type: 'POST',
        data: {
            destination_code: destinationCode
        },
        success: function (data) {
            console.log('success');
        },
        error: function () {
            console.log('error');
        }
    });
}