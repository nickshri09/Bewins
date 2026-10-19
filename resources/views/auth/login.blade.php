@extends('layouts.auth')

@section('content')
<div class="login-wrapper">
    <!-- Left Side - Animated Background -->
    <div class="login-left">
        <div class="animated-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
        </div>
        <div class="brand-content">
            <img class="brand-logo animate__animated animate__fadeInDown" src="{{ get_logo() }}" alt="Logo">
            <h1 class="brand-title animate__animated animate__fadeInUp">{{ _lang('Welcome to') }} {{ get_option('company_name') }}</h1>
            <p class="brand-subtitle animate__animated animate__fadeInUp animate__delay-1s">{{ _lang('Your trusted financial partner') }}</p>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="login-right">
        <div class="login-form-container">
            <div class="form-header">
                <h2 class="animate__animated animate__fadeIn">{{ _lang('Sign In') }}</h2>
                <p class="animate__animated animate__fadeIn animate__delay-1s">{{ _lang('Enter your credentials to access your account') }}</p>
            </div>

            @if(Session::has('error'))
                <div class="alert alert-danger animate__animated animate__shakeX" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>{{ session('error') }}</strong>
                </div>
            @endif

            @if(Session::has('registration_success'))
                <div class="alert alert-success animate__animated animate__fadeIn" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <strong>{{ session('registration_success') }}</strong>
                </div>
            @endif

            @if(Session::has('otp_sent'))
                <div class="alert alert-info animate__animated animate__fadeIn" role="alert">
                    <i class="fas fa-info-circle"></i>
                    <strong>{{ session('otp_sent') }}</strong>
                </div>
            @endif

            <!-- Login Type Toggle -->
            <div class="login-toggle">
                <button type="button" class="toggle-btn active" data-form="normal">
                    <i class="fas fa-key"></i>
                    {{ _lang('Password Login') }}
                </button>
                <button type="button" class="toggle-btn" data-form="otp">
                    <i class="fas fa-envelope"></i>
                    {{ _lang('Email OTP') }}
                </button>
            </div>

            <!-- Normal Login Form -->
            <form method="POST" class="login-form animate__animated animate__fadeIn animate__delay-1s" id="normal-login-form" action="{{ route('login') }}">
                @csrf

                <!-- Email Input -->
                <div class="form-group">
                    <label class="form-label">{{ _lang('Email Address') }}</label>
                    <div class="input-group">
                        <span class="input-icon">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                               name="email" value="{{ old('email') }}" placeholder="{{ _lang('Enter your email') }}" required autofocus>
                    </div>
                    @if ($errors->has('email'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label class="form-label">{{ _lang('Password') }}</label>
                    <div class="input-group">
                        <span class="input-icon">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                               name="password" placeholder="{{ _lang('Enter your password') }}" required>
                        <span class="input-icon toggle-password">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                    @if ($errors->has('password'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <input type="hidden" name="g-recaptcha-response" id="recaptcha">
                @if ($errors->has('g-recaptcha-response'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                    </span>
                @endif

                <!-- Remember Me & Forgot Password -->
                <div class="form-options">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        {{ _lang('Remember Me') }}
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        {{ _lang('Forgot Password?') }}
                    </a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="login-button">
                    <span>{{ _lang('Sign In') }}</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <!-- Email OTP Login Form -->
            <form method="POST" class="login-form animate__animated animate__fadeIn animate__delay-1s" id="otp-login-form" action="{{ route('login.otp') }}" style="display: none;">
                @csrf

                <!-- Email Input for OTP -->
                <div class="form-group">
                    <label class="form-label">{{ _lang('Email Address') }}</label>
                    <div class="input-group">
                        <span class="input-icon">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input id="otp_email" type="email" class="form-control{{ $errors->has('otp_email') ? ' is-invalid' : '' }}"
                               name="otp_email" value="{{ old('otp_email') }}" placeholder="{{ _lang('Enter your email') }}" required>
                    </div>
                    @if ($errors->has('otp_email'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('otp_email') }}</strong>
                        </span>
                    @endif
                </div>

                <!-- Send OTP Button -->
                <button type="button" class="send-otp-button" id="send-otp-btn">
                    <span>{{ _lang('Send OTP') }}</span>
                    <i class="fas fa-paper-plane"></i>
                </button>

                <!-- OTP Input -->
                <div class="form-group" id="otp-input-group" style="display: none;">
                    <label class="form-label">{{ _lang('Enter OTP') }}</label>
                    <div class="input-group">
                        <span class="input-icon">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        <input id="otp_code" type="text" class="form-control{{ $errors->has('otp_code') ? ' is-invalid' : '' }}"
                               name="otp_code" placeholder="{{ _lang('Enter 6-digit OTP') }}" maxlength="6" pattern="[0-9]{6}">
                    </div>
                    @if ($errors->has('otp_code'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('otp_code') }}</strong>
                        </span>
                    @endif
                    <div class="otp-timer" id="otp-timer" style="display: none;">
                        <small>Resend OTP in <span id="countdown">60</span> seconds</small>
                    </div>
                </div>

                <!-- Verify OTP Button -->
                <button type="submit" class="login-button" id="verify-otp-btn" style="display: none;">
                    <span>{{ _lang('Verify OTP & Login') }}</span>
                    <i class="fas fa-check"></i>
                </button>
            </form>

            <!-- Register Link -->
            @if(get_option('member_signup') == 1)
                <div class="register-link">
                    {{ _lang("Don't have an account?") }}
                    <a href="{{ route('register') }}">{{ _lang('Create Account') }}</a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
body, html, #app, main, .container, .content, .auth-wrapper, .auth-content {
    margin: 0 !important;
    padding: 0 !important;
}

/* Reset default margins and padding */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    overflow: hidden;
}

/* Modern Login Page Styles */
.login-wrapper {
    height: 100vh;
    display: flex;
    background: #f8f9fa;
    overflow: hidden;
}

/* Left Side Styles */
.login-left {
    flex: 1;
    background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.animated-shapes {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
}

.shape {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 15s infinite;
}

.shape-1 {
    width: 300px;
    height: 300px;
    top: -150px;
    left: -150px;
    animation-delay: 0s;
}

.shape-2 {
    width: 200px;
    height: 200px;
    bottom: -100px;
    right: -100px;
    animation-delay: -5s;
}

.shape-3 {
    width: 150px;
    height: 150px;
    top: 50%;
    left: 50%;
    animation-delay: -2s;
}

.shape-4 {
    width: 100px;
    height: 100px;
    top: 20%;
    right: 20%;
    animation-delay: -7s;
}

@keyframes float {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(50px, 50px) rotate(90deg);
    }
    50% {
        transform: translate(0, 100px) rotate(180deg);
    }
    75% {
        transform: translate(-50px, 50px) rotate(270deg);
    }
}

.brand-content {
    position: relative;
    z-index: 1;
    text-align: center;
    color: white;
}

.brand-logo {
    max-width: 180px;
    margin-bottom: 20px;
    filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
}

.brand-title {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.brand-subtitle {
    font-size: 16px;
    opacity: 0.9;
}

/* Right Side Styles */
.login-right {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.login-form-container {
    width: 100%;
    max-width: 400px;
    background: white;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.form-header {
    text-align: center;
    margin-bottom: 25px;
}

.form-header h2 {
    font-size: 24px;
    font-weight: 700;
    color: #1a237e;
    margin-bottom: 8px;
}

.form-header p {
    color: #666;
    font-size: 14px;
}

/* Login Toggle Styles */
.login-toggle {
    display: flex;
    background: #f8f9fa;
    border-radius: 12px;
    padding: 4px;
    margin-bottom: 20px;
}

.toggle-btn {
    flex: 1;
    padding: 10px 15px;
    border: none;
    background: transparent;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #666;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.toggle-btn.active {
    background: #1a237e;
    color: white;
    box-shadow: 0 2px 8px rgba(26, 35, 126, 0.3);
}

.toggle-btn:hover:not(.active) {
    background: rgba(26, 35, 126, 0.1);
    color: #1a237e;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 6px;
    color: #333;
    font-weight: 500;
    font-size: 14px;
}

.input-group {
    position: relative;
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.input-group:focus-within {
    box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
}

.input-icon {
    padding: 12px;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toggle-password {
    cursor: pointer;
    transition: color 0.3s ease;
}

.toggle-password:hover {
    color: #1a237e;
}

.form-control {
    flex: 1;
    border: none;
    padding: 12px;
    background: transparent;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    box-shadow: none;
}

.custom-checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    font-size: 14px;
}

.custom-checkbox input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.checkmark {
    width: 18px;
    height: 18px;
    border: 2px solid #1a237e;
    border-radius: 4px;
    margin-right: 8px;
    position: relative;
    transition: all 0.3s ease;
}

.custom-checkbox input:checked ~ .checkmark {
    background: #1a237e;
}

.checkmark:after {
    content: '';
    position: absolute;
    display: none;
    left: 5px;
    top: 2px;
    width: 4px;
    height: 8px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.custom-checkbox input:checked ~ .checkmark:after {
    display: block;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 15px 0;
}

.forgot-link {
    color: #1a237e;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 14px;
}

.forgot-link:hover {
    color: #0d47a1;
}

.login-button, .send-otp-button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(45deg, #1a237e, #0d47a1);
    border: none;
    border-radius: 12px;
    color: white;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 20px;
}

.login-button:hover, .send-otp-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(26, 35, 126, 0.3);
}

.send-otp-button:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.otp-timer {
    margin-top: 10px;
    text-align: center;
    color: #666;
    font-size: 12px;
}

.register-link {
    text-align: center;
    margin-top: 20px;
    color: #666;
    font-size: 14px;
}

.register-link a {
    color: #1a237e;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.register-link a:hover {
    color: #0d47a1;
}

.alert {
    border-radius: 12px;
    padding: 12px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.alert i {
    font-size: 16px;
}

.alert-danger {
    background: #fee;
    color: #c33;
    border: 1px solid #fcc;
}

.alert-success {
    background: #efe;
    color: #3c3;
    border: 1px solid #cfc;
}

.alert-info {
    background: #eff;
    color: #33c;
    border: 1px solid #ccf;
}

/* Responsive Design */
@media (max-width: 992px) {
    .login-wrapper {
        flex-direction: column;
        height: 100vh;
        overflow: hidden;
    }

    .login-left {
        padding: 20px;
        min-height: 200px;
    }

    .login-right {
        padding: 20px;
        flex: 1;
    }

    .brand-title {
        font-size: 24px;
    }

    .brand-subtitle {
        font-size: 14px;
    }
}

@media (max-width: 576px) {
    .login-form-container {
        padding: 20px;
    }

    .form-header h2 {
        font-size: 20px;
    }
}
</style>

<!-- Add required JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"></script>

@if(get_option('enable_recaptcha', 0) == 1)
<script src="https://www.google.com/recaptcha/api.js?render={{ get_option('recaptcha_site_key') }}"></script>
<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('{{ get_option('recaptcha_site_key') }}', {action: 'login'}).then(function(token) {
            if (token) {
                document.getElementById('recaptcha').value = token;
            }
        });
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Password toggle functionality
        const togglePassword = document.querySelector('.toggle-password');
        const passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }

        // Login form toggle functionality
        const toggleBtns = document.querySelectorAll('.toggle-btn');
        const normalForm = document.getElementById('normal-login-form');
        const otpForm = document.getElementById('otp-login-form');

        toggleBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const formType = this.getAttribute('data-form');

                // Update active button
                toggleBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // Show/hide forms
                if (formType === 'normal') {
                    normalForm.style.display = 'block';
                    otpForm.style.display = 'none';
                } else {
                    normalForm.style.display = 'none';
                    otpForm.style.display = 'block';
                }
            });
        });

        // Send OTP functionality
        const sendOtpBtn = document.getElementById('send-otp-btn');
        const otpInputGroup = document.getElementById('otp-input-group');
        const verifyOtpBtn = document.getElementById('verify-otp-btn');
        const otpEmail = document.getElementById('otp_email');
        const countdown = document.getElementById('countdown');
        const otpTimer = document.getElementById('otp-timer');

        if (sendOtpBtn) {
            sendOtpBtn.addEventListener('click', function() {
                const email = otpEmail.value.trim();

                if (!email) {
                    alert('Please enter your email address');
                    return;
                }

                // Disable button and show loading
                this.disabled = true;
                this.innerHTML = '<span>Sending...</span><i class="fas fa-spinner fa-spin"></i>';

                // Send AJAX request to send OTP
                fetch('{{ route("login.send-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email: email })
                })
                .then(response => response.json())
                                .then(data => {
                    if (data.success) {
                        // Show OTP input
                        otpInputGroup.style.display = 'block';
                        verifyOtpBtn.style.display = 'block';

                        // Start countdown
                        let timeLeft = 60;
                        otpTimer.style.display = 'block';

                        const timer = setInterval(() => {
                            countdown.textContent = timeLeft;
                            timeLeft--;

                            if (timeLeft < 0) {
                                clearInterval(timer);
                                otpTimer.style.display = 'none';
                                sendOtpBtn.disabled = false;
                                sendOtpBtn.innerHTML = '<span>Resend OTP</span><i class="fas fa-paper-plane"></i>';
                            }
                        }, 1000);

                        // Show success message
                        if (data.debug_otp) {
                            alert('OTP generated: ' + data.debug_otp + '\n\n(Development mode - mail not configured)');
                        } else {
                            alert('OTP sent successfully to your email!');
                        }
                    } else {
                        alert(data.message || 'Failed to send OTP');
                        this.disabled = false;
                        this.innerHTML = '<span>Send OTP</span><i class="fas fa-paper-plane"></i>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to send OTP. Please try again.');
                    this.disabled = false;
                    this.innerHTML = '<span>Send OTP</span><i class="fas fa-paper-plane"></i>';
                });
            });
        }
    });
</script>
@endsection
