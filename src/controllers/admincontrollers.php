<?php
class AdminController {
    private $userModel;
    private $pterodactyl;

    public function __construct() {
        $this->userModel = new User();
        $this->pterodactyl = new Pterodactyl();
        
        // Cek role admin
        if($_SESSION['user_role'] != 'admin') {
            redirect('auth/login');
        }
    }

    public function dashboard() {
        // Ambil semua user
        $users = $this->userModel->getUsers();
        
        // Ambil data dari Pterodactyl
        $servers = $this->pterodactyl->getAllServers();
        $nodes = $this->pterodactyl->getNodes();

        $data = [
            'title' => 'Admin Dashboard',
            'users' => $users,
            'servers' => $servers,
            'nodes' => $nodes
        ];

        $this->view('admin/dashboard', $data);
    }

    public function users() {
        $users = $this->userModel->getUsers();

        $data = [
            'title' => 'Manage Users',
            'users' => $users
        ];

        $this->view('admin/users', $data);
    }

    public function addUser() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'role' => trim($_POST['role']),
                'username_err' => '',
                'email_err' => '',
                'password_err' => ''
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

            if(empty($data['username_err']) && empty($data['email_err']) && empty($data['password_err'])) {
                // Buat user di database
                if($this->userModel->register($data)) {
                    flash('user_message', 'User added successfully');
                    redirect('admin/users');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('admin/add_user', $data);
            }
        } else {
            $data = [
                'username' => '',
                'email' => '',
                'password' => '',
                'role' => 'member',
                'username_err' => '',
                'email_err' => '',
                'password_err' => ''
            ];

            $this->view('admin/add_user', $data);
        }
    }

    public function editUser($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'role' => trim($_POST['role']),
                'username_err' => '',
                'email_err' => ''
            ];

            // Validasi
            if(empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            }

            if(empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            }

            if(empty($data['username_err']) && empty($data['email_err'])) {
                if($this->userModel->updateUser($data)) {
                    flash('user_message', 'User updated');
                    redirect('admin/users');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('admin/edit_user', $data);
            }
        } else {
            // Get existing user
            $user = $this->userModel->getUserById($id);

            $data = [
                'id' => $id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'username_err' => '',
                'email_err' => ''
            ];

            $this->view('admin/edit_user', $data);
        }
    }

    public function deleteUser($id) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if($this->userModel->deleteUser($id)) {
                flash('user_message', 'User removed');
                redirect('admin/users');
            } else {
                die('Something went wrong');
            }
        } else {
            redirect('admin/users');
        }
    }
}