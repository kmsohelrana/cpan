<x-t-script route='users.list'>
    <script src="{{ asset('assets/js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    @include('scripts.select-2')
</x-t-script>

<script type="module">
    import { ShowFn, CreateDataFn, UpdateDataFn, SubmitFn, DeleteDataFn, ResetFormErrorFn } from "{{ asset('js/app.js') }}";

    $(document).on('click', '.show-data', function() {
        ShowFn("{{ route('users.show', ':dataId') }}".replace(':dataId', $(this).data('row')));
    });

    $(document).on('click', '.create-data', function() {
        let $modal = $('#form-modal');
        let form = $modal.find('.data-form');
        CreateDataFn($modal, form, "Add New User");
        ResetFormErrorFn(form);
        form.find('#role').val(null).trigger('change');
        form.find('#status').val(1).trigger('change');
        form.find('.hover-data small').addClass('d-none');
        form.find('.hover-data .preview img').addClass('d-none').attr('src', '#');
    });

    $(document).on('click', '.update-data', function() {
        let $modal = $('#form-modal');
        let dataId = $(this).data('row');
        let form = $modal.find('.data-form');
        let url = "{{ route('users.edit', ':dataId') }}".replace(':dataId', dataId);

        UpdateDataFn($modal, form, url, "Edit User", function(form, data) {
            form.attr('data-id', data.id);
            form.find('#name').val(data.name);
            form.find('#employee_id').val(data.employee_id || '');
            form.find('#mobile').val(data.mobile);
            form.find('#email').val(data.email);
            let selectedRole = data.roles.length > 0 ? data.roles[0].name : '';
            form.find('#role').val(selectedRole).trigger('change');
            form.find('#status').val(data.status).trigger('change');
            form.find('.hover-data small').addClass('d-none');
            form.find('.hover-data .preview img').addClass('d-none');
            if (data.image) {
                form.find('.hover-data small').removeClass('d-none');
                form.find('.hover-data small a').attr('href', data.image);
                form.find('.hover-data .preview img').removeClass('d-none').attr('src', data.image);
            }
        });
    });

    $(document).on('submit', '.data-form', function(e) {
        e.preventDefault();
        let form = $(this);
        let dataId = form.attr('data-id');
        let url = "{{ route('users.store') }}";
        let method = "POST";
        let formData = new FormData(this);
        if (dataId) {
            url = "{{ route('users.update', ':dataId') }}".replace(':dataId', dataId);
            method = "PUT";
        }
        SubmitFn(form, url, formData, method, csrfToken());
    });

    $(document).on('click', '.delete-data', function() {
        var row = $(this).data('row');
        var url = "{{ route('users.destroy', ':data') }}".replace(':data', row);
        DeleteDataFn(url, csrfToken());
    });

    $(document).on('click', '.update-password', function() {
        var userId = $(this).data('row');
        var url = "{{ route('users.update-password', ':userId') }}".replace(':userId', userId);

        Swal.fire({
            title: 'Reset Password',
            text: 'A new password will be generated for this user. Do you want to continue?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, reset it!',
            cancelButtonText: 'Cancel',
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { _token: csrfToken() },
                    success: function(response) {
                        showToast('success', response.message || 'Password reset successfully.');
                        getDataList();
                    },
                    error: function(xhr) {
                        var response = JSON.parse(xhr.responseText);
                        showToast('error', response.message || 'Something went wrong.');
                    }
                });
            }
        });
    });

    function csrfToken() {
        return '{{ csrf_token() }}';
    }
</script>
