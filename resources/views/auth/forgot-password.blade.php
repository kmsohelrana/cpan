<x-app-layout title="Reset Password">
    <div class="w-100">
        <div class="text-center mb-5">
            <h1 class="fw-bold mb-2">
                Reset Password
            </h1>
            <p class="fw-medium text-muted">
                Enter email to reset your password
            </p>
            <p class="fw-medium text-success">
                @if (session('status'))
                <div class="text-success">
                    {{ session('status') }}
                </div>
                @endif
            </p>
        </div>
        <div class="row g-0 justify-content-center">
            <div class="col-sm-8 col-xl-4">
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-4">
                        <input type="text" class="form-control form-control-lg form-control-alt @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Email">
                        @error('email')
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
                                <i class="fa fa-fw fa-sign-in-alt me-1 opacity-50"></i> Send
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
