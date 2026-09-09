<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #111; color: #f5f5f5; display: grid; place-items: center; min-height: 100vh; }
        .card { background: #1a1a1a; border: 1px solid #2d2d2d; border-radius: 12px; padding: 2rem; width: min(420px, 90vw); }
        h1 { margin-top: 0; }
        form { display: grid; gap: 1rem; }
        input { padding: 0.8rem; border-radius: 8px; border: 1px solid #333; background: #111; color: white; }
        button { background: #dd4814; color: white; border: none; padding: 0.9rem; border-radius: 8px; cursor: pointer; }
        a { color: #dd4814; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Login</h1>
        <form method="POST" action="/login">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign In</button>
        </form>
        <p>Need an account? <a href="/register">Register</a></p>
    </div>
</body>
</html>
