<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

new #[Layout('layouts.app-customer')]
class extends Component {
    //
};
?>

<div>
    <x-header :heading="__('NEED & NOTES CAFFE')"></x-header>

    <div class="card shadow-none bg-transparent bg-none">
        <div class="card-body">
            <div class="text-center">
                <img src="{{ asset('assets/img/logo.webp') }}" class="img-fluid" alt="Logo" width="150">
            </div>
            <div class="row">
                <h4 class="mb-1">Selamat Datang 👋</h4>
                <p>Silahkan pilih jenis layanan</p>
                @if(session('error'))
                    <div class="alert alert-danger border-0 bg-grd-danger alert-dismissible fade show">
                        <div class="d-flex align-items-center">
                            <div class="font-35 text-white"><span class="material-icons-outlined fs-2">report_gmailerrorred</span>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 text-white">Notifikasi</h6>
                                <div class="text-white">{{ session('error') }}</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="mt-2">
                    <div class="col-12 mb-4">
                        <div class="d-grid">
                            <a href="{{ route('registrations', 'dine-in') }}" class="btn btn-grd btn-lg btn-grd-primary"
                               wire:navigate>DINE IN</a>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="d-grid">
                            <a href="{{ route('registrations', 'take-away') }}" class="btn btn-grd btn-lg btn-grd-info"
                               wire:navigate>TAKE AWAY</a>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <p>Selamat menikmati layanan kami 😄</p>
                </div>
            </div>
        </div>
    </div>
</div>
