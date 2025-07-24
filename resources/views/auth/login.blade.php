@extends('layouts.guest')
@section('content')
<div class="container" style="min-height:100vh;display:flex;align-items:center;justify-content:center;">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header text-center">
                    <img src="{{ URL::to('files/assets/images/logo.png') }}" alt="Logo" style="max-width:120px; margin-bottom:20px;">
                    <h4 class="mb-0">Login</h4>
                    <span class="text-muted">Sign in to your account</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('login') }}" method="POST" class="j-pro" id="j-pro" novalidate>
                        @csrf
                        <div class="j-content">
                            <div class="j-unit mb-3">
                                <div class="j-input">
                                    <label class="j-icon-right" for="email">
                                        <i class="icofont icofont-ui-user"></i>
                                    </label>
                                    <input type="text" id="email" name="email" placeholder="your email..." value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                                    @error('email')
                                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="j-unit mb-3">
                                <div class="j-input">
                                    <label class="j-icon-right" for="password">
                                        <i class="icofont icofont-lock"></i>
                                    </label>
                                    <input type="password" id="password" name="password" placeholder="your password..." class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                    <span class="j-hint">
                                        <a href="{{ route('password.request') }}" class="j-link">Forgot password?</a>
                                    </span>
                                </div>
                            </div>
                            <div class="j-unit mb-3">
                                <label class="j-checkbox">
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <i></i> Remember Me
                                </label>
                            </div>
                            <div class="j-response"></div>
                        </div>
                        <div class="j-footer">
                            <button type="submit" class="btn btn-primary w-100">Sign in</button>
                        </div>
                    </form>
                    <div class="mt-4 text-center">
                        <span>Don't have an account?</span>
                        <a class="text-primary" href="{{ route('register') }}">Create account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
