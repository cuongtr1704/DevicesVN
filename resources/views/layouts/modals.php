<!-- Login Modal -->
<div id="loginModal" class="auth-modal-container">
    <div class="auth-modal">
        <button type="button" class="modal-close" onclick="closeAllModals()">
            <i class="fas fa-times"></i>
        </button>
        <div class="row g-0">
            <!-- Left Side - Features -->
            <div class="col-lg-5 auth-left">
                <h2>Welcome Back!</h2>
                <p class="auth-subtitle">Sign in to access your account</p>
                
                <div class="auth-features">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Track Orders</h4>
                            <p>Monitor your purchases in real-time</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Save Favorites</h4>
                            <p>Keep your wishlist synced</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Fast Checkout</h4>
                            <p>Save time with stored info</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="col-lg-7 auth-right">
                <h3 class="auth-title">Sign In</h3>
                
                <div id="loginError" class="alert alert-danger" style="display: none;"></div>
                <form action="<?= url('auth/login') ?>" method="POST" class="auth-form" id="loginForm">
                    <div class="form-group">
                        <label for="login_email">
                            <i class="fas fa-envelope me-2"></i>Email Address
                        </label>
                        <input type="email" 
                               id="login_email" 
                               name="email" 
                               class="form-control" 
                               placeholder="Enter your email"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="login_password">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password" 
                                   id="login_password" 
                                   name="password" 
                                   class="form-control" 
                                   placeholder="Enter your password"
                                   required>
                            <button type="button" class="password-toggle" onclick="togglePassword('login_password', 'toggleIconLogin')">
                                <i class="fas fa-eye" id="toggleIconLogin"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <div class="form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="remember" 
                                   name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-auth">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </form>

                <div class="auth-footer">
                    <p>New to DevicesVN? <a href="javascript:void(0)" onclick="openRegisterModal()">Create an account</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div id="registerModal" class="auth-modal-container">
    <div class="auth-modal">
        <button type="button" class="modal-close" onclick="closeAllModals()">
            <i class="fas fa-times"></i>
        </button>
        <div class="row g-0">
            <!-- Left Side - Features -->
            <div class="col-lg-5 auth-left">
                <h2>Join DevicesVN</h2>
                <p class="auth-subtitle">Create your account and start shopping</p>
                
                <div class="auth-features">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Secure Shopping</h4>
                            <p>Your data is protected</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Fast Delivery</h4>
                            <p>Get devices delivered quickly</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="feature-text">
                            <h4>24/7 Support</h4>
                            <p>We're here to help anytime</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Register Form -->
            <div class="col-lg-7 auth-right">
                <h3 class="auth-title">Create Account</h3>
                
                <div id="registerError" class="custom-auth-alert custom-auth-alert-danger" style="display: none;">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div id="registerErrorMsg"></div>
                </div>
                <div id="registerSuccess" class="custom-auth-alert custom-auth-alert-success" style="display: none;">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="registerSuccessMsg"></span>
                </div>
                <form action="<?= url('auth/register') ?>" method="POST" class="auth-form" id="registerForm">
                    <div class="form-group">
                        <label for="reg_full_name">
                            <i class="fas fa-user me-2"></i>Full Name
                        </label>
                        <input type="text" 
                               id="reg_full_name" 
                               name="full_name" 
                               class="form-control" 
                               placeholder="Enter your full name"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="reg_email">
                            <i class="fas fa-envelope me-2"></i>Email Address
                        </label>
                        <input type="email" 
                               id="reg_email" 
                               name="email" 
                               class="form-control" 
                               placeholder="Enter your email"
                               required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reg_password">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="password-input-wrapper">
                                    <input type="password" 
                                           id="reg_password" 
                                           name="password" 
                                           class="form-control" 
                                           placeholder="Create password"
                                           minlength="8"
                                           required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('reg_password', 'toggleIconReg1')">
                                        <i class="fas fa-eye" id="toggleIconReg1"></i>
                                    </button>
                                </div>
                                <small class="password-hint">At least 8 characters</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reg_password_confirm">
                                    <i class="fas fa-lock me-2"></i>Confirm Password
                                </label>
                                <div class="password-input-wrapper">
                                    <input type="password" 
                                           id="reg_password_confirm" 
                                           name="confirm_password" 
                                           class="form-control" 
                                           placeholder="Confirm password"
                                           required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('reg_password_confirm', 'toggleIconReg2')">
                                        <i class="fas fa-eye" id="toggleIconReg2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="terms" 
                                   name="terms"
                                   required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="<?php echo url('about'); ?>" target="_blank">Terms of Service</a> and <a href="<?php echo url('about'); ?>" target="_blank">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-auth">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Already have an account? <a href="javascript:void(0)" onclick="openLoginModal()">Sign in</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Handle login form submission
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const errorDiv = document.getElementById('loginError');
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to reload page with logged in state
            window.location.reload();
        } else {
            // Show error message
            errorDiv.textContent = data.errors ? data.errors.join(', ') : data.message;
            errorDiv.style.display = 'block';
        }
    })
    .catch(error => {
        errorDiv.textContent = 'An error occurred. Please try again.';
        errorDiv.style.display = 'block';
    });
});

// Handle register form submission
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const errorDiv = document.getElementById('registerError');
    const errorMsg = document.getElementById('registerErrorMsg');
    const successDiv = document.getElementById('registerSuccess');
    const successMsg = document.getElementById('registerSuccessMsg');
    
    const password = document.getElementById('reg_password').value;
    const confirmPassword = document.getElementById('reg_password_confirm').value;
    
    // Client-side validation
    if (password.length < 8) {
        errorMsg.innerHTML = '<div>Password must be at least 8 characters</div>';
        errorDiv.style.display = 'flex';
        successDiv.style.display = 'none';
        return;
    }
    
    if (password !== confirmPassword) {
        errorMsg.innerHTML = '<div>Passwords do not match</div>';
        errorDiv.style.display = 'flex';
        successDiv.style.display = 'none';
        return;
    }
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message and switch to login
            successMsg.textContent = data.message;
            successDiv.style.display = 'flex';
            errorDiv.style.display = 'none';
            
            // Reset form
            form.reset();
            
            // After 2 seconds, switch to login modal
            setTimeout(() => {
                closeAllModals();
                openLoginModal();
            }, 2000);
        } else {
            // Show error message
            if (Array.isArray(data.errors)) {
                errorMsg.innerHTML = data.errors.map(err => `<div>${err}</div>`).join('');
            } else {
                errorMsg.innerHTML = `<div>${data.message || 'Registration failed'}</div>`;
            }
            errorDiv.style.display = 'flex';
            successDiv.style.display = 'none';
        }
    })
    .catch(error => {
        errorMsg.innerHTML = '<div>An error occurred. Please try again.</div>';
        errorDiv.style.display = 'flex';
        successDiv.style.display = 'none';
    });
});
</script>
