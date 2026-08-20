<?php

$page_title = "My Wishlist";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_login();

$user_id = (int) $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| Get Wishlist Campaigns
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        w.id AS wishlist_id,
        w.created_at AS saved_at,

        c.id,
        c.title,
        c.description,
        c.event_date,
        c.event_time,
        c.city,
        c.state,
        c.tree_species,
        c.current_volunteers,
        c.max_volunteers,
        c.status

    FROM wishlist w

    INNER JOIN campaigns c
        ON c.id = w.campaign_id

    WHERE w.user_id = ?

    ORDER BY w.created_at DESC
");

$stmt->execute([$user_id]);

$wishlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container py-5">

    <!-- Header -->

    <div class="d-flex justify-content-between
                align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">

                <i class="fas fa-heart text-danger me-2"></i>

                My Wishlist

            </h2>

            <p class="text-muted mb-0">

                Campaigns you've saved for later.

            </p>

        </div>


        <a
            href="<?php echo base_url('campaigns.php'); ?>"
            class="btn btn-primary-green rounded-pill">

            <i class="fas fa-tree me-1"></i>

            Explore Campaigns

        </a>

    </div>


    <?php if (!empty($wishlist)): ?>

        <div class="row g-4">

            <?php foreach ($wishlist as $camp): ?>

                <?php

                $current =
                    (int)$camp['current_volunteers'];

                $maximum =
                    (int)$camp['max_volunteers'];

                $available =
                    max(
                        0,
                        $maximum - $current
                    );

                $status =
                    strtolower(
                        trim(
                            (string)$camp['status']
                        )
                    );

                ?>

                <div class="col-md-6 col-lg-4">

                    <div
                        class="card border-0 shadow-sm
                               rounded-4 h-100 overflow-hidden">

                        <!-- Image -->

                        <img
                            src="https://picsum.photos/600/350?random=<?php echo (int)$camp['id']; ?>"
                            class="card-img-top"
                            style="height:220px;
                                   object-fit:cover;"
                            alt="<?php
                            echo htmlspecialchars(
                                $camp['title']
                            );
                            ?>"
                            loading="lazy"
                        >


                        <div class="card-body p-4
                                    d-flex flex-column">

                            <div
                                class="d-flex justify-content-between
                                       align-items-center mb-2">

                                <span
                                    class="badge bg-success
                                           rounded-pill">

                                    <?php
                                    echo ucfirst(
                                        htmlspecialchars(
                                            $status
                                        )
                                    );
                                    ?>

                                </span>

                                <small class="text-muted">

                                    <i
                                        class="fas fa-heart
                                               text-danger me-1">
                                    </i>

                                    Saved

                                </small>

                            </div>


                            <h5 class="fw-bold">

                                <?php
                                echo htmlspecialchars(
                                    $camp['title']
                                );
                                ?>

                            </h5>


                            <p class="text-muted small flex-grow-1">

                                <?php

                                $description =
                                    strip_tags(
                                        $camp['description']
                                    );

                                echo htmlspecialchars(
                                    mb_strimwidth(
                                        $description,
                                        0,
                                        110,
                                        '...'
                                    )
                                );

                                ?>

                            </p>


                            <div
                                class="small text-secondary mb-3">

                                <div class="mb-2">

                                    <i
                                        class="fas fa-map-marker-alt
                                               text-danger me-2">
                                    </i>

                                    <?php
                                    echo htmlspecialchars(
                                        $camp['city']
                                    );
                                    ?>,
                                    <?php
                                    echo htmlspecialchars(
                                        $camp['state']
                                    );
                                    ?>

                                </div>


                                <div class="mb-2">

                                    <i
                                        class="fas fa-calendar
                                               text-success me-2">
                                    </i>

                                    <?php
                                    echo date(
                                        'M d, Y',
                                        strtotime(
                                            $camp['event_date']
                                        )
                                    );
                                    ?>

                                </div>


                                <div class="mb-2">

                                    <i
                                        class="fas fa-leaf
                                               text-success me-2">
                                    </i>

                                    <?php
                                    echo htmlspecialchars(
                                        $camp['tree_species']
                                    );
                                    ?>

                                </div>


                                <div>

                                    <i
                                        class="fas fa-users
                                               text-primary me-2">
                                    </i>

                                    <?php echo $available; ?>

                                    slots available

                                </div>

                            </div>


                            <!-- View -->

                            <a
                                href="<?php
                                echo base_url(
                                    'campaign-detail.php?id=' .
                                    (int)$camp['id']
                                );
                                ?>"
                                class="btn btn-primary-green
                                       rounded-pill w-100 mb-2">

                                <i
                                    class="fas fa-eye me-1">
                                </i>

                                View Campaign

                            </a>


                            <!-- Remove -->

                            <form
                                action="<?php
                                echo base_url(
                                    'wishlist.php'
                                );
                                ?>"
                                method="POST">

                                <input
                                    type="hidden"
                                    name="action"
                                    value="remove"
                                >

                                <input
                                    type="hidden"
                                    name="campaign_id"
                                    value="<?php
                                    echo (int)$camp['id'];
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

                                <input
                                    type="hidden"
                                    name="redirect"
                                    value="user/wishlist.php"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-outline-danger
                                           rounded-pill w-100">

                                    <i
                                        class="fas fa-heart-crack
                                               me-1">
                                    </i>

                                    Remove

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>


    <?php else: ?>

        <!-- Empty Wishlist -->

        <div
            class="text-center py-5">

            <div class="mb-4">

                <i
                    class="far fa-heart text-muted"
                    style="font-size:70px;">
                </i>

            </div>

            <h4 class="fw-bold">

                Your Wishlist is Empty

            </h4>

            <p class="text-muted">

                Save plantation campaigns you're interested in
                and find them here later.

            </p>

            <a
                href="<?php
                echo base_url('campaigns.php');
                ?>"
                class="btn btn-primary-green
                       rounded-pill px-4">

                <i
                    class="fas fa-search me-1">
                </i>

                Explore Campaigns

            </a>

        </div>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>