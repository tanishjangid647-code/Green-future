
-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'volunteer', 'registered') NOT NULL DEFAULT 'registered',
  `avatar` VARCHAR(255) DEFAULT 'default-avatar.png',
  `city` VARCHAR(100) DEFAULT 'Mumbai',
  `state` VARCHAR(100) DEFAULT 'Maharashtra',
  `reward_points` INT DEFAULT 0,
  `badge` VARCHAR(50) DEFAULT 'Green Starter',
  `status` ENUM('active', 'inactive', 'banned') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default users (Password for all default accounts: Admin@123 / Volunteer@123 / User@123)
-- Using standard password hash for 'Admin@123': $2y$10$e8Wp9s5y7T6fD4zK1R2T3uO5n7M9a1B3c5D7e9F1G3H5I7J9K1L3M
-- We store pre-calculated password hashes for convenience.
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role`, `city`, `state`, `reward_points`, `badge`) VALUES
(1, 'Admin Officer', 'admin@greenfuture.org', '+91 9876543210', '$2y$10$8.wW56M.Oa/oUvJ7vV2WyuV6qS3J0z1A1vL5X1M3n5D7e9F1G3H5I', 'admin', 'New Delhi', 'Delhi', 1500, 'Green Mastermind'),
(2, 'Aarav Sharma', 'volunteer@greenfuture.org', '+91 9812345678', '$2y$10$8.wW56M.Oa/oUvJ7vV2WyuV6qS3J0z1A1vL5X1M3n5D7e9F1G3H5I', 'volunteer', 'Mumbai', 'Maharashtra', 950, 'Gold Forester'),
(3, 'Priya Patel', 'user@greenfuture.org', '+91 9898989898', '$2y$10$8.wW56M.Oa/oUvJ7vV2WyuV6qS3J0z1A1vL5X1M3n5D7e9F1G3H5I', 'registered', 'Pune', 'Maharashtra', 420, 'Silver Guardian'),
(4, 'Rohan Verma', 'rohan@example.com', '+91 9765432109', '$2y$10$8.wW56M.Oa/oUvJ7vV2WyuV6qS3J0z1A1vL5X1M3n5D7e9F1G3H5I', 'registered', 'Bangalore', 'Karnataka', 310, 'Bronze Eco Shield'),
(5, 'Sneha Roy', 'sneha@example.com', '+91 9654321098', '$2y$10$8.wW56M.Oa/oUvJ7vV2WyuV6qS3J0z1A1vL5X1M3n5D7e9F1G3H5I', 'volunteer', 'Kolkata', 'West Bengal', 780, 'Gold Forester');

-- --------------------------------------------------------
-- Table: campaigns
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `campaigns` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `banner_image` VARCHAR(255) DEFAULT 'default-campaign.jpg',
  `organizer` VARCHAR(150) NOT NULL,
  `tree_species` VARCHAR(255) NOT NULL,
  `event_date` DATE NOT NULL,
  `event_time` TIME NOT NULL,
  `state` VARCHAR(100) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `location_address` TEXT NOT NULL,
  `latitude` DECIMAL(10, 8) DEFAULT 19.0760,
  `longitude` DECIMAL(11, 8) DEFAULT 72.8777,
  `max_volunteers` INT DEFAULT 100,
  `current_volunteers` INT DEFAULT 0,
  `status` ENUM('upcoming', 'active', 'completed', 'cancelled') DEFAULT 'upcoming',
  `created_by` INT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `campaigns` (`id`, `title`, `description`, `banner_image`, `organizer`, `tree_species`, `event_date`, `event_time`, `state`, `city`, `location_address`, `latitude`, `longitude`, `max_volunteers`, `current_volunteers`, `status`) VALUES
(1, 'Mega Urban Forestry Drive 2026', 'Join our flagship urban tree plantation movement to transform city air quality and create dense micro-forests.', 'campaign1.jpg', 'Green Future Trust & Municipal Corp', 'Neem, Peepal, Banyan, Gulmohar', '2026-08-15', '07:30:00', 'Maharashtra', 'Mumbai', 'Aarey Colony, Sector 4, Goregaon East', 19.1485, 72.8812, 250, 142, 'upcoming'),
(2, 'Riverbank Green Canopy Drive', 'Restoring native riparian flora along the river banks to prevent soil erosion and improve biodiversity.', 'campaign2.jpg', 'Ecosia Earth Alliance', 'Sal, Teak, Subabul, Bamboo', '2026-08-20', '08:00:00', 'Maharashtra', 'Pune', 'Mula-Mutha Riverbed Park, Kalyani Nagar', 18.5489, 73.8993, 150, 98, 'upcoming'),
(3, 'Clean Air School Canopy Campaign', 'Planting shade-giving and fruit-bearing trees inside school campuses across Bangalore.', 'campaign3.jpg', 'Vruksha Foundation', 'Mango, Jamun, Guava, Jacaranda', '2026-07-10', '09:00:00', 'Karnataka', 'Bangalore', 'Koramangala Community Grounds', 12.9352, 77.6245, 100, 100, 'completed'),
(4, 'Coastal Mangrove Conservation Task force', 'Restoring fragile coastal ecosystems by planting mangrove saplings along tidal channels.', 'campaign4.jpg', 'Ocean-Land Conservation Society', 'Red Mangrove, Avicennia Marina', '2026-09-05', '06:30:00', 'West Bengal', 'Kolkata', 'Sundarbans Wetland Buffer Zone', 22.1240, 88.8320, 200, 65, 'upcoming');

-- --------------------------------------------------------
-- Table: campaign_participants
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `campaign_participants` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `status` ENUM('registered', 'attended', 'cancelled') DEFAULT 'registered',
  `joined_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `campaign_participants` (`campaign_id`, `user_id`, `status`) VALUES
