@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="modal-content mb-sm-3">
                <div class="modal-header">
                    <div class="col-md-12">
                        <h4>Flights</h4>

                        <span>
                            Search for flights of Ryanair and Wizzair.
                            The default departure airport is Budapest, and the default length 3 to 8 days.<br />
                            Enjoy!
                        </span>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="bd-example">
                        <form action="{{route('ajax-fare-finder')}}" method="post" id="fareFinderForm">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">From </span>
                                        </div>
                                        <input type="text" name="departure" id="flightsDeparture" data-target="#departureCode" class="form-control" placeholder="the airport you departure" aria-label="Departure" aria-describedby="basic-addon1">
                                        <div class="ajax-select">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">To </span>
                                        </div>
                                        <input type="text" name="arrival" id="filghtsArrival"  data-target="#destinationCode" class="form-control" placeholder="the airport you wish to arrive" aria-label="Arrive" aria-describedby="basic-addon1">
                                        <div class="ajax-select">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" id="ajaxSearchFlights" class="btn btn-outline-primary btn-md">Search!</button>

                                </div>
                            </div>
                            <input type="hidden" id="departureCode" name="departure_code" value=""/>
                            <input type="hidden" id="destinationCode" name="destination_code" value=""/>
                        </form>


                    </div>
                </div>
            </div>
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Flights table</h4>
                </div>
                <div class="modal-body">
                    @if( $flightsTableData )
                        <table class="table table-striped table-dark table-responsive" id="flightsTable">
                            <thead>
                            <tr>
                                <th colspan="3" scope="col">From</th>
                                <th colspan="3" scope="col">To</th>
                                <th colspan="4" scope="col">Data</th>
                            </tr>
                            <tr>
                                <th scope="col">Country</th>
                                <th scope="col">City</th>
                                <th scope="col">Airport</th>

                                <th scope="col">Country</th>
                                <th scope="col">City</th>
                                <th scope="col">Airport</th>


                                <th scope="col">Date</th>
                                <th scope="col">Length</th>
                                <th scope="col">Price</th>
                                <th scope="col">Company</th>
                            </tr>
                            <tbody>
                            @foreach ($flightsTableData as $row)
                                <tr>
                                    <td scope="row">{{$row->from_country}}</td>
                                    <td scope="row">{{$row->from_city}}</td>
                                    <td scope="row">{{$row->from_airport}}</td>

                                    <td scope="row">{{$row->to_country}}</td>
                                    <td scope="row">{{$row->to_city}}</td>
                                    <td scope="row">{{$row->to_airport}}</td>

                                    <td scope="row">{{$row->date}}</td>
                                    <td scope="row">{{$row->length}}</td>
                                    <td scope="row">{{$row->price}}</td>
                                    <td scope="row">{{$row->company}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            </thead>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
