<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Calculator</title>
</head>
<body>

    <h1>Create Account</h1>

    <?php if (!empty($error)): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p style="color: green;">
            <?= htmlspecialchars($success) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="/register">
        <div>
            <label for="username">Username</label><br>
            <input
                type="text"
                id="username"
                name="username"
                required
            >
        </div>

        <br>

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

        <div>
            <label for="confirm_password">Confirm Password</label><br>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                required
            >
        </div>

        <br>
        

        <button type="submit">Sign Up</button>
    </form>
    <p>
    Already have an account?
    <a href="/login">Log in</a>
</p>

</body>
</html>