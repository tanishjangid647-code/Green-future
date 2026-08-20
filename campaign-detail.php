
<?php

$page_title = "Campaign Details";

require_once __DIR__ . '/config/helpers.php';

$id = intval($_GET['id'] ?? 0);
/*
|--------------------------------------------------------------------------
| Get Campaign
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM campaigns
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$camp = $stmt->fetch(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Campaign Not Found
|--------------------------------------------------------------------------
*/

if (!$camp) {

    echo "
        <div class='container py-5 text-center'>
            <div class='alert alert-danger'>
                <i class='fas fa-exclamation-circle me-2'></i>
                Campaign Not Found
            </div>

            <a href='" . base_url('campaigns.php') . "'
               class='btn btn-success'>
                <i class='fas fa-arrow-left me-1'></i>
                Back to Campaigns
            </a>
        </div>
    ";

    require_once __DIR__ . '/includes/footer.php';

    exit;
}


/*
|--------------------------------------------------------------------------
| JOIN / CANCEL ACTIONS
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action'])
) {

    require_login();

    /*
    |--------------------------------------------------------------------------
    | CSRF Verification
    |--------------------------------------------------------------------------
    */

    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrf_token)) {

        set_flash(
            'error',
            'Invalid security token. Please refresh the page and try again.'
        );

        header(
            'Location: ' .
            base_url('campaign-detail.php?id=' . $id)
        );

        exit;
    }


    $user_id = (int) $_SESSION['user_id'];

    $action = $_POST['action'];


    /*
    |--------------------------------------------------------------------------
    | JOIN CAMPAIGN
    |--------------------------------------------------------------------------
    */

    if ($action === 'join') {

        try {

            $pdo->beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | Lock Campaign Row
            |--------------------------------------------------------------------------
            |
            | Prevents two users from taking the final slot simultaneously.
            |
            */

            $stmt = $pdo->prepare("
                SELECT *
                FROM campaigns
                WHERE id = ?
                FOR UPDATE
            ");

            $stmt->execute([$id]);

            $campaign = $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$campaign) {

                throw new Exception(
                    'Campaign no longer exists.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Campaign Status
            |--------------------------------------------------------------------------
            */

            $campaign_status =
                strtolower(
                    trim(
                        (string)($campaign['status'] ?? '')
                    )
                );

            $blocked_statuses = [
                'cancelled',
                'completed',
                'closed',
                'inactive'
            ];

            if (in_array($campaign_status, $blocked_statuses, true)) {

                throw new Exception(
                    'This campaign is no longer accepting participants.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Event Date Check
            |--------------------------------------------------------------------------
            */

            if (
                !empty($campaign['event_date']) &&
                strtotime($campaign['event_date']) < strtotime(date('Y-m-d'))
            ) {

                throw new Exception(
                    'This plantation campaign has already taken place.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Check Existing Participation
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                SELECT id, status
                FROM campaign_participants
                WHERE campaign_id = ?
                  AND user_id = ?
                LIMIT 1
            ");

            $stmt->execute([
                $id,
                $user_id
            ]);

            $participation =
                $stmt->fetch(PDO::FETCH_ASSOC);


            /*
            |--------------------------------------------------------------------------
            | Already Registered
            |--------------------------------------------------------------------------
            */

            if (
                $participation &&
                $participation['status'] !== 'cancelled'
            ) {

                throw new Exception(
                    'You are already registered for this campaign.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Capacity Check
            |--------------------------------------------------------------------------
            */

            $current_volunteers =
                (int)($campaign['current_volunteers'] ?? 0);

            $max_volunteers =
                (int)($campaign['max_volunteers'] ?? 0);


            if (
                $max_volunteers > 0 &&
                $current_volunteers >= $max_volunteers
            ) {

                throw new Exception(
                    'Sorry, this plantation drive is already full.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Register / Re-register User
            |--------------------------------------------------------------------------
            */

            if ($participation) {

                /*
                | User previously cancelled.
                | Reactivate the existing record instead of
                | creating a duplicate.
                */

                $stmt = $pdo->prepare("
                    UPDATE campaign_participants
                    SET status = 'registered'
                    WHERE id = ?
                ");

                $stmt->execute([
                    $participation['id']
                ]);

            } else {

                /*
                | New participant.
                */

                $stmt = $pdo->prepare("
                    INSERT INTO campaign_participants
                    (
                        campaign_id,
                        user_id,
                        status
                    )
                    VALUES
                    (?, ?, 'registered')
                ");

                $stmt->execute([
                    $id,
                    $user_id
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Increase Volunteer Count
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                UPDATE campaigns
                SET current_volunteers =
                    current_volunteers + 1
                WHERE id = ?
            ");

            $stmt->execute([
                $id
            ]);


            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */

            log_activity(
                "Joined campaign #{$id}"
            );


            $pdo->commit();


            set_flash(
                'success',
                'Successfully registered for this plantation drive!'
            );

        } catch (Exception $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            set_flash(
                'error',
                $e->getMessage()
            );

        } catch (PDOException $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            /*
            | Handle duplicate database constraint safely.
            */

            if ($e->getCode() === '23000') {

                set_flash(
                    'error',
                    'You are already registered for this campaign.'
                );

            } else {

                set_flash(
                    'error',
                    'Unable to register for the campaign. Please try again.'
                );
            }
        }


        header(
            'Location: ' .
            base_url('campaign-detail.php?id=' . $id)
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | CANCEL PARTICIPATION
    |--------------------------------------------------------------------------
    */

    elseif ($action === 'cancel') {

        try {

            $pdo->beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | Find Participation
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                SELECT id, status
                FROM campaign_participants
                WHERE campaign_id = ?
                  AND user_id = ?
                LIMIT 1
                FOR UPDATE
            ");

            $stmt->execute([
                $id,
                $user_id
            ]);

            $participation =
                $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$participation) {

                throw new Exception(
                    'You are not registered for this campaign.'
                );
            }


            if ($participation['status'] === 'cancelled') {

                throw new Exception(
                    'Your participation is already cancelled.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Mark Participation Cancelled
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                UPDATE campaign_participants
                SET status = 'cancelled'
                WHERE id = ?
            ");

            $stmt->execute([
                $participation['id']
            ]);


            /*
            |--------------------------------------------------------------------------
            | Decrease Volunteer Count
            |--------------------------------------------------------------------------
            |
            | GREATEST prevents the count from becoming negative.
            |
            */

            $stmt = $pdo->prepare("
                UPDATE campaigns
                SET current_volunteers =
                    GREATEST(current_volunteers - 1, 0)
                WHERE id = ?
            ");

            $stmt->execute([
                $id
            ]);


            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */

            log_activity(
                "Cancelled participation in campaign #{$id}"
            );


            $pdo->commit();


            set_flash(
                'success',
                'Your campaign participation has been cancelled.'
            );

        } catch (Exception $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            set_flash(
                'error',
                $e->getMessage()
            );

        } catch (PDOException $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            set_flash(
                'error',
                'Unable to cancel participation. Please try again.'
            );
        }


        header(
            'Location: ' .
            base_url('campaign-detail.php?id=' . $id)
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Unknown Action
    |--------------------------------------------------------------------------
    */

    else {

        set_flash(
            'error',
            'Invalid campaign action.'
        );

        header(
            'Location: ' .
            base_url('campaign-detail.php?id=' . $id)
        );

        exit;
    }
}
/*
|--------------------------------------------------------------------------
| Load Page Layout
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

/*
|--------------------------------------------------------------------------
| Current User
|--------------------------------------------------------------------------
*/

$user = current_user();


/*
|--------------------------------------------------------------------------
| Check Participation Status
|--------------------------------------------------------------------------
*/

$is_registered = false;
$is_cancelled = false;

if ($user) {

    $stmt = $pdo->prepare("
        SELECT status
        FROM campaign_participants
        WHERE campaign_id = ?
          AND user_id = ?
        LIMIT 1
    ");

    $stmt->execute([
        $id,
        $user['id']
    ]);

    $participation =
        $stmt->fetch(PDO::FETCH_ASSOC);


    if ($participation) {

        if ($participation['status'] === 'registered') {

            $is_registered = true;

        } elseif ($participation['status'] === 'cancelled') {

            $is_cancelled = true;
        }
    }
}


/*
|--------------------------------------------------------------------------
| Campaign Reviews
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        r.*,
        u.full_name
    FROM reviews r
    LEFT JOIN users u
        ON r.user_id = u.id
    WHERE r.campaign_id = ?
    ORDER BY r.id DESC
");

$stmt->execute([$id]);

$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container py-5">

    <div class="row g-4">

        <!-- ==========================================================
             MAIN CAMPAIGN
        =========================================================== -->

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">

                <img
                    src="https://picsum.photos/1200/500?random=<?php echo (int)$camp['id']; ?>"
                    class="img-fluid"
                    style="height: 350px; object-fit: cover;"
                    alt="Campaign Banner"
                >

                <div class="card-body p-4 p-md-5">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <span
                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">

                            <i class="fas fa-leaf me-1"></i>

                            Green Future Campaign

                        </span>

                        <span class="text-muted small">

                            <i class="fas fa-calendar me-1"></i>

                            <?php
                            echo date(
                                'F d, Y',
                                strtotime($camp['event_date'])
                            );
                            ?>

                        </span>

                    </div>


                    <h2 class="fw-bold mb-3">

                        <?php
                        echo sanitize($camp['title']);
                        ?>

                    </h2>


                    <p class="text-secondary leading-relaxed mb-4">

                        <?php
                        echo nl2br(
                            sanitize($camp['description'])
                        );
                        ?>

                    </p>


                    <!-- ==================================================
                         EVENT INFORMATION
                    =================================================== -->

                    <h5 class="fw-bold mb-3">

                        <i class="fas fa-info-circle text-success me-2"></i>

                        Event Specifications

                    </h5>


                    <div class="row g-3 mb-4">

                        <div class="col-md-6">

                            <div class="p-3 bg-light rounded-3">

                                <small class="text-muted d-block">
                                    Organizing Body
                                </small>

                                <strong class="text-dark">

                                    <?php
                                    echo sanitize(
                                        $camp['organizer']
                                    );
                                    ?>

                                </strong>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="p-3 bg-light rounded-3">

                                <small class="text-muted d-block">
                                    Target Tree Species
                                </small>

                                <strong class="text-success">

                                    <?php
                                    echo sanitize(
                                        $camp['tree_species']
                                    );
                                    ?>

                                </strong>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="p-3 bg-light rounded-3">

                                <small class="text-muted d-block">
                                    Event Time
                                </small>

                                <strong class="text-dark">

                                    <?php
                                    echo date(
                                        'h:i A',
                                        strtotime(
                                            $camp['event_time']
                                        )
                                    );
                                    ?>

                                    IST

                                </strong>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="p-3 bg-light rounded-3">

                                <small class="text-muted d-block">
                                    Volunteer Slots
                                </small>

                                <strong class="text-primary">

                                    <?php
                                    echo (int)
                                        $camp['current_volunteers'];
                                    ?>

                                    /

                                    <?php
                                    echo (int)
                                        $camp['max_volunteers'];
                                    ?>

                                    Joined

                                </strong>

                            </div>

                        </div>

                    </div>


                    <!-- ==================================================
                         REVIEWS
                    =================================================== -->

                    <h5 class="fw-bold mb-3">

                        <i class="fas fa-star text-warning me-2"></i>

                        Participant Feedback & Reviews

                    </h5>


                    <?php if (!empty($reviews)): ?>

                        <?php foreach ($reviews as $rev): ?>

                            <div class="border-bottom py-3">

                                <div
                                    class="d-flex justify-content-between align-items-center mb-1">

                                    <strong class="text-dark">

                                        <?php
                                        echo sanitize(
                                            $rev['full_name'] ??
                                            'Anonymous'
                                        );
                                        ?>

                                    </strong>


                                    <div class="text-warning small">

                                        <?php
                                        for (
                                            $i = 1;
                                            $i <= 5;
                                            $i++
                                        ):
                                        ?>

                                            <i
                                                class="fas fa-star<?php
                                                echo $i <= $rev['rating']
                                                    ? ''
                                                    : '-o';
                                                ?>">
                                            </i>

                                        <?php endfor; ?>

                                    </div>

                                </div>


                                <p class="small text-muted mb-0">

                                    <?php
                                    echo sanitize(
                                        $rev['comment']
                                    );
                                    ?>

                                </p>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <p class="small text-muted">

                            No reviews posted yet for this campaign.

                        </p>

                    <?php endif; ?>

                </div>

            </div>

        </div>


        <!-- ==========================================================
             SIDEBAR
        =========================================================== -->

        <div class="col-lg-4">

            <div
                class="glass-card p-4 rounded-4 sticky-top"
                style="top: 90px;">

                <h5 class="fw-bold mb-3">

                    Registration & Location

                </h5>


                <p class="small text-muted mb-3">

                    <i class="fas fa-map-marker-alt text-danger me-2"></i>

                    <?php
                    echo sanitize(
                        $camp['location_address']
                    );
                    ?>,

                    <?php
                    echo sanitize(
                        $camp['city']
                    );
                    ?>

                </p>


                <!-- ==================================================
                     REGISTRATION
                =================================================== -->

                <?php if ($is_registered): ?>

                    <div
                        class="alert alert-success rounded-3 text-center mb-3">

                        <i class="fas fa-check-circle me-1"></i>

                        You are enrolled in this drive!

                    </div>


                    <!-- Cancel Button -->

                    <form
                        action="<?php
                        echo base_url(
                            'campaign-detail.php?id=' .
                            $camp['id']
                        );
                        ?>"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to cancel your participation?');">

                        <input
                            type="hidden"
                            name="action"
                            value="cancel"
                        >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?php
                            echo htmlspecialchars(
                                generate_csrf_token()
                            );
                            ?>"
                        >

                        <button
                            type="submit"
                            class="btn btn-outline-danger w-100 py-2 mb-3">

                            <i class="fas fa-user-minus me-1"></i>

                            Cancel Participation

                        </button>

                    </form>


                <?php elseif ($is_cancelled): ?>

                    <div
                        class="alert alert-warning rounded-3 text-center mb-3">

                        <i class="fas fa-info-circle me-1"></i>

                        Your previous participation was cancelled.

                    </div>


                    <form
                        action="<?php
                        echo base_url(
                            'campaign-detail.php?id=' .
                            $camp['id']
                        );
                        ?>"
                        method="POST">

                        <input
                            type="hidden"
                            name="action"
                            value="join"
                        >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?php
                            echo htmlspecialchars(
                                generate_csrf_token()
                            );
                            ?>"
                        >

                        <button
                            type="submit"
                            class="btn btn-primary-green w-100 py-2 fs-6 mb-3">

                            <i class="fas fa-user-plus me-1"></i>

                            Join Again

                        </button>

                    </form>


                <?php else: ?>

                    <form
                        action="<?php
                        echo base_url(
                            'campaign-detail.php?id=' .
                            $camp['id']
                        );
                        ?>"
                        method="POST">

                        <input
                            type="hidden"
                            name="action"
                            value="join"
                        >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?php
                            echo htmlspecialchars(
                                generate_csrf_token()
                            );
                            ?>"
                        >

                        <button
                            type="submit"
                            class="btn btn-primary-green w-100 py-2 fs-6 mb-3">

                            <i class="fas fa-user-plus me-1"></i>

                            Join Plantation Drive

                        </button>

                    </form>

                <?php endif; ?>


                <!-- ==================================================
                     QR CODE
                =================================================== -->

                <div class="text-center p-3 bg-light rounded-3">

                    <small class="text-muted d-block mb-2">

                        Scan QR for Campaign Check-in

                    </small>


                    <img
                        src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=GF-CAMPAIGN-<?php echo (int)$camp['id']; ?>"
                        class="img-fluid rounded border"
                        alt="Campaign QR Code"
                        loading="lazy"
                    >

                </div>

            </div>

        </div>

    </div>

</div>


<?php require_once __DIR__ . '/includes/footer.php'; ?>