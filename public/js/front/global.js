/**
 * Created by endre on 2018. 02. 06..
 */
$(document).ready(function () {
    $('#ajaxSearchFlights,#ajaxAddCheapTicket').on('click',function (e) {
        e.preventDefault();
        searchTickets($(this));
    });

    $('#filghtsArrival, #flightsDeparture').on('input',function () {
        searchAirports($(this));
    });

    $(document).on('click','.destination-result',function () {
        if($(this).parent().prev('input[type=text]')[0] == $('#filghtsArrival')[0]){
            $('#filghtsArrival').val($(this).attr('data-dest-airport'));
            $('#destinationCode').val($(this).attr('data-dest-code'));
        }else if($(this).parent().prev('input[type=text]')[0] == $('#flightsDeparture')[0]){
            $('#flightsDeparture').val($(this).attr('data-dest-airport'));
            $('#departureCode').val($(this).attr('data-dest-code'));
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
                        + '" data-dest-code="' + data[i]['code'] + '">'
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
    }else if(airport == ""){
        $(element.attr('data-target')).val("");
    }
}

function searchTickets(element) {
    var form = $(element).closest('form').get(0);
    var token = $('#fareFinderForm').find('input[name="_token"]').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token
        }
    });

    $.ajax({
        url: form.action,
        dataType: 'json',
        type: 'POST',
        data: {
            format: 'json',
            departure_code: form.departure_code.value,
            destination_code: form.destination_code.value
        },
        success: function(data) {
            if(typeof(data['result'] === "undefined") ) {
                let htmlContent = "";
                for(let i = 0; i < data['result'].length; i++){
                   let row = data['result'][i];
                    htmlContent +=
                    '<tr>' +
                        '<td scope="row">' + row['from_country'] + '</td>' +
                        '<td scope="row">' + row['from_city'] + '</td>' +
                        '<td scope="row">' + row['from_airport'] + '</td>' +
                        '<td scope="row">' + row['to_country'] + '</td>' +
                        '<td scope="row">' + row['to_city'] + '</td>' +
                        '<td scope="row">' + row['to_airport'] + '</td>' +
                        '<td scope="row">' + row['date'] + '</td>' +
                        '<td scope="row">' + row['length'] + '</td>' +
                        '<td scope="row">' + row['price'] + '</td>' +
                        '<td scope="row">' + row['company'] + '</td>' +
                    '</tr>';
                }

                $('#flightsTable tbody').html(htmlContent);
            }
            displayAjaxMessage(data['app_message'], data['app_message_type']);
        },
        error: function() {
            console.log('error');
        }
    });
}

function displayAjaxMessage(message, type = 'success'){
    let htmlMessage =
    '<div class="alert alert-dismissible fade show col-md-4 offset-md-4 alert-' + type + '" role="alert">'+
        '<strong>' +
            '<span>' + message + '</span>' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">×</span>' +
            '</button>' +
        '</strong>' +
    '</div>';
    $('#app > nav').next().prepend(htmlMessage);
}