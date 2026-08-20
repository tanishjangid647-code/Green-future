<?php

$page_title = "Update Profile";

require_once __DIR__ . '/../config/helpers.php';

require_login();

$user = current_user();


/*
|--------------------------------------------------------------------------
| Process Profile Update BEFORE Any HTML Output
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = sanitize($_POST['full_name'] ?? '');
    $phone     = sanitize($_POST['phone'] ?? '');
    $city      = sanitize($_POST['city'] ?? '');
    $state     = sanitize($_POST['state'] ?? '');


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (empty($full_name)) {

        set_flash(
            'error',
            'Full name cannot be empty.'
        );

        header(
            'Location: ' .
            base_url('user/profile.php')
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Update Database
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        UPDATE users
        SET
            full_name = ?,
            phone = ?,
            city = ?,
            state = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $full_name,
        $phone,
        $city,
        $state,
        $user['id']
    ]);


    /*
    |--------------------------------------------------------------------------
    | Update Session Name
    |--------------------------------------------------------------------------
    */

    $_SESSION['user_name'] = $full_name;


    /*
    |--------------------------------------------------------------------------
    | Success Message
    |--------------------------------------------------------------------------
    */

    set_flash(
        'success',
        'Profile updated successfully!'
    );


    /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

    header(
        'Location: ' .
        base_url('user/profile.php')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Load Header / Navbar AFTER POST Processing
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

?>

<div class="container py-4">

    <div class="row g-4">

        <!-- Sidebar -->

        <div class="col-lg-3">

            <?php
            require_once __DIR__ . '/../includes/sidebar.php';
            ?>

        </div>


        <!-- Main Content -->

        <div class="col-lg-9">

            <div
                class="glass-card p-4 rounded-4 mb-4">

                <h4 class="fw-bold mb-3">

                    <i
                        class="fas fa-user-edit
                               text-success me-2">
                    </i>

                    Profile & Settings

                </h4>


                <form
                    action="<?php
                    echo base_url(
                        'user/profile.php'
                    );
                    ?>"
                    method="POST">


                    <!-- Name + Email -->

                    <div
                        class="row g-3 mb-3">

                        <div class="col-md-6">

                            <label
                                class="form-label
                                       fw-semibold">

                                Full Name

                            </label>

                            <input
                                type="text"
                                name="full_name"
                                class="form-control"
                                value="<?php
                                echo sanitize(
                                    $user['full_name']
                                );
                                ?>"
                                maxlength="100"
                                required
                            >

                        </div>


                        <div class="col-md-6">

                            <label
                                class="form-label
                                       fw-semibold">

                                Email Address
                                (Read Only)

                            </label>

                            <input
                                type="email"
                                class="form-control bg-light"
                                value="<?php
                                echo sanitize(
                                    $user['email']
                                );
                                ?>"
                                readonly
                            >

                        </div>

                    </div>


                    <!-- Phone / City / State -->

                    <div
                        class="row g-3 mb-3">

                        <div class="col-md-4">

                            <label
                                class="form-label
                                       fw-semibold">

                                Phone Number

                            </label>

                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="<?php
                                echo sanitize(
                                    $user['phone'] ?? ''
                                );
                                ?>"
                                maxlength="20"
                            >

                        </div>


                        <div class="col-md-4">

                            <label
                                class="form-label
                                       fw-semibold">

                                City

                            </label>

                            <input
                                type="text"
                                name="city"
                                class="form-control"
                                value="<?php
                                echo sanitize(
                                    $user['city'] ?? ''
                                );
                                ?>"
                                maxlength="100"
                            >

                        </div>


                        <div class="col-md-4">

                            <label
                                class="form-label
                                       fw-semibold">

                                State

                            </label>

                            <input
                                type="text"
                                name="state"
                                class="form-control"
                                value="<?php
                                echo sanitize(
                                    $user['state'] ?? ''
                                );
                                ?>"
                                maxlength="100"
                            >

                        </div>

                    </div>


                    <!-- Save -->

                    <button
                        type="submit"
                        class="btn btn-primary-green px-4">

                        <i
                            class="fas fa-save me-1">
                        </i>

                        Save Changes

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>


<?php

require_once __DIR__ . '/../includes/footer.php';

?>