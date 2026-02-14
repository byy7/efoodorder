<?php

use App\Livewire\Forms\Customer\RegistrationForm;
use App\Models\Customer;
use Livewire\Component;

new class extends Component {
    public RegistrationForm $form;
    public string $type = '';

    public function mount(string $type): void
    {
        $this->type = $type;
        $this->form->setData(new Customer());
    }
};
?>

<div>
    <div class="card shadow-none bg-transparent bg-none">
        <div class="card-body">
            <div class="form-body">
                <h4 class="mb-1">Welcome Back</h4>
                <p>Login to your account to continue</p>
                <form class="mt-4">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="position-relative">
                                <label for="enterMobile" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control form-control-lg ps-5" id="enterMobile" placeholder="Mobile Number">
                                <span class="material-icons-outlined position-absolute top-50 start-0 translate-middle-x ms-4">phone_iphone</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="position-relative">
                                <label for="InputPassword" class="form-label">Password</label>
                                <input type="text" class="form-control form-control-lg ps-5" id="InputPassword" placeholder="Password">
                                <span class="material-icons-outlined position-absolute top-50 start-0 translate-middle-x ms-4">lock</span>
                                <span class="material-icons-outlined position-absolute top-50 end-0 translate-middle-x me-0">visibility_off</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">Remember</label>
                                </div>
                                <div><a href="verify-account.html" class="forgot-link">Forgot Password?</a></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-grid">
                                <a href="home.html" class="btn btn-grd btn-lg btn-grd-primary">Login</a>
                            </div>
                            <div class="separator my-4">
                                <div class="line"></div>
                                <p class="mb-0 fw-bold px-3">OR</p>
                                <div class="line"></div>
                            </div>
                            <div class="d-flex gap-3 justify-content-center">
                                <a href="javascript:;" class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-grd-danger">
                                    <i class="bi bi-google fs-5 text-white"></i>
                                </a>
                                <a href="javascript:;" class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-grd-deep-blue">
                                    <i class="bi bi-facebook fs-5 text-white"></i>
                                </a>
                                <a href="javascript:;" class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-grd-info">
                                    <i class="bi bi-linkedin fs-5 text-white"></i>
                                </a>
                                <a href="javascript:;" class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-grd-royal">
                                    <i class="bi bi-github fs-5 text-white"></i>
                                </a>
                            </div>
                            <div class="text-center mt-3">
                                <p class="mb-0 rounded"><span class="me-1">Dont have an account ?</span><a href="register.html">Register</a></p>
                            </div>
                        </div>

                    </div><!--end row-->
                </form>
            </div>
        </div>
    </div>
</div>
