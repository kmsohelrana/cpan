<link rel="stylesheet" href="{{ asset('assets/css/flatpickr.min.css') }}">

<style>
    .flatpickr-input {
        border: var(--bs-border-width) solid #c3c7cd !important;
    }

    #page-container.dark-mode .flatpickr-input {
        border: var(--bs-border-width) solid #2e3c51 !important;
    }

    .flatpickr-input:focus {
        border-color: #91abeb !important;
        outline: 0;
        box-shadow: 0 0 0 0.1rem rgba(35, 86, 215, 0.25) !important;
    }

    /* Flatpickr dark mode — calendar appends to <body>, use :has() to reach it */
    body:has(#page-container.dark-mode) .flatpickr-calendar {
        background: #1F2937;
        border-color: #2a384b;
        box-shadow: 0 0.25rem 2rem rgba(0, 0, 0, 0.35);
        color: #bec8d5;
    }

    body:has(#page-container.dark-mode) .flatpickr-calendar.arrowTop::before,
    body:has(#page-container.dark-mode) .flatpickr-calendar.arrowTop::after {
        border-bottom-color: #2a384b;
    }

    body:has(#page-container.dark-mode) .flatpickr-calendar.arrowBottom::before,
    body:has(#page-container.dark-mode) .flatpickr-calendar.arrowBottom::after {
        border-top-color: #2a384b;
    }

    body:has(#page-container.dark-mode) .flatpickr-months {
        background: #253041;
        border-radius: 5px 5px 0 0;
    }

    body:has(#page-container.dark-mode) .flatpickr-months .flatpickr-month,
    body:has(#page-container.dark-mode) .flatpickr-months .flatpickr-prev-month,
    body:has(#page-container.dark-mode) .flatpickr-months .flatpickr-next-month {
        color: #d1d8de;
        fill: #d1d8de;
    }

    body:has(#page-container.dark-mode) .flatpickr-months .flatpickr-prev-month:hover svg,
    body:has(#page-container.dark-mode) .flatpickr-months .flatpickr-next-month:hover svg {
        fill: #fff;
    }

    body:has(#page-container.dark-mode) .flatpickr-current-month input.cur-year,
    body:has(#page-container.dark-mode) .flatpickr-current-month .flatpickr-monthDropdown-months {
        color: #d1d8de;
        background: transparent;
    }

    body:has(#page-container.dark-mode) .flatpickr-weekdays {
        background: #253041;
    }

    body:has(#page-container.dark-mode) span.flatpickr-weekday {
        background: #253041;
        color: #90a0b7;
    }

    body:has(#page-container.dark-mode) .flatpickr-day {
        color: #bec8d5;
    }

    body:has(#page-container.dark-mode) .flatpickr-day:hover,
    body:has(#page-container.dark-mode) .flatpickr-day:focus {
        background: #2e3c51;
        border-color: #2e3c51;
        color: #d1d8de;
    }

    body:has(#page-container.dark-mode) .flatpickr-day.today {
        border-color: #2356d7;
        color: #bec8d5;
    }

    body:has(#page-container.dark-mode) .flatpickr-day.today:hover {
        background: #2356d7;
        border-color: #2356d7;
        color: #fff;
    }

    body:has(#page-container.dark-mode) .flatpickr-day.selected,
    body:has(#page-container.dark-mode) .flatpickr-day.startRange,
    body:has(#page-container.dark-mode) .flatpickr-day.endRange {
        background: #2356d7;
        border-color: #2356d7;
        color: #fff;
    }

    body:has(#page-container.dark-mode) .flatpickr-day.inRange {
        background: #253041;
        border-color: #253041;
        color: #bec8d5;
        box-shadow: -5px 0 0 #253041, 5px 0 0 #253041;
    }

    body:has(#page-container.dark-mode) .flatpickr-day.prevMonthDay,
    body:has(#page-container.dark-mode) .flatpickr-day.nextMonthDay {
        color: #4e6179;
    }

    body:has(#page-container.dark-mode) .flatpickr-day.disabled,
    body:has(#page-container.dark-mode) .flatpickr-day.disabled:hover {
        color: #314158;
    }

    body:has(#page-container.dark-mode) .flatpickr-innerContainer,
    body:has(#page-container.dark-mode) .flatpickr-rContainer {
        background: #1F2937;
    }

    body:has(#page-container.dark-mode) .flatpickr-time {
        background: #1F2937;
        border-top-color: #2a384b;
    }

    body:has(#page-container.dark-mode) .flatpickr-time input,
    body:has(#page-container.dark-mode) .flatpickr-time .flatpickr-am-pm {
        color: #bec8d5;
        background: transparent;
    }

    body:has(#page-container.dark-mode) .flatpickr-time input:hover,
    body:has(#page-container.dark-mode) .flatpickr-time .flatpickr-am-pm:hover {
        background: #2e3c51;
    }

</style>
