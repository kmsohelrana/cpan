<x-t-script route='notifications.list'>
    @include('scripts.flatpickr')
    <script src="{{ asset('assets/js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    @include('scripts.select-2')
</x-t-script>

<script type="module">
    import { ConfirmActionFn } from "{{ asset('js/app.js') }}";

    $(document).on('click', '.mark-read-data', function () {
        var row = $(this).data('row');
        var url = "{{ route('notifications.mark-read', ':data') }}".replace(':data', row);
        ConfirmActionFn(url, '{{ csrf_token() }}', 'POST', 'Mark this notification as read?', 'Yes, Mark Read', 'No, Cancel');
    });
</script>
