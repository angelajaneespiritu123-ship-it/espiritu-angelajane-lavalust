<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AuthController extends Controller
{
    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->call->view('login');
    }

    public function register()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->call->view('register_view');
    }

  public function authenticate()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $this->call->library('database');

    $identity = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Kapag walang nilagay
    if ($identity === '' || $password === '') {
        $_SESSION['login_error'] = 'Incorrect Information';
        redirect('login');
        return;
    }

    $user_model = $this->call->model('UsersModel', 'users');

    // Hanapin gamit ang username
    $user = $user_model->find_by('username', $identity);

    // Kung walang username match, try email
    if (empty($user) && filter_var($identity, FILTER_VALIDATE_EMAIL)) {
        $user = $user_model->find_by('email', $identity);
    }

    // CHECK USERNAME/EMAIL + PASSWORD
    if (
        !empty($user) &&
        !empty($user['password']) &&
        password_verify($password, $user['password'])
    ) {

        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'] ?? 'user';

        // Welcome message
        if ($_SESSION['role'] === 'admin') {
            $_SESSION['success'] = 'Welcome, Admin!';
        } else {
            $_SESSION['success'] = 'Welcome, ' . $user['username'] . '!';
        }

        redirect('products');
        return;
    }

    // Wrong username/password
    $_SESSION['login_error'] = 'Incorrect Information';

    redirect('login');
    return;
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

        // Required fields
        if ($username === '' || $email === '' || $password === '') {
            $_SESSION['register_error'] = 'Please complete all fields.';
            redirect('register');
            return;
        }

        // Valid email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['register_error'] = 'Please enter a valid email.';
            redirect('register');
            return;
        }

        $user_model = $this->call->model('UsersModel', 'users');

        // Check duplicate account
        if (
            $user_model->exists(['username' => $username]) ||
            $user_model->exists(['email' => $email])
        ) {
            $_SESSION['register_error'] = 'Username or email already exists.';
            redirect('register');
            return;
        }

        // Create account
        $user_id = $user_model->insert([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user',
            'is_active' => 1,
        ]);

        // SUCCESS
        if ($user_id) {
            $_SESSION['success'] = 'Account Successfully Created';

            redirect('login');
            return;
        }

        // FAILED
        $_SESSION['register_error'] = 'Registration failed. Please try again.';

        redirect('register');
        return;
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