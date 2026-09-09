<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProductController extends Controller
{
    private function require_auth()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            redirect('login');
        }
    }

    private function require_admin()
    {
        $this->require_auth();

        if (($_SESSION['role'] ?? 'user') !== 'admin') {
            redirect('products');
        }
    }

    private function validate_product(array $data): array
    {
        $errors = [];

        $product_name = trim((string) ($data['product_name'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));
        $price = trim((string) ($data['price'] ?? ''));
        $quantity = trim((string) ($data['quantity'] ?? ''));

        if ($product_name === '') {
            $errors[] = 'Product name is required.';
        }

        if ($description === '') {
            $errors[] = 'Description is required.';
        }

        if ($price === '' || !is_numeric($price) || (float) $price < 0) {
            $errors[] = 'Price must be a valid number.';
        }

        if (
            $quantity === '' ||
            !ctype_digit((string) $quantity) ||
            (int) $quantity < 0
        ) {
            $errors[] = 'Quantity must be a valid non-negative integer.';
        }

        return $errors;
    }

    // =========================
    // PRODUCT LIST
    // =========================
    public function index()
    {
        $this->require_auth();

        $this->call->library('database');

        $product_model = $this->call->model(
            'ProductModel',
            'products'
        );

        $products = $product_model->all();

        $this->call->view('product/index', [
            'products' => $products,
            'username' => $_SESSION['username'] ?? 'Admin',
        ]);
    }

    // =========================
    // SHOW CREATE FORM
    // =========================
    public function create()
    {
        $this->require_admin();

        $this->call->view('product/form', [
            'product' => [],
            'errors' => [],
            'mode' => 'create',
        ]);
    }

    // =========================
    // ADD PRODUCT
    // =========================
    public function store()
    {
        $this->require_admin();

        $errors = $this->validate_product($_POST);

        if (!empty($errors)) {
            $this->call->view('product/form', [
                'product' => $_POST,
                'errors' => $errors,
                'mode' => 'create',
            ]);

            return;
        }

        $this->call->library('database');

        $product_model = $this->call->model(
            'ProductModel',
            'products'
        );

        $product_model->insert([
            'product_name' => trim($_POST['product_name']),
            'description' => trim($_POST['description']),
            'price' => (float) $_POST['price'],
            'quantity' => (int) $_POST['quantity'],
        ]);

        // SUCCESS MESSAGE
        $_SESSION['success'] = 'Product Successfully Added';

        redirect('products');
    }

    // =========================
    // SHOW EDIT FORM
    // =========================
    public function edit($id)
    {
        $this->require_admin();

        $this->call->library('database');

        $product_model = $this->call->model(
            'ProductModel',
            'products'
        );

        $product = $product_model->find($id);

        if (empty($product)) {
            redirect('products');
        }

        $this->call->view('product/form', [
            'product' => $product,
            'errors' => [],
            'mode' => 'edit',
        ]);
    }

    // =========================
    // UPDATE PRODUCT
    // =========================
    public function update($id)
    {
        $this->require_admin();

        $errors = $this->validate_product($_POST);

        if (!empty($errors)) {

            $this->call->library('database');

            $product_model = $this->call->model(
                'ProductModel',
                'products'
            );

            $product = $product_model->find($id);

            $this->call->view('product/form', [
                'product' => $product ?: $_POST,
                'errors' => $errors,
                'mode' => 'edit',
            ]);

            return;
        }

        $this->call->library('database');

        $product_model = $this->call->model(
            'ProductModel',
            'products'
        );

        $product_model->update($id, [
            'product_name' => trim($_POST['product_name']),
            'description' => trim($_POST['description']),
            'price' => (float) $_POST['price'],
            'quantity' => (int) $_POST['quantity'],
        ]);

        // SUCCESS MESSAGE
        $_SESSION['success'] = 'Product Successfully Edited';

        redirect('products');
    }

    // =========================
    // DELETE PRODUCT
    // =========================
    public function delete($id)
    {
        $this->require_admin();

        $this->call->library('database');

        $product_model = $this->call->model(
            'ProductModel',
            'products'
        );

        $product_model->delete($id);

        // SUCCESS MESSAGE
        $_SESSION['success'] = 'Product Successfully Deleted';

        redirect('products');
    }
}