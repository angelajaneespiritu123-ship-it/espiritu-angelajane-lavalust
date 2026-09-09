<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AuthController extends Controller
{
    public function login()
    {
        $this->call->view('login');
    }

    public function register()
    {
        $this->call->view('register_view');
    }

    public function authenticate()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->call->library('database');

        $identity = trim($_POST['username'] ?? $_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($identity === '' || $password === '') {
            redirect('login');
        }

        $user_model = $this->call->model('UsersModel', 'users');
        $user = $user_model->find_by('username', $identity);

        if (empty($user) && filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $user = $user_model->find_by('email', $identity);
        }

        if (!empty($user) && password_verify($password, $user['password'] ?? '')) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            redirect('products');
        }

        redirect('login');
    }

    public function store_register()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->call->library('database');

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $email === '' || $password === '') {
            redirect('register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirect('register');
        }

        $user_model = $this->call->model('UsersModel', 'users');

        if ($user_model->exists(['username' => $username]) || $user_model->exists(['email' => $email])) {
            $_SESSION['register_error'] = 'Username or email already exists.';
            redirect('register');
        }

        $user_id = $user_model->insert([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user',
            'is_active' => 1,
        ]);

        if ($user_id) {
            redirect('login');
        }

        $_SESSION['register_error'] = 'Registration failed. Please try again.';
        redirect('register');
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();

        redirect('login');
    }
}
