@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="row">
            @foreach ($covers as $cover)
                <div class="col-12 col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body p-0">
                            <a href="{{ route('user.cover.index', $cover->code) }}" class="text-decoration-none">
                                <img src="{{ $cover->image_url() }}" alt="{{ $cover->name }}" class="img-fluid w-100 home-cover-img">
                                <h5 class="card-title text-center border-top py-3">{{ $cover->name }}</h5>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
