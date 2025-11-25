<?php
/**
 * Home Controller
 */

class HomeController extends Controller {
    
    public function index() {
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');
        
        $featuredProducts = $productModel->getFeatured(8);
        $categories = $categoryModel->getActive();
        
        $data = [
            'title' => 'Home - ' . APP_NAME,
            'featuredProducts' => $featuredProducts,
            'categories' => $categories
        ];
        
        $this->view('home/index', $data);
    }

    public function about() {
        $data = ['title' => 'About Us - ' . APP_NAME];
        $this->view('about/index', $data);
    }

    public function contact() {
        $data = ['title' => 'Contact Us - ' . APP_NAME];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $message = $_POST['message'] ?? '';
            
            $errors = [];
            
            if (empty($name)) $errors[] = 'Name is required';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required';
            }
            if (empty($message)) $errors[] = 'Message is required';
            
            if (empty($errors)) {
                $this->setFlash('contact', 'Thank you for contacting us!', 'success');
                $this->redirect('home/contact');
            } else {
                $data['errors'] = $errors;
                $data['old'] = $_POST;
            }
        }
        
        $this->view('home/contact', $data);
    }

    public function categories() {
        $categoryModel = $this->model('Category');
        $categories = $categoryModel->getActive();
        
        $breadcrumbs = [
            ['label' => 'Categories', 'url' => '']
        ];
        
        $data = [
            'title' => 'All Categories - ' . APP_NAME,
            'categories' => $categories,
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('categories/index', $data);
    }

    /**
     * 404 Not Found page
     */
    public function notFound() {
        http_response_code(404);
        $data = ['title' => '404 - Page Not Found'];
        $this->view('errors/404', $data);
    }
}
