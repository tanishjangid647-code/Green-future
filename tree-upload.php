<?php

require_once __DIR__ . '/config/helpers.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . base_url('trees.php'));
    exit;
}


/*
|--------------------------------------------------------------------------
| CSRF Protection
|--------------------------------------------------------------------------
*/

$csrf_token = $_POST['csrf_token'] ?? '';

if (!verify_csrf_token($csrf_token)) {

    set_flash(
        'error',
        'Invalid security token. Please refresh the page and try again.'
    );

    header('Location: ' . base_url('trees.php'));
    exit;
}


/*
|--------------------------------------------------------------------------
| Input
|--------------------------------------------------------------------------
*/

$tree_id = (int)($_POST['tree_id'] ?? 0);

$growth_height =
    trim($_POST['growth_height_cm'] ?? '');

$note =
    trim($_POST['note'] ?? '');


if ($tree_id <= 0) {

    set_flash(
        'error',
        'Invalid tree selected.'
    );

    header('Location: ' . base_url('trees.php'));
    exit;
}


/*
|--------------------------------------------------------------------------
| Get Current User
|--------------------------------------------------------------------------
*/

$user = current_user();

$user_id =
    (int)$user['id'];

$user_role =
    strtolower(
        trim(
            (string)($user['role'] ?? '')
        )
    );


/*
|--------------------------------------------------------------------------
| Find Tree
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        tree_code,
        user_id,
        assigned_volunteer_id,
        current_height_cm
    FROM trees
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([
    $tree_id
]);

$tree =
    $stmt->fetch(PDO::FETCH_ASSOC);


if (!$tree) {

    set_flash(
        'error',
        'Tree not found.'
    );

    header('Location: ' . base_url('trees.php'));
    exit;
}


/*
|--------------------------------------------------------------------------
| Authorization
|--------------------------------------------------------------------------
|
| Allowed:
| Admin
| Volunteer assigned to tree
| Tree owner
|
*/

$is_admin =
    $user_role === 'admin';

$is_owner =
    (int)$tree['user_id'] === $user_id;

$is_assigned_volunteer =
    !empty($tree['assigned_volunteer_id']) &&
    (int)$tree['assigned_volunteer_id'] === $user_id;


