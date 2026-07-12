<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculator Dashboard</title>
</head>
<body>

    <h1>Calculator</h1>

    <p>
        Welcome,
        <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
    </p>

    <?php if (!empty($error)): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <?php if ($result !== null): ?>
        <h2>
            Result:
            <?= htmlspecialchars((string) $result) ?>
        </h2>
    <?php endif; ?>

    <form method="POST" action="/calculate">

        <div>
            <label for="first_number">First Number</label><br>
            <input
                type="number"
                step="any"
                id="first_number"
                name="first_number"
                required
            >
        </div>

        <br>

        <div>
            <label for="operation">Operation</label><br>

            <select id="operation" name="operation" required>
                <option value="ADD">Addition (+)</option>
                <option value="SUBTRACT">Subtraction (-)</option>
                <option value="MULTIPLY">Multiplication (×)</option>
                <option value="DIVIDE">Division (÷)</option>
            </select>
        </div>

        <br>

        <div>
            <label for="second_number">Second Number</label><br>
            <input
                type="number"
                step="any"
                id="second_number"
                name="second_number"
                required
            >
        </div>

        <br>

        <button type="submit">Calculate</button>

    </form>

    <hr>

    <a href="/logout">Log Out</a>

</body>
</html>