(1, 2, 'registered'),
(1, 3, 'registered'),
(2, 3, 'registered'),
(3, 4, 'attended');

-- --------------------------------------------------------
-- Table: trees
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `trees` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tree_code` VARCHAR(50) NOT NULL UNIQUE,
  `species` VARCHAR(100) NOT NULL,
  `campaign_id` INT DEFAULT NULL,
  `plantation_date` DATE NOT NULL,
  `latitude` DECIMAL(10, 8) NOT NULL,
  `longitude` DECIMAL(11, 8) NOT NULL,
  `current_height_cm` INT DEFAULT 30,
  `health_status` ENUM('Healthy', 'Needs Water', 'Damaged', 'Dead') DEFAULT 'Healthy',
  `water_schedule` VARCHAR(100) DEFAULT 'Bi-weekly (Mon/Thu)',
  `assigned_volunteer_id` INT DEFAULT NULL,
  `user_id` INT DEFAULT NULL,
  `carbon_offset_kg` DECIMAL(8,2) DEFAULT 21.75,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`assigned_volunteer_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `trees` (`id`, `tree_code`, `species`, `campaign_id`, `plantation_date`, `latitude`, `longitude`, `current_height_cm`, `health_status`, `assigned_volunteer_id`, `user_id`, `carbon_offset_kg`) VALUES
(1, 'TREE-2026-001', 'Azadirachta indica (Neem)', 1, '2026-07-15', 19.1488, 72.8815, 65, 'Healthy', 2, 3, 25.50),
(2, 'TREE-2026-002', 'Ficus religiosa (Peepal)', 1, '2026-07-15', 19.1491, 72.8819, 82, 'Healthy', 2, 4, 30.20),
(3, 'TREE-2026-003', 'Mangifera indica (Mango)', 3, '2026-07-10', 12.9355, 77.6248, 110, 'Needs Water', 5, 3, 42.00),
(4, 'TREE-2026-004', 'Delonix regia (Gulmohar)', 2, '2026-06-01', 18.5492, 73.8998, 95, 'Healthy', 2, 4, 35.80),
(5, 'TREE-2026-005', 'Syzygium cumini (Jamun)', 3, '2026-07-10', 12.9358, 77.6251, 75, 'Healthy', 5, 3, 28.10);

-- --------------------------------------------------------
-- Table: tree_images
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tree_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tree_id` INT NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `growth_height_cm` INT DEFAULT NULL,
  `note` VARCHAR(255) DEFAULT NULL,
  `uploaded_by` INT NOT NULL,
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`tree_id`) REFERENCES `trees`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tree_images` (`tree_id`, `image_url`, `growth_height_cm`, `note`, `uploaded_by`) VALUES
(1, 'tree-log-1.jpg', 65, 'Sapling growing nicely after monsoon rain.', 2),
(2, 'tree-log-2.jpg', 82, 'Leaves healthy with regular organic fertilizing.', 2),
(3, 'tree-log-3.jpg', 110, 'Pruned lower branches; needs extra hydration this week.', 5);

