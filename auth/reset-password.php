<?php
// Green Future - Reset Password

require_once __DIR__ . '/../config/helpers.php';

$token = trim($_GET['token'] ?? '');

if (empty($token) || !preg_match('/^[a-f0-9]{64}$/i', $token)) {
    set_flash('error', 'Invalid or missing password reset link.');
    header('Location: ' . base_url('auth/login.php'));
    exit;
}

$token_hash = hash('sha256', $token);

/*
|--------------------------------------------------------------------------
| Find Valid Token
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        pr.id AS reset_id,
        pr.user_id,
        pr.expires_at,
        u.email,
        u.full_name
    FROM password_resets pr
    INNER JOIN users u
        ON u.id = pr.user_id
    WHERE pr.token_hash = ?
      AND pr.used_at IS NULL
      AND pr.expires_at > NOW()
      AND u.status = 'active'
    LIMIT 1
");

$stmt->execute([$token_hash]);

$reset = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reset) {
    set_flash(
        'error',
        'This password reset link is invalid, expired, or has already been used.'
    );

    header('Location: ' . base_url('auth/login.php'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Reset Password - Green Future</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        rel="stylesheet">

    <style>

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                linear-gradient(
                    135deg,
                    #e8f5e9,
                    #f1f8e9
                );
            font-family: Arial, sans-serif;
        }

        .reset-card {
            width: 100%;
            max-width: 450px;
            background: #ffffff;
            border-radius: 20px;
            padding: 35px;
            box-shadow:
                0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #2e7d32;
            color: white;
            font-size: 30px;
        }

        .btn-green {
            background: #2e7d32;
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-green:hover {
            background: #1b5e20;
            color: white;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
        }

        .strength-bar {
            height: 5px;
            border-radius: 5px;
            background: #e0e0e0;
            margin-top: 8px;
            overflow: hidden;
        }

        .strength-progress {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }

    </style>

</head>

<body>

<div class="reset-card">

    <div class="logo">
        <i class="fa-solid fa-leaf"></i>
    </div>

    <h3 class="text-center fw-bold mb-2">
        Reset Password
    </h3>

    <p class="text-center text-muted mb-4">
        Create a new secure password for your Green Future account.
    </p>

    <form
        action="<?php echo base_url('auth/process-auth.php'); ?>"
        method="POST"
        id="resetPasswordForm">

        <input
            type="hidden"
            name="action"
            value="reset_password">

        <input
            type="hidden"
            name="csrf_token"
            value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">

        <input
            type="hidden"
            name="token"
            value="<?php echo htmlspecialchars($token); ?>">

        <div class="mb-3">

            <label class="form-label">
                New Password
            </label>

            <div class="password-wrapper">

                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    minlength="8"
                    required
                    autocomplete="new-password"
                    placeholder="Enter new password">

                <i
                    class="fa-solid fa-eye password-toggle"
                    id="togglePassword">
                </i>

            </div>

            <div class="strength-bar">
                <div
                    class="strength-progress"
                    id="strengthProgress">
                </div>
            </div>

            <small
                class="text-muted"
                id="strengthText">
                Minimum 8 characters
            </small>

        </div>


        <div class="mb-4">

            <label class="form-label">
                Confirm New Password
            </label>

            <div class="password-wrapper">

                <input
                    type="password"
                    name="confirm_password"
                    id="confirmPassword"
                    class="form-control"
                    minlength="8"
                    required
                    autocomplete="new-password"
                    placeholder="Confirm new password">

                <i
                    class="fa-solid fa-eye password-toggle"
                    id="toggleConfirmPassword">
                </i>

            </div>

        </div>


        <button
            type="submit"
            class="btn btn-green w-100">

            <i class="fa-solid fa-lock me-2"></i>

            Reset Password

        </button>

    </form>


    <div class="text-center mt-4">

        <a
            href="<?php echo base_url('auth/login.php'); ?>"
            class="text-decoration-none">

            <i class="fa-solid fa-arrow-left me-1"></i>

            Back to Login

        </a>

    </div>

</div>


<script>

const password = document.getElementById('password');

const strengthProgress =
    document.getElementById('strengthProgress');

const strengthText =
    document.getElementById('strengthText');


password.addEventListener('input', function () {

    const value = password.value;

    let strength = 0;

    if (value.length >= 8) {
        strength++;
    }

    if (/[A-Z]/.test(value)) {
        strength++;
    }

    if (/[a-z]/.test(value)) {
        strength++;
    }

    if (/[0-9]/.test(value)) {
        strength++;
    }

    if (/[^A-Za-z0-9]/.test(value)) {
        strength++;
    }


    const percentage =
        (strength / 5) * 100;

    strengthProgress.style.width =
        percentage + '%';


    if (strength <= 1) {

        strengthText.textContent =
            'Weak password';

    } else if (strength <= 3) {

        strengthText.textContent =
            'Medium password';

    } else {

        strengthText.textContent =
            'Strong password';
    }

});


document.getElementById('togglePassword')
    .addEventListener('click', function () {

        const type =
            password.type === 'password'
                ? 'text'
                : 'password';

        password.type = type;

        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');

    });


document.getElementById('toggleConfirmPassword')
    .addEventListener('click', function () {

        const confirmPassword =
            document.getElementById('confirmPassword');

        const type =
            confirmPassword.type === 'password'
                ? 'text'
                : 'password';

        confirmPassword.type = type;

        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');

    });


document.getElementById('resetPasswordForm')
    .addEventListener('submit', function (event) {

        const password =
            document.getElementById('password').value;

        const confirmPassword =
            document.getElementById('confirmPassword').value;


        if (password !== confirmPassword) {

            event.preventDefault();

            alert('Passwords do not match.');

            return;
        }


        if (password.length < 8) {

            event.preventDefault();

            alert(
                'Password must contain at least 8 characters.'
            );

        }

    });

</script>

</body>
</html>