<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">
<link rel="stylesheet" href="<?= asset('css/dashboard-profile.css') ?>">

<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../components/dashboard-sidebar.php'; ?>
        
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

// Check for password flash message from PHP
<?php if (isset($_SESSION['flash_password'])): ?>
    <?php 
        $flash = $_SESSION['flash_password'];
        unset($_SESSION['flash_password']);
    ?>
    <?php if ($flash['type'] === 'success'): ?>
        passwordSuccessMsg.textContent = '<?= addslashes($flash['message']) ?>';
        passwordSuccess.style.display = 'block';
        passwordSuccess.scrollIntoView({ behavior: 'smooth', block: 'center' });
        currentPassword.value = '';
        newPassword.value = '';
        confirmPassword.value = '';
    <?php else: ?>
        passwordErrorMsg.innerHTML = '<?= addslashes($flash['message']) ?>';
        passwordError.style.display = 'block';
        passwordError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    <?php endif; ?>
<?php endif; ?>
</script>

<script src="<?= asset('js/dashboard.js') ?>"></script>
