<?php

$page_title = "Campaigns";

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';


/*
|--------------------------------------------------------------------------
| Search & Filters
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');
$city_filter = trim($_GET['city'] ?? '');
$species_filter = trim($_GET['species'] ?? '');
$status_filter = trim($_GET['status'] ?? '');
$date_filter = trim($_GET['date'] ?? '');
$sort = trim($_GET['sort'] ?? 'date_asc');


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$per_page = 6;

$page = max(
    1,
    intval($_GET['page'] ?? 1)
);


/*
|--------------------------------------------------------------------------
| Allowed Sorting
|--------------------------------------------------------------------------
*/

$allowed_sorts = [

    'date_asc' => 'event_date ASC',

    'date_desc' => 'event_date DESC',

    'newest' => 'id DESC',

    'oldest' => 'id ASC',

    'available' => '(max_volunteers - current_volunteers) DESC',

    'popular' => 'current_volunteers DESC'

];


$order_by = $allowed_sorts[$sort]
    ?? $allowed_sorts['date_asc'];


/*
|--------------------------------------------------------------------------
| Build WHERE Conditions
|--------------------------------------------------------------------------
*/

$where = [
    '1=1'
];

$params = [];


/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

if ($search !== '') {

    $where[] = "
        (
            title LIKE ?
            OR description LIKE ?
            OR tree_species LIKE ?
            OR organizer LIKE ?
            OR city LIKE ?
        )
    ";

    $search_term = "%{$search}%";

    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}


/*
|--------------------------------------------------------------------------
| City Filter
|--------------------------------------------------------------------------
*/

if ($city_filter !== '') {

    $where[] = "city = ?";

    $params[] = $city_filter;
}


/*
|--------------------------------------------------------------------------
| Tree Species Filter
|--------------------------------------------------------------------------
*/

if ($species_filter !== '') {

    $where[] = "tree_species = ?";

    $params[] = $species_filter;
}


/*
|--------------------------------------------------------------------------
| Status Filter
|--------------------------------------------------------------------------
*/

if ($status_filter !== '') {

    $where[] = "status = ?";

    $params[] = $status_filter;
}


/*
|--------------------------------------------------------------------------
| Date Filter
|--------------------------------------------------------------------------
*/

if ($date_filter !== '') {

    if ($date_filter === 'today') {

        $where[] = "event_date = CURDATE()";

    } elseif ($date_filter === 'week') {

        $where[] = "
            event_date BETWEEN
            CURDATE()
            AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ";

    } elseif ($date_filter === 'month') {

        $where[] = "
            event_date BETWEEN
            CURDATE()
            AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        ";

    } elseif ($date_filter === 'upcoming') {

        $where[] = "event_date >= CURDATE()";
    }
}


/*
|--------------------------------------------------------------------------
| WHERE SQL
|--------------------------------------------------------------------------
*/

$where_sql = implode(
    ' AND ',
    $where
);


/*
|--------------------------------------------------------------------------
| Count Total Campaigns
|--------------------------------------------------------------------------
*/

$count_sql = "
    SELECT COUNT(*)
    FROM campaigns
    WHERE {$where_sql}
";

$count_stmt = $pdo->prepare($count_sql);

$count_stmt->execute($params);

$total_campaigns =
    (int) $count_stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| Pagination Calculation
|--------------------------------------------------------------------------
*/

$total_pages = max(
    1,
    (int) ceil(
        $total_campaigns / $per_page
    )
);

$page = min(
    $page,
    $total_pages
);

$offset =
    ($page - 1) * $per_page;


/*
|--------------------------------------------------------------------------
| Fetch Campaigns
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT *
    FROM campaigns
    WHERE {$where_sql}
    ORDER BY {$order_by}
    LIMIT {$per_page}
    OFFSET {$offset}
";

$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$campaigns =
    $stmt->fetchAll(PDO::FETCH_ASSOC);

    /*
|--------------------------------------------------------------------------
| Wishlist Status
|--------------------------------------------------------------------------
*/

$wishlist_ids = [];

