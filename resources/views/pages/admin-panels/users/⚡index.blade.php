<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="main-content">
    {{-- BREADCRUMB --}}
    <x-breadcrumb :heading="__('Pengguna')" :sub-heading="__('Data Pengguna')"></x-breadcrumb>
    {{-- END BREADCRUMB --}}

    <div class="row g-3">
        <div class="col-auto">
            <div class="d-flex align-items-center gap-2 justify-content-lg-end">
                <div class="position-relative">
                    <input class="form-control px-5" type="search" placeholder="Cari...">
                    <span
                        class="material-icons-outlined position-absolute ms-3 translate-middle-y start-0 top-50 fs-5">search</span>
                </div>
                <button class="btn btn-primary px-4"><i class="bi bi-plus-lg me-2"></i>Tambah Data</button>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive white-space-nowrap">
                <table class="table align-middle">
                    <thead class="sticky-top table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Dibuat Pada</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @for($i=1;$i<=10;$i++)
                        <tr>
                            <td>1</td>
                            <td>Nama</td>
                            <td>Email</td>
                            <td>Tanggal</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-filter dropdown-toggle dropdown-toggle-nocaret"
                                            type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Action</a></li>
                                        <li><a class="dropdown-item" href="#">Another action</a></li>
                                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
