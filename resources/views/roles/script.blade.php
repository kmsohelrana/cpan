<x-t-script route='roles.list'>
    <script src="{{ asset('assets/js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
</x-t-script>

@foreach(['success', 'error', 'warning', 'info'] as $msg)
@if(session()->has($msg))
<script>
    showToast('{{ $msg }}', '{{ session()->get($msg) }}')

</script>
@endif
@endforeach

<script type="module">
    import { ShowFn } from "{{ asset('js/app.js') }}";
    $(document).on('click', '.show-data', function() {
        ShowFn("{{ route('roles.show', ':dataId') }}".replace(':dataId', $(this).data('row')));
    });
</script>