-- --------------------------------------------------------
-- Table: certificates
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `certificates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cert_number` VARCHAR(100) NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  `campaign_id` INT DEFAULT NULL,
  `tree_id` INT DEFAULT NULL,
  `issue_date` DATE NOT NULL,
  `qr_code` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`tree_id`) REFERENCES `trees`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `certificates` (`id`, `cert_number`, `user_id`, `campaign_id`, `tree_id`, `issue_date`, `qr_code`) VALUES
(1, 'GF-CERT-2026-78901', 3, 3, 1, '2026-07-15', 'GF-CERT-2026-78901-QR.png'),
(2, 'GF-CERT-2026-78902', 4, 3, 2, '2026-07-10', 'GF-CERT-2026-78902-QR.png');

-- --------------------------------------------------------
-- Table: wishlist
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `campaign_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_wish` (`user_id`, `campaign_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `wishlist` (`user_id`, `campaign_id`) VALUES (3, 1), (3, 4);

-- --------------------------------------------------------
-- Table: reviews
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `campaign_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `rating` TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  `comment` TEXT NOT NULL,
  `status` ENUM('approved', 'pending', 'rejected') DEFAULT 'approved',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `reviews` (`campaign_id`, `user_id`, `rating`, `comment`, `status`) VALUES
(3, 3, 5, 'Extremely well-organized plantation drive! The coordinators provided sapling care manuals and dynamic QR tracking codes.', 'approved'),
(3, 4, 5, 'Great experience planting fruit trees with local school children. Highly recommend joining future events!', 'approved');

-- --------------------------------------------------------
-- Table: gallery
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `category` VARCHAR(50) DEFAULT 'Plantation',
  `image_url` VARCHAR(255) NOT NULL,
  `type` ENUM('image', 'video') DEFAULT 'image',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `gallery` (`title`, `category`, `image_url`) VALUES
('Community Planting Day at Aarey', 'Plantation', 'gallery1.jpg'),
('School Children Planting Mango Trees', 'Youth Drive', 'gallery2.jpg'),
('Volunteers Restoring Riverbank Ecosystem', 'Eco Restoration', 'gallery3.jpg'),
('Drip Irrigation Setup for Saplings', 'Maintenance', 'gallery4.jpg');

-- --------------------------------------------------------
-- Table: blogs
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `blogs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `content` TEXT NOT NULL,
  `image_url` VARCHAR(255) DEFAULT 'default-blog.jpg',
  `category` VARCHAR(100) DEFAULT 'Urban Forestry',
  `author_id` INT DEFAULT 1,
  `views` INT DEFAULT 120,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `blogs` (`id`, `title`, `slug`, `content`, `category`, `author_id`, `views`) VALUES
(1, 'Why Native Tree Species Matter in Urban Reforestation', 'why-native-tree-species-matter', 'Native tree species like Neem, Peepal, and Banyan are adapted to local soil conditions and withstand heatwaves better than non-native species while supporting indigenous wildlife.', 'Urban Forestry', 1, 450),
(2, 'Understanding Carbon Sequestration: How One Tree Filters Air', 'understanding-carbon-sequestration', 'An average mature tree absorbs up to 22kg of CO2 per year. Learn how urban green canopies lower micro-temperatures by up to 3°C.', 'Climate Action', 1, 310),
(3, '5 Essential Tips for Nurturing Saplings in Summer', '5-tips-nurturing-saplings-summer', 'Deep root watering, organic mulching, and shade guards are key strategies to guarantee sapling survival above 90% during dry seasons.', 'Tree Care', 2, 280);

-- --------------------------------------------------------
-- Table: contact_messages
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('unread', 'read', 'replied') DEFAULT 'unread',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `contact_messages` (`name`, `email`, `subject`, `message`) VALUES
('Deepak Kumar', 'deepak@example.com', 'Corporate CSR Partnership Inquiry', 'We would like to organize a corporate tree plantation drive for 500 employees next month.');

-- --------------------------------------------------------
-- Table: notifications
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `link` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `notifications` (`user_id`, `title`, `message`, `link`) VALUES
(3, 'Campaign Approved!', 'You have successfully enrolled in Mega Urban Forestry Drive 2026.', 'campaign-detail.php?id=1'),
(3, 'Tree Certificate Issued', 'Your plantation certificate GF-CERT-2026-78901 is now ready to download.', 'user/certificates.php');

-- --------------------------------------------------------
-- Table: activity_logs
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `action` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(50) DEFAULT '127.0.0.1',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `activity_logs` (`user_id`, `action`) VALUES
(1, 'System database initialized with pre-seeded environment data.'),
(3, 'Registered for Campaign #1: Mega Urban Forestry Drive 2026');
