<script src="{{ asset('assets/js/flatpickr.min.js') }}"></script>
<script>
    function initFlatpickr($scope) {
        var $root = $scope || $(document);

        $root.find('.js-flatpickr-datetime').each(function() {
            if (!this._flatpickr) {
                flatpickr(this, {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i',
                    time_24hr: true,
                    minuteIncrement: 1,
                });
            }
        });

        $root.find('.js-flatpickr-date').each(function() {
            if (!this._flatpickr) {
                flatpickr(this, {
                    enableTime: false,
                    dateFormat: 'Y-m-d',
                });
            }
        });

        $root.find('.js-flatpickr-time').each(function() {
            if (!this._flatpickr) {
                flatpickr(this, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: 'H:i',
                    time_24hr: true,
                });
            }
        });
    }

    $(document).ready(function() {
        initFlatpickr();

        $('.modal').on('shown.bs.modal', function() {
            initFlatpickr($(this));
        });

        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('.js-flatpickr-datetime, .js-flatpickr-date, .js-flatpickr-time').each(function() {
                if (this._flatpickr) {
                    this._flatpickr.destroy();
                }
            });
            initFlatpickr();
        });
    });
</script>