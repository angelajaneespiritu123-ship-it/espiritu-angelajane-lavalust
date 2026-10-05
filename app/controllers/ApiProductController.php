<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiProductController extends Controller
{
    // GET /api/products
    public function index()
    {
        // Load database
        $this->call->library('database');

        // Load LavaLust API library
        $api = $this->call->library('api');

        // Only allow GET requests
        $api->require_method('GET');

        // Require a valid JWT access token
        $user = $api->require_jwt();

        // Load ProductModel
        $product_model = $this->call->model(
            'ProductModel',
            'products'
        );

        // Get all products
        $products = $product_model->all();

        // Return products as JSON
        $api->respond([
            'message' => 'Products retrieved successfully.',
            'products' => $products
        ], 200);
    }


    // POST /api/products
    public function store()
    {
        // Load database
        $this->call->library('database');

        // Load LavaLust API library
        $api = $this->call->library('api');

        // Only allow POST requests
        $api->require_method('POST');

        // Require a valid JWT access token
        $user = $api->require_jwt();

        // Get JSON request body
        $data = $api->body();

        // Get product information
        $product_name = trim($data['product_name'] ?? '');
        $description = trim($data['description'] ?? '');
        $price = $data['price'] ?? null;
        $quantity = $data['quantity'] ?? null;

        // Validate product name
        if ($product_name === '') {
            $api->respond_error(
                'Product name is required.',
                422
            );
        }

        // Validate description
        if ($description === '') {
            $api->respond_error(
                'Description is required.',
                422
            );
        }

        // Validate price
        if (
            $price === null ||
            !is_numeric($price) ||
            $price < 0
        ) {
            $api->respond_error(
                'Price must be a valid number.',
                422
            );
        }

        // Validate quantity
        if (
            $quantity === null ||
            !is_numeric($quantity) ||
            $quantity < 0
        ) {
            $api->respond_error(
                'Quantity must be a valid number.',
                422
            );
        }

        // Load ProductModel
        $product_model = $this->call->model(
            'ProductModel',
            'products'
        );

        // Insert product into database
        $product_model->insert([
            'product_name' => $product_name,
            'description' => $description,
            'price' => $price,
            'quantity' => $quantity
        ]);

        // Return successful response
        $api->respond([
            'message' => 'Product added successfully.',
            'product' => [
                'product_name' => $product_name,
                'description' => $description,
                'price' => $price,
                'quantity' => $quantity
            ]
        ], 201);
    }


    // PUT /api/products/{id}
    public function update($id)
    {
        // Load database
        $this->call->library('database');

        // Load LavaLust API library
        $api = $this->call->library('api');

        // Only allow PUT requests
        $api->require_method('PUT');

        // Require a valid JWT access token
        $user = $api->require_jwt();

        // Load ProductModel
        $product_model = $this->call->model(
            'ProductModel',
            'products'
        );

        // Find existing product
        $product = $product_model->find($id);

        // Check if product exists
        if (empty($product)) {
            $api->respond_error(
                'Product not found.',
                404
            );
        }

        // Get JSON request body
        $data = $api->body();

        // Get updated product information
        $product_name = trim($data['product_name'] ?? '');
        $description = trim($data['description'] ?? '');
        $price = $data['price'] ?? null;
        $quantity = $data['quantity'] ?? null;

        // Validate product name
        if ($product_name === '') {
            $api->respond_error(
                'Product name is required.',
                422
            );
        }

        // Validate description
        if ($description === '') {
            $api->respond_error(
                'Description is required.',
                422
            );
        }

        // Validate price
        if (
            $price === null ||
            !is_numeric($price) ||
            $price < 0
        ) {
            $api->respond_error(
                'Price must be a valid number.',
                422
            );
        }

        // Validate quantity
        if (
            $quantity === null ||
            !is_numeric($quantity) ||
            $quantity < 0
        ) {
            $api->respond_error(
                'Quantity must be a valid number.',
                422
            );
        }

        // Update product in database
        $product_model->update($id, [
            'product_name' => $product_name,
            'description' => $description,
            'price' => $price,
            'quantity' => $quantity
        ]);

        // Return successful response
        $api->respond([
            'message' => 'Product updated successfully.',
            'product' => [
                'id' => $id,
                'product_name' => $product_name,
                'description' => $description,
                'price' => $price,
                'quantity' => $quantity
            ]
        ], 200);

  

    }

    // DELETE /api/products/{id}
public function delete($id)
{
    // Load database
    $this->call->library('database');

    // Load LavaLust API library
    $api = $this->call->library('api');

    // Only allow DELETE requests
    $api->require_method('DELETE');

    // Require a valid JWT access token
    $user = $api->require_jwt();

    // Load ProductModel
    $product_model = $this->call->model(
        'ProductModel',
        'products'
    );

    // Find existing product
    $product = $product_model->find($id);

    // Check if product exists
    if (empty($product)) {
        $api->respond_error(
            'Product not found.',
            404
        );
    }

    // Delete product
    $product_model->delete($id);

    // Return successful response
    $api->respond([
        'message' => 'Product deleted successfully.',
        'id' => $id
    ], 200);
}
}