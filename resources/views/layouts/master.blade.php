<div id="page-container" class="sidebar-o sidebar-dark side-scroll page-header-fixed main-content-boxed theme-mode remember-theme">
    @include('layouts.sidebar.index')
    @include('layouts.header')
    <main id="main-container">
        <div class="content">
            {{ $slot }}
        </div>
    </main>
</div>
