<div id="page-container">
    <main id="main-container">
        <div class="bg-image" style="background-image: url({{asset('assets/media/photos/photo28@2x.jpg')}});">
            <div class="hero-static d-flex flex-column bg-body-extra-light">
                <div class="w-100 flex-grow-1 d-flex align-items-center justify-content-center">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </main>
</div>