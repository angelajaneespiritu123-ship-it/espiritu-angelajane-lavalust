<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class UsersController extends Controller
{
    public function index()
    {
        $this->call->library('database');
        $users_model = $this->call->model('UsersModel', 'users');
        $users = $users_model->all();

        $this->call->view('users/index', ['users' => $users]);
    }
}