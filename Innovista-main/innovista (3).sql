-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2025 at 08:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `innovista`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Michael Smith', 'msmith@email.com', 'Question about services', 'Do you offer services in the Kilinochchi area? I am looking to renovate my office space.', '2025-07-20 13:21:51'),
(2, 'Sarah Lee', 'sarah.l@email.com', 'Partnership Inquiry', 'We are a furniture supplier and would be interested in a partnership. Who can I speak to?', '2025-07-20 13:21:51'),
(3, 'David Chen', 'd.chen@email.com', 'Feedback on website', 'Your website is very easy to use! Great job.', '2025-07-20 13:21:51'),
(4, 'Laura Wilson', 'laura.w@email.com', 'Urgent: Water Damage Restoration', 'I have urgent water damage in my home. Do you offer emergency services?', '2025-07-20 13:21:51'),
(5, 'James Taylor', 'jtaylor@email.com', 'Request for a specific material', 'Can your providers source Italian marble for a kitchen countertop?', '2025-07-20 13:21:51'),
(6, 'Emily White', 'emily.w@email.com', 'Job Application', 'Are you hiring project managers? I have attached my resume.', '2025-07-20 13:21:51'),
(7, 'Chris Green', 'chris.g@email.com', 'Quotation follow-up', 'I submitted a quotation request a few days ago and haven\'t heard back yet.', '2025-07-20 13:21:51'),
(8, 'Jessica Brown', 'jess.b@email.com', 'Complaint about a provider', 'I had an issue with one of the listed providers. Can you please help me resolve it?', '2025-07-20 13:21:51'),
(9, 'Kevin Harris', 'kevin.h@email.com', 'Large Commercial Project', 'We are looking to renovate a 5-story hotel and need a comprehensive quote.', '2025-07-20 13:21:51'),
(10, 'Olivia Martin', 'olivia.m@email.com', 'Simple painting job', 'How much would it cost to paint a single small bedroom?', '2025-07-20 13:21:51'),
(11, 'kisho', 'kishojeyapragash15@gmail.com', 'gv b', 'fr', '2025-07-20 14:24:22');

-- --------------------------------------------------------

--
-- Table structure for table `custom_quotations`
--

