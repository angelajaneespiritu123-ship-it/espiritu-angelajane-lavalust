<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiAuthController extends Controller
{
    public function register()
    {
        $this->call->library('database');
        $api = $this->call->library('api');
        $api->require_method('POST');

        $data = $api->body();
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if ($username === '' || $email === '' || $password === '') {
            $api->respond_error('Please complete all fields.', 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $api->respond_error('Please enter a valid email.', 400);
        }

        $users = $this->call->model('UsersModel', 'users');

        if (
            $users->exists(['username' => $username]) ||
            $users->exists(['email' => $email])
        ) {
            $api->respond_error('Username or email already exists.', 409);
        }

        $userId = $users->insert([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user',
            'is_active' => 1,
        ]);

        if (!$userId) {
            $api->respond_error('Registration failed. Please try again.', 500);
        }

        $api->respond([
            'message' => 'Account Successfully Created',
        ], 201);
    }

    public function login()
    {
        // Load database
        $this->call->library('database');

        // Load LavaLust API library
        $api = $this->call->library('api');

        // Only allow POST requests
        $api->require_method('POST');

        // Get JSON request body
        $data = $api->body();

        // Get username/email and password
        $identity = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        // Check required fields
        if ($identity === '' || $password === '') {
            $api->respond_error(
                'Username/email and password are required.',
                400
            );
        }

        // Load UsersModel
        $user_model = $this->call->model('UsersModel', 'users');

        // Find user by username
        $user = $user_model->find_by('username', $identity);

        // If username was not found, try email
        if (empty($user) && filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $user = $user_model->find_by('email', $identity);
        }

        // Check if user exists and password is correct
        if (
            empty($user) ||
            empty($user['password']) ||
            !password_verify($password, $user['password'])
        ) {
            $api->respond_error(
                'Incorrect username/email or password.',
                401
            );
        }

        // Check if account is active
        if ((int)($user['is_active'] ?? 1) !== 1) {
            $api->respond_error(
                'This account is inactive.',
                403
            );
        }

        // Create JWT access token and refresh token
        $tokens = $api->issue_tokens([
            'id' => $user['id'],
            'role' => $user['role'] ?? 'user',
            'scopes' => ['read', 'write']
        ]);

        // Send successful response
        $api->respond([
            'message' => 'Login successful.',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']
            ],
            'tokens' => $tokens
        ], 200);
    }
}