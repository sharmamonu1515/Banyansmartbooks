@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify your email address') }}</div>
                
                <div class="card-body">

                    @include('_partials.flash-messages')
                    
                    <div class="alert alert-success" role="alert">
                        {{ __('OTP has been sent to your email address.') }}
                    </div>

                    <form method="POST" action="{{ $type === 'register' ? route('register') : route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="otp" class="col-md-4 col-form-label text-md-end">{{ __('Enter OTP') }}</label>

                            <div class="col-md-6">
                                <input id="otp" type="text" class="form-control @error('otp') is-invalid @enderror" name="otp" value="{{ old('otp') }}" required autocomplete="otp" autofocus>

                                @error('otp')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        @if ($type === 'login')
                        <div class="mb-3">
                            <div class="text-danger col-md-6 offset-md-4 px-2">
                                Multiple login are not allowed. If found your previous login device will be logged out.<br>
                                Avoid sharing your OTP to others
                            </div>
                        </div>
                        @endif

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ $type === 'register' ? __('Register') : __('Login') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="row mb-3" id="resendOtp" style="display: none">
                        <div class="col-md-6 offset-md-4">
                            <form class="d-inline" method="POST" action="{{ $type === 'register' ? route('register.resend.otp') : route('login.resend.otp') }}">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('Resend OTP') }}</button
                            </form>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        setTimeout(() => {
            $('#resendOtp').show();
        }, 120 * 1000);
    })
</script>
@endsection