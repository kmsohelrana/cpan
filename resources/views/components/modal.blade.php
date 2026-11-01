@props(['title' => '', 'size' => 'md', 'backdrop' => false])
<div class="modal" {{ $backdrop ? "data-bs-backdrop=static" : '' }} id="{{ $title }}" tabindex="-1" role="dialog"
    aria-labelledby="{{ $title }}" aria-hidden="true">
    <div class="modal-dialog modal-{{ $size }}" role="document">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>