<?php

require_once __DIR__ . '/config/helpers.php';

require_login();

$user_id = (int) $_SESSION['user_id'];

$action = $_POST['action'] ?? '';

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

        header(
            'Location: ' . base_url('campaigns.php')
        );

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Validate Campaign ID
|--------------------------------------------------------------------------
*/

$campaign_id = (int) ($_POST['campaign_id'] ?? 0);

if ($campaign_id <= 0) {

    set_flash(
        'error',
        'Invalid campaign.'
    );

    header(
        'Location: ' . base_url('campaigns.php')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Check Campaign Exists
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id, title
    FROM campaigns
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$campaign_id]);

$campaign = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$campaign) {

    set_flash(
        'error',
        'Campaign not found.'
    );

    header(
        'Location: ' . base_url('campaigns.php')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| ADD TO WISHLIST
|--------------------------------------------------------------------------
*/

if ($action === 'add') {

    try {

        $stmt = $pdo->prepare("
            INSERT INTO wishlist
            (
                user_id,
                campaign_id
            )
            VALUES
            (?, ?)
        ");

        $stmt->execute([
            $user_id,
            $campaign_id
        ]);

        log_activity(
            "Added campaign #{$campaign_id} to wishlist"
        );

        set_flash(
            'success',
            'Campaign added to your wishlist.'
        );

    } catch (PDOException $e) {

        /*
        | Unique constraint prevents duplicates.
        */

        if ($e->getCode() === '23000') {

            set_flash(
                'info',
                'This campaign is already in your wishlist.'
            );

        } else {

            set_flash(
                'error',
                'Unable to add campaign to wishlist.'
            );
        }
    }
}


/*
|--------------------------------------------------------------------------
| REMOVE FROM WISHLIST
|--------------------------------------------------------------------------
*/

elseif ($action === 'remove') {

    $stmt = $pdo->prepare("
        DELETE FROM wishlist
        WHERE user_id = ?
          AND campaign_id = ?
    ");

    $stmt->execute([
        $user_id,
        $campaign_id
    ]);

    if ($stmt->rowCount() > 0) {

        log_activity(
            "Removed campaign #{$campaign_id} from wishlist"
        );

        set_flash(
            'success',
            'Campaign removed from your wishlist.'
        );

    } else {

        set_flash(
            'info',
            'Campaign was not in your wishlist.'
        );
    }
}


/*
|--------------------------------------------------------------------------
| Invalid Action
|--------------------------------------------------------------------------
*/

else {

    set_flash(
        'error',
        'Invalid wishlist action.'
    );
}


/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

$redirect =
    $_POST['redirect'] ?? 'campaigns.php';

/*
| Prevent external redirects.
*/

if (
    !is_string($redirect) ||
    str_contains($redirect, '://') ||
    str_starts_with($redirect, '//')
) {

    $redirect = 'campaigns.php';
}

header(
    'Location: ' .
    base_url($redirect)
);

exit;