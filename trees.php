<?php

$page_title = "Public Tree Tracking Portal";

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';


/*
|--------------------------------------------------------------------------
| Tree Search
|--------------------------------------------------------------------------
*/

$code = trim($_GET['code'] ?? '');


/*
|--------------------------------------------------------------------------
| Find Tree
|--------------------------------------------------------------------------
*/

$tree = null;

if ($code !== '') {

    $stmt = $pdo->prepare("
        SELECT
            t.*,

            c.title AS campaign_name,

            u.full_name AS planter_name,

            v.full_name AS volunteer_name

        FROM trees t

        LEFT JOIN campaigns c
            ON t.campaign_id = c.id

        LEFT JOIN users u
            ON t.user_id = u.id

        LEFT JOIN users v
            ON t.assigned_volunteer_id = v.id

        WHERE t.tree_code = ?
           OR t.id = ?

        LIMIT 1
    ");

    $stmt->execute([
        $code,
        is_numeric($code) ? (int)$code : 0
    ]);

    $tree = $stmt->fetch(PDO::FETCH_ASSOC);
}


/*
|--------------------------------------------------------------------------
| Current User
|--------------------------------------------------------------------------
*/

$user = current_user();

$can_upload = false;


/*
|--------------------------------------------------------------------------
| Check Upload Authorization
|--------------------------------------------------------------------------
*/

if ($tree && $user) {

    $user_id =
        (int)$user['id'];

    $user_role =
        strtolower(
            trim(
                (string)($user['role'] ?? '')
            )
        );

    $can_upload =

        $user_role === 'admin'

        ||

        (
            !empty($tree['user_id']) &&
            (int)$tree['user_id'] === $user_id
        )

        ||

        (
            !empty($tree['assigned_volunteer_id']) &&
            (int)$tree['assigned_volunteer_id'] === $user_id
        );
}


/*
|--------------------------------------------------------------------------
| Growth / Inspection Logs
|--------------------------------------------------------------------------
*/

$tree_logs = [];

if ($tree) {

    $stmt = $pdo->prepare("
        SELECT
            ti.*,

            u.full_name

        FROM tree_images ti

        LEFT JOIN users u
            ON ti.uploaded_by = u.id

        WHERE ti.tree_id = ?

        ORDER BY ti.uploaded_at DESC
    ");

    $stmt->execute([
        $tree['id']
    ]);

    $tree_logs =
        $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/*
|--------------------------------------------------------------------------
| Health Badge
|--------------------------------------------------------------------------
*/

function tree_health_badge($status)
{
    $status =
        strtolower(
            trim(
                (string)$status
            )
        );

    if ($status === 'healthy') {

        return 'bg-success';

    }

    if ($status === 'needs water') {

        return 'bg-warning text-dark';

    }

    if ($status === 'damaged') {

        return 'bg-danger';

    }

    if ($status === 'dead') {

        return 'bg-dark';

    }

    return 'bg-secondary';
}

?>

<div class="container py-5">


    <!-- ==========================================================
         HEADER
    =========================================================== -->

    <div class="text-center max-w-700 mx-auto mb-5">

        <span
            class="badge bg-success-subtle text-success
                   border border-success-subtle
                   rounded-pill mb-2">

            <i class="fas fa-seedling me-1"></i>

            Green Future Tree Tracking

        </span>


        <h2 class="fw-bold mb-2">

            Tree Tracking & Growth Timeline

        </h2>


        <p class="text-muted">

            Track plantation information, health,
            location and growth progress of registered trees.

        </p>


        <!-- ======================================================
             SEARCH
        ======================================================= -->

        <form
            action="<?php echo base_url('trees.php'); ?>"
            method="GET"
            class="d-flex gap-2 max-w-500 mx-auto mt-3">

            <input
                type="text"
                name="code"
                class="form-control form-control-lg rounded-pill"
                placeholder="Enter Tree ID / Tree Code"
                value="<?php
                echo htmlspecialchars($code);
                ?>"
                required
            >


            <button
                type="submit"
                class="btn btn-primary-green
                       btn-lg rounded-pill px-4">

                <i class="fas fa-search"></i>

            </button>

        </form>

    </div>


    <?php if ($tree): ?>


        <!-- ======================================================
             TREE CONTENT
        ======================================================= -->

        <div class="row g-4">


            <!-- ==================================================
                 TREE INFORMATION
            =================================================== -->

            <div class="col-lg-6">

                <div
                    class="glass-card p-4 p-md-5
                           rounded-4 shadow-sm h-100">


                    <!-- Tree Header -->

                    <div
                        class="d-flex justify-content-between
                               align-items-center mb-3">

                        <span
                            class="badge bg-success fs-6
                                   rounded-pill px-3 py-2">

                            <i class="fas fa-tree me-1"></i>

                            <?php
                            echo htmlspecialchars(
                                $tree['tree_code']
                            );
                            ?>

                        </span>


                        <span
                            class="badge
                            <?php
                            echo tree_health_badge(
                                $tree['health_status']
                            );
                            ?>
                            px-3 py-2 rounded-pill">

                            <?php
                            echo htmlspecialchars(
                                $tree['health_status']
                            );
                            ?>

                        </span>

                    </div>


                    <!-- Species -->

                    <h3
                        class="fw-bold text-dark mb-1">

                        <?php
                        echo htmlspecialchars(
                            $tree['species']
                        );
                        ?>

                    </h3>


                    <p
                        class="text-muted small mb-4">

                        <i
                            class="fas fa-bullhorn
                                   text-success me-1">
                        </i>

                        Campaign:

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $tree['campaign_name']
                                ??
                                'Direct Adoption'
                            );
                            ?>

                        </strong>

                    </p>


                    <!-- ==================================================
                         STATISTICS
                    =================================================== -->

                    <div
                        class="row g-3 mb-4 text-center">


                        <!-- Height -->

                        <div class="col-4">

                            <div
                                class="p-3 bg-light rounded-3">

                                <small
                                    class="text-muted d-block">

                                    Current Height

                                </small>

                                <h4
                                    class="fw-bold text-dark mb-0">

                                    <?php
                                    echo (int)(
                                        $tree['current_height_cm']
                                        ?? 0
                                    );
                                    ?>

                                    cm

                                </h4>

                            </div>

                        </div>


                        <!-- Carbon -->

                        <div class="col-4">

                            <div
                                class="p-3 bg-light rounded-3">

                                <small
                                    class="text-muted d-block">

                                    CO₂ Offset

                                </small>

                                <h4
                                    class="fw-bold text-success mb-0">

                                    <?php
                                    echo htmlspecialchars(
                                        $tree['carbon_offset_kg']
                                        ?? '0'
                                    );
                                    ?>

                                    kg

                                </h4>

                            </div>

                        </div>


                        <!-- Date -->

                        <div class="col-4">

                            <div
                                class="p-3 bg-light rounded-3">

                                <small
                                    class="text-muted d-block">

                                    Planted Date

                                </small>

                                <h6
                                    class="fw-bold text-dark
                                           mb-0 mt-1">

                                    <?php
                                    echo date(
                                        'M d, Y',
                                        strtotime(
                                            $tree['plantation_date']
                                        )
                                    );
                                    ?>

                                </h6>

                            </div>

                        </div>

                    </div>


                    <!-- ==================================================
                         TREE DETAILS
                    =================================================== -->

                    <div
                        class="small text-secondary mb-4">


                        <div class="mb-2">

                            <i
                                class="fas fa-user
                                       text-primary me-2">
                            </i>

                            Planted / Adopted By:

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $tree['planter_name']
                                    ??
                                    'Green Community'
                                );
                                ?>

                            </strong>

                        </div>


                        <div class="mb-2">

                            <i
                                class="fas fa-user-shield
                                       text-success me-2">
                            </i>

                            Monitoring Volunteer:

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $tree['volunteer_name']
                                    ??
                                    'Not Assigned'
                                );
                                ?>

                            </strong>

                        </div>


                        <div class="mb-2">

                            <i
                                class="fas fa-tint
                                       text-info me-2">
                            </i>

                            Water Routine:

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $tree['water_schedule']
                                    ??
                                    'Not specified'
                                );
                                ?>

                            </strong>

                        </div>


                        <div>

                            <i
                                class="fas fa-location-dot
                                       text-danger me-2">
                            </i>

                            Coordinates:

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $tree['latitude']
                                );
                                ?>,

                                <?php
                                echo htmlspecialchars(
                                    $tree['longitude']
                                );
                                ?>

                            </strong>

                        </div>

                    </div>


                    <!-- ==================================================
                         QR VERIFICATION
                    =================================================== -->

                    <div
                        class="d-flex align-items-center
                               gap-3 p-3 bg-light rounded-3">

                        <img
                            src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=GF-TREE-<?php echo urlencode($tree['tree_code']); ?>"
                            class="rounded border"
                            alt="Tree QR Verification"
                            loading="lazy"
                        >


                        <div>

                            <h6
                                class="fw-bold mb-1">

                                Official QR Verification

                            </h6>

                            <small
                                class="text-muted">

                                Scan this QR code to open
                                the public tracking page
                                for this tree.

                            </small>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 MAP + UPLOAD
            =================================================== -->

            <div class="col-lg-6">


                <!-- ==================================================
                     MAP
                =================================================== -->

                <div
                    class="glass-card p-4
                           rounded-4 shadow-sm mb-4">

                    <h5 class="fw-bold mb-3">

                        <i
                            class="fas fa-map-marked-alt
                                   text-danger me-2">
                        </i>

                        Plantation Location

                    </h5>


                    <?php

                    $latitude =
                        (float)$tree['latitude'];

                    $longitude =
                        (float)$tree['longitude'];

                    ?>

                    <div
                        class="rounded-3 overflow-hidden
                               shadow-sm mb-3"
                        style="height:250px;">

                        <iframe
                            src="https://maps.google.com/maps?q=<?php
                            echo $latitude;
                            ?>,<?php
                            echo $longitude;
                            ?>&hl=en&z=15&output=embed"
                            width="100%"
                            height="100%"
                            style="border:0;"
                            allowfullscreen
                            loading="lazy">
                        </iframe>

                    </div>


                    <div
                        class="d-flex justify-content-between
                               align-items-center">

                        <small
                            class="text-muted">

                            <i
                                class="fas fa-crosshairs
                                       me-1">
                            </i>

                            <?php
                            echo $latitude;
                            ?>,

                            <?php
                            echo $longitude;
                            ?>

                        </small>


                        <a
                            href="https://www.google.com/maps/search/?api=1&query=<?php echo $latitude; ?>,<?php echo $longitude; ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-sm btn-outline-success
                                   rounded-pill">

                            <i
                                class="fas fa-external-link-alt me-1">
                            </i>

                            Open Map

                        </a>

                    </div>

                </div>


                <!-- ==================================================
                     UPLOAD FORM
                =================================================== -->

                <?php if ($can_upload): ?>

                    <div
                        class="glass-card p-4
                               rounded-4 shadow-sm mb-4">

                        <h5 class="fw-bold mb-3">

                            <i
                                class="fas fa-camera
                                       text-success me-2">
                            </i>

                            Update Tree Progress

                        </h5>


                        <p
                            class="small text-muted">

                            Upload a recent image and
                            record the tree's latest growth.

                        </p>


                        <form
                            action="<?php
                            echo base_url(
                                'tree-upload.php'
                            );
                            ?>"
                            method="POST"
                            enctype="multipart/form-data">


                            <input
                                type="hidden"
                                name="tree_id"
                                value="<?php
                                echo (int)$tree['id'];
                                ?>"
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


                            <!-- Image -->

                            <div class="mb-3">

                                <label
                                    class="form-label fw-semibold">

                                    Progress Image

                                </label>

                                <input
                                    type="file"
                                    name="tree_image"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                    required
                                >

                                <small
                                    class="text-muted">

                                    JPG, PNG or WEBP.
                                    Maximum 5 MB.

                                </small>

                            </div>


                            <!-- Height -->

                            <div class="mb-3">

                                <label
                                    class="form-label fw-semibold">

                                    Current Height (cm)

                                </label>

                                <input
                                    type="number"
                                    name="growth_height_cm"
                                    class="form-control"
                                    min="0"
                                    max="100000"
                                    step="1"
                                    placeholder="Example: 45"
                                    value="<?php
                                    echo (int)(
                                        $tree['current_height_cm']
                                        ?? 0
                                    );
                                    ?>"
                                >

                            </div>


                            <!-- Note -->

                            <div class="mb-3">

                                <label
                                    class="form-label fw-semibold">

                                    Observation / Note

                                </label>

                                <textarea
                                    name="note"
                                    class="form-control"
                                    rows="3"
                                    maxlength="255"
                                    placeholder="Example: Tree is healthy and new leaves are visible."></textarea>

                            </div>


                            <button
                                type="submit"
                                class="btn btn-primary-green
                                       rounded-pill w-100">

                                <i
                                    class="fas fa-cloud-upload-alt
                                           me-1">
                                </i>

                                Upload Progress

                            </button>

                        </form>

                    </div>

                <?php endif; ?>


            </div>

        </div>


        <!-- ==========================================================
             GROWTH HISTORY
        =========================================================== -->

        <div class="row mt-4">

            <div class="col-12">

                <div
                    class="glass-card p-4 rounded-4
                           shadow-sm">

                    <div
                        class="d-flex justify-content-between
                               align-items-center mb-4">

                        <div>

                            <h5
                                class="fw-bold mb-1">

                                <i
                                    class="fas fa-chart-line
                                           text-success me-2">
                                </i>

                                Tree Growth Timeline

                            </h5>

                            <small
                                class="text-muted">

                                Track progress through
                                uploaded inspections.

                            </small>

                        </div>


                        <span
                            class="badge bg-success-subtle
                                   text-success rounded-pill">

                            <?php
                            echo count($tree_logs);
                            ?>

                            Updates

                        </span>

                    </div>


                    <?php if (!empty($tree_logs)): ?>

                        <div class="row g-4">

                            <?php foreach ($tree_logs as $index => $log): ?>

                                <div class="col-md-6 col-lg-4">

                                    <div
                                        class="card border-0
                                               shadow-sm rounded-4
                                               h-100 overflow-hidden">


                                        <!-- Image -->

                                        <?php if (!empty($log['image_url'])): ?>

                                            <a
                                                href="<?php
                                                echo base_url(
                                                    $log['image_url']
                                                );
                                                ?>"
                                                target="_blank"
                                                rel="noopener noreferrer">

                                                <img
                                                    src="<?php
                                                    echo base_url(
                                                        $log['image_url']
                                                    );
                                                    ?>"
                                                    class="w-100"
                                                    style="height:220px;
                                                           object-fit:cover;"
                                                    alt="Tree progress image"
                                                    loading="lazy"
                                                >

                                            </a>

                                        <?php else: ?>

                                            <div
                                                class="d-flex
                                                       align-items-center
                                                       justify-content-center
                                                       bg-light"
                                                style="height:220px;">

                                                <i
                                                    class="fas fa-tree
                                                           text-success"
                                                    style="font-size:50px;">
                                                </i>

                                            </div>

                                        <?php endif; ?>


                                        <div
                                            class="card-body p-4">


                                            <div
                                                class="d-flex
                                                       justify-content-between
                                                       align-items-center
                                                       mb-2">

                                                <span
                                                    class="badge
                                                           bg-success
                                                           rounded-pill">

                                                    Update

                                                    <?php
                                                    echo count(
                                                        $tree_logs
                                                    ) - $index;
                                                    ?>

                                                </span>


                                                <small
                                                    class="text-muted">

                                                    <?php
                                                    echo date(
                                                        'M d, Y',
                                                        strtotime(
                                                            $log['uploaded_at']
                                                        )
                                                    );
                                                    ?>

                                                </small>

                                            </div>


                                            <!-- Height -->

                                            <div
                                                class="p-3 bg-light
                                                       rounded-3 mb-3">

                                                <small
                                                    class="text-muted
                                                           d-block">

                                                    Measured Height

                                                </small>

                                                <strong
                                                    class="fs-5 text-success">

                                                    <?php
                                                    echo $log[
                                                        'growth_height_cm'
                                                    ] !== null

                                                    ? (int)$log[
                                                        'growth_height_cm'
                                                    ] . ' cm'

                                                    : 'Not recorded';
                                                    ?>

                                                </strong>

                                            </div>


                                            <!-- Uploaded By -->

                                            <p
                                                class="small text-muted
                                                       mb-2">

                                                <i
                                                    class="fas fa-user
                                                           me-1">
                                                </i>

                                                Uploaded by:

                                                <strong>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $log['full_name']
                                                        ??
                                                        'Volunteer'
                                                    );
                                                    ?>

                                                </strong>

                                            </p>


                                            <!-- Note -->

                                            <?php if (!empty($log['note'])): ?>

                                                <p
                                                    class="small text-secondary
                                                           mb-0">

                                                    <i
                                                        class="fas fa-note-sticky
                                                               text-warning
                                                               me-1">
                                                    </i>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $log['note']
                                                    );
                                                    ?>

                                                </p>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>


                    <?php else: ?>

                        <div
                            class="text-center py-5">

                            <i
                                class="fas fa-camera
                                       text-success opacity-50"
                                style="font-size:50px;">
                            </i>


                            <h5 class="fw-bold mt-3">

                                No Growth Updates Yet

                            </h5>


                            <p
                                class="text-muted mb-0">

                                The first progress inspection
                                will appear here after an image
                                is uploaded.

                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>


    <?php else: ?>


        <!-- ======================================================
             TREE NOT FOUND
        ======================================================= -->

        <div
            class="text-center py-5 text-muted">

            <i
                class="fas fa-tree text-warning mb-3"
                style="font-size:70px;">
            </i>


            <h5 class="fw-bold">

                <?php
                echo $code !== ''
                    ? 'Tree Code Not Found'
                    : 'Enter a Tree Code';
                ?>

            </h5>


            <p>

                Enter an official Green Future
                Tree ID or Tree Code to view tracking information.

            </p>


            <a
                href="<?php
                echo base_url('campaigns.php');
                ?>"
                class="btn btn-primary-green rounded-pill">

                <i
                    class="fas fa-seedling me-1">
                </i>

                Explore Campaigns

            </a>

        </div>

    <?php endif; ?>

</div>


<?php require_once __DIR__ . '/includes/footer.php'; ?>