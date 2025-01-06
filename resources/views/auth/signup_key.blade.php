@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Sign Up') }}</div>

                <div class="card-body">
                    
                    @include('_partials.flash-messages')

                    <form method="POST" action="{{ route('signup.key.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Your Sign Up Key') }}</label>

                            <div class="col-md-6">
                                <input id="signup_key" type="text" class="form-control @error('signup_key') is-invalid @enderror" name="signup_key" required>

                                @error('signup_key')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-1">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Submit') }}
                                </button>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4 px-0">
                                @if (Route::has('login'))
                                    <a class="btn btn-link" href="{{ route('login') }}">
                                        {{ __('Already have an account? Sign In') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
