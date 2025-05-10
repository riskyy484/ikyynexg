<?php
class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function register() {
        // Proses form register
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize input
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'role' => isset($_POST['role']) ? trim($_POST['role']) : 'member',
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Validasi
            if(empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            }

            if(empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } elseif($this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Email is already taken';
            }

            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } elseif(strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            if(empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password';
            } else {
                if($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }

            // Jika tidak ada error
            if(empty($data['email_err']) && empty($data['username_err']) && 
               empty($data['password_err']) && empty($data['confirm_password_err'])) {
                
                // Register user
                if($this->userModel->register($data)) {
                    flash('register_success', 'You are registered and can log in');
                    redirect('auth/login');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load view dengan error
                $this->view('auth/register', $data);
            }

        } else {
            // Load form register
            $data = [
                'username' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'role' => 'member',
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            $this->view('auth/register', $data);
        }
    }

    public function login() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize input
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => '',
            ];

            // Validasi
            if(empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            }

            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            // Cek email
            if(!$this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'No user found';
            }

            // Jika tidak ada error
            if(empty($data['email_err']) && empty($data['password_err'])) {
                // Cek dan set session
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if($loggedInUser) {
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Password incorrect';
                    $this->view('auth/login', $data);
                }
            } else {
                $this->view('auth/login', $data);
            }

        } else {
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => '',
            ];

            $this->view('auth/login', $data);
        }
    }

    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_username'] = $user->username;
        $_SESSION['user_role'] = $user->role;
        
        if($user->role === 'admin') {
            redirect('admin/dashboard');
        } else {
            redirect('member/dashboard');
        }
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_username']);
        unset($_SESSION['user_role']);
        session_destroy();
        redirect('auth/login');
    }
}