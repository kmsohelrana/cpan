{{-- NOTE: Please do not make any modifications or add any scripts in this file. This is a dynamic data table JS file. --}}

@props(['route' => ''])
{{ $slot }}
<script>
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        let payload = $('.filter-form').serialize();
        getDataList(page, payload);
    });

    $(document).on('click', '.reset-data', function (e) {
        $('.filter-form').trigger('reset');
        $('.filter-form').find('select.js-select2').val('').trigger('change');
        let payload = $('.filter-form').serialize();
        getDataList(1, payload);
    });

    $('.filter-form').on('submit', function (e) {
        e.preventDefault();
        let payload = $(this).serialize();
        getDataList(1, payload);
    });

    function getDataList(page = 1, payload = null) {
        $('.table-block').addClass('block-mode-loading');
        let url = "{{ route($route) }}" + "?page=" + page;
        $.ajax({
            url: url,
            type: "POST",
            data: payload ? payload + '&_token={{ csrf_token() }}' : '_token={{ csrf_token() }}',
            success: function (data) {
                $('.data-table').html(data);
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
</script>

{{-- NOTE: Please do not make any modifications or add any scripts in this file. This is a dynamic data table JS file. --}}
