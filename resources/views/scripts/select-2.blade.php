<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script>
    function initSelect2() {
        $('.js-select2').not('.modal .js-select2').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    theme: 'bootstrap-5'
                    , width: '100%'
                , });
            }
        });
    }

    $(document).ready(function() {
        initSelect2();
        // Initialize Select2 inside modals on open with dropdownParent to prevent z-index clipping
        $('.modal').on('shown.bs.modal', function() {
            var $modal = $(this);
            $modal.find('.js-select2').each(function() {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        theme: 'bootstrap-5'
                        , width: '100%'
                        , dropdownParent: $modal
                    , });
                }
            });
        });

        // Destroy modal Select2 on close, then re-init non-modal ones
        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('.js-select2').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
            initSelect2();
        });
    });

</script>
