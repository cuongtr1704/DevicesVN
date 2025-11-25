<?php
/**
 * Auth Controller - User Authentication
 */

class AuthController extends Controller {
    
    public function login() {
        if ($this->isLoggedIn()) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo json_encode(['success' => false, 'message' => 'Already logged in']);
                return;
            }
            $this->redirect('');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            $errors = [];
            
            if (empty($email)) $errors[] = 'Email is required';
            if (empty($password)) $errors[] = 'Password is required';
            
            if (empty($errors)) {
                $userModel = $this->model('User');
                $user = $userModel->verifyPassword($email, $password);
                
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // Transfer guest cart to user cart
                    if (isset($_SESSION['cart_session_id'])) {
                        $cartModel = $this->model('Cart');
                        $cartModel->transferGuestCart($_SESSION['cart_session_id'], $user['id']);
                    }
                    
                    // Return JSON for AJAX requests
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        echo json_encode(['success' => true, 'message' => 'Welcome back, ' . $user['full_name'] . '!']);
                        return;
                    }
                    
                    $this->setFlash('login', 'Welcome back, ' . $user['full_name'] . '!', 'success');
                    $this->redirect('');
                } else {
                    $errors[] = 'Invalid email or password';
                }
            }
            
            // Return JSON for AJAX requests
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }
        }
        
        // Show login page if not AJAX
        $this->redirect('');
    }

    public function register() {
        if ($this->isLoggedIn()) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo json_encode(['success' => false, 'message' => 'Already logged in']);
                return;
            }
            $this->redirect('');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $fullName = trim($_POST['full_name'] ?? '');
            
            $errors = [];
            
            if (empty($fullName)) $errors[] = 'Full name is required';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Password must be at least 8 characters';
            }
            if ($password !== $confirmPassword) $errors[] = 'Passwords do not match';
            
            $userModel = $this->model('User');
            if ($userModel->findByEmail($email)) {
                $errors[] = 'Email already exists';
            }
            
            if (empty($errors)) {
                $userId = $userModel->register([
                    'email' => $email,
                    'password' => $password,
                    'full_name' => $fullName
                ]);
                
                if ($userId) {
                    // Transfer guest cart if exists
                    if (isset($_SESSION['cart_session_id'])) {
                        $cartModel = $this->model('Cart');
                        $cartModel->transferGuestCart($_SESSION['cart_session_id'], $userId);
                    }
                    
                    // Return JSON for AJAX requests
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        echo json_encode(['success' => true, 'message' => 'Registration successful! Please login.']);
                        return;
                    }
                    
                    $this->setFlash('register', 'Registration successful! Please login.', 'success');
                    $this->redirect('');
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
            }
            
            // Return JSON for AJAX requests
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }
        }
        
        // Redirect to home if not AJAX
        $this->redirect('');
    }

    public function logout() {
        session_destroy();
        $this->redirect('');
    }

    public function forgotPassword() {
        $data = ['title' => 'Forgot Password - ' . APP_NAME];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            
            if (empty($email)) {
                $data['errors'] = ['Email is required'];
            } else {
                $userModel = $this->model('User');
                $token = $userModel->createResetToken($email);
                
                if ($token) {
                    $resetLink = url('auth/reset-password?token=' . $token);
                    $this->setFlash('forgot', 'Password reset link: ' . $resetLink, 'success');
                } else {
                    $this->setFlash('forgot', 'Email not found.', 'error');
                }
                
                $this->redirect('auth/forgot-password');
            }
        }
        
        $this->view('auth/forgot-password', $data);
    }

    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        
        $data = [
            'title' => 'Reset Password - ' . APP_NAME,
            'token' => $token
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $token = $_POST['token'] ?? '';
            
            $errors = [];
            
            if (strlen($password) < PASSWORD_MIN_LENGTH) {
                $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
            }
            if ($password !== $confirmPassword) $errors[] = 'Passwords do not match';
            
            if (empty($errors)) {
                $userModel = $this->model('User');
                
                if ($userModel->resetPassword($token, $password)) {
                    $this->setFlash('reset', 'Password reset successful! Please login.', 'success');
                    $this->redirect('auth/login');
                } else {
                    $errors[] = 'Invalid or expired reset token';
                }
            }
            
            $data['errors'] = $errors;
        }
        
        $this->view('auth/reset-password', $data);
    }
}
