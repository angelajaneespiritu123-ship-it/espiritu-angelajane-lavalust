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

        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'admin';

        redirect('users');
    }

    public function store_register()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'admin';

        redirect('users');
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
