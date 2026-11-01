<x-app-layout title="Reset Password">
    <div class="w-100">
        <div class="text-center mb-5">
            <p class="mb-3">
                <i class="fa fa-2x fa-circle-notch text-primary-light"></i>
            </p>
            <h1 class="fw-bold mb-2">
                Reset Password
            </h1>
            <p class="fw-medium text-muted">
                Enter new password and submit to reset your password
            </p>
        </div>
        <div class="row g-0 justify-content-center">
            <div class="col-sm-8 col-xl-4">
                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="mb-4">
                        <input type="text" class="form-control form-control-lg form-control-alt @error('email') is-invalid @enderror" id="email" name="email" value="{{ $email ?? old('email') }}" placeholder="Email">
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <input type="password" class="form-control form-control-lg form-control-alt @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <input type="password_confirmation" class="form-control form-control-lg form-control-alt @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password">
                        @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <a class="text-muted fs-sm fw-medium d-block d-lg-inline-block mb-1" href="{{ route('login') }}">
                                Login Here?
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-alt-primary">
                                <i class="fa fa-fw fa-sign-in-alt me-1 opacity-50"></i> Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
