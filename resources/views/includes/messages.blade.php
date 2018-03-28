@if(Session::has('app_message') && Session::has('app_message_type'))
    <div class="col-12">
        <div class="alert alert-dismissible fade show col-md-4 offset-md-4 alert-{{ Session::get('app_message_type') }}" role="alert">
            <strong>
                <span>{{ Session::get('app_message') }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </strong>
        </div>
    </div>
    {{ Session::forget('app_message') }}
    {{ Session::forget('app_message_type') }}
@endif
