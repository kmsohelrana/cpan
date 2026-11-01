<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/select2-bootstrap-5-theme.min.css') }}">

<style>
    .select2-container--bootstrap-5 .select2-selection {
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
        font-size: 0.860rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        border: var(--bs-border-width) solid #c3c7cd;
    }

    #page-container.dark-mode .select2-container--bootstrap-5 .select2-selection {
        color: #bec8d5;
        background-color: #171e28;
        border-color: #2e3c51
    }

    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #91abeb;
        outline: 0;
        box-shadow: 0 0 0 0.1rem rgba(35, 86, 215, 0.25);
    }

    .select2-container--bootstrap-5 .select2-dropdown .select2-search .select2-search__field:focus {
        border-color: #91abeb;
        outline: 0;
        box-shadow: 0 0 0 0.1rem rgba(35, 86, 215, 0.25);
    }

    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option {
        font-size: 0.860rem !important;
    }

    #page-container.dark-mode .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #bec8d5;
    }

    body:has(#page-container.dark-mode) .select2-container--bootstrap-5 .select2-dropdown {
        background-color: #1F2937;
        border-color: #2e3c51;
        color: #bec8d5;
    }

    body:has(#page-container.dark-mode) .select2-container--bootstrap-5 .select2-dropdown .select2-search .select2-search__field {
        background-color: #171e28;
        border-color: #2e3c51;
        color: #bec8d5;
    }

    body:has(#page-container.dark-mode) .select2-container--bootstrap-5 .select2-dropdown .select2-results__option {
        color: #bec8d5;
    }

    body:has(#page-container.dark-mode) .select2-container--bootstrap-5 .select2-dropdown .select2-results__option--highlighted {
        background-color: #2356d7;
        color: #fff;
    }

    body:has(#page-container.dark-mode) .select2-container--bootstrap-5 .select2-dropdown .select2-results__option--selected {
        background-color: #253041;
        color: #e8edf3;
    }
</style>
