<?php
/**
 * Auth Controller - User Authentication
 */

class AuthController extends Controller {
    
    public function login() {
        if ($this->isLoggedIn()) {
            $this->redirect('');
        }
        
        $data = ['title' => 'Login - ' . APP_NAME];
        
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
                    $_SESSION['is_admin'] = $user['is_admin'];
                    
                    $this->setFlash('login', 'Welcome back, ' . $user['full_name'] . '!', 'success');
                    $this->redirect('');
                } else {
                    $errors[] = 'Invalid email or password';
                }
            }
            
            $data['errors'] = $errors;
            $data['old'] = $_POST;
        }
        
        $this->view('auth/login', $data);
    }

    public function register() {
        if ($this->isLoggedIn()) {
            $this->redirect('');
        }
        
        $data = ['title' => 'Register - ' . APP_NAME];
        
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
            if (strlen($password) < PASSWORD_MIN_LENGTH) {
                $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
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
                    $this->setFlash('register', 'Registration successful! Please login.', 'success');
                    $this->redirect('auth/login');
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
            }
            
            $data['errors'] = $errors;
            $data['old'] = $_POST;
        }
        
        $this->view('auth/register', $data);
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
