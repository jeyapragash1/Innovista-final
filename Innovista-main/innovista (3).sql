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