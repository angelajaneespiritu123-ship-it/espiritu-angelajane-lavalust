<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StudentController extends Controller
{
    public function index()
    {
        $this->call->view('student/home');
    }

    public function profile()
    {
        $this->call->view('student/profile');
    }
}
