<x-app-layout title="Register">
    <div class="w-100">
        <div class="text-center mb-5">
            <h1 class="fw-bold mb-2">
                Register
            </h1>
            <p class="fw-medium text-muted">
                Create your account
            </p>
        </div>
        <div class="row g-0 justify-content-center">
            <div class="col-sm-8 col-xl-4">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-4">
                        <input type="text" class="form-control form-control-lg form-control-alt @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Name" required autofocus autocomplete="name">
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <input type="email" class="form-control form-control-lg form-control-alt @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="username">
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <input type="password" class="form-control form-control-lg form-control-alt @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required autocomplete="new-password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <input type="password" class="form-control form-control-lg form-control-alt @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
                        @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <a class="text-muted fs-sm fw-medium d-block d-lg-inline-block mb-1" href="{{ route('login') }}">
                                Already registered?
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-alt-primary">
                                <i class="fa fa-fw fa-user-plus me-1 opacity-50"></i> Register
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </x-guest-layout>
