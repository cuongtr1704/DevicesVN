<?php
/**
 * Dashboard Controller
 */

class DashboardController extends Controller {
    
    public function index() {
        if (!$this->isLoggedIn()) {
            $this->redirect('');
        }
        
        $userRole = $_SESSION['user_role'] ?? 'customer';
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => '']
        ];
        
        $data = [
            'title' => 'Dashboard - ' . APP_NAME,
            'activeSection' => 'overview',
            'userRole' => $userRole,
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('dashboard/index', $data);
    }
    
    public function profile() {
        if (!$this->isLoggedIn()) {
            $this->redirect('');
        }
        
        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if it's a password change request
            if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                $errors = [];
                
                if (empty($currentPassword)) {
                    $errors[] = 'Current password is required';
                } elseif (!password_verify($currentPassword, $user['password'])) {
                    $errors[] = 'Current password is incorrect';
                }
                
                if (strlen($newPassword) < 8) {
                    $errors[] = 'New password must be at least 8 characters';
                }
                
                if ($newPassword !== $confirmPassword) {
                    $errors[] = 'New password and confirm password do not match';
                }
                
                if (empty($errors)) {
                    $updated = $userModel->update($_SESSION['user_id'], [
                        'password' => password_hash($newPassword, PASSWORD_DEFAULT)
                    ]);
                    
                    if ($updated) {
                        $this->setFlash('password', 'Password changed successfully!', 'success');
                        $this->redirect('dashboard/profile');
                    } else {
                        $errors[] = 'Failed to update password';
                    }
                }
                
                $data['errors'] = $errors;
            } else {
                // Profile update
                $fullName = trim($_POST['full_name'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $address = trim($_POST['address'] ?? '');
                
                $errors = [];
                
                if (empty($fullName)) {
                    $errors[] = 'Full name is required';
                }
                
                if (empty($errors)) {
                    $updated = $userModel->update($_SESSION['user_id'], [
                        'full_name' => $fullName,
                        'phone' => $phone,
                        'address' => $address
                    ]);
                    
                    if ($updated) {
                        $_SESSION['user_name'] = $fullName;
                        $this->setFlash('profile', 'Profile updated successfully!', 'success');
                        $this->redirect('dashboard/profile');
                    } else {
                        $errors[] = 'Failed to update profile';
                    }
                }
                
                $data['errors'] = $errors;
            }
        }
        
        $userRole = $_SESSION['user_role'] ?? 'customer';
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => url('dashboard')],
            ['label' => 'My Profile', 'url' => '']
        ];
        
        $data = [
            'title' => 'My Profile - ' . APP_NAME,
            'user' => $user,
            'activeSection' => 'profile',
            'userRole' => $userRole,
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('dashboard/profile', $data);
    }
}
