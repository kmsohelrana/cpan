<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mb-3">
    <div class="flex-grow-1 d-none d-sm-block">
        <h1 class="h4 fw-bold mb-0">
            {{ $title }}
        </h1>
    </div>
    <nav class="flex-shrink-0 pe-2" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
            {{ $slot }}
        </ol>
    </nav>
</div>