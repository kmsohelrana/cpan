<x-app-layout title="{{ $title }}">

    <x-header title=''>
        <li class="breadcrumb-item"><a class="link-fx" href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">{{ $title }}</li>
    </x-header>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fa fa-triangle-exclamation me-1"></i> {{ session('warning') }}
            </div>
            @endif
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        Change Password
                    </h3>
                    <div class="block-options">
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-circle-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="block-content">

                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form id="cpForm">
                        @csrf
                        @method('PUT')
                        {{-- Old Password --}}
                        <div class="mb-3">
                            <label class="form-label" for="cp-userpassword">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                <input type="password" id="cp-userpassword" name="password" class="form-control" placeholder="Enter current password" autocomplete="current-password">
                            </div>
                            <span class="invalid-feedback d-block" id="cp-error-password"></span>
                        </div>

                        {{-- New Password --}}
                        <div class="mb-2">
                            <label class="form-label" for="cp-new_password">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-key"></i></span>
                                <input type="password" id="cp-new_password" name="new_password" class="form-control" placeholder="Enter new password" autocomplete="new-password">
                            </div>
                            <span class="invalid-feedback d-block" id="cp-error-new_password"></span>
                        </div>
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" id="cp-btn-generate" class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-wand-magic-sparkles"></i> Generate
                            </button>
                            <button type="button" id="cp-btn-toggle" class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-eye"></i> <span class="toggle-label">Show</span>
                            </button>
                            <button type="button" id="cp-btn-copy" class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-copy"></i> Copy
                            </button>
                            <span id="cp-copy-feedback" class="text-success align-self-center d-none">
                                <i class="fa fa-check"></i> Copied!
                            </span>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-3">
                            <label class="form-label" for="cp-new_password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-key"></i></span>
                                <input type="password" id="cp-new_password_confirmation" name="new_password_confirmation" class="form-control" placeholder="Confirm new password" autocomplete="new-password">
                            </div>
                            <span class="invalid-feedback d-block" id="cp-error-new_password_confirmation"></span>
                        </div>

                        {{-- Password Rules --}}
                        <div class="alert alert-info py-2 px-3 mb-4" style="font-size: 0.82rem;">
                            <p class="fw-semibold mb-1"><i class="fa fa-shield-halved me-1"></i> Password Requirements</p>
                            <ul class="mb-0 ps-3">
                                <li>Your password must be a minimum of 8 characters in length.</li>
                                <li>It must include at least one uppercase letter (A-Z).</li>
                                <li>It must include at least one lowercase letter (a-z).</li>
                                <li>It must contain at least one number (0-9).</li>
                                <li>It must contain at least one special character from the following set: <code>#</code>, <code>$</code>, <code>%</code>, <code>&amp;</code>, <code>*</code>, <code>(</code>, <code>)</code>.</li>
                                <li>Avoid using the same character consecutively 3 times, e.g., <code>"aaa"</code> or <code>"111"</code>.</li>
                                <li>Avoid sequential characters in the password (e.g., <code>"abc"</code> or <code>"123"</code>).</li>
                                <li>Your new password cannot match any of your last 3 passwords.</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary w-100" id="cp-btn-submit">
                                <span class="spinner-border spinner-border-sm me-2 d-none" id="cp-btn-loader" role="status" aria-hidden="true"></span>
                                <i class="fa fa-floppy-disk me-2" id="cp-btn-icon"></i>
                                <span id="cp-btn-text">Update Password</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            $(function () {

                // ── Generate Password ─────────────────────────────────────
                function generatePassword() {
                    const upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    const lower = 'abcdefghijklmnopqrstuvwxyz';
                    const numbers = '0123456789';
                    const special = '#$%&*()';
                    const all = upper + lower + numbers + special;

                    let password = '';
                    password += upper[Math.floor(Math.random() * upper.length)];
                    password += lower[Math.floor(Math.random() * lower.length)];
                    password += numbers[Math.floor(Math.random() * numbers.length)];
                    password += special[Math.floor(Math.random() * special.length)];

                    while (password.length < 8) {
                        password += all[Math.floor(Math.random() * all.length)];
                    }

                    password = password.split('').sort(() => 0.5 - Math.random()).join('');

                    $('#cp-new_password').val(password);
                    $('#cp-new_password_confirmation').val(password);
                }

                // ── Copy Password ─────────────────────────────────────────
                function copyPassword() {
                    const value = $('#cp-new_password').val();
                    if (!value) return;

                    navigator.clipboard.writeText(value).then(function () {
                        showCopyFeedback();
                    }).catch(function () {
                        const field = document.getElementById('cp-new_password');
                        field.select();
                        field.setSelectionRange(0, 99999);
                        try {
                            document.execCommand('copy');
                            showCopyFeedback();
                        } catch (e) {
                            alert('Copy failed. Please copy manually.');
                        }
                    });
                }

                function showCopyFeedback() {
                    $('#cp-copy-feedback').removeClass('d-none');
                    setTimeout(function () {
                        $('#cp-copy-feedback').addClass('d-none');
                    }, 2000);
                }

                // ── Toggle Password Visibility ────────────────────────────
                function toggleVisibility() {
                    const $fields = $('#cp-new_password, #cp-new_password_confirmation');
                    const $btn = $('#cp-btn-toggle');
                    const $label = $btn.find('.toggle-label');
                    const $icon = $btn.find('i');

                    if ($('#cp-new_password').attr('type') === 'password') {
                        $fields.attr('type', 'text');
                        $label.text('Hide');
                        $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    } else {
                        $fields.attr('type', 'password');
                        $label.text('Show');
                        $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    }
                }

                // ── Button Events ─────────────────────────────────────────
                $('#cp-btn-generate').on('click', generatePassword);
                $('#cp-btn-copy').on('click', copyPassword);
                $('#cp-btn-toggle').on('click', toggleVisibility);

                // ── Form Submit ───────────────────────────────────────────
                $('#cpForm').on('submit', function (e) {
                    e.preventDefault();

                    const $form = $(this);
                    const $btn = $('#cp-btn-submit');
                    const $loader = $('#cp-btn-loader');
                    const $icon = $('#cp-btn-icon');

                    $form.find('.invalid-feedback').text('');
                    $form.find('.form-control').removeClass('is-invalid');
                    $loader.removeClass('d-none');
                    $icon.addClass('d-none');
                    $btn.prop('disabled', true);

                    $.ajax({
                        url: '{{ route('password.change') }}',
                        type: 'POST',
                        data: $form.serialize(),
                        success: function () {
                            window.location.href = '{{ route('login') }}';
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                $.each(xhr.responseJSON.errors, function (field, messages) {
                                    $form.find('[name="' + field + '"]').addClass('is-invalid');
                                    $('#cp-error-' + field).text(messages[0]);
                                });
                            }
                        },
                        complete: function () {
                            $loader.addClass('d-none');
                            $icon.removeClass('d-none');
                            $btn.prop('disabled', false);
                        }
                    });
                });

            });
        </script>
    </x-slot>

</x-app-layout>
