@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Set Device Ownership') }}</div>

                <div class="card-body">

                    @include('_partials.flash-messages')

                    <form method="POST" action="{{ route('register.send.otp') }}">
                        @csrf

                        <input type="hidden" name="signup_key" value="{{ $signup_key }}">

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="address" class="col-md-4 col-form-label text-md-end">{{ __('Address') }}</label>

                            <div class="col-md-6">
                                <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" required autocomplete="address" autofocus>

                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="school_name" class="col-md-4 col-form-label text-md-end">{{ __('School Name') }}</label>

                            <div class="col-md-6">
                                <input id="school_name" type="text" class="form-control @error('school_name') is-invalid @enderror" name="school_name" value="{{ old('school_name') }}" required autocomplete="school_name" autofocus>

                                @error('school_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="language" class="col-md-4 col-form-label text-md-end">{{ __('Language') }}</label>

                            <div class="col-md-6">
                                <select name="language" id="language" class="form-select @error('language') is-invalid @enderror" required>
                                    <option value="">Choose language</option>
                                    @foreach ($languages as $language)
                                        <option {{ old('language') === $language ? 'selected' : '' }}>{{ $language }}</option>
                                    @endforeach
                                </select>

                                @error('language')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3" style="display: none">
                            <label for="standard" class="col-md-4 col-form-label text-md-end">{{ __('Standard') }}</label>
                            <div class="col-md-6">
                                <select name="standard" id="standard" class="form-select @error('standard') is-invalid @enderror" required data-value="{{ old('standard') }}"></select>

                                @error('standard')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> --}}

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Verify details') }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let standards = [];

        const setStandards = () => {
            const $select = $('#standard');

            if ( standards.length  ) {
                $select.closest('.row').show();
            } else {
                $select.closest('.row').hide();
            }

            const oldValue = $select.data('value');
            let html = '<option value="">Choose standard</option>';
            standards.forEach(standard => {
                html += `<option ${oldValue === standard ? 'selected' : ''}>${standard}</option>`
            })
            $select.html(html);
        }

        $('#language').on('change', function() {
            const $select = $(this)
            const val = $select.val().trim()
            if ( ! val.length ) {
                standards = [];
                setStandards();
                return;
            }

            let url = '{{ route("standards.by.language", [1111]) }}';
            url = url.replace(1111, val);

            $.ajax({
                url: url,
                beforeSend: function() {
                    $select.attr('disabled', 'disabled')
                },
                complete: function() {
                    $select.removeAttr('disabled')
                },
                success: function(res) {
                    if ( res.success ) {
                        standards = res.standards
                        setStandards();
                    } else {
                        console.error('Some error occurred in fetching standards.', res);
                    }
                }
            })
        }).trigger('change');
    });
</script>
@endsection
