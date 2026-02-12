<div class="modal fade" id="{{ $modalId ?? '' }}" data-bs-backdrop="static" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>