if (
    !$is_admin &&
    !$is_owner &&
    !$is_assigned_volunteer
) {

    set_flash(
        'error',
        'You are not authorized to update this tree.'
    );

    header(
        'Location: ' .
        base_url(
            'trees.php?code=' .
            urlencode($tree['tree_code'])
        )
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Validate Image
|--------------------------------------------------------------------------
*/

if (
    !isset($_FILES['tree_image']) ||
    $_FILES['tree_image']['error'] !== UPLOAD_ERR_OK
) {

    set_flash(
        'error',
        'Please select a valid image to upload.'
    );

    header(
        'Location: ' .
        base_url(
            'trees.php?code=' .
            urlencode($tree['tree_code'])
        )
    );

    exit;
}


$file = $_FILES['tree_image'];


/*
|--------------------------------------------------------------------------
| File Size
|--------------------------------------------------------------------------
|
| Maximum: 5 MB
|
*/

$max_size =
    5 * 1024 * 1024;


if ($file['size'] > $max_size) {

    set_flash(
        'error',
        'Image size must be 5 MB or less.'
    );

    header(
        'Location: ' .
        base_url(
            'trees.php?code=' .
            urlencode($tree['tree_code'])
        )
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Detect MIME Type
|--------------------------------------------------------------------------
*/

$finfo =
    new finfo(FILEINFO_MIME_TYPE);

$mime_type =
    $finfo->file(
        $file['tmp_name']
    );


$allowed_types = [

    'image/jpeg' => 'jpg',

    'image/png' => 'png',

    'image/webp' => 'webp'

];


if (!isset($allowed_types[$mime_type])) {

    set_flash(
        'error',
        'Only JPG, PNG, and WEBP images are allowed.'
    );

    header(
        'Location: ' .
        base_url(
            'trees.php?code=' .
            urlencode($tree['tree_code'])
        )
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Validate Growth Height
|--------------------------------------------------------------------------
*/

if ($growth_height !== '') {

    if (
        !is_numeric($growth_height) ||
        $growth_height < 0 ||
        $growth_height > 100000
    ) {

        set_flash(
            'error',
            'Please enter a valid tree height.'
        );

        header(
            'Location: ' .
            base_url(
                'trees.php?code=' .
                urlencode($tree['tree_code'])
            )
        );

        exit;
    }

    $growth_height =
        (int)$growth_height;

} else {

    $growth_height = null;
}


/*
|--------------------------------------------------------------------------
| Prepare Upload Directory
|--------------------------------------------------------------------------
*/

$upload_dir =
    __DIR__ .
    '/uploads/tree-images/';


if (!is_dir($upload_dir)) {

    if (
        !mkdir(
            $upload_dir,
            0755,
            true
        )
    ) {

        set_flash(
            'error',
            'Unable to create image upload directory.'
        );

        header(
            'Location: ' .
            base_url(
                'trees.php?code=' .
                urlencode($tree['tree_code'])
            )
        );

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Generate Secure Filename
|--------------------------------------------------------------------------
*/

$extension =
    $allowed_types[$mime_type];

$filename =
    'tree_' .
    $tree_id .
    '_' .
    bin2hex(
        random_bytes(12)
    ) .
    '.' .
    $extension;


$destination =
    $upload_dir .
    $filename;


/*
|--------------------------------------------------------------------------
| Move Uploaded File
|--------------------------------------------------------------------------
*/

if (
    !move_uploaded_file(
        $file['tmp_name'],
        $destination
    )
) {

    set_flash(
        'error',
        'Unable to save the uploaded image.'
    );

    header(
        'Location: ' .
        base_url(
            'trees.php?code=' .
            urlencode($tree['tree_code'])
        )
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Database Insert
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();


    /*
    |--------------------------------------------------------------------------
    | Image URL
    |--------------------------------------------------------------------------
    */

    $image_url =
        'uploads/tree-images/' .
        $filename;


    /*
    |--------------------------------------------------------------------------
    | Insert Image Record
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        INSERT INTO tree_images
        (
            tree_id,
            image_url,
            growth_height_cm,
            note,
            uploaded_by
        )
        VALUES
        (?, ?, ?, ?, ?)
    ");

    $stmt->execute([

        $tree_id,

        $image_url,

        $growth_height,

        $note !== ''
            ? $note
            : null,

        $user_id

    ]);


    /*
    |--------------------------------------------------------------------------
    | Update Current Tree Height
    |--------------------------------------------------------------------------
    */

    if ($growth_height !== null) {

        $current_height =
            (int)($tree['current_height_cm'] ?? 0);


        /*
        | Only update if the new measurement
        | is greater than the current value.
        */

        if (
            $growth_height >
            $current_height
        ) {

            $stmt = $pdo->prepare("
                UPDATE trees
                SET current_height_cm = ?
                WHERE id = ?
            ");

            $stmt->execute([

                $growth_height,

                $tree_id

            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */

    log_activity(
        "Uploaded progress image for tree {$tree['tree_code']}"
    );


    $pdo->commit();


    set_flash(
        'success',
        'Tree progress image uploaded successfully!'
    );

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    /*
    | Remove uploaded file if database operation fails.
    */

    if (file_exists($destination)) {
        unlink($destination);
    }


    set_flash(
        'error',
        'Unable to save tree progress. Please try again.'
    );
}


/*
|--------------------------------------------------------------------------
| Redirect Back
|--------------------------------------------------------------------------
*/

header(
    'Location: ' .
    base_url(
        'trees.php?code=' .
        urlencode($tree['tree_code'])
    )
);

exit;