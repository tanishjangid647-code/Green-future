<?php
// Green Future - Secure Authentication Backend Handler

require_once __DIR__ . '/../config/helpers.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

/*
|--------------------------------------------------------------------------
| CSRF Protection
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrf_token)) {

        set_flash(
            'error',
            'Invalid security token. Please refresh the page and try again.'
        );

        header('Location: ' . base_url('auth/login.php'));
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/
if ($action === 'login') {

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (empty($email) || empty($password)) {

        set_flash(
            'error',
            'Please fill in both email and password.'
        );

        header('Location: ' . base_url('auth/login.php'));
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        set_flash(
            'error',
            'Please enter a valid email address.'
        );

        header('Location: ' . base_url('auth/login.php'));
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Find User
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE email = ?
        LIMIT 1
    ");

    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    /*
    |--------------------------------------------------------------------------
    | Secure Password Verification
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | There are NO hard-coded passwords anymore.
    |
    */

    if (!$user || !password_verify($password, $user['password'])) {

        set_flash(
            'error',
            'Invalid email or password. Please try again.'
        );

        header('Location: ' . base_url('auth/login.php'));
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Account Status
    |--------------------------------------------------------------------------
    */

    if ($user['status'] !== 'active') {

        set_flash(
            'error',
            'Your account has been deactivated or suspended.'
        );

        header('Location: ' . base_url('auth/login.php'));
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Regenerate Session ID
    |--------------------------------------------------------------------------
    |
    | Protects against session fixation attacks.
    |
    */

    session_regenerate_id(true);


    /*
    |--------------------------------------------------------------------------
    | Store User Session
    |--------------------------------------------------------------------------
    */

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];


    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */

    log_activity(
        "Logged into the system as {$user['role']}"
    );


    set_flash(
        'success',
        "Welcome back, {$user['full_name']}!"
    );


    /*
    |--------------------------------------------------------------------------
    | Role Based Redirect
    |--------------------------------------------------------------------------
    */

    if ($user['role'] === 'admin') {

        header(
            'Location: ' .
            base_url('admin/dashboard.php')
        );

    } elseif ($user['role'] === 'volunteer') {

        header(
            'Location: ' .
            base_url('volunteer/dashboard.php')
        );

    } else {

        header(
            'Location: ' .
            base_url('user/dashboard.php')
        );
    }

    exit;
}


