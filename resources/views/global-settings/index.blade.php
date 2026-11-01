<x-app-layout title="{{ $title }}">
    <x-slot name="styles">
        <link rel="stylesheet" href="{{ asset('assets/js/plugins/sweetalert2/sweetalert2.min.css') }}">
    </x-slot>

    <x-header title="{{ $title }}">
        <li class="breadcrumb-item"><a class="link-fx" href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">{{ $title }}</li>
    </x-header>

    <form method="POST" action="{{ route('global-setting.update') }}" id="global-setting-form">
        @csrf
        @method('PUT')

        @php $firstTab = true; @endphp

        <div class="block block-rounded mb-0">

            {{-- Tab Nav --}}
            <div class="block-content p-0">
                <ul class="nav nav-tabs nav-tabs-block" id="globalSettingTabs" role="tablist" style="--bs-nav-tabs-border-width:0;">
                    <style>
                        #globalSettingTabs .nav-link.active {
                            border-bottom: 3px solid #6c5ce7 !important;
                        }
                    </style>
                    @can(App\Enums\PermissionEnum::GLOBAL_SETTING_GENERAL)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $firstTab ? 'active' : '' }}" id="tab-general-setting-btn" data-bs-toggle="tab"
                            data-bs-target="#tab-general-setting" type="button" role="tab"
                            aria-controls="tab-general-setting" aria-selected="{{ $firstTab ? 'true' : 'false' }}">
                            <i class="fa fa-fw fa-gear me-1"></i> General
                        </button>
                    </li>
                    @php $firstTab = false; @endphp
                    @endcan

                    @can(App\Enums\PermissionEnum::GLOBAL_SETTING_SMS_TEMPLATE)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $firstTab ? 'active' : '' }}" id="tab-sms-template-btn" data-bs-toggle="tab"
                            data-bs-target="#tab-sms-template" type="button" role="tab"
                            aria-controls="tab-sms-template" aria-selected="{{ $firstTab ? 'true' : 'false' }}">
                            <i class="fa fa-fw fa-comment-sms me-1"></i> SMS Template
                        </button>
                    </li>
                    @php $firstTab = false; @endphp
                    @endcan

                    @can(App\Enums\PermissionEnum::GLOBAL_SETTING_PUSH_TEMPLATE)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $firstTab ? 'active' : '' }}" id="tab-push-template-btn" data-bs-toggle="tab"
                            data-bs-target="#tab-push-template" type="button" role="tab"
                            aria-controls="tab-push-template" aria-selected="{{ $firstTab ? 'true' : 'false' }}">
                            <i class="fa fa-fw fa-bell me-1"></i> Push Template
                        </button>
                    </li>
                    @php $firstTab = false; @endphp
                    @endcan

                    @can(App\Enums\PermissionEnum::GLOBAL_SETTING_EMAIL_TEMPLATE)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $firstTab ? 'active' : '' }}" id="tab-email-template-btn" data-bs-toggle="tab"
                            data-bs-target="#tab-email-template" type="button" role="tab"
                            aria-controls="tab-email-template" aria-selected="{{ $firstTab ? 'true' : 'false' }}">
                            <i class="fa fa-fw fa-envelope me-1"></i> Email Template
                        </button>
                    </li>
                    @php $firstTab = false; @endphp
                    @endcan
                </ul>
            </div>

            @php $firstPane = true; @endphp

            {{-- Tab Content --}}
            <div class="tab-content" id="globalSettingTabContent">

                {{-- General Setting Tab --}}
                @can(App\Enums\PermissionEnum::GLOBAL_SETTING_GENERAL)
                <div class="tab-pane fade {{ $firstPane ? 'show active' : '' }} p-4" id="tab-general-setting" role="tabpanel" aria-labelledby="tab-general-setting-btn">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label d-block fw-semibold">System SMS Notifications</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="system_sms_enabled" name="system_sms_enabled" value="1"
                                        {{ globalSetting('system_sms_enabled', '1') !== '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="system_sms_enabled">Enable system SMS notifications</label>
                                </div>
                                <small class="text-muted">Enables SMS delivery for password resets and verification.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label d-block fw-semibold">Push Notifications</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="system_push_enabled" name="system_push_enabled" value="1"
                                        {{ globalSetting('system_push_enabled', '1') !== '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="system_push_enabled">Enable FCM web push notifications</label>
                                </div>
                                <small class="text-muted">Broadcasts real-time alerts to logged-in administrator browsers.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label d-block fw-semibold">System Email Notifications</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="system_email_enabled" name="system_email_enabled" value="1"
                                        {{ globalSetting('system_email_enabled', '1') !== '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="system_email_enabled">Enable email notifications</label>
                                </div>
                                <small class="text-muted">Sends credentials and administrative notices via SMTP.</small>
                            </div>
                        </div>
                    </div>
                </div>
                @php $firstPane = false; @endphp
                @endcan

                {{-- SMS Template Tab --}}
                @can(App\Enums\PermissionEnum::GLOBAL_SETTING_SMS_TEMPLATE)
                <div class="tab-pane fade {{ $firstPane ? 'show active' : '' }} p-4" id="tab-sms-template" role="tabpanel" aria-labelledby="tab-sms-template-btn">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="sms_template_body">Default SMS Template</label>
                                <textarea class="form-control" id="sms_template_body" name="sms_template_body" rows="4" placeholder="Hello {name}, your security code is {code}.">{{ globalSetting('sms_template_body', '') }}</textarea>
                                <small class="text-muted">Placeholders: <code>{name}</code>, <code>{code}</code>, <code>{time}</code></small>
                            </div>
                        </div>
                    </div>
                </div>
                @php $firstPane = false; @endphp
                @endcan

                {{-- Push Template Tab --}}
                @can(App\Enums\PermissionEnum::GLOBAL_SETTING_PUSH_TEMPLATE)
                <div class="tab-pane fade {{ $firstPane ? 'show active' : '' }} p-4" id="tab-push-template" role="tabpanel" aria-labelledby="tab-push-template-btn">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="push_template_title">Push Notification Title</label>
                                <input type="text" class="form-control" id="push_template_title" name="push_template_title" value="{{ globalSetting('push_template_title', 'System Notification') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="push_template_body">Push Notification Body</label>
                                <textarea class="form-control" id="push_template_body" name="push_template_body" rows="3">{{ globalSetting('push_template_body', '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                @php $firstPane = false; @endphp
                @endcan

                {{-- Email Template Tab --}}
                @can(App\Enums\PermissionEnum::GLOBAL_SETTING_EMAIL_TEMPLATE)
                <div class="tab-pane fade {{ $firstPane ? 'show active' : '' }} p-4" id="tab-email-template" role="tabpanel" aria-labelledby="tab-email-template-btn">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="email_template_subject">Default Email Subject</label>
                                <input type="text" class="form-control" id="email_template_subject" name="email_template_subject" value="{{ globalSetting('email_template_subject', 'Account Notice') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="email_template_body">Email Template Body</label>
                                <textarea class="form-control" id="email_template_body" name="email_template_body" rows="6">{{ globalSetting('email_template_body', '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                @php $firstPane = false; @endphp
                @endcan

            </div>

            {{-- Sticky Footer --}}
            <div class="block-content border-top py-3 d-flex justify-content-end align-items-center">
                @can(App\Enums\PermissionEnum::GLOBAL_SETTING)
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fa fa-fw fa-save me-1"></i>
                    <span>Save Settings</span>
                </button>
                @endcan
            </div>

        </div>
    </form>

    <x-slot name="scripts">
        <script src="{{ asset('assets/js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.getElementById('global-setting-form').addEventListener('submit', function(e) {
                e.preventDefault();
                let form = this;
                let formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message || 'Settings updated successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update settings.'
                    });
                });
            });
        </script>
    </x-slot>

</x-app-layout>
