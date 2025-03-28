@extends('layouts.WEBSITE.ADMIN.adminApp')
@section('content')
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-md-12">
                <div class="card shadow mt-5">
                    <div class="card-header">{{ __('Update') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('POST')

                            <div class="row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name') ?? Auth::user()->name }}" required autocomplete="name"
                                        autofocus>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') ?? Auth::user()->email }}" required autocomplete="email">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password-confirm"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow mt-5">
                        <div class="card-header">{{ __('Save Logo') }}</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('logo.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('POST')
                                <div class="row">
                                    <div class="row py-2">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                {{-- <label for="formFile" class="form-label">Logo</label> --}}
                                                <input class="form-control" name="file" type="file" id="formFile">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="py-3">
                                        <button type="submit" style="width: 100%" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow mt-5">
                        <div class="card-header">{{ __('Save Fav Icon') }}</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('favicon.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('POST')
                                <div class="row">
                                    <div class="row py-2">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                {{-- <label for="formFile" class="form-label">Fave Icon</label> --}}
                                                <input class="form-control" name="file" type="file" id="formFile">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="py-3">
                                        <button type="submit" style="width: 100%" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
