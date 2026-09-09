<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $mode === 'edit' ? 'Edit Product' : 'Add Product' ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #111; color: #f5f5f5; margin: 0; padding: 2rem; }
        .container { max-width: 680px; margin: 0 auto; }
        .card { background: #181818; border: 1px solid #2a2a2a; border-radius: 12px; padding: 2rem; }
        h1 { margin-top: 0; }
        form { display: grid; gap: 1rem; }
        input, textarea { width: 100%; padding: 0.8rem 0.9rem; border-radius: 8px; border: 1px solid #333; background: #0d0d0d; color: white; }
        textarea { min-height: 120px; resize: vertical; }
        .actions { display: flex; gap: 0.75rem; margin-top: 0.5rem; }
        .btn { display: inline-block; padding: 0.8rem 1rem; border-radius: 8px; text-decoration: none; color: white; background: #dd4814; border: none; cursor: pointer; }
        .btn.secondary { background: #222; border: 1px solid #444; }
        .error-list { background: rgba(255, 92, 92, 0.08); border: 1px solid rgba(255, 105, 105, 0.4); color: #ffb0b0; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        ul { margin: 0; padding-left: 1.2rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1><?= $mode === 'edit' ? 'Edit Product' : 'Add Product' ?></h1>

            <?php if (!empty($errors)): ?>
                <div class="error-list">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= $mode === 'edit' ? '/products/update/' . (int) ($product['id'] ?? 0) : '/products/store' ?>">
                <div>
                    <label for="product_name">Product Name</label>
                    <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars((string) ($product['product_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div>
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required><?= htmlspecialchars((string) ($product['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div>
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" value="<?= htmlspecialchars((string) ($product['price'] ?? '0.00'), ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div>
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="0" value="<?= htmlspecialchars((string) ($product['quantity'] ?? 0), ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="actions">
                    <button class="btn" type="submit"><?= $mode === 'edit' ? 'Update Product' : 'Save Product' ?></button>
                    <a class="btn secondary" href="/products">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
