<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">

<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="dashboard-sidebar">
                <div class="user-info mb-4">
                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #2F1067, #151C32); color: white; border-radius: 50%; font-weight: 600; font-size: 32px;">
                        <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                    </div>
                    <h5 class="text-center mb-1"><?= escape($_SESSION['user_name']) ?></h5>
                    <p class="text-center text-muted small"><?= escape($_SESSION['user_email']) ?></p>
                </div>
                
                <ul class="nav flex-column dashboard-nav">
                    <li class="nav-item">
                        <a class="nav-link <?= ($activeSection ?? '') === 'overview' ? 'active' : '' ?>" href="<?= url('dashboard') ?>">
                            <i class="fas fa-home me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activeSection ?? '') === 'profile' ? 'active' : '' ?>" href="<?= url('dashboard/profile') ?>">
                            <i class="fas fa-user me-2"></i> My Profile
                        </a>
                    </li>
                    
                    <?php if (($userRole ?? 'customer') === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard/categories') ?>">
                                <i class="fas fa-tags me-2"></i> Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard/products') ?>">
                                <i class="fas fa-box me-2"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard/customers') ?>">
                                <i class="fas fa-users me-2"></i> Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard/orders') ?>">
                                <i class="fas fa-shopping-cart me-2"></i> Orders
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard/orders') ?>">
                                <i class="fas fa-shopping-bag me-2"></i> My Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard/wishlist') ?>">
                                <i class="fas fa-heart me-2"></i> Wishlist
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item mt-3">
                        <a class="nav-link text-danger" href="<?= url('logout') ?>" id="logoutLink">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="dashboard-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0"><i class="fas fa-user me-2"></i>My Profile</h2>
                    <button type="button" class="btn btn-outline-primary" id="editProfileBtn">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </button>
                </div>
                
                <?php if (isset($_SESSION['flash_profile'])): ?>
                    <?php 
                        $flash = $_SESSION['flash_profile'];
                        unset($_SESSION['flash_profile']);
                    ?>
                    <div class="custom-alert custom-alert-<?= $flash['type'] ?>">
                        <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                        <?= escape($flash['message']) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="custom-alert custom-alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div>
                            <?php foreach ($errors as $error): ?>
                                <div><?= escape($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="card profile-card">
                    <div class="card-body">
                        <form method="POST" action="<?= url('dashboard/profile') ?>" id="profileForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label"><i class="fas fa-user me-2"></i>Full Name</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="full_name" 
                                           name="full_name" 
                                           value="<?= escape($user['full_name']) ?>" 
                                           disabled
                                           required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label"><i class="fas fa-envelope me-2"></i>Email Address</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           value="<?= escape($user['email']) ?>" 
                                           disabled>
                                    <small class="text-muted">Email cannot be changed</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label"><i class="fas fa-phone me-2"></i>Phone Number</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="phone" 
                                           name="phone" 
                                           value="<?= escape($user['phone'] ?? '') ?>" 
                                           placeholder="+84 xxx xxx xxx"
                                           disabled>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label"><i class="fas fa-shield-alt me-2"></i>Account Type</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="<?= ucfirst($user['role']) ?>" 
                                           disabled>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Address</label>
                                <textarea class="form-control" 
                                          id="address" 
                                          name="address" 
                                          rows="3" 
                                          placeholder="Enter your full address"
                                          disabled><?= escape($user['address'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="d-flex gap-2" id="profileActions" style="display: none !important;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                                <button type="button" class="btn btn-secondary" id="cancelProfileBtn">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-4 password-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h5>
                    </div>
                    <div class="card-body">
                        <div id="passwordSuccess" class="custom-alert custom-alert-success" style="display: none;">
                            <i class="fas fa-check-circle me-2"></i>
                            <span id="passwordSuccessMsg"></span>
                        </div>
                        
                        <div id="passwordError" class="custom-alert custom-alert-danger" style="display: none;">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <div id="passwordErrorMsg"></div>
                        </div>
                        
                        <p class="text-muted mb-4">For your security, please enter your current password before setting a new one.</p>
                        
                        <form method="POST" action="<?= url('dashboard/profile') ?>" id="passwordForm">
                            <input type="hidden" name="action" value="change_password">
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label"><i class="fas fa-key me-2"></i>Current Password</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="current_password" 
                                       name="current_password" 
                                       placeholder="Enter your current password">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="new_password" class="form-label"><i class="fas fa-lock me-2"></i>New Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="new_password" 
                                           name="new_password" 
                                           minlength="8"
                                           placeholder="Min. 8 characters">
                                    <small class="text-muted">At least 8 characters</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label"><i class="fas fa-lock me-2"></i>Confirm New Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           placeholder="Re-enter new password">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('logoutLink').addEventListener('click', function(e) {
    e.preventDefault();
    
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = this.href;
    }
});

// Profile Edit Mode
const editProfileBtn = document.getElementById('editProfileBtn');
const cancelProfileBtn = document.getElementById('cancelProfileBtn');
const profileForm = document.getElementById('profileForm');
const profileActions = document.getElementById('profileActions');
const editableInputs = ['full_name', 'phone', 'address'];
const originalValues = {};

editableInputs.forEach(id => {
    const input = document.getElementById(id);
    if (input) originalValues[id] = input.value;
});

editProfileBtn.addEventListener('click', function() {
    editableInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) input.disabled = false;
    });
    editProfileBtn.style.display = 'none';
    profileActions.style.display = 'flex !important';
    profileActions.style.setProperty('display', 'flex', 'important');
});