if (isset($_SESSION['user_id'])) {

    $wishlist_stmt = $pdo->prepare("
        SELECT campaign_id
        FROM wishlist
        WHERE user_id = ?
    ");

    $wishlist_stmt->execute([
        (int) $_SESSION['user_id']
    ]);

    $wishlist_ids = array_map(
        'intval',
        $wishlist_stmt->fetchAll(PDO::FETCH_COLUMN)
    );
}

/*
|--------------------------------------------------------------------------
| Dropdown Data
|--------------------------------------------------------------------------
*/

$cities = $pdo->query("
    SELECT DISTINCT city
    FROM campaigns
    WHERE city IS NOT NULL
      AND city != ''
    ORDER BY city ASC
")->fetchAll(PDO::FETCH_COLUMN);


$species = $pdo->query("
    SELECT DISTINCT tree_species
    FROM campaigns
    WHERE tree_species IS NOT NULL
      AND tree_species != ''
    ORDER BY tree_species ASC
")->fetchAll(PDO::FETCH_COLUMN);


/*
|--------------------------------------------------------------------------
| Helper for Pagination URLs
|--------------------------------------------------------------------------
*/

function campaign_filter_url($page_number)
{
    $params = $_GET;

    $params['page'] = $page_number;

    return base_url(
        'campaigns.php?' .
        http_build_query($params)
    );
}

?>

<div class="container py-5">

    <!-- ==========================================================
         PAGE HEADER
    =========================================================== -->

    <div class="row align-items-center mb-4">

        <div class="col-lg-5 mb-3 mb-lg-0">

            <h2 class="fw-bold mb-1">

                <i class="fas fa-tree text-success me-2"></i>

                Tree Plantation Campaigns

            </h2>

            <p class="text-muted small mb-0">

                Explore plantation drives and make a difference
                in your community.

            </p>

        </div>


        <div class="col-lg-7">

            <div class="d-flex justify-content-lg-end">

                <span class="badge bg-success-subtle text-success
                             px-3 py-2 rounded-pill">

                    <i class="fas fa-seedling me-1"></i>

                    <?php echo $total_campaigns; ?>

                    Campaign<?php
                    echo $total_campaigns == 1 ? '' : 's';
                    ?>

                </span>

            </div>

        </div>

    </div>


    <!-- ==========================================================
         FILTER PANEL
    =========================================================== -->

    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body p-4">

            <form
                action="<?php echo base_url('campaigns.php'); ?>"
                method="GET">

                <div class="row g-3">

                    <!-- Search -->

                    <div class="col-lg-4">

                        <label class="form-label small fw-semibold">

                            Search Campaigns

                        </label>

                        <div class="input-group">

                            <span class="input-group-text bg-white">

                                <i class="fas fa-search text-success"></i>

                            </span>

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Campaign, city, species..."
                                value="<?php
                                echo htmlspecialchars($search);
                                ?>"
                            >

                        </div>

                    </div>


                    <!-- City -->

                    <div class="col-md-6 col-lg-2">

                        <label class="form-label small fw-semibold">

                            City

                        </label>

                        <select
                            name="city"
                            class="form-select">

                            <option value="">
                                All Cities
                            </option>

                            <?php foreach ($cities as $city): ?>

                                <option
                                    value="<?php
                                    echo htmlspecialchars($city);
                                    ?>"
                                    <?php
                                    echo $city_filter === $city
                                        ? 'selected'
                                        : '';
                                    ?>>

                                    <?php
                                    echo htmlspecialchars($city);
                                    ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- Species -->

                    <div class="col-md-6 col-lg-2">

                        <label class="form-label small fw-semibold">

                            Tree Species

                        </label>

                        <select
                            name="species"
                            class="form-select">

                            <option value="">
                                All Species
                            </option>

                            <?php foreach ($species as $tree_species): ?>

                                <option
                                    value="<?php
                                    echo htmlspecialchars(
                                        $tree_species
                                    );
                                    ?>"
                                    <?php
                                    echo $species_filter === $tree_species
                                        ? 'selected'
                                        : '';
                                    ?>>

                                    <?php
                                    echo htmlspecialchars(
                                        $tree_species
                                    );
                                    ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- Date -->

                    <div class="col-md-6 col-lg-2">

                        <label class="form-label small fw-semibold">

                            Date

                        </label>

                        <select
                            name="date"
                            class="form-select">

                            <option value="">
                                Any Date
                            </option>

                            <option
                                value="today"
                                <?php
                                echo $date_filter === 'today'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Today

                            </option>

                            <option
                                value="week"
                                <?php
                                echo $date_filter === 'week'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Next 7 Days

                            </option>

                            <option
                                value="month"
                                <?php
                                echo $date_filter === 'month'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Next 30 Days

                            </option>

                            <option
                                value="upcoming"
                                <?php
                                echo $date_filter === 'upcoming'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Upcoming

                            </option>

                        </select>

                    </div>


                    <!-- Status -->

                    <div class="col-md-6 col-lg-2">

                        <label class="form-label small fw-semibold">

                            Status

                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option value="">
                                All Status
                            </option>

                            <option
                                value="active"
                                <?php
                                echo $status_filter === 'active'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Active

                            </option>

                            <option
                                value="upcoming"
                                <?php
                                echo $status_filter === 'upcoming'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Upcoming

                            </option>

                            <option
                                value="completed"
                                <?php
                                echo $status_filter === 'completed'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Completed

                            </option>

                            <option
                                value="cancelled"
                                <?php
                                echo $status_filter === 'cancelled'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Cancelled

                            </option>

                        </select>

                    </div>


                    <!-- Sort -->

                    <div class="col-md-6 col-lg-3">

                        <label class="form-label small fw-semibold">

                            Sort By

                        </label>

                        <select
                            name="sort"
                            class="form-select">

                            <option
                                value="date_asc"
                                <?php
                                echo $sort === 'date_asc'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Nearest Date

                            </option>

                            <option
                                value="date_desc"
                                <?php
                                echo $sort === 'date_desc'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Latest Date

                            </option>

                            <option
                                value="newest"
                                <?php
                                echo $sort === 'newest'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Recently Added

                            </option>

                            <option
                                value="oldest"
                                <?php
                                echo $sort === 'oldest'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Oldest Added

                            </option>

                            <option
                                value="available"
                                <?php
                                echo $sort === 'available'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Most Available Slots

                            </option>

                            <option
                                value="popular"
                                <?php
                                echo $sort === 'popular'
                                    ? 'selected'
                                    : '';
                                ?>>

                                Most Popular

                            </option>

                        </select>

                    </div>


                    <!-- Buttons -->

                    <div class="col-md-6 col-lg-3 d-flex align-items-end gap-2">

                        <button
                            type="submit"
                            class="btn btn-primary-green rounded-pill px-4">

                            <i class="fas fa-search me-1"></i>

                            Search

                        </button>


                        <a
                            href="<?php
                            echo base_url('campaigns.php');
                            ?>"
                            class="btn btn-outline-secondary rounded-pill">

                            <i class="fas fa-rotate-left"></i>

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- ==========================================================
         CAMPAIGN CARDS
    =========================================================== -->

    <div class="row g-4">

        <?php if (!empty($campaigns)): ?>

            <?php foreach ($campaigns as $camp): ?>

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


                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                $status =
                    strtolower(
                        trim(
                            (string)$camp['status']
                        )
                    );


                $status_class = 'bg-secondary';

                if ($status === 'active') {

                    $status_class = 'bg-success';

                } elseif ($status === 'upcoming') {

                    $status_class = 'bg-primary';

                } elseif ($status === 'completed') {

                    $status_class = 'bg-dark';

                } elseif ($status === 'cancelled') {

                    $status_class = 'bg-danger';

                }

                ?>

                <div class="col-md-6 col-lg-4">

                    <div
                        class="campaign-card h-100 d-flex flex-column">

                        <!-- Image -->

                        <div class="campaign-img-wrapper">

                            <img
                                src="https://picsum.photos/600/350?random=<?php echo (int)$camp['id']; ?>"
                                alt="<?php
                                echo htmlspecialchars(
                                    $camp['title']
                                );
                                ?>"
                                loading="lazy"
                            >
                            <?php if (isset($_SESSION['user_id'])): ?>

    <?php
    $is_wishlisted =
        in_array(
            (int) $camp['id'],
            $wishlist_ids,
            true
        );
    ?>

    <form
        action="<?php echo base_url('wishlist.php'); ?>"
        method="POST"
        class="position-absolute top-0 start-0 m-3">

        <input
            type="hidden"
            name="action"
            value="<?php echo $is_wishlisted ? 'remove' : 'add'; ?>">

        <input
            type="hidden"
            name="campaign_id"
            value="<?php echo (int)$camp['id']; ?>">

        <input
            type="hidden"
            name="csrf_token"
            value="<?php
            echo htmlspecialchars(
                generate_csrf_token()
            );
            ?>">

        <input
            type="hidden"
            name="redirect"
            value="campaigns.php">

        <button
            type="submit"
            class="btn btn-light rounded-circle shadow-sm"
            style="width:42px;height:42px;"
            title="<?php
            echo $is_wishlisted
                ? 'Remove from Wishlist'
                : 'Save to Wishlist';
            ?>">

            <i
                class="<?php
                echo $is_wishlisted
                    ? 'fas fa-heart text-danger'
                    : 'far fa-heart text-danger';
                ?>">
            </i>

        </button>

    </form>

<?php endif; ?>
                            <span
                                class="badge <?php echo $status_class; ?>
                                       position-absolute top-0 end-0
                                       m-3 rounded-pill px-3 py-2">

                                <?php
                                echo ucfirst(
                                    htmlspecialchars($status)
                                );
                                ?>

                            </span>

                        </div>


                        <!-- Content -->

                        <div
                            class="p-4 d-flex flex-column flex-grow-1">

                            <h5
                                class="fw-bold mb-2 text-dark">

                                <?php
                                echo htmlspecialchars(
                                    $camp['title']
                                );
                                ?>

                            </h5>


                            <p
                                class="text-muted small flex-grow-1">

                                <?php

                                $description =
                                    strip_tags(
                                        $camp['description']
                                    );

                                echo htmlspecialchars(
                                    mb_strimwidth(
                                        $description,
                                        0,
                                        120,
                                        '...'
                                    )
                                );

                                ?>

                            </p>


                            <!-- Campaign Information -->

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

                                    <?php echo $current; ?>

                                    /

                                    <?php echo $maximum; ?>

                                    Volunteers

                                </div>

                            </div>


                            <!-- Availability -->

                            <div class="mb-3">

                                <div
                                    class="d-flex justify-content-between
                                           small mb-1">

                                    <span class="text-muted">

                                        Volunteer Capacity

                                    </span>

                                    <strong>

                                        <?php
                                        echo $available;
                                        ?>

                                        slots left

                                    </strong>

                                </div>


                                <div
                                    class="progress"
                                    style="height: 6px;">

                                    <?php

                                    $percentage =

                                        $maximum > 0

                                        ? min(
                                            100,
                                            (
                                                $current /
                                                $maximum
                                            ) * 100
                                        )

                                        : 0;

                                    ?>

                                    <div
                                        class="progress-bar bg-success"
                                        style="width: <?php
                                        echo $percentage;
                                        ?>%;">

                                    </div>

                                </div>

                            </div>


                            <!-- Action -->

                            <a
                                href="<?php
                                echo base_url(
                                    'campaign-detail.php?id=' .
                                    (int)$camp['id']
                                );
                                ?>"
                                class="btn btn-primary-green w-100
                                       rounded-pill mt-auto">

                                <i
                                    class="fas fa-arrow-right me-1">
                                </i>

                                View Details & Join

                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>


        <?php else: ?>

            <!-- Empty State -->

            <div class="col-12">

                <div
                    class="text-center py-5">

                    <div class="mb-3">

                        <i
                            class="fas fa-seedling
                                   fs-1 text-success opacity-50">
                        </i>

                    </div>

                    <h5 class="fw-bold">

                        No Campaigns Found

                    </h5>

                    <p class="text-muted">

                        Try changing your search or filters.

                    </p>


                    <a
                        href="<?php
                        echo base_url('campaigns.php');
                        ?>"
                        class="btn btn-outline-success rounded-pill">

                        <i
                            class="fas fa-rotate-left me-1">
                        </i>

                        Clear Filters

                    </a>

                </div>

            </div>

        <?php endif; ?>

    </div>


    <!-- ==========================================================
         PAGINATION
    =========================================================== -->

    <?php if ($total_pages > 1): ?>

        <nav
            class="mt-5"
            aria-label="Campaign pagination">

            <ul
                class="pagination justify-content-center">


                <!-- Previous -->

                <li
                    class="page-item <?php
                    echo $page <= 1
                        ? 'disabled'
                        : '';
                    ?>">

                    <a
                        class="page-link rounded-start-pill"
                        href="<?php
                        echo $page > 1
                            ? campaign_filter_url(
                                $page - 1
                            )
                            : '#';
                        ?>">

                        <i class="fas fa-chevron-left"></i>

                    </a>

                </li>


                <?php

                /*
                |--------------------------------------------------------------------------
                | Show Pagination Numbers
                |--------------------------------------------------------------------------
                */

                $start_page =
                    max(
                        1,
                        $page - 2
                    );

                $end_page =
                    min(
                        $total_pages,
                        $page + 2
                    );

                ?>


                <?php for (
                    $i = $start_page;
                    $i <= $end_page;
                    $i++
                ): ?>

                    <li
                        class="page-item <?php
                        echo $i === $page
                            ? 'active'
                            : '';
                        ?>">

                        <a
                            class="page-link"
                            href="<?php
                            echo campaign_filter_url($i);
                            ?>">

                            <?php echo $i; ?>

                        </a>

                    </li>

                <?php endfor; ?>


                <!-- Next -->

                <li
                    class="page-item <?php
                    echo $page >= $total_pages
                        ? 'disabled'
                        : '';
                    ?>">

                    <a
                        class="page-link rounded-end-pill"
                        href="<?php
                        echo $page < $total_pages
                            ? campaign_filter_url(
                                $page + 1
                            )
                            : '#';
                        ?>">

                        <i class="fas fa-chevron-right"></i>

                    </a>

                </li>

            </ul>

        </nav>

    <?php endif; ?>


</div>


<?php require_once __DIR__ . '/includes/footer.php'; ?>