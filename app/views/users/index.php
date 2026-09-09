<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | LavaLust</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --background: #090909;
            --surface: #121212;
            --surface-raised: #191919;
            --border: rgba(255, 255, 255, 0.08);
            --text: #f5f5f5;
            --muted: #858585;
            --accent: #e84b2c;
            --accent-soft: rgba(232, 75, 44, 0.14);
            --sans: 'DM Sans', sans-serif;
            --mono: 'Space Mono', monospace;
        }

        html, body { margin: 0; min-height: 100%; }

        body {
            background: var(--background);
            color: var(--text);
            font-family: var(--sans);
        }

        body::before {
            background: linear-gradient(135deg, rgba(232, 75, 44, 0.06), transparent 36%);
            content: '';
            inset: 0;
            pointer-events: none;
            position: fixed;
        }

        .site-header {
            border-bottom: 1px solid var(--border);
            position: relative;
            z-index: 1;
        }

        .nav {
            align-items: center;
            display: flex;
            justify-content: space-between;
            margin: 0 auto;
            max-width: 1000px;
            padding: 1.2rem 1.5rem;
        }

        .brand {
            color: var(--text);
            font-size: 0.92rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-decoration: none;
        }

        .brand span { color: var(--accent); }

        .links { display: flex; gap: 0.35rem; }

        .links a {
            border-radius: 5px;
            color: var(--muted);
            font-size: 0.72rem;
            padding: 0.45rem 0.7rem;
            text-decoration: none;
        }

        .links a:hover, .links a.active {
            background: var(--accent-soft);
            color: var(--text);
        }

        main {
            margin: 0 auto;
            max-width: 1000px;
            padding: 3.7rem 1.5rem 5rem;
            position: relative;
            z-index: 1;
        }

        .eyebrow {
            color: var(--accent);
            font-family: var(--mono);
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            margin: 0 0 0.8rem;
            text-transform: uppercase;
        }

        h1 {
            font-size: clamp(1.8rem, 4vw, 2.35rem);
            letter-spacing: -0.04em;
            line-height: 1.1;
            margin: 0 0 0.45rem;
        }

        .intro {
            color: var(--muted);
            font-size: 0.84rem;
            margin: 0 0 1.7rem;
        }

        .table-wrap {
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow-x: auto;
        }

        table { border-collapse: collapse; min-width: 680px; width: 100%; }

        th {
            background: var(--surface-raised);
            color: var(--muted);
            font-family: var(--mono);
            font-size: 0.58rem;
            font-weight: 400;
            letter-spacing: 0.06em;
            padding: 0.8rem 0.75rem;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            border-top: 1px solid var(--border);
            color: #e6e6e6;
            font-size: 0.76rem;
            padding: 0.78rem 0.75rem;
        }

        tbody tr:nth-child(even) { background: rgba(255, 255, 255, 0.025); }
        tbody tr:hover { background: var(--accent-soft); }
        td:first-child { color: var(--muted); font-family: var(--mono); font-size: 0.68rem; }

        .back-link {
            background: var(--surface-raised);
            border: 1px solid var(--border);
            border-radius: 5px;
            color: var(--text);
            display: inline-block;
            font-size: 0.72rem;
            margin-top: 1.6rem;
            padding: 0.65rem 0.85rem;
            text-decoration: none;
        }

        .back-link:hover { border-color: var(--accent); }

        @media (max-width: 540px) {
            .nav { padding: 1rem; }
            .links a { padding: 0.4rem; }
            main { padding: 2.8rem 1rem 4rem; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <nav class="nav" aria-label="Main navigation">
            <a class="brand" href="./">Lava<span>Lust</span></a>
            <div class="links">
                <a href="./">Home</a>
                <a href="student/profile">Profile</a>
                <a class="active" href="users">Users</a>
            </div>
        </nav>
    </header>

    <main>
        <p class="eyebrow">Database records</p>
        <h1>User Management Directory</h1>
        <p class="intro">Overview of dynamically fetched records from Aiven MySQL database.</p>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) ($user['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($user['username'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($user['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($user['role'] ?? 'user'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) (($user['is_active'] ?? 1) ? 'Active' : 'Inactive'), ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
            </table>
        </div>

        <a class="back-link" href="./">&larr; Back to Home</a>
    </main>
</body>
</html>
