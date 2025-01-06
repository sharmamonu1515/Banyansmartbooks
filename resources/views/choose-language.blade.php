@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="row">
            @foreach ($languages as $language)
                <div class="col-12 col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body p-0">
                            <a href="{{ route('user.choose.standard', $language->language) }}" class="text-decoration-none">
                                <img src="https://banyansmartbook.s3.ap-south-1.amazonaws.com/BSB/{{ $language->language }}.jpg" alt="{{ $language->language }}" class="img-fluid w-100 home-cover-img">
                                <h5 class="card-title text-center border-top py-3">{{ $language->language }}</h5>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
