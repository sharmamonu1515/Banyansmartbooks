@if ( Session::has('success') )
<div class="alert alert-success alert-dismissible">
    <strong>{!! Session::pull('success') !!}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if ( Session::has('error') )
<div class="alert alert-danger alert-dismissible">
    <strong>{!! Session::pull('error') !!}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if ( Session::has('warning') )
<div class="alert alert-warning alert-dismissible">
    <strong>{!! Session::pull('warning') !!}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if ( Session::has('info') )
<div class="alert alert-info alert-dismissible">
    <strong>{!! Session::pull('info') !!}</strong>
</div>
@endif

@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div class="alert alert-danger alert-dismissible">
            {!! $error !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach
@endif

<div id="ajax-messages-wrapper"></div>
