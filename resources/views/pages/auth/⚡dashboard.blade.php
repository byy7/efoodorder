<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

<div class="main-content">
    {{-- BREADCRUMB --}}
    <x-breadcrumb :heading="__('Dashboard')" :sub-heading="__('Dashboard')"></x-breadcrumb>
    {{-- END BREADCRUMB --}}

    {{-- DATE FILTER --}}
    <div class="row mb-3">
        <div class="col-auto">
            <label>Tanggal Awal</label>
            <input type="text" value="{{ today()->startOfMonth()->format('Y-m-d') }}" class="form-control datepicker">
        </div>
        <div class="col-auto">
            <label>Tanggal Akhir</label>
            <input type="text" value="{{ today()->endOfMonth()->format('Y-m-d') }}" class="form-control datepicker">
        </div>
    </div>
    {{-- END DATE FILTER --}}

    {{-- SUMMARY--}}
    <div class="row">
        <div class="col-12">
            <h5 class="mb-3 fw-bold">RINGKASAN PESANAN</h5>
        </div>
        <div class="col-12 col-lg-3 col-xxl-3 d-flex">
            <div class="card rounded-4 w-100">
                <div class="card-body">
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <div
                            class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary">
                            <span class="material-icons-outlined fs-5">shopping_cart</span>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-0">248k</h4>
                        <p class="mb-3">Pesanan Diproses</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 col-xxl-3 d-flex">
            <div class="card rounded-4 w-100">
                <div class="card-body">
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <div
                            class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 text-warning">
                            <span class="material-icons-outlined fs-5">leaderboard</span>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-0">$47.6k</h4>
                        <p class="mb-3">Pesanan Selesai</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 col-xxl-3 d-flex">
            <div class="card rounded-4 w-100">
                <div class="card-body">
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <div
                            class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-info bg-opacity-10 text-info">
                            <span class="material-icons-outlined fs-5">visibility</span>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-0">189K</h4>
                        <p class="mb-3">Pesanan Dibatalkan</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 col-xxl-3 d-flex">
            <div class="card rounded-4 w-100">
                <div class="card-body">
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <div
                            class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success">
                            <span class="material-icons-outlined fs-5">attach_money</span>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-0">24.6%</h4>
                        <p class="mb-3">Total Pendapatan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END SUMMARY--}}

    {{-- LATEST ORDER --}}
    <div class="row">
        <div class="col-12">
            <h5 class="mb-3 fw-bold">PESANAN TERBARU</h5>
        </div>
        <div class="col-12 d-flex">
            <div class="card rounded-4 w-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="">
                            <h5 class="mb-0">Daftar Pesanan Terbaru</h5>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                               data-bs-toggle="dropdown">
                                <span class="material-icons-outlined fs-5">more_vert</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                                <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                                <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Source Name</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <div class="">
                                        <h6 class="mb-0">10 Sep,2024</h6>
                                        <p class="mb-0">8:20 PM</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center flex-row gap-3">
                                        <div class="">
                                            <img src="assets/images/apps/paypal.png" width="35" alt="">
                                        </div>
                                        <div class="">
                                            <h6 class="mb-0">Paypal</h6>
                                            <p class="mb-0">Business Plan</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="card-lable bg-success text-success bg-opacity-10">
                                        <p class="text-success mb-0">Paid</p>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="mb-0">$5897</h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="">
                                        <h6 class="mb-0">10 Sep,2024</h6>
                                        <p class="mb-0">8:20 PM</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center flex-row gap-3">
                                        <div class="">
                                            <img src="assets/images/apps/13.png" width="35" alt="">
                                        </div>
                                        <div class="">
                                            <h6 class="mb-0">Visa</h6>
                                            <p class="mb-0">Business Plan</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="card-lable bg-danger text-danger bg-opacity-10">
                                        <p class="text-danger mb-0">Unpaid</p>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="mb-0">$9638</h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="">
                                        <h6 class="mb-0">10 Sep,2024</h6>
                                        <p class="mb-0">8:20 PM</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center flex-row gap-3">
                                        <div class="">
                                            <img src="assets/images/apps/behance.png" width="35" alt="">
                                        </div>
                                        <div class="">
                                            <h6 class="mb-0">Behance</h6>
                                            <p class="mb-0">Business Plan</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="card-lable bg-success text-success bg-opacity-10">
                                        <p class="text-success mb-0">Paid</p>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="mb-0">$9638</h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="">
                                        <h6 class="mb-0">10 Sep,2024</h6>
                                        <p class="mb-0">8:20 PM</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center flex-row gap-3">
                                        <div class="">
                                            <img src="assets/images/apps/07.png" width="35" alt="">
                                        </div>
                                        <div class="">
                                            <h6 class="mb-0">Spotify</h6>
                                            <p class="mb-0">Business Plan</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="card-lable bg-success text-success bg-opacity-10">
                                        <p class="text-success mb-0">Paid</p>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="mb-0">$9638</h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="">
                                        <h6 class="mb-0">10 Sep,2024</h6>
                                        <p class="mb-0">8:20 PM</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center flex-row gap-3">
                                        <div class="">
                                            <img src="assets/images/apps/05.png" width="35" alt="">
                                        </div>
                                        <div class="">
                                            <h6 class="mb-0">Google</h6>
                                            <p class="mb-0">Business Plan</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="card-lable bg-danger text-danger bg-opacity-10">
                                        <p class="text-danger mb-0">Unpaid</p>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="mb-0">$9638</h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="">
                                        <h6 class="mb-0">10 Sep,2024</h6>
                                        <p class="mb-0">8:20 PM</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center flex-row gap-3">
                                        <div class="">
                                            <img src="assets/images/apps/apple.png" width="35" alt="">
                                        </div>
                                        <div class="">
                                            <h6 class="mb-0">Apple</h6>
                                            <p class="mb-0">Business Plan</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="card-lable bg-success text-success bg-opacity-10">
                                        <p class="text-success mb-0">Paid</p>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="mb-0">$9638</h5>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END LATEST ORDER --}}
</div>

<script src="{{ asset('assets/template/vertical-menu/assets/plugins/apexchart/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/template/vertical-menu/assets/plugins/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('assets/template/vertical-menu/assets/js/dashboard2.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(".data-attributes span").peity("donut")
</script>
<script>
    $(".datepicker").flatpickr();
</script>
