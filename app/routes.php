<?php


declare(strict_types=1);
use App\Database;

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });


    $app->get('/register', function (
    Request $request,
    Response $response
) {
    $error = null;
    $success = null;

    ob_start();
    require __DIR__ . '/../templates/register.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);

    return $response;
});


$app->post('/register', function (
    Request $request,
    Response $response
) {
    $data = $request->getParsedBody();

    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';

    $error = null;
    $success = null;

    if (
        $username === '' ||
        $email === '' ||
        $password === '' ||
        $confirmPassword === ''
    ) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $db = Database::connect();

        $statement = $db->prepare(
            'SELECT id
             FROM users
             WHERE username = :username
                OR email = :email
             LIMIT 1'
        );

        $statement->execute([
            'username' => $username,
            'email' => $email
        ]);

        if ($statement->fetch()) {
            $error = 'Username or email is already registered.';
        } else {
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $statement = $db->prepare(
                'INSERT INTO users (username, email, password)
                 VALUES (:username, :email, :password)'
            );

            $statement->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword
            ]);

            $success = 'Account created successfully. You can now log in.';
        }
    }

    ob_start();
    require __DIR__ . '/../templates/register.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);

    return $response;
});


$app->get('/login', function (
    Request $request,
    Response $response
) {
    if (isset($_SESSION['user_id'])) {
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    $error = null;

    ob_start();
    require __DIR__ . '/../templates/login.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);

    return $response;
});


$app->post('/login', function (
    Request $request,
    Response $response
) {
    $data = $request->getParsedBody();

    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    $error = null;

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $db = Database::connect();

        $statement = $db->prepare(
            'SELECT id, username, email, password
             FROM users
             WHERE email = :email
             LIMIT 1'
        );

        $statement->execute([
            'email' => $email
        ]);

        $user = $statement->fetch();

        if (
            !$user ||
            !password_verify($password, $user['password'])
        ) {
            $error = 'Invalid email or password.';
        } else {
            // Prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            return $response
                ->withHeader('Location', '/dashboard')
                ->withStatus(302);
        }
    }

    ob_start();
    require __DIR__ . '/../templates/login.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);

    return $response;
});


$app->get('/dashboard', function (
    Request $request,
    Response $response
) {
    if (!isset($_SESSION['user_id'])) {
        return $response
            ->withHeader('Location', '/login')
            ->withStatus(302);
    }

    $result = null;
    $error = null;

    ob_start();
    require __DIR__ . '/../templates/dashboard.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);

    return $response;
});


// Log out route
$app->get('/logout', function (
    Request $request,
    Response $response
) {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();

    return $response
        ->withHeader('Location', '/login')
        ->withStatus(302);
});

//calculator route
$app->post('/calculate', function (
    Request $request,
    Response $response
) {
    // Protect the calculator
    if (!isset($_SESSION['user_id'])) {
        return $response
            ->withHeader('Location', '/login')
            ->withStatus(302);
    }

    $data = $request->getParsedBody();

    $firstNumber = $data['first_number'] ?? '';
    $secondNumber = $data['second_number'] ?? '';
    $operation = $data['operation'] ?? '';

    $result = null;
    $error = null;

    // Validate numbers
    if (
        !is_numeric($firstNumber) ||
        !is_numeric($secondNumber)
    ) {
        $error = 'Please enter valid numbers.';
    } else {
        $firstNumber = (float) $firstNumber;
        $secondNumber = (float) $secondNumber;

        switch ($operation) {
            case 'ADD':
                $result = $firstNumber + $secondNumber;
                break;

            case 'SUBTRACT':
                $result = $firstNumber - $secondNumber;
                break;

            case 'MULTIPLY':
                $result = $firstNumber * $secondNumber;
                break;

            case 'DIVIDE':
                if ($secondNumber == 0) {
                    $error = 'You cannot divide by zero.';
                } else {
                    $result = $firstNumber / $secondNumber;
                }
                break;

            default:
                $error = 'Please select a valid operation.';
        }
    }

    ob_start();
    require __DIR__ . '/../templates/dashboard.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);

    return $response;
});




};
