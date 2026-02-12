<x-layouts::auth>
    <div class="auth-basic-wrapper d-flex align-items-center justify-content-center">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5 col-xxl-4 mx-auto">
                    <div class="card rounded-4 border-4 border-primary border-gradient-1">
                        <div class="card-body p-5">
                            <h4 class="fw-bold">Login Admin</h4>
                            <p class="mb-0">Masukkan akun anda</p>
                            <div class="form-body my-4">
                                <form id="loginForm" class="row g-3" method="post" action="{{ route('login.store') }}">
                                    @csrf
                                    <div class="col-12">
                                        <label for="inputEmailAddress" class="form-label">Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                                               id="inputEmailAddress"
                                               placeholder="Masukkan Email" required/>
                                        <div class="invalid-feedback">TEsting</div>
                                        @error('email')
                                        <div class="alert alert-border-danger alert-dismissible fade show mt-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="font-35 text-danger"><span
                                                        class="material-icons-outlined fs-2"></span>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="mb-0 text-danger">Error</h6>
                                                    <div class="">{{ $message }}</div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="inputChoosePassword" class="form-label">Password</label>
                                        <div class="input-group" id="show_hide_password">
                                            <input type="password" name="password" class="form-control border-end-0"
                                                   id="inputChoosePassword" placeholder="Masukkan Password" required/>
                                            <a href="javascript:0;" class="input-group-text bg-transparent"><i
                                                    class="bi bi-eye-slash-fill"></i></a>
                                            @error('password')
                                            <div
                                                class="alert alert-border-danger alert-dismissible fade show mt-2 mb-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="font-35 text-danger"><span
                                                            class="material-icons-outlined fs-2"></span>
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="mb-0 text-danger">Error</h6>
                                                        <div class="">{{ $message }}</div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                        aria-label="Close"></button>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-grid">
                                            <button id="submitBtn" class="btn btn-primary" type="submit">Login</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#show_hide_password a").on("click", function (event) {
                event.preventDefault();
                if ($("#show_hide_password input").attr("type") == "text") {
                    $("#show_hide_password input").attr("type", "password");
                    $("#show_hide_password i").addClass("bi-eye-slash-fill");
                    $("#show_hide_password i").removeClass("bi-eye-fill");
                } else if ($("#show_hide_password input").attr("type") == "password") {
                    $("#show_hide_password input").attr("type", "text");
                    $("#show_hide_password i").removeClass("bi-eye-slash-fill");
                    $("#show_hide_password i").addClass("bi-eye-fill");
                }
            });
        });

        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
        });
    </script>
</x-layouts::auth>
