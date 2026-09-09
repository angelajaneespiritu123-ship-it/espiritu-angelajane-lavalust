<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <style>
        body { font-family: Arial, sans-serif; background: #111; color: #f5f5f5; margin: 0; padding: 2rem; }
        .container { max-width: 1100px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .topbar h1 { margin: 0; }
        .actions { display: flex; gap: 0.75rem; align-items: center; }
        .btn { display: inline-block; padding: 0.7rem 1rem; border-radius: 8px; text-decoration: none; color: white; background: #dd4814; border: none; }
        .btn.secondary { background: #222; color: #fff; border: 1px solid #444; }
        table { width: 100%; border-collapse: collapse; background: #181818; border: 1px solid #2a2a2a; border-radius: 10px; overflow: hidden; }
        th, td { padding: 0.9rem 1rem; border-bottom: 1px solid #2a2a2a; text-align: left; }
        th { background: #1f1f1f; }
        .muted { color: #c9c9c9; }
        .small { color: #d8d8d8; }
        .badge { background: #dd4814; color: white; padding: 0.25rem 0.5rem; border-radius: 999px; font-size: 0.8rem; }
        .link-btn { color: #f5b39f; text-decoration: none; }
        .empty { background: #181818; border: 1px solid #2a2a2a; padding: 2rem; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <h1>Product Management</h1>
            <div class="actions">
                <span class="muted">Welcome, <?= htmlspecialchars((string) ($username ?? 'Admin'), ENT_QUOTES, 'UTF-8') ?></span>
                <a class="btn secondary" href="/logout">Logout</a>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <a class="btn" href="/products/create">+ Add Product</a>
        </div>

        <?php if (empty($products)): ?>
            <div class="empty">
                <p>No products found yet.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) ($product['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($product['product_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="small"><?= htmlspecialchars((string) ($product['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($product['price'] ?? '0.00'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="badge"><?= htmlspecialchars((string) ($product['quantity'] ?? 0), ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td class="small"><?= htmlspecialchars((string) ($product['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a class="link-btn" href="/products/edit/<?= (int) ($product['id'] ?? 0) ?>">Edit</a>
                                &nbsp;|&nbsp;
                                <a class="link-btn" href="/products/delete/<?= (int) ($product['id'] ?? 0) ?>" onclick="return confirm('Delete this product?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