/*
|--------------------------------------------------------------------------
| SIGNUP
|--------------------------------------------------------------------------
*/
elseif ($action === 'signup') {

    $full_name = trim($_POST['full_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');

    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');

    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';


    /*
    |--------------------------------------------------------------------------
    | SECURITY:
    | Normal users can NEVER choose their own role.
    |--------------------------------------------------------------------------
    */

    $role = 'registered';


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (
        empty($full_name) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        set_flash(
            'error',
            'Please complete all required fields.'
        );

        header('Location: ' . base_url('auth/signup.php'));
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Name Validation
    |--------------------------------------------------------------------------
    */

    if (
        strlen($full_name) < 2 ||
        strlen($full_name) > 100
    ) {

        set_flash(
            'error',
            'Please enter a valid name.'
        );

        header('Location: ' . base_url('auth/signup.php'));
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Email Validation
    |--------------------------------------------------------------------------
    */

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        set_flash(
            'error',
            'Please enter a valid email address.'
        );

        header('Location: ' . base_url('auth/signup.php'));
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Password Validation
    |--------------------------------------------------------------------------
    */

    if (strlen($password) < 8) {

        set_flash(
            'error',
            'Password must contain at least 8 characters.'
        );

        header('Location: ' . base_url('auth/signup.php'));
        exit;
    }

    if ($password !== $confirm_password) {

        set_flash(
            'error',
            'Passwords do not match.'
        );

        header('Location: ' . base_url('auth/signup.php'));
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Check Existing Email
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT id
        FROM users
        WHERE email = ?
        LIMIT 1
    ");

    $stmt->execute([$email]);

    if ($stmt->fetch()) {

        set_flash(
            'error',
            'Email is already registered. Please log in.'
        );

        header('Location: ' . base_url('auth/signup.php'));
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Secure Password Hash
    |--------------------------------------------------------------------------
    */

    $hashed_password = password_hash(
        $password,
        PASSWORD_DEFAULT
    );


    /*
    |--------------------------------------------------------------------------
    | Default User Badge
    |--------------------------------------------------------------------------
    */

    $badge = 'Green Starter';


    /*
    |--------------------------------------------------------------------------
    | Create Account
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        INSERT INTO users
        (
            full_name,
            email,
            phone,
            password,
            role,
            city,
            state,
            badge,
            status
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, 'active')
    ");

    $stmt->execute([
        $full_name,
        $email,
        $phone,
        $hashed_password,
        $role,
        $city,
        $state,
        $badge
    ]);


    /*
    |--------------------------------------------------------------------------
    | Automatically Login New User
    |--------------------------------------------------------------------------
    */

    $new_id = $pdo->lastInsertId();

    session_regenerate_id(true);

    $_SESSION['user_id'] = $new_id;
    $_SESSION['user_name'] = $full_name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = $role;


    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */

    log_activity(
        'New registered user account created'
    );


    set_flash(
        'success',
        'Registration successful! Welcome to Green Future.'
    );


    header(
        'Location: ' .
        base_url('user/dashboard.php')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| FORGOT PASSWORD
|--------------------------------------------------------------------------
|
| We will implement the real reset-token + email system in the
| next authentication step.
|
*/

elseif ($action === 'forgot_password') {

    $email = strtolower(trim($_POST['email'] ?? ''));

    /*
    |--------------------------------------------------------------------------
    | Validate Email
    |--------------------------------------------------------------------------
    */

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        set_flash(
            'error',
            'Please enter a valid email address.'
        );

        header(
            'Location: ' .
            base_url('auth/forgot-password.php')
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Find User
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT id, email
        FROM users
        WHERE email = ?
        AND status = 'active'
        LIMIT 1
    ");

    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    /*
    |--------------------------------------------------------------------------
    | Generic Response
    |--------------------------------------------------------------------------
    |
    | We don't reveal whether an email exists.
    |
    */

    if (!$user) {

        set_flash(
            'success',
            'If an account exists with this email, password reset instructions will be sent.'
        );

        header(
            'Location: ' .
            base_url('auth/login.php')
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Previous Reset Tokens
    |--------------------------------------------------------------------------
    */

    $delete = $pdo->prepare("
        DELETE FROM password_resets
        WHERE user_id = ?
    ");

    $delete->execute([$user['id']]);


    /*
    |--------------------------------------------------------------------------
    | Generate Secure Reset Token
    |--------------------------------------------------------------------------
    */

    $token = bin2hex(random_bytes(32));

    /*
    | Only the SHA-256 hash is stored in the database.
    */

    $token_hash = hash(
        'sha256',
        $token
    );


    /*
    |--------------------------------------------------------------------------
    | Token Expiration
    |--------------------------------------------------------------------------
    |
    | Token is valid for 30 minutes.
    |
    */



    /*
    |--------------------------------------------------------------------------
    | Store Token
    |--------------------------------------------------------------------------
    */

   $stmt = $pdo->prepare("
    INSERT INTO password_resets
    (
        user_id,
        token_hash,
        expires_at
    )
    VALUES
    (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))
");

$stmt->execute([
    $user['id'],
    $token_hash
]);

    /*
    |--------------------------------------------------------------------------
    | DEVELOPMENT MODE
    |--------------------------------------------------------------------------
    |
    | XAMPP doesn't automatically send email.
    |
    | For LOCAL TESTING ONLY, we temporarily redirect directly
    | to the reset page.
    |
    | Later we will replace this with real email sending.
    |
    */

    $reset_url =
        base_url('auth/reset-password.php') .
        '?token=' .
        urlencode($token);


    /*
    |--------------------------------------------------------------------------
    | Local Development Reset
    |--------------------------------------------------------------------------
    */

    if (
        isset($_SERVER['HTTP_HOST']) &&
        (
            str_contains($_SERVER['HTTP_HOST'], 'localhost') ||
            str_contains($_SERVER['HTTP_HOST'], '127.0.0.1')
        )
    ) {

        header(
            'Location: ' .
            $reset_url
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Production Response
    |--------------------------------------------------------------------------
    |
    | Real email notification will be connected here later.
    |
    */

    set_flash(
        'success',
        'If an account exists with this email, password reset instructions have been sent.'
    );

    header(
        'Location: ' .
        base_url('auth/login.php')
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| INVALID ACTION
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| RESET PASSWORD
|--------------------------------------------------------------------------
*/
elseif ($action === 'reset_password') {

    $token = trim($_POST['token'] ?? '');

    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';


    /*
    |--------------------------------------------------------------------------
    | Validate Token
    |--------------------------------------------------------------------------
    */

    if (
        empty($token) ||
        !preg_match('/^[a-f0-9]{64}$/i', $token)
    ) {

        set_flash(
            'error',
            'Invalid password reset token.'
        );

        header(
            'Location: ' .
            base_url('auth/login.php')
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Password
    |--------------------------------------------------------------------------
    */

    if (strlen($password) < 8) {

        set_flash(
            'error',
            'Password must contain at least 8 characters.'
        );

        header(
            'Location: ' .
            base_url('auth/login.php')
        );

        exit;
    }


    if ($password !== $confirm_password) {

        set_flash(
            'error',
            'Passwords do not match.'
        );

        header(
            'Location: ' .
            base_url('auth/login.php')
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Hash Token
    |--------------------------------------------------------------------------
    */

    $token_hash = hash(
        'sha256',
        $token
    );


    /*
    |--------------------------------------------------------------------------
    | Find Valid Reset Token
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT
            id,
            user_id
        FROM password_resets
        WHERE token_hash = ?
          AND used_at IS NULL
          AND expires_at > NOW()
        LIMIT 1
    ");

    $stmt->execute([$token_hash]);

    $reset = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$reset) {

        set_flash(
            'error',
            'This password reset link is invalid or expired.'
        );

        header(
            'Location: ' .
            base_url('auth/login.php')
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Hash New Password
    |--------------------------------------------------------------------------
    */

    $new_password_hash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );


    /*
    |--------------------------------------------------------------------------
    | Update Password + Consume Token
    |--------------------------------------------------------------------------
    */

    try {

        $pdo->beginTransaction();


        /*
        | Update user password
        */

        $update = $pdo->prepare("
            UPDATE users
            SET password = ?
            WHERE id = ?
        ");

        $update->execute([
            $new_password_hash,
            $reset['user_id']
        ]);


        /*
        | Mark reset token as used
        */

        $consume = $pdo->prepare("
            UPDATE password_resets
            SET used_at = NOW()
            WHERE id = ?
        ");

        $consume->execute([
            $reset['id']
        ]);


        $pdo->commit();


        log_activity(
            'Password was successfully reset'
        );


        set_flash(
            'success',
            'Your password has been reset successfully. Please login.'
        );


        header(
            'Location: ' .
            base_url('auth/login.php')
        );

        exit;

    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        set_flash(
            'error',
            'Unable to reset your password. Please try again.'
        );

        header(
            'Location: ' .
            base_url('auth/login.php')
        );

        exit;
    }
}

else {

    set_flash(
        'error',
        'Invalid authentication request.'
    );

    header(
        'Location: ' .
        base_url('index.php')
    );

    exit;
}
?>