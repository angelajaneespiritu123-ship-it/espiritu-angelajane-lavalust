<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #111;
            color: #f5f5f5;
            display: grid;
            place-items: center;
            min-height: 100vh;
        }

        .card {
            background: #1a1a1a;
            border: 1px solid #2d2d2d;
            border-radius: 12px;
            padding: 2rem;
            width: min(420px, 90vw);
        }

        h1 {
            margin-top: 0;
        }

        form {
            display: grid;
            gap: 1rem;
        }

        input {
            padding: 0.8rem;
            border-radius: 8px;
            border: 1px solid #333;
            background: #111;
            color: white;
        }

        button {
            background: #dd4814;
            color: white;
            border: none;
            padding: 0.9rem;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }

        a {
            color: #dd4814;
        }

        /* SUCCESS MESSAGE */

        .flash-success {
            background: #d1fae5;
            color: #166534;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 0.8rem 1rem;
            margin-bottom: 1rem;
            font-weight: 600;

            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-success .check {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #166534;
            color: white;
            font-size: 0.8rem;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            flex-shrink: 0;
        }


        /* ERROR MESSAGE */

        .flash-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 0.8rem 1rem;
            margin-bottom: 1rem;
            font-weight: 600;

            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-error .error-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #991b1b;
            color: white;
            font-size: 0.8rem;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            flex-shrink: 0;
        }

    </style>

</head>


<body>

    <div class="card">

        <h1>Login</h1>


        <!-- ================================= -->
        <!-- ACCOUNT SUCCESS MESSAGE -->
        <!-- ================================= -->

        <?php if (!empty($_SESSION['success'])): ?>

            <div class="flash-success">

                <span class="check">
                    ✓
                </span>

                <span>
                    <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?>
                </span>

            </div>

            <?php unset($_SESSION['success']); ?>

        <?php endif; ?>


        <!-- ================================= -->
        <!-- INCORRECT INFORMATION MESSAGE -->
        <!-- ================================= -->

        <?php if (!empty($_SESSION['login_error'])): ?>

            <div class="flash-error">

                <span class="error-icon">
                    !
                </span>

                <span>
                    <?= htmlspecialchars($_SESSION['login_error'], ENT_QUOTES, 'UTF-8') ?>
                </span>

            </div>

            <?php unset($_SESSION['login_error']); ?>

        <?php endif; ?>


        <!-- ================================= -->
        <!-- LOGIN FORM -->
        <!-- ================================= -->

        <form method="POST" action="/login">

            <input
                type="text"
                name="username"
                placeholder="Username"
                required
            >

            <input
                type="password"
                name="password"
                placeholder="Password"
                required
            >

            <button type="submit">
                Sign In
            </button>

        </form>


        <p>
            Need an account?
            <a href="/register">Register</a>
        </p>

    </div>

</body>

</html>