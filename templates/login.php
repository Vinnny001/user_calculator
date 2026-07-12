<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Calculator</title>
</head>
<body>

    <h1>Log In</h1>

    <?php if (!empty($error)): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="/login">
        <div>
            <label for="email">Email</label><br>
            <input
                type="email"
                id="email"
                name="email"
                required
            >
        </div>

        <br>

        <div>
            <label for="password">Password</label><br>
            <input
                type="password"
                id="password"
                name="password"
                required
            >
        </div>

        <br>

        <button type="submit">Log In</button>
    </form>

    <p>
        Don't have an account?
        <a href="/register">Sign up</a>
    </p>

</body>
</html>