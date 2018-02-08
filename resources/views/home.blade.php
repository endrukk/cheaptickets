@extends('layouts.app')

@section('content')
<div class="container">
    @if(Session::has('app_message') && Session::has('app_message_type'))

        <div class="row">
            <div class="col-md-4 col-md-offset-4 bg-{{ Session::get('app_message_type') }}">
                <p>{{ Session::get('app_message') }}</p>
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        </div>
        {{ Session::forget('app_message') }}
        {{ Session::forget('app_message_type') }}
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="col-md-3 col-sm-6">
                        <a href="{{route('apply-tickets')}}" class="btn btn-primary btn-lg">Apply cheap tickets</a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{route('generate-ryanair')}}" class="btn btn-success btn-lg">Sync Ryanair</a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{route('generate-wizzair')}}" class="btn btn-success btn-lg">Sync Wizzair</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
