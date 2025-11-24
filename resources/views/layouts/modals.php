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
                
                <form action="<?= BASE_URL ?>auth/login" method="POST" class="auth-form">
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
                        <a href="<?= BASE_URL ?>auth/forgot-password" class="forgot-link">
                            Forgot Password?
                        </a>
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
                
                <form action="<?= BASE_URL ?>auth/register" method="POST" class="auth-form" id="registerForm">
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

                    <div class="form-group">
                        <label for="reg_phone">
                            <i class="fas fa-phone me-2"></i>Phone Number
                        </label>
                        <input type="tel" 
                               id="reg_phone" 
                               name="phone" 
                               class="form-control" 
                               placeholder="Enter your phone number">
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
                                           name="password_confirm" 
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
                                I agree to the <a href="<?= BASE_URL ?>pages/terms" target="_blank">Terms of Service</a> and <a href="<?= BASE_URL ?>pages/privacy" target="_blank">Privacy Policy</a>
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
