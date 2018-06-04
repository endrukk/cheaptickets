@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="modal-content mb-sm-3">
            <div class="modal-header">
                <h4>Dashboard</h4>
            </div>
            <div class="modal-body">
                <div class="bd-example">
                    {{--<a href="{{route('apply-tickets')}}" class="btn btn-outline-primary btn-lg">Apply cheap tickets</a>--}}
                    <a href="{{route('my-cheap-tickets')}}" class="btn btn-outline-primary btn-lg">Apply cheap tickets</a>
                    <a href="{{route('generate-ryanair')}}" class="btn btn-outline-success btn-lg">Sync Ryanair</a>
                    <a href="{{route('generate-wizzair')}}" class="btn btn-outline-success btn-lg">Sync Wizzair</a>
                </div>
            </div>
        </div>
        <div class="modal-content">
            <div class="modal-header">
                <h4>Flights table</h4>
            </div>
            <div class="modal-body">
                @if( $flightsTableHeaders )
                    <table class="table table-striped table-dark table-responsive">
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