cancelProfileBtn.addEventListener('click', function() {
    editableInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.value = originalValues[id];
            input.disabled = true;
        }
    });
    editProfileBtn.style.display = 'block';
    profileActions.style.display = 'none !important';
    profileActions.style.setProperty('display', 'none', 'important');
});

// Password form validation
const passwordForm = document.getElementById('passwordForm');
const currentPassword = document.getElementById('current_password');
const newPassword = document.getElementById('new_password');
const confirmPassword = document.getElementById('confirm_password');
const passwordError = document.getElementById('passwordError');
const passwordErrorMsg = document.getElementById('passwordErrorMsg');
const passwordSuccess = document.getElementById('passwordSuccess');
const passwordSuccessMsg = document.getElementById('passwordSuccessMsg');

passwordForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    passwordError.style.display = 'none';
    passwordSuccess.style.display = 'none';
    
    let errors = [];
    
    if (!currentPassword.value) {
        errors.push('Current password is required');
    }
    
    if (newPassword.value.length < 8) {
        errors.push('New password must be at least 8 characters');
    }
    
    if (newPassword.value !== confirmPassword.value) {
        errors.push('New password and confirm password do not match');
    }
    
    if (errors.length > 0) {
        passwordErrorMsg.innerHTML = errors.map(err => `<div>${err}</div>`).join('');
        passwordError.style.display = 'block';
        passwordError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    
    // Submit the form
    this.submit();
});

// Check for password success from PHP
<?php if (isset($_SESSION['flash_password'])): ?>
    <?php 
        $flash = $_SESSION['flash_password'];
        unset($_SESSION['flash_password']);
    ?>
    passwordSuccessMsg.textContent = '<?= addslashes($flash['message']) ?>';
    passwordSuccess.style.display = 'block';
    passwordSuccess.scrollIntoView({ behavior: 'smooth', block: 'center' });
    currentPassword.value = '';
    newPassword.value = '';
    confirmPassword.value = '';
<?php endif; ?>
</script>

<style>
.dashboard-sidebar {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    position: sticky;
    top: 20px;
}

.dashboard-nav .nav-link {
    color: #333;
    padding: 12px 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
    margin-bottom: 5px;
    font-weight: 500;
}

.dashboard-nav .nav-link:hover {
    background: #f8f9fa;
    color: #2F1067;
    transform: translateX(5px);
}

.dashboard-nav .nav-link.active {
    background: linear-gradient(135deg, #2F1067, #151C32);
    color: white;
    box-shadow: 0 2px 8px rgba(47, 16, 103, 0.3);
}

.dashboard-content {
    background: white;
    border-radius: 12px;
    padding: 35px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.profile-card, .password-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
}

.profile-card:hover, .password-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    border-bottom: 2px solid #e9ecef;
    padding: 20px 25px;
    border-radius: 12px 12px 0 0 !important;
}

.card-header h5 {
    color: #2F1067;
    font-weight: 600;
}

.card-body {
    padding: 30px 25px;
}

.form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 10px 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #2F1067;
    box-shadow: 0 0 0 0.2rem rgba(47, 16, 103, 0.15);
}

.form-control:disabled {
    background-color: #f8f9fa;
    cursor: not-allowed;
}

textarea.form-control {
    resize: vertical;
}

.btn {
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #2F1067, #151C32);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1a0a3d, #0a0f1f);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(47, 16, 103, 0.3);
}

.btn-outline-primary {
    color: #2F1067;
    border: 2px solid #2F1067;
}

.btn-outline-primary:hover {
    background: #2F1067;
    color: white;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #6c757d;
    border: none;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107, #ff9800);
    border: none;
    color: #000;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #ff9800, #f57c00);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
}

.custom-alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    font-weight: 500;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.custom-alert-success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
    border-left: 4px solid #28a745;
}

.custom-alert-danger {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.custom-alert i {
    font-size: 20px;
    margin-top: 2px;
}

.password-hint {
    color: #6c757d;
    font-size: 0.875rem;
    margin-top: 5px;
}

.user-info .avatar-circle {
    transition: all 0.3s ease;
}

.user-info .avatar-circle:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(47, 16, 103, 0.3);
}

#profileActions {
    margin-top: 15px;
}
</style>
