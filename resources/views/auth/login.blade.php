<x-app-layout title="Login">
    <div class="w-100 h-100 d-flex align-items-center justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
            <div class="card shadow-sm rounded-4">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        {{-- <img src="{{ asset('images/janata-bank/jb-blue.png') }}" alt="Logo" class="img-fluid" style="max-height: 70px;"> --}}
                        <i class="fa fa-university fa-3x text-primary"></i>
                        <div class="fw-bold fs-4 text-primary mt-2">
                            {{ config('app.name', 'SSL Wireless CRM') }}
                        </div>
                        <hr class="my-3">
                        <h5 class="fw-bold mb-0">Sign In to your account</h5>
                    </div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="login" class="form-label small fw-semibold">Email or Bank ID/Staff ID</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-user text-muted"></i></span>
                                <input type="text" class="form-control @error('login') is-invalid @enderror" id="login" name="login" value="{{ old('login') }}" placeholder="Enter your email or (Bank ID/Staff ID)" autofocus>
                                @error('login')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label small fw-semibold">Password</label>
                                @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="small text-primary text-decoration-none">Forgot password?</a>
                                @endif
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-lock text-muted"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password">
                                @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">
                                Sign In
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