CREATE TABLE `custom_quotations` (
  `id` int(11) NOT NULL,
  `quotation_id` varchar(50) DEFAULT NULL,
  `provider_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `advance` decimal(10,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `validity` int(11) DEFAULT NULL,
  `provider_notes` text DEFAULT NULL,
  `photos` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `project_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disputes`
--

CREATE TABLE `disputes` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `reported_by_id` int(11) NOT NULL COMMENT 'Usually the customer ID',
  `reported_against_id` int(11) NOT NULL COMMENT 'Usually the provider ID',
  `reason` text NOT NULL,
  `status` enum('open','under_review','resolved') NOT NULL DEFAULT 'open',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disputes`
--

INSERT INTO `disputes` (`id`, `quotation_id`, `reported_by_id`, `reported_against_id`, `reason`, `status`, `admin_notes`, `created_at`, `updated_at`) VALUES
(1, 8, 11, 12, 'The provider installed the wrong tiles in the bathroom. They are a different color from what we agreed upon and they are refusing to correct it without extra charges.', 'resolved', 'hi', '2025-07-20 13:21:51', '2025-07-21 05:06:39');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_type` enum('advance','final') NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `quotation_id`, `amount`, `payment_type`, `transaction_id`, `payment_date`) VALUES
(1, 1, 1875.00, 'advance', 'TRANS-ADV-1A2B3C', '2025-07-20 13:21:51'),
(2, 2, 550.00, 'advance', 'TRANS-ADV-4D5E6F', '2025-07-20 13:21:51'),
(3, 5, 3125.00, 'advance', 'TRANS-ADV-7G8H9I', '2025-07-20 13:21:51'),
(4, 5, 9375.00, 'final', 'TRANS-FIN-7G8H9I', '2025-07-20 13:21:51'),
(5, 8, 450.00, 'advance', 'TRANS-ADV-J1K2L3', '2025-07-20 13:21:51');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_items`
--

CREATE TABLE `portfolio_items` (
  `id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portfolio_items`
--

INSERT INTO `portfolio_items` (`id`, `provider_id`, `title`, `description`, `image_path`, `created_at`) VALUES
(1, 6, 'Minimalist Scandinavian Living Room', 'A complete overhaul focusing on clean lines, natural light, and functional furniture.', 'public/uploads/portfolio/sample1.jpg', '2025-07-20 13:21:51'),
(2, 6, 'Modern Kitchen Remodel', 'High-gloss cabinets, quartz countertops, and a smart lighting system.', 'public/uploads/portfolio/sample2.jpg', '2025-07-20 13:21:51'),
(3, 7, '19th Century Oak Wardrobe Restoration', 'Stripped, repaired, and refinished a family heirloom to its former glory.', 'public/uploads/portfolio/sample3.jpg', '2025-07-20 13:21:51'),
(4, 7, 'Victorian Terrace Facade Repair', 'Painstakingly repaired and repainted the exterior of a historic home.', 'public/uploads/portfolio/sample4.jpg', '2025-07-20 13:21:51'),
(5, 8, 'Luxury Hotel Lobby Design', 'Created a welcoming and luxurious space using marble, brass, and custom upholstery.', 'public/uploads/portfolio/sample5.jpg', '2025-07-20 13:21:51'),
(6, 12, 'Urban Loft Conversion', 'Transformed an industrial space into a chic, two-bedroom loft apartment.', 'public/uploads/portfolio/sample6.jpg', '2025-07-20 13:21:51'),
(7, 6, 'Cozy Home Office Setup', 'Designed a functional and inspiring workspace for a remote professional.', 'public/uploads/portfolio/sample7.jpg', '2025-07-20 13:21:51'),
(8, 7, 'Antique Chair Reupholstery', 'Brought a set of antique dining chairs back to life with new fabric and padding.', 'public/uploads/portfolio/sample8.jpg', '2025-07-20 13:21:51'),
(9, 8, 'Commercial Retail Space', 'Designed the interior for a new boutique, focusing on brand identity and customer flow.', 'public/uploads/portfolio/sample9.jpg', '2025-07-20 13:21:51'),
(10, 12, 'Patio and Outdoor Kitchen', 'Built a custom patio with an integrated outdoor kitchen and seating area.', 'public/uploads/portfolio/sample10.jpg', '2025-07-20 13:21:51');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `status` enum('awaiting_advance','in_progress','awaiting_final_payment','completed','disputed') NOT NULL DEFAULT 'awaiting_advance',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `quotation_id`, `status`, `start_date`, `end_date`) VALUES
(1, 1, 'in_progress', NULL, NULL),
(2, 2, 'awaiting_final_payment', NULL, NULL),
(3, 5, 'completed', NULL, NULL),
(4, 6, 'awaiting_advance', NULL, NULL),
(5, 8, 'disputed', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_updates`
--

CREATE TABLE `project_updates` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'ID of user who posted',
  `update_text` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_updates`
--

INSERT INTO `project_updates` (`id`, `project_id`, `user_id`, `update_text`, `image_path`, `created_at`) VALUES
(1, 1, 6, 'Great news, we have sourced the flooring and will begin installation on Monday!', NULL, '2025-07-20 13:21:51'),
(2, 1, 2, 'That sounds wonderful! Can\'t wait to see it.', NULL, '2025-07-20 13:21:51'),
(3, 1, 6, 'Here\'s a quick look at the progress today. The new paint color is up!', 'public/uploads/projects/livingroom_progress.jpg', '2025-07-20 13:21:51'),
(4, 2, 7, 'The furniture restoration is complete. The pieces look brand new! We are ready for final payment.', NULL, '2025-07-20 13:21:51'),
(5, 2, 3, 'Wow, that was fast! I will process the payment this evening.', NULL, '2025-07-20 13:21:51');

-- --------------------------------------------------------

--
-- Table structure for table `provider_availability`
--

CREATE TABLE `provider_availability` (
  `id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `provider_name` varchar(100) DEFAULT NULL,
  `available_date` date NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `service_type` varchar(100) DEFAULT NULL,
  `project_description` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `photos` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotations`
--

INSERT INTO `quotations` (`id`, `customer_id`, `provider_id`, `service_type`, `project_description`, `status`, `created_at`, `photos`) VALUES
(11, 16, 19, 'Interior Design', 'same as the image ', 'Awaiting Quote', '2025-09-04 13:20:36', NULL),
(12, 16, 19, 'Interior Design', 'i want the peacefull feeling attractive', 'Awaiting Quote', '2025-09-04 21:35:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `customer_id`, `provider_id`, `rating`, `review_text`, `created_at`) VALUES
(1, 2, 12, 5, 'Urban Crafters were amazing for our office project. On time, on budget, and the result is better than we imagined. Highly recommend!', '2025-07-20 13:21:51'),
(2, 3, 7, 5, 'I am speechless. My antique furniture looks incredible. Classic Restorations has a true artist on their team.', '2025-07-20 13:21:51'),
(3, 5, 8, 2, 'The initial design was great, but communication was very poor throughout the project. The end result was okay but the process was stressful.', '2025-07-20 13:21:51'),
(4, 11, 12, 4, 'The bathroom renovation is lovely. A few minor delays but overall a very positive experience. The team was clean and professional.', '2025-07-20 13:21:51'),
(5, 14, 6, 4, 'hi', '2025-07-20 14:19:53'),
(6, 14, 8, 3, 'cc', '2025-07-20 14:22:16');

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `provider_name` varchar(100) DEFAULT NULL,
  `provider_email` varchar(100) DEFAULT NULL,
  `main_service` varchar(100) DEFAULT NULL,
  `subcategories` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `provider_phone` varchar(20) DEFAULT NULL,
  `provider_address` varchar(255) DEFAULT NULL,
  `portfolio` text DEFAULT NULL,
  `provider_bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`id`, `provider_id`, `provider_name`, `provider_email`, `main_service`, `subcategories`, `created_at`, `provider_phone`, `provider_address`, `portfolio`, `provider_bio`) VALUES
(3, 19, 'daniel company', 'daniel@gmail.com', 'Interior Design', 'Interior Design - Ceiling & Lighting,Interior Design - Space Planning,Interior Design - Bathroom Design,Interior Design - Carpentry & Woodwork', '2025-09-04 13:12:32', '0764876568', 'Ithikandal adampan mannar', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('homepage_about_text', 'Innovista is a premier platform connecting skilled interior designers and restoration experts with clients seeking quality and reliability. Our mission is to simplify the process of creating beautiful spaces.'),
('homepage_welcome_message', 'Welcome to Innovista! Your one-stop solution for interior design and restoration services.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer','provider') NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `provider_status` enum('pending','approved','rejected') DEFAULT NULL,
  `credentials_verified` enum('yes','no') NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `portfolio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `provider_status`, `credentials_verified`, `created_at`, `phone`, `address`, `bio`, `portfolio`) VALUES
(1, 'Admin Innovista', 'admin@innovista.com', '$2y$10$g1lrPhsGOEZI9.A3U92GXe4Upxw4bGFU.q6/yyYuDQdFGK1dQ8DCW', 'admin', 'active', NULL, 'no', '2025-07-20 13:21:51', NULL, NULL, NULL, NULL),
(2, 'Alice Johnson', 'customer@test.com', '$2y$10$EiemzC30iYJ3/Ld259QxIeSgoYJOSAb51Iq8mFhEblCGeLg43qQo.', 'customer', 'active', NULL, 'no', '2025-07-20 13:21:51', NULL, NULL, NULL, NULL),
(3, 'Bob Williams', 'bob.w@example.com', '$2y$10$EiemzC30iYJ3/Ld259QxIeSgoYJOSAb51Iq8mFhEblCGeLg43qQo.', 'customer', 'active', NULL, 'no', '2025-07-20 13:21:51', NULL, NULL, NULL, NULL),
(4, 'Charlie Brown', 'charlie.b@example.com', '$2y$10$EiemzC30iYJ3/Ld259QxIeSgoYJOSAb51Iq8mFhEblCGeLg43qQo.', 'customer', 'inactive', NULL, 'no', '2025-07-20 13:21:51', NULL, NULL, NULL, NULL),
(5, 'Diana Miller', 'diana.m@example.com', '$2y$10$EiemzC30iYJ3/Ld259QxIeSgoYJOSAb51Iq8mFhEblCGeLg43qQo.', 'customer', 'active', NULL, 'no', '2025-07-20 13:21:51', NULL, NULL, NULL, NULL),
(13, 'jps', 'admin16@innovista.com', '$2y$10$e.rWG.qQIk5zESXKe3RH8eoVCAvnREGMhQSLqRuVMj8XLeVe.qXO.', 'customer', 'active', NULL, 'no', '2025-07-20 13:23:38', NULL, NULL, NULL, NULL),
(14, 'kisho', 'kishojeyapragash@gmail.com', '$2y$10$m1o8wfKIEJqa/TShindsTO2MZk3g9z6/FuHh./y9./bNboLP0MwYW', 'customer', 'active', NULL, 'no', '2025-07-20 13:25:52', NULL, NULL, NULL, NULL),
(16, 'kristo praveejiny', 'kristokristo323@gmail.com', '$2y$10$jpBj6FtCCz9rOOTkB3ClHOfjpoq6l5niQb1SUpBW5bsjxMJ1AFhBa', 'customer', 'active', NULL, 'no', '2025-08-28 12:19:37', NULL, NULL, NULL, NULL),
(19, 'daniel company', 'daniel@gmail.com', '$2y$10$qsiCTn/OKOXvc98fwrNB7uAwsXg/8sfTmfdfSupePzj/beD0U/ReK', 'provider', 'active', NULL, 'no', '2025-09-04 07:42:32', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_quotations`
--
ALTER TABLE `custom_quotations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_id` (`provider_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `disputes`
--
ALTER TABLE `disputes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_id` (`quotation_id`),
  ADD KEY `reported_by_id` (`reported_by_id`),
  ADD KEY `reported_against_id` (`reported_against_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_id` (`quotation_id`);

--
-- Indexes for table `portfolio_items`
--
ALTER TABLE `portfolio_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotation_id` (`quotation_id`);

--
-- Indexes for table `project_updates`
--
ALTER TABLE `project_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `provider_availability`
--
ALTER TABLE `provider_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `custom_quotations`
--
ALTER TABLE `custom_quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `disputes`
--
ALTER TABLE `disputes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `portfolio_items`
--
ALTER TABLE `portfolio_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `project_updates`
--
ALTER TABLE `project_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `provider_availability`
--
ALTER TABLE `provider_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `custom_quotations`
--
ALTER TABLE `custom_quotations`
  ADD CONSTRAINT `custom_quotations_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `custom_quotations_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- Add is_read column to contacts table
ALTER TABLE `contacts` ADD `is_read` TINYINT(1) NOT NULL DEFAULT 0 AFTER `created_at`;


INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('homepage_hero_h1', 'Transforming Spaces, Restoring Dreams'),
('homepage_hero_p', 'Your one-stop platform for interior design, painting, and restoration services in the Northern Province'),
('homepage_how_it_works_title', 'How It Works'),
('homepage_services_title', 'Our Core Services'),
('homepage_products_title', 'Complete Your Project'),
('homepage_products_description', 'Find high-quality products from trusted brands, all in one place. From paints to furniture, get everything you need for your project delivered.'),
('homepage_why_choose_us_title', 'Why Choose Innovista?'),
('homepage_testimonials_title', 'What Our Clients Say'),
('homepage_our_work_title', 'Our Recent Work'),
('homepage_our_work_description', 'A glimpse into the spaces we\'ve transformed.'),
('homepage_faq_title', 'Frequently Asked Questions'),
('homepage_cta_title', 'Ready to Start Your Next Project?'),
('homepage_cta_description', 'Whether you\'re looking to transform your home or grow your service business, the Innovista community is here for you. Join today for a seamless, transparent, and trustworthy experience.');


-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Sep 11, 2025 at 06:30 AM (Updated with new data)
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `innovista`
--

-- Add profile_image_path column to users table if it doesn't exist already
-- This line is safe to run even if the column exists (it will just be ignored).
-- If you just added it, ensure it's here before TRUNCATE USERS.
ALTER TABLE `users` ADD `profile_image_path` VARCHAR(255) DEFAULT 'assets/images/default-avatar.jpg' AFTER `portfolio`;


-- Disable foreign key checks to allow truncating tables with foreign keys
SET FOREIGN_KEY_CHECKS = 0;

-- --- TRUNCATE ALL TABLES (clear all existing data) ---
-- Order matters for truncating with foreign keys, but disabling checks makes it safe.
TRUNCATE TABLE contacts;
TRUNCATE TABLE payments;
TRUNCATE TABLE project_updates;
TRUNCATE TABLE disputes;
TRUNCATE TABLE projects;
TRUNCATE TABLE custom_quotations;
TRUNCATE TABLE quotations;
TRUNCATE TABLE portfolio_items;
TRUNCATE TABLE service;
TRUNCATE TABLE provider_availability;
TRUNCATE TABLE reviews;
TRUNCATE TABLE settings;
TRUNCATE TABLE users;

-- Reset AUTO_INCREMENT for all tables to start IDs from 1
ALTER TABLE contacts AUTO_INCREMENT = 1;
ALTER TABLE custom_quotations AUTO_INCREMENT = 1;
ALTER TABLE disputes AUTO_INCREMENT = 1;
ALTER TABLE payments AUTO_INCREMENT = 1;
ALTER TABLE portfolio_items AUTO_INCREMENT = 1;
ALTER TABLE projects AUTO_INCREMENT = 1;
ALTER TABLE project_updates AUTO_INCREMENT = 1;
ALTER TABLE provider_availability AUTO_INCREMENT = 1;
ALTER TABLE quotations AUTO_INCREMENT = 1;
ALTER TABLE reviews AUTO_INCREMENT = 1;
ALTER TABLE service AUTO_INCREMENT = 1;
-- Settings table uses setting_key as PRIMARY KEY, AUTO_INCREMENT is not applicable
ALTER TABLE users AUTO_INCREMENT = 1;

-- --- USERS TABLE (1 Admin, 4 Customers, 3 Providers) ---
-- Passwords for all users are 'password123' (hashed)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `provider_status`, `credentials_verified`, `created_at`, `phone`, `address`, `bio`, `portfolio`, `profile_image_path`) VALUES
(1, 'Admin Innovista', 'admin@innovista.com', '$2y$10$08xulE1tmiTJg9oeXKWu4oiXjekRhBuJ.', 'admin', 'active', NULL, 'no', '2025-07-20 07:51:51', NULL, NULL, NULL, NULL, 'assets/images/default-avatar.jpg'),
(2, 'Jane Doe', 'jane.doe@example.com', '$2y$10$w8.m2.k0.v3.e5.p9.q7.o1.s4.t6.u2.a7.b9.c1.', 'customer', 'active', NULL, 'no', '2025-09-01 04:30:00', '0771234567', '101 Main St, Jaffna', NULL, NULL, 'assets/images/default-avatar.jpg'),
(3, 'John Smith', 'john.s@example.com', '$2y$10$x1.y3.z5.a7.b9.c2.d4.f6.g8.h0.i2.j4.k6.l8.', 'customer', 'active', NULL, 'no', '2025-09-02 06:00:00', '0772345678', '202 Ocean View, Vavuniya', NULL, NULL, 'assets/images/default-avatar.jpg'),
(4, 'Emily White', 'emily.w@example.com', '$2y$10$q9.r1.s3.t5.u7.v0.w2.x4.y6.z8.a0.b2.c4.d6.', 'customer', 'inactive', NULL, 'no', '2025-09-03 08:45:00', '0773456789', '303 Hilltop Rd, Kilinochchi', NULL, NULL, 'assets/images/default-avatar.jpg'),
(5, 'David Lee Designs', 'david.lee@innovista.com', '$2y$10$c7.d9.e1.f3.g5.h8.i0.j2.k4.l6.m8.n0.o3.p5.', 'provider', 'active', 'approved', 'yes', '2025-09-04 03:30:00', '0774567890', '404 Art Gallery St, Jaffna', 'Specializing in modern and minimalist interior transformations.', '[]', 'https://images.unsplash.com/photo-1556157382-97eda2d62296?crop=entropy&cs=tinysrgb&fit=facearea&facepad=2&w=100&h=100&q=80'),
(6, 'Maria G. Restorations', 'maria.g@innovista.com', '$2y$10$f0.g2.h4.i6.j8.k1.l3.m5.n7.o9.p1.q3.r5.s7.', 'provider', 'active', 'approved', 'yes', '2025-09-05 05:00:00', '0775678901', '505 Heritage Lane, Mannar', 'Expert in antique furniture and architectural restoration, bringing old pieces back to life with care.', '[]', 'https://images.unsplash.com/photo-1611432579402-7037e3e2c1e4?crop=entropy&cs=tinysrgb&fit=facearea&facepad=2&w=100&h=100&q=60'),
(7, 'Robert J. Paints', 'robert.j@innovista.com', '$2y$10$k3.l5.m7.n9.o1.p3.q5.r7.s9.t2.u4.v6.w8.x0.', 'provider', 'active', 'pending', 'no', '2025-09-06 07:30:00', '0776789012', '606 Colourful Rd, Trincomalee', 'Professional painting services for homes and commercial spaces, ensuring a flawless finish every time.', '[]', 'https://images.unsplash.com/flagged/photo-1553642618-de0381320ff3?crop=entropy&cs=tinysrgb&fit=facearea&facepad=2&w=100&h=100&q=80'),
(8, 'Sarah Chen', 'sarah.c@example.com', '$2y$10$e4.f6.g8.h0.i2.j4.k6.l8.m0.n3.o5.p7.q9.r1.', 'customer', 'active', NULL, 'no', '2025-09-07 10:15:00', '0777890123', '707 Sunshine Apt, Batticaloa', NULL, NULL, 'assets/images/default-avatar.jpg'),
(9, 'Michael Green', 'michael.g@example.com', '$2y$10$v2.w4.x6.y8.z0.a3.b5.c7.d9.e1.f3.g5.h7.i9.', 'customer', 'active', NULL, 'no', '2025-09-08 12:00:00', '0778901234', '808 Palm St, Galle', NULL, NULL, 'assets/images/default-avatar.jpg');


-- --- SERVICE TABLE (Linked to Providers) ---
INSERT INTO `service` (`id`, `provider_id`, `provider_name`, `provider_email`, `main_service`, `subcategories`, `created_at`, `provider_phone`, `provider_address`, `portfolio`, `provider_bio`) VALUES
(1, 5, 'David Lee Designs', 'david.lee@innovista.com', 'Interior Design', 'Ceiling & Lighting, Space Planning, Bathroom Design, Carpentry & Woodwork', '2025-09-04 03:35:00', '0774567890', '404 Art Gallery St, Jaffna', 'Minimalist Scandinavian Living Room, Modern Kitchen Remodel', 'Specializing in modern and minimalist interior transformations.'),
(2, 6, 'Maria G. Restorations', 'maria.g@innovista.com', 'Restoration', 'Furniture Restoration, Floor Restoration, Door & Window Repairs, Art & Sculpture Repair', '2025-09-05 05:05:00', '0775678901', '505 Heritage Lane, Mannar', '19th Century Oak Wardrobe Restoration, Victorian Terrace Facade Repair', 'Expert in antique furniture and architectural restoration, bringing old pieces back to life with care.'),
(3, 7, 'Robert J. Paints', 'robert.j@innovista.com', 'Painting', 'Interior Painting, Exterior Painting, Water & Damp Proofing, Murals & Decorative Finishes', '2025-09-06 07:35:00', '0776789012', '606 Colourful Rd, Trincomalee', 'Luxury Hotel Lobby Design, Urban Loft Conversion', 'Professional painting services for homes and commercial spaces, ensuring a flawless finish every time.');

-- --- PORTFOLIO_ITEMS TABLE (Linked to Providers, using internet images) ---
INSERT INTO `portfolio_items` (`id`, `provider_id`, `title`, `description`, `image_path`, `created_at`) VALUES
(1, 5, 'Minimalist Scandinavian Living Room', 'A complete overhaul focusing on clean lines, natural light, and functional furniture.', 'https://images.unsplash.com/photo-1615873968403-f0ed14e4142f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQ5ODh8MHwxfHNlYXJjaHw3fHNhbmRpbmF2aWFuJTIwbGl2aW5nJTIwcm9vbXxlbnwwfHx8fDE3MjU4ODY1NjV8MA&ixlib=rb-4.0.3&q=80&w=1080', '2025-09-04 09:10:00'),
(2, 5, 'Modern Kitchen Remodel', 'High-gloss cabinets, quartz countertops, and a smart lighting system.', 'https://images.unsplash.com/photo-1595493014138-0382d6199f7d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQ5ODh8MHwxfHNlYXJjaHw3fG1vZGVybiUyMGtpdGNoZW4lMjByZW1vZGVsJmZufDB8MHx8fDE3MjU4ODY1NjZ8MA&ixlib=rb-4.0.3&q=80&w=1080', '2025-09-04 09:15:00'),
(3, 6, '19th Century Oak Wardrobe Restoration', 'Stripped, repaired, and refinished a family heirloom to its former glory.', 'https://images.unsplash.com/photo-1606787994801-44754a614051?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQ5ODh8MHwxfHNlYXJjaHwxNXxmdXJuaXR1cmUlMjByZXN0b3JhdGlvbnxlbnwwfHx8fDE3MjU4ODY1Njl8MA&ixlib=rb-4.0.3&q=80&w=1080', '2025-09-05 10:40:00'),
(4, 6, 'Victorian Terrace Facade Repair', 'Painstakingly repaired and repainted the exterior of a historic home.', 'https://images.unsplash.com/photo-1569429598284-88481358b534?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQ5ODh8MHwxfHNlYXJjaHw0fG9sZCUyMGhvdXNlJTIwcmVzdG9yYXRpb258ZW52MHwxfHx8fDE3MjU4ODY1NzB8MA&ixlib=rb-4.0.3&q=80&w=1080', '2025-09-05 10:45:00'),
(5, 7, 'Luxury Hotel Lobby Design', 'Created a welcoming and luxurious space using marble, brass, and custom upholstery.', 'https://images.unsplash.com/photo-1579621970795-87fbb2f71617?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQ5ODh8MHwxfHNlYXJjaHw3fGhvdGVsJTIwbG9iYnl8ZW58MHx8fHwxNzI1ODg2NTc0fDA&ixlib=rb-4.0.3&q=80&w=1080', '2025-09-06 13:10:00'),
(6, 7, 'Urban Loft Conversion', 'Transformed an industrial space into a chic, two-bedroom loft apartment.', 'https://images.unsplash.com/photo-1582268482024-5d9c7d81249e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQ5ODh8MHwxfHNlYXJjaHwxOHxsb2Z0JTIwY29udmVyc2lvbnxlbnwwfHx8fDE3MjU4ODY1NzV8MA&ixlib=rb-4.0.3&q=80&w=1080', '2025-09-06 13:15:00'),
(7, 5, 'Cozy Home Office Setup', 'Designed a functional and inspiring workspace for a remote professional.', 'https://images.unsplash.com/photo-1596541223963-7c38520268a7?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQ5ODh8MHwxfHNlYXJjaHw3fGhvbWUlMjBvZmZpY2V8ZW58MHx8fHwxNzI1ODg2NzQxfDA&ixlib=rb-4.0.3&q=80&w=1080', '2025-09-07 10:00:00'),
(8, 6, 'Modern Farmhouse Kitchen', 'Blends rustic charm with modern amenities, featuring custom cabinetry and a large island.', 'https://images.unsplash.com/photo-1580173663702-f19b5f93b5d1?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQ5ODh8MHwxfHNlYXJjaHwxNHxraXRjaGVuJTIwcmVtb2RlbHxlbnwwfHx8fDE3MjU4ODY1NzV8MA&ixlib=rb-4.0.3&q=80&w=1080', '2025-09-08 11:00:00'),
(9, 5, 'Luxury Bathroom Remodel', 'Spa-like atmosphere with marble finishes, freestanding tub, and smart lighting.', 'https://images.unsplash.com/photo-1580879483863-74b8c9d0f3c5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQ5ODh8MHwxfHNlYXJjaHw0fGx1eHVyeSUyMGJhdGhyb29tfGVufDB8fHx8MTcyNTg4Njg1NXww&ixlib=rb-4.0.3&q=80&w=1080', '2025-09-09 14:00:00');


-- --- CONTACTS TABLE ---
INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `is_read`) VALUES
(1, 'Michael Smith', 'msmith@email.com', 'Question about services', 'Do you offer services in the Kilinochchi area? I am looking to renovate my office space.', '2025-09-09 13:21:51', 0),
(2, 'Laura Wilson', 'laura.w@email.com', 'Urgent: Water Damage Restoration', 'I have urgent water damage in my home. Do you offer emergency services? Please contact me ASAP.', '2025-09-10 08:30:00', 0),
(3, 'James Taylor', 'jtaylor@email.com', 'Request for a specific material', 'Can your providers source Italian marble for a kitchen countertop? I am looking for a specific type.', '2025-09-08 16:00:00', 1),
(4, 'Olivia Martin', 'olivia.m@email.com', 'Simple painting job', 'How much would it cost to paint a single small bedroom? I need a quote for a standard size room.', '2025-09-07 09:10:00', 1),
(5, 'Chris Green', 'chris.g@email.com', 'Quotation follow-up', 'I submitted a quotation request a few days ago and haven\'t heard back yet. My reference is #INV-0005.', '2025-09-11 02:00:00', 0),
(6, 'Anna Johnson', 'anna.j@mail.com', 'Partnership Inquiry', 'We are a local furniture workshop interested in collaborating with your platform. Who should I speak to?', '2025-09-11 04:30:00', 0),
(7, 'Peter Jones', 'peter.j@mail.com', 'Feedback on recent project', 'The interior design service was excellent! Very happy with the outcome. Kudos to David Lee Designs.', '2025-09-11 08:00:00', 0),
(8, 'Sophia Brown', 'sophia.b@mail.com', 'Technical issue', 'I am having trouble uploading images to my portfolio. The upload seems to fail every time. Can you help?', '2025-09-11 09:15:00', 0),
(9, 'Liam Davis', 'liam.d@mail.com', 'Query about pricing', 'Could you provide more details on the pricing structure for restoration services?', '2025-09-11 10:30:00', 1);


-- --- QUOTATIONS TABLE (Customer requests for quotes) ---
INSERT INTO `quotations` (`id`, `customer_id`, `provider_id`, `service_type`, `project_description`, `status`, `created_at`, `photos`) VALUES
(1, 2, 5, 'Interior Design', 'Design a modern living room with a focus on natural light and comfort. Budget is around Rs 150,000.', 'Awaiting Quote', '2025-09-08 14:00:00', NULL),
(2, 3, 6, 'Restoration', 'Restore an old wooden dining table and six chairs. They are quite worn but have sentimental value.', 'Awaiting Quote', '2025-09-09 09:30:00', NULL),
(3, 8, 7, 'Painting', 'Repaint a 3-bedroom house exterior. Need weather-resistant paint. Estimated area is 200 sq meters.', 'Quote Sent', '2025-09-09 16:00:00', NULL),
(4, 2, 6, 'Restoration', 'Repair a cracked ceramic vase from the 18th century. It\'s delicate work.', 'Awaiting Quote', '2025-09-10 11:00:00', NULL),
(5, 9, 5, 'Interior Design', 'Complete renovation of a small bathroom. Looking for contemporary design with smart storage solutions.', 'Awaiting Quote', '2025-09-11 10:00:00', NULL),
(6, 3, 7, 'Painting', 'Interior painting of a new office space, including accent walls. Roughly 1000 sq ft.', 'Awaiting Quote', '2025-09-11 11:30:00', NULL),
(7, 4, 6, 'Restoration', 'Wooden floor sanding and re-polishing for a master bedroom (20 sq meters).', 'Awaiting Quote', '2025-09-11 12:45:00', NULL);


-- --- CUSTOM_QUOTATIONS TABLE (Provider responses to requests) ---
INSERT INTO `custom_quotations` (`id`, `quotation_id`, `provider_id`, `customer_id`, `amount`, `advance`, `start_date`, `end_date`, `validity`, `provider_notes`, `photos`, `status`, `created_at`, `project_description`) VALUES
(1, 1, 5, 2, 120000.00, 30000.00, '2025-09-15', '2025-10-15', 30, 'Initial design concepts will be shared within 5 business days. Advance payment secures booking.', NULL, 'pending', '2025-09-09 10:00:00', 'Modern living room design with custom furniture.'),
(2, 3, 7, 8, 95000.00, 23750.00, '2025-09-18', '2025-10-05', 20, 'Using high-quality exterior emulsion with 5-year warranty. Weather permitting schedule.', NULL, 'approved', '2025-09-10 09:00:00', 'Exterior repainting for a 3-bedroom house.'),
(3, 5, 5, 9, 85000.00, 21250.00, '2025-10-01', '2025-10-20', 30, 'Includes waterproofing and selection of anti-slip tiles. Design mockups will be provided.', NULL, 'approved', '2025-09-11 12:00:00', 'Contemporary bathroom renovation with smart storage.'),
(4, 7, 6, 4, 35000.00, 8750.00, '2025-09-22', '2025-09-25', 15, 'Floor treatment includes three layers of varnish for durability. Schedule subject to floor drying times.', NULL, 'pending', '2025-09-11 14:00:00', 'Wooden floor restoration for master bedroom.');


-- --- PROJECTS TABLE (Linked to custom_quotations) ---
INSERT INTO `projects` (`id`, `quotation_id`, `status`, `start_date`, `end_date`) VALUES
(1, 2, 'in_progress', '2025-09-18', '2025-10-05'), -- Project from custom_quotation ID 2 (for quotation ID 3)
(2, 1, 'awaiting_advance', '2025-09-15', '2025-10-15'), -- Project from custom_quotation ID 1 (for quotation ID 1)
(3, 3, 'in_progress', '2025-10-01', '2025-10-20'), -- Project from custom_quotation ID 3 (for quotation ID 5)
(4, 4, 'awaiting_advance', '2025-09-22', '2025-09-25'); -- Project from custom_quotation ID 4 (for quotation ID 7)


-- --- PAYMENTS TABLE (Linked to custom_quotations) ---
INSERT INTO `payments` (`id`, `quotation_id`, `amount`, `payment_type`, `transaction_id`, `payment_date`) VALUES
(1, 2, 23750.00, 'advance', 'INV-TRX-001A', '2025-09-12 10:00:00'), -- Advance for Project 1 (custom_quotation ID 2)
(2, 2, 71250.00, 'final', 'INV-TRX-001F', '2025-10-06 15:00:00'), -- Final for Project 1 (custom_quotation ID 2)
(3, 3, 21250.00, 'advance', 'INV-TRX-002A', '2025-09-13 11:00:00'), -- Advance for Project 3 (custom_quotation ID 3)
(4, 4, 8750.00, 'advance', 'INV-TRX-003A', '2025-09-15 09:30:00'); -- Advance for Project 4 (custom_quotation ID 4)


-- --- PROJECT_UPDATES TABLE (Linked to projects and users) ---
INSERT INTO `project_updates` (`id`, `project_id`, `user_id`, `update_text`, `image_path`, `created_at`) VALUES
(1, 1, 7, 'Exterior walls prepped and primed. Ready for the first coat of paint.', 'https://images.unsplash.com/photo-1582046487920-5c7a40f11d9d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQ5ODh8MHwxfHNlYXJjaHwzfHdlbGwlMjBwYWludGluZ3xlbnwwfHx8fDE3MjU4ODY3NDd8MA&ixlib=rb-4.0.3&q=80&w=1080', '2025-09-19 09:00:00'),
(2, 1, 8, 'Looking great! Thanks for the update. Can\'t wait to see the final color.', NULL, '2025-09-19 11:30:00'),
(3, 1, 7, 'First coat of exterior paint applied. Will dry overnight. Second coat tomorrow.', 'https://images.unsplash.com/photo-1596541223963-7c38520268a7?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQ5ODh8MHwxfHNlYXJjaHwxMHxwYWludGluZyUyMGhvdXNlfGVufDB8fHx8MTcyNTg4Njc0N3ww&ixlib=rb-4.0.3&q=80&w=1080', '2025-09-20 14:00:00'),
(4, 3, 5, 'Bathroom tiling work has begun. Progress photos attached. We expect to finish tiling by end of week.', 'https://images.unsplash.com/photo-1627387340062-850785a974b0?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQ5ODh8MHwxfHNlYXJjaHwxNnxrZXRjaGVuJTIwdGlsZXN8ZW58MHx8fHwxNzI1ODg2NzQ1fDA&ixlib=rb-4.0.3&q=80&w=1080', '2025-10-03 10:00:00'),
(5, 3, 9, 'Tiling looks fantastic! Thanks for the update.', NULL, '2025-10-03 11:30:00');


-- --- PROVIDER_AVAILABILITY TABLE (Simple entries for example) ---
INSERT INTO `provider_availability` (`id`, `provider_id`, `provider_name`, `available_date`, `created_at`) VALUES
(1, 5, 'David Lee Designs', '2025-09-25', '2025-09-01 08:00:00'),
(2, 5, 'David Lee Designs', '2025-09-26', '2025-09-01 08:00:00'),
(3, 6, 'Maria G. Restorations', '2025-10-01', '2025-09-02 11:00:00'),
(4, 7, 'Robert J. Paints', '2025-09-20', '2025-09-05 14:00:00'),
(5, 5, 'David Lee Designs', '2025-10-05', '2025-09-08 09:00:00'),
(6, 6, 'Maria G. Restorations', '2025-10-02', '2025-09-10 10:00:00'),
(7, 7, 'Robert J. Paints', '2025-10-10', '2025-09-11 08:00:00');


-- --- REVIEWS TABLE (Customer reviews for providers) ---
INSERT INTO `reviews` (`id`, `customer_id`, `provider_id`, `rating`, `review_text`, `created_at`) VALUES
(1, 2, 5, 5, 'David Lee Designs transformed our living room beyond our expectations! Highly professional and great eye for detail.', '2025-10-18 10:00:00'),
(2, 3, 6, 4, 'Maria G. did a wonderful job restoring our antique table. A little slow on delivery but the quality is undeniable.', '2025-09-20 14:00:00'),
(3, 8, 7, 5, 'Robert J. Paints was efficient and the exterior paint job looks fantastic. Very satisfied with their work.', '2025-10-08 09:00:00'),
(4, 9, 5, 5, 'Exceptional bathroom renovation! The team was clean, efficient, and the final look is exactly what I envisioned.', '2025-10-25 11:00:00'),
(5, 4, 6, 3, 'The floor restoration was okay, but took longer than expected and communication could have been better.', '2025-10-01 10:00:00');


-- --- DISPUTES TABLE (Linked to custom_quotations, customers, providers) ---
INSERT INTO `disputes` (`id`, `quotation_id`, `reported_by_id`, `reported_against_id`, `reason`, `status`, `admin_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 5, 'The project started late and the materials used were different from the agreement. Quality is not as expected.', 'open', NULL, '2025-10-20 11:00:00', '2025-10-20 11:00:00'),
(2, 2, 8, 7, 'Minor miscommunication regarding paint color shade, but resolved quickly after admin intervention.', 'resolved', 'Admin facilitated a quick color correction with the provider. Customer satisfied with outcome.', '2025-10-01 09:00:00', '2025-10-03 14:00:00'),
(3, 3, 9, 5, 'The new shower head installation has a slow leak. I\'ve contacted the provider but haven\'t received a response.', 'open', NULL, '2025-10-28 15:00:00', '2025-10-28 15:00:00');


-- --- SETTINGS TABLE (for dynamic homepage content) ---
-- These settings will be loaded dynamically by your public/index.php and admin/settings.php
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('admin_contact_email', 'contact@innovista.com'),
('facebook_url', 'https://facebook.com/innovista'),
('homepage_about_text', 'Innovista is a premier platform connecting skilled interior designers and restoration experts with clients seeking quality and reliability. Our mission is to simplify the process of creating beautiful spaces.'),
('homepage_cta_description', 'Whether you\'re looking to transform your home or grow your service business, the Innovista community is here for you. Join today for a seamless, transparent, and trustworthy experience.'),
('homepage_cta_title', 'Ready to Start Your Next Project?'),
('homepage_faq_title', 'Frequently Asked Questions'),
('homepage_hero_h1', 'Transforming Spaces, Restoring Dreams'),
('homepage_hero_p', 'Your one-stop platform for interior design, painting, and restoration services in the Northern Province'),
('homepage_how_it_works_title', 'How It Works'),
('homepage_our_work_description', 'A glimpse into the spaces we\'ve transformed, showcasing our best projects and diverse expertise.'),
('homepage_our_work_title', 'Our Recent Work'),
('homepage_products_description', 'Find high-quality products from trusted brands, all in one place. From paints to furniture, get everything you need for your project delivered.'),
('homepage_products_title', 'Complete Your Project'),
('homepage_services_title', 'Our Core Services'),
('homepage_testimonials_title', 'What Our Clients Say'),
('homepage_why_choose_us_title', 'Why Choose Innovista?'),
('instagram_url', 'https://instagram.com/innovista'),
('platform_address', '123 Design Lane, Jaffna, Sri Lanka'),
('platform_name', 'Innovista'),
('homepage_welcome_message', 'Welcome to Innovista! Your one-stop solution for interior design and restoration services.');


-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

COMMIT;