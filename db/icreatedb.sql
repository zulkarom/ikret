-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 04, 2026 at 08:19 PM
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
-- Database: `icreatedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_assignment`
--

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_item`
--

CREATE TABLE `auth_item` (
  `name` varchar(64) NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text DEFAULT NULL,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` blob DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_item_child`
--

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_rule`
--

CREATE TABLE `auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` blob DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cert_tmpl`
--

CREATE TABLE `cert_tmpl` (
  `id` int(11) NOT NULL,
  `template_name` varchar(255) DEFAULT NULL,
  `name_mt` double DEFAULT 10,
  `name_size` double DEFAULT 10,
  `field1_mt` double DEFAULT NULL,
  `field1_size` double DEFAULT NULL,
  `field2_mt` double DEFAULT NULL,
  `field2_size` double DEFAULT NULL,
  `field3_mt` double DEFAULT NULL,
  `field3_size` double DEFAULT NULL,
  `field4_mt` double DEFAULT NULL,
  `field4_size` double DEFAULT NULL,
  `field5_mt` double DEFAULT NULL,
  `field5_size` double DEFAULT NULL,
  `margin_right` double DEFAULT NULL,
  `margin_left` double DEFAULT NULL,
  `set_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=preset,2=custom_html',
  `custom_html` text DEFAULT NULL,
  `template_file` text DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `published` tinyint(1) DEFAULT 0,
  `is_portrait` tinyint(1) DEFAULT 1,
  `published_at` datetime DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `align` tinyint(1) NOT NULL DEFAULT 3 COMMENT '1=left,2=right,3=center'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cert_tmpl`
--

INSERT INTO `cert_tmpl` (`id`, `template_name`, `name_mt`, `name_size`, `field1_mt`, `field1_size`, `field2_mt`, `field2_size`, `field3_mt`, `field3_size`, `field4_mt`, `field4_size`, `field5_mt`, `field5_size`, `margin_right`, `margin_left`, `set_type`, `custom_html`, `template_file`, `updated_at`, `published`, `is_portrait`, `published_at`, `publish_date`, `align`) VALUES
(1, 'cert of participation', 250, 21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 110, 1, NULL, 'cert-participation-program.jpg', '2024-05-06 13:30:28', 0, 0, '2024-05-06 13:30:28', NULL, 3),
(2, 'cert of committee', 270, 27, 700, 26, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, NULL, 'cert-committee.jpg', '2024-05-06 13:30:28', 0, 0, '2024-05-06 13:30:28', NULL, 3),
(3, 'cert of jury', 255, 27, 700, 26, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, NULL, 'cert-jury.jpg', '2024-05-06 13:30:28', 0, 0, '2024-05-06 13:30:28', NULL, 3),
(4, 'cert of achievement', 250, 21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 110, 1, NULL, 'cert-achievement.jpg', '2024-05-06 13:30:28', 0, 0, '2024-05-06 13:30:28', NULL, 3),
(5, 'cert of excellence', 250, 21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 110, 1, NULL, 'cert-excellence.jpg', '2024-05-06 13:30:28', 0, 0, '2024-05-06 13:30:28', NULL, 3),
(6, 'cert of participation QR', 350, 27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 110, 1, NULL, 'cert-participation-qr.jpg', '2024-05-06 13:30:28', 0, 0, '2024-05-06 13:30:28', NULL, 3),
(7, 'cert of participation session', 250, 27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 110, 1, NULL, 'cert-participation-session.jpg', '2024-05-06 13:30:28', 0, 0, '2024-05-06 13:30:28', NULL, 3);

-- --------------------------------------------------------

--
-- Table structure for table `committee`
--

CREATE TABLE `committee` (
  `id` int(11) NOT NULL,
  `com_name` varchar(200) DEFAULT NULL,
  `is_jawatankuasa` int(1) DEFAULT NULL,
  `is_student` tinyint(1) DEFAULT 0,
  `com_name_en` varchar(200) DEFAULT NULL,
  `is_pengarah` tinyint(1) NOT NULL DEFAULT 0,
  `can_approve` tinyint(1) NOT NULL DEFAULT 0,
  `cert_only` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `committee`
--

INSERT INTO `committee` (`id`, `com_name`, `is_jawatankuasa`, `is_student`, `com_name_en`, `is_pengarah`, `can_approve`, `cert_only`) VALUES
(1, 'PENASIHAT I', 0, 0, 'Advisor I', 0, 1, 0),
(3, 'PENGERUSI I', 0, 0, 'Chairman I', 0, 1, 0),
(4, 'PENGERUSI II', 0, 0, 'Chairman II', 0, 1, 0),
(5, 'KETUA PROGRAM', 0, 0, 'Head Program Director', 0, 1, 0),
(6, 'TIMB. PENGARAH PROGRAM', 0, 0, 'Deputy Head Program Director', 0, 1, 0),
(7, 'SETIAUSAHA I', 0, 0, 'Secretary I', 0, 1, 0),
(8, 'SETIAUSAHA II', 0, 0, 'Secretary II', 0, 1, 0),
(9, 'BENDAHARI I', 0, 0, 'Treasurer I', 0, 1, 0),
(10, 'BENDAHARI II', 0, 0, 'Treasurer II', 0, 1, 0),
(11, 'PENGARAH PROGRAM COMEI 3.0', 0, 0, 'Program Director of COMEI 3.0', 1, 1, 0),
(12, 'PENGARAH PROGRAM JFED', 0, 0, 'Program Director of JFED', 1, 1, 0),
(13, 'PENGARAH PROGRAM AIFIF', 0, 0, 'Program Director of AIFIF', 1, 1, 0),
(14, 'PENGARAH PROGRAM NeWEEK', 0, 0, 'Program Director of NeWEEK', 1, 1, 0),
(15, 'PENGARAH PROGRAM RISE', 0, 0, 'Program Director of RISE', 1, 1, 0),
(16, 'PENGARAH PROGRAM IMPACT', 0, 0, 'Program Director of IMPACT', 1, 1, 0),
(17, 'JAWATANKUASA PUBLISITI DAN PROMOSI', 1, 0, 'Publicity and Promotion Committee', 0, 0, 0),
(18, 'JAWATANKUASA PENDAFTARAN (I-CREATE)', 1, 0, 'Registration Committee (I-CREATE)', 0, 0, 0),
(19, 'JAWATANKUASA CENDERAHATI & SIJIL', 1, 0, 'Souvenir And Certificate Committee', 0, 0, 0),
(20, 'JAWATANKUASA KECERIAAN DAN KEBERSIHAN TEMPAT', 1, 0, 'Venue Ambiance and Cleanliness Committee', 0, 0, 0),
(21, 'JAWATANKUASA ATURCARA MAJLIS DAN BUKU', 1, 0, 'Event Planning and Program Book Committee', 0, 0, 0),
(22, 'JAWATANKUASA PERTANDINGAN', 1, 0, 'Competition Committee', 0, 0, 0),
(23, 'JAWATANKUASA PENJURIAN', 1, 0, 'Jury Committee', 0, 0, 0),
(24, 'Officiating and Protocol Committee', 1, 0, 'Officiating and Protocol Committee', 0, 0, 0),
(25, 'JAWATANKUASA PERSEGARAN DAN HI-TEA', 1, 0, 'Refreshment and Hi-Tea Committee', 0, 0, 0),
(26, 'JAWATANKUASA PERSIAPAN TEMPAT, TEKNIKAL DAN JURUGAMBAR', 1, 0, 'Venue Preparation, Technical and Photography Committee', 0, 0, 0),
(27, 'JAWATANKUASA SEMINAR DAN PERKONGSIAN', 1, 0, 'Seminar and Sharing Session Committee', 0, 0, 0),
(28, 'JAWATANKUASA LOGISTIK DAN PENGINAPAN', 1, 0, 'Logistics and Accommodation Committee', 0, 0, 0),
(29, 'JAWATANKUASA PERASMIAN, PROTOKOL DAN PAMERAN', 1, 0, 'Exhibition Committee', 0, 0, 0),
(30, 'JAWATANKUASA JEMPUTAN', 1, 0, 'Invitation Committee', 0, 0, 0),
(31, 'JAWATANKUASA PEGAWAI PENGIRING', 1, 0, 'Liaison Officer Committee ', 0, 0, 0),
(32, 'JAWATANKUASA PELAJAR', 0, 1, 'Student Committee ', 0, 0, 0),
(33, 'JAWATANKUASA PENDAFTARAN (HI-TEA)', 1, 0, 'Registration Committee (HI-TEA)', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `competition_cat`
--

CREATE TABLE `competition_cat` (
  `id` int(11) NOT NULL,
  `cat_name` varchar(100) DEFAULT NULL,
  `lecturer` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `competition_cat_program`
--

CREATE TABLE `competition_cat_program` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `cat_name` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competition_cat_program`
--

INSERT INTO `competition_cat_program` (`id`, `program_id`, `cat_name`, `sort_order`, `is_active`) VALUES
(1, 7, 'Educator & Learning Transformation Innovation', 1, 1),
(2, 7, 'Business & Digital Economy Innovation', 2, 1),
(3, 7, 'AI, Emerging Technology & Digital Media Innovation', 3, 1),
(4, 7, 'Sustainability & Environmental Innovation', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `negeri`
--

CREATE TABLE `negeri` (
  `negeri_name` varchar(15) DEFAULT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `negeri`
--

INSERT INTO `negeri` (`negeri_name`, `id`) VALUES
('Kelantan', 1),
('Johor', 2),
('Kedah', 3),
('Melaka', 4),
('Negeri Sembilan', 5),
('Pahang', 6),
('Perak', 7),
('Perlis', 8),
('Pulau Pinang', 9),
('Sabah', 10),
('Sarawak', 11),
('Selangor', 12),
('Terengganu', 13),
('Kuala Lumpur', 14);

-- --------------------------------------------------------

--
-- Table structure for table `participant_cat_program`
--

CREATE TABLE `participant_cat_program` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `cat_name` varchar(255) NOT NULL,
  `mode` tinyint(1) NOT NULL COMMENT '1=physical 2=online',
  `fee` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `participant_cat_program`
--

INSERT INTO `participant_cat_program` (`id`, `program_id`, `cat_name`, `mode`, `fee`, `sort_order`, `is_active`) VALUES
(1, 7, 'Primary School', 1, 'RM50/ Group', 1, 1),
(2, 7, 'Secondary School', 1, 'RM60/ Group', 2, 1),
(3, 7, 'University (UMK)', 1, 'RM70/ Group', 3, 1),
(4, 7, 'University (External)', 2, 'RM60/ Group', 4, 1),
(5, 7, 'Professional', 2, 'RM150/ Group', 5, 1),
(6, 7, 'Industry', 2, 'RM200/ Group', 6, 1),
(7, 7, 'International', 2, 'USD50/ Group', 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE `program` (
  `id` int(11) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `program_abbr` varchar(50) DEFAULT NULL,
  `public_reg_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `reg_info` text DEFAULT NULL,
  `payment_info` text NOT NULL,
  `payment_short` varchar(255) DEFAULT NULL,
  `has_sub` tinyint(1) DEFAULT 0 COMMENT '1=yes,2=no',
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `program_type` tinyint(4) DEFAULT 1 COMMENT '1=has competition, 2 = sharing -attendance'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program`
--

INSERT INTO `program` (`id`, `program_name`, `program_abbr`, `public_reg_enabled`, `reg_info`, `payment_info`, `payment_short`, `has_sub`, `date_start`, `date_end`, `program_type`) VALUES
(1, 'THE INITIATIVE FOR MEANINGFUL PROJECT AND COMMUNITY TRANSFORMATION (IMPACT)', 'IMPACT', 0, '<div><span>The Initiative for Meaningful Project and Community Transformation (IMPACT) is a community project competition in conjunction with International Convention on Resourceful Entrepreneurs Achieving Tomorrow\'s Excellence (i-CREATE) where the students (participants), guided by their lecturer (advisor), showcase ideas or past community projects involved via poster presentation. The scope of the project is wide, including environmental initiatives, health awareness, arts, culture, education, skills development, community infrastructure, technology, social justice, etc. IMPACT was expected to involve participants from high institution students in the two stages of the competition.&nbsp;</span><span>Only shortlisted participants in the final stage.</span></div><div><span><br></span></div><div><span>Initial stage:&nbsp;</span></div><div><span>Date: 25th April - 23 May 2024&nbsp;</span></div><div><span>Venue: Online</span></div><div><br></div><div><span>Final Stage:</span></div><div><span>Date: 10th - 13th June 2024</span></div><div><span>Time: 8.30AM - 5.00PM</span></div><div><span>Venue: Universiti Malaysia Kelantan, City Campus.</span></div><div><div><br></div><div>For any inquiries regarding the registration, please do not hesitate to email us at icreate.fkp@umk.edu.my or directly contact +60134264975 (Nur A\'mirah binti Mohd Yaziz), IMPACT Director in conjunction with i-CREATE.</div><div><br></div><div>Thank you.</div><div><br></div><div>Regards,</div><div><b>THE COMMITTEE&nbsp;</b></div><div><b>IMPACT</b></div></div>', '', '(Please upload the completed proof of payment and all information as shows in Attachment A, PDF format only)', 0, '2024-05-01', '2024-06-11', 1),
(2, 'THE CARNIVAL OF MEGAPRENEURSHIP & INNOVATION (COMEI 3.0)', 'COME 3.0', 0, '<div>Carnival of Megapreneurship &amp; Innovation (COMEI 3.0) will be held on 10th June 2024 until 13th June 2024 at UMK City Campus. There are seven (7) competitions will be held;&nbsp;\r\n<div>1. E-Preneur</div>\r\n<div>2. Business Idea Pitching</div>\r\n<div>3. Product Marketing Creative Video Competition</div>\r\n<div>4. Most Viable Student Venture</div>\r\n<div>5.&nbsp;Takaful Product Innovation</div>\r\n<div>6. TaxPro Idea Competition</div>\r\n<div>&nbsp;</div>\r\n<div>For any inquiries regarding the registration, please do not hesitate to email us at comei.fkp@umk.edu.my or directly contact your respective lecturer.&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>Thank you.</div>\r\n<div>&nbsp;</div>\r\n<div>Regards,</div>\r\n<div><strong>THE COMMITTEE&nbsp;</strong></div>\r\n<div><strong>COMEI 3.0&nbsp;</strong></div>\r\n</div>', '', '(Please upload the proof payment here, in PDF ONLY)', 1, '2024-06-09', '2024-06-11', 1),
(3, 'NASCENT ENTREPRENEURIAL WEEK (NEWEEK) 2024', 'NEWEEK', 0, '<div>Nascent Entrepreneurial Week (NEWeek) will be held on <b>10th June 2024 until 13th June 2024 </b>at UMK City Campus.\r\n<div>For any inquiries regarding the registration, please do not hesitate to email us at icreate.fkp@umk.edu.my or directly contact your respective lecturer.&nbsp;<br></div><div><br></div><div>Thank you.</div><div><br></div><div>Regards,</div><div><b>THE COMMITTEE&nbsp;</b></div><div><b>NEWEEK</b></div></div></div>', '', '', 0, '2024-06-10', '2024-06-13', 1),
(4, 'THE ACCOUNTING, BANKING AND ISLAMIC FINANCE FESTIVAL (AIFIF)', 'AIFIF', 0, '<div><div><span><b>Calling all accounting and finance enthusiasts!</b></span></div><span><div><span><br></span></div>The Accounting, Banking and\nIslamic Finance Festival (AIFIF) is happening</span>&nbsp;on 12th June 2024 (Wednesday) at the DTK,&nbsp; UMK City Campus. This exciting event features forum, seminar, sharing session, booth and exhibition designed to equip you with valuable financial knowledge and career insights.<div><br></div><div><b>Event Highlights:</b></div><div><ul><li><span style=\"font-weight:700\">Forum </span>titled&nbsp;Strategic Financial Management and Cultivating Financial Stability</li><li><span style=\"font-weight:700\">Seminar </span>titled&nbsp;Trailblazing your Career: Industry Trends and Taxation Tips</li><li><b>Booth and Exhibition</b>&nbsp;from Accounting, Banking, and Islamic Financial Institutions</li><li><b>Sharing Sessions</b> from selected Industries</li></ul><div><p><span style=\"font-weight:700\">Registration Fees:</span></p><ul><li>UMK Students: RM 5</li><li>Non-UMK Students: RM 10</li></ul><p><span style=\"font-weight:700\">Registration Deadline:</span> 15th May 2024</p><p><span style=\"font-weight:700\">Please note:</span><span> Registration fees are non-refundable.</span><br></p><p><span style=\"font-weight:700\">For Inquiries:</span></p><ul><li>Email: icreate.fkp@umk.edu.my</li><li>Contact Person: Dr. Amira binti Jamil (+60102540101)</li></ul><p><span style=\"font-weight:700\">Don\'t miss this opportunity to:</span></p><ul><li>Gain insights into strategic financial management and financial stability.</li><li>Learn about current industry trends and valuable taxation tips.</li><li>Network with other finance professionals.</li><li>Submit your resumes and CV for internships or job placements</li><li>Chance to win Lucky Draw prizes !</li></ul><p><span style=\"font-weight:700\">Register today and take the first step towards a successful career in finance!</span></p><p><span style=\"font-weight:700\">Organized by:</span></p><p>THE COMMITTEE\nAIFIF</p></div></div></div>', '', '(Please upload the proof payment here, in PDF ONLY)', 0, '2024-06-12', '2024-07-17', 2),
(5, 'REVOLUTIONIZING IDEAS AND STARTUP EXCELLENCE (RISE)', 'RISE', 0, '<div><span>Revolutionizing Ideas and Startup Excellence (RISE)</span>&nbsp;will be held on 10th June 2024 until 13th June 2024 at UMK City Campus.\n<div><br></div>\n<div>The registration fee for this competition are:</div><div>i. RM 10 / group participant</div><div>ii.&nbsp;RM5 / individual participant.&nbsp;</div><div><br></div><div>Kindly make the payment latest by&nbsp;<b>30th May 2024</b>&nbsp;via:</div><div><b>Account Holder: Universiti Malaysia Kelantan</b></div><div><b>Account Number: 553038019271</b></div><div><b>Bank: Maybank Berhad</b></div><div><b>Reference: RISE</b><b>&nbsp;ICREATE</b></div><div><br></div><div>Please note that this registration fee in&nbsp;<b>not refundable.&nbsp;</b></div><div>The method of payment is&nbsp;<b>online transfer&nbsp;</b>only.&nbsp;</div><div><br></div><div>For any inquiries regarding the registration, please do not hesitate to email us at icreate.fkp@umk.edu.my or directly contact your respective lecturer.&nbsp;</div><div><br></div><div>Thank you.</div><div><br></div><div>Regards,</div><div><b>THE COMMITTEE&nbsp;</b></div><div><b>RISE</b></div></div>', '', '(Please upload the proof payment here, in PDF ONLY)', 1, '2024-06-11', '2024-06-12', 1),
(6, 'THE JOM FRANCHISE EXHIBITION DAY (JFED)', 'JFED', 0, '<div>Jom Franchise Exhibition Poster will be held from 9th June 2024 until 10th June 2024 at UMK City Campus. Students are required to present the Franchise Plan project during the exhibition. There are 6 awards/achievements available:<div>a. Gold</div><div>b. Silver</div><div>c. Bronze</div><div>d. Best Franchise Poster Award</div><div>e. Best Franchise Pitching Award</div><div>f. Best Franchise Business Idea Award</div><div><br><div><br></div><div>For any inquiries regarding the registration, please do not hesitate to email us at icreate.fkp@umk.edu.my or directly contact your respective lecturer.&nbsp;</div><div><br></div><div>Thank you.</div><div><br></div><div>Regards,</div><div><strong>THE COMMITTEE&nbsp;</strong></div><div><strong>JFED</strong></div></div></div>', '', '(Please upload the proof payment here, in PDF ONLY)', 0, '2024-06-10', '2024-06-10', 1),
(7, 'I-CREATE International STEMpreneur & EduTech Innovation Challenge 2026 (IISEIC)', 'IISEIC', 1, '<h2>About IISEIC 2026</h2>\r\n<p>IISEIC 2026 is an international innovation competition open to students, educators, professionals, and global participants to showcase transformative ideas that shape the future of learning, technology, business, and sustainability.</p>', '', NULL, 0, '2026-05-09', '2026-05-13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `program_achievement`
--

CREATE TABLE `program_achievement` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `program_sub` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_achievement`
--

INSERT INTO `program_achievement` (`id`, `program_id`, `program_sub`, `name`) VALUES
(1, 2, 1, 'Best E-Commerce Award'),
(2, 2, 1, 'Most Social Media Creative Award'),
(3, 2, 1, 'Corporate Business Award'),
(4, 2, 1, 'Excellence E-Preneur Award'),
(5, 2, 2, 'Best Business Pitching Award'),
(6, 2, 5, 'Best Takaful Laureate Award'),
(7, 2, 6, 'The TaxPro Champion Award'),
(8, 2, 6, 'The Stellar Performance Award'),
(9, 2, 6, 'The Think Tank Trophy'),
(10, 2, 4, 'Most Viable Student Venture Award'),
(11, 2, 7, 'Best Poster Award'),
(12, 1, NULL, 'Best Community Project Ideation'),
(13, 1, NULL, 'Best Community Project Implementation'),
(14, 1, NULL, 'Best Community Project Presentation'),
(15, 5, NULL, 'Best Project Ideation'),
(16, 5, NULL, 'Best Product/Service Offered'),
(17, 5, NULL, 'Best Presenter Award (Ideation)'),
(18, 5, NULL, 'Best Presenter Award (Product/Service)'),
(19, 5, NULL, 'Best Young Entrepreneur'),
(20, 6, NULL, 'Best Franchise Business Pitching'),
(21, 6, NULL, 'Best Franchise Poster Award'),
(22, 6, NULL, 'Best Franchise Business Idea'),
(23, 6, NULL, 'Best Franchise Innovation Award'),
(24, 3, NULL, 'The Most Attractive Booth'),
(25, 3, NULL, 'The Most Innovative Product'),
(26, 3, NULL, 'The Best Promotional Strategies'),
(27, 2, 3, 'Best Video Marketing Award');

-- --------------------------------------------------------

--
-- Table structure for table `program_method`
--

CREATE TABLE `program_method` (
  `id` int(11) NOT NULL,
  `method_name` varchar(255) NOT NULL,
  `program_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_reg`
--

CREATE TABLE `program_reg` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `program_sub` int(11) DEFAULT NULL COMMENT 'categori/lecturer',
  `project_name` varchar(255) DEFAULT NULL,
  `participant_cat_program` int(11) DEFAULT NULL,
  `competition_cat_program` int(11) DEFAULT NULL,
  `nric` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `group_code` varchar(100) DEFAULT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `booth_number` varchar(100) DEFAULT NULL,
  `participant_cat_local` tinyint(1) DEFAULT 1 COMMENT '1=local,2=int',
  `participant_cat_group` tinyint(1) DEFAULT NULL COMMENT '1=local,2 international',
  `participant_cat_umk` tinyint(1) DEFAULT NULL COMMENT '1=umk 2 non umk',
  `participant_mode` tinyint(1) DEFAULT NULL COMMENT '1=physical 2 online',
  `participant_program` int(11) DEFAULT NULL,
  `other_program` varchar(255) DEFAULT NULL,
  `advisor` varchar(255) DEFAULT NULL,
  `advisor_dropdown` int(11) DEFAULT NULL,
  `institution` text DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_no` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) NOT NULL,
  `edit_password_hash` varchar(255) DEFAULT NULL,
  `project_desc` text DEFAULT NULL,
  `competition_type` tinyint(1) DEFAULT NULL COMMENT '1=ideation, 2 implementation',
  `poster_file` text DEFAULT NULL,
  `abstract_file` text DEFAULT NULL,
  `payment_file` text DEFAULT NULL,
  `score` decimal(11,2) DEFAULT NULL,
  `award` tinyint(4) DEFAULT NULL,
  `flag` tinyint(1) DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `video_link` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_reg`
--

INSERT INTO `program_reg` (`id`, `user_id`, `program_id`, `program_sub`, `project_name`, `participant_cat_program`, `competition_cat_program`, `nric`, `status`, `group_code`, `group_name`, `booth_number`, `participant_cat_local`, `participant_cat_group`, `participant_cat_umk`, `participant_mode`, `participant_program`, `other_program`, `advisor`, `advisor_dropdown`, `institution`, `contact_person`, `contact_no`, `contact_email`, `edit_password_hash`, `project_desc`, `competition_type`, `poster_file`, `abstract_file`, `payment_file`, `score`, `award`, `flag`, `created_at`, `updated_at`, `submitted_at`, `video_link`) VALUES
(867, 336, 7, NULL, 'asdf', 2, 3, NULL, 10, NULL, NULL, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'aasd', 'Ahmad Zarif', 'asd', 'zul@umk.edu.my', '$2y$13$JO1e7tOOMLkV5pA10bsGWuqOeP9oUZF2vT718EO52yQWuCMlW90Ji', NULL, NULL, NULL, '7/abstract/zul_umk_edu_my_1775325576.docx', '7/payment/zul_umk_edu_my_1775325576.pdf', NULL, NULL, 0, 1775325576, 1775326747, '2026-04-05 01:59:36', '');

-- --------------------------------------------------------

--
-- Table structure for table `program_reg_achieve`
--

CREATE TABLE `program_reg_achieve` (
  `id` int(11) NOT NULL,
  `program_reg_id` int(11) NOT NULL,
  `achieve_id` int(11) DEFAULT NULL,
  `achieved_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_reg_field`
--

CREATE TABLE `program_reg_field` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `field_name` varchar(64) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `layout_width` tinyint(2) NOT NULL DEFAULT 12,
  `show_matric` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_reg_field`
--

INSERT INTO `program_reg_field` (`id`, `program_id`, `field_name`, `is_enabled`, `is_required`, `layout_width`, `show_matric`, `sort_order`) VALUES
(1, 7, 'participant_mode', 1, 1, 12, 1, 10),
(2, 7, 'participant_cat_program', 1, 0, 6, 1, 20),
(3, 7, 'competition_cat_program', 1, 0, 6, 1, 30),
(4, 7, 'group_member', 1, 1, 12, 0, 40),
(5, 7, 'institution', 1, 1, 6, 1, 50),
(6, 1, 'project_name', 0, 0, 12, 1, 0),
(7, 1, 'project_desc', 0, 0, 12, 1, 0),
(8, 1, 'participant_cat_local', 0, 0, 12, 1, 0),
(9, 1, 'participant_cat_group', 0, 0, 12, 1, 0),
(10, 1, 'participant_mode', 0, 0, 12, 1, 0),
(11, 1, 'participant_cat_umk', 0, 0, 12, 1, 0),
(12, 1, 'participant_program', 0, 0, 12, 1, 0),
(13, 1, 'other_program', 0, 0, 12, 1, 0),
(14, 1, 'program_sub', 0, 0, 12, 1, 0),
(15, 1, 'advisor_dropdown', 0, 0, 12, 1, 0),
(16, 1, 'booth_number', 0, 0, 12, 1, 0),
(17, 1, 'advisor', 0, 0, 12, 1, 0),
(18, 1, 'institution', 0, 0, 12, 1, 0),
(19, 1, 'group_member', 0, 0, 12, 0, 0),
(20, 1, 'group_code', 0, 0, 12, 1, 0),
(21, 1, 'group_name', 0, 0, 12, 1, 0),
(22, 1, 'mentor_main', 0, 0, 12, 1, 0),
(23, 1, 'mentor_co', 0, 0, 12, 1, 0),
(24, 1, 'poster_file', 0, 0, 12, 1, 0),
(25, 1, 'payment_file', 0, 0, 12, 1, 0),
(26, 1, 'nric', 0, 0, 12, 1, 0),
(27, 1, 'competition_type', 0, 0, 12, 1, 0),
(28, 1, 'participant_cat_program', 0, 0, 12, 1, 0),
(29, 1, 'competition_cat_program', 0, 0, 12, 1, 0),
(30, 7, 'contact_person', 1, 1, 6, 1, 55),
(31, 7, 'contact_no', 1, 1, 6, 1, 56),
(32, 7, 'contact_email', 1, 1, 6, 1, 57),
(33, 7, 'project_name', 1, 1, 12, 1, 5),
(34, 7, 'abstract_file', 1, 1, 12, 1, 60),
(35, 7, 'poster_file', 1, 1, 12, 1, 70),
(36, 7, 'video_link', 1, 0, 12, 1, 80),
(37, 7, 'payment_file', 1, 1, 12, 1, 90);

-- --------------------------------------------------------

--
-- Table structure for table `program_reg_jury`
--

CREATE TABLE `program_reg_jury` (
  `id` int(11) NOT NULL,
  `reg_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) DEFAULT 0,
  `score` decimal(11,2) DEFAULT 0.00,
  `stage` int(11) DEFAULT NULL,
  `method` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `rubric_id` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `link` text DEFAULT NULL,
  `is_nullified` tinyint(1) NOT NULL DEFAULT 0,
  `reason_nullified` text DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_reg_member`
--

CREATE TABLE `program_reg_member` (
  `id` int(11) NOT NULL,
  `program_reg_id` int(11) NOT NULL,
  `member_name` varchar(255) DEFAULT NULL,
  `member_matric` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_reg_member`
--

INSERT INTO `program_reg_member` (`id`, `program_reg_id`, `member_name`, `member_matric`) VALUES
(4042, 867, 'ZUL@UMK.EDU.MY', NULL),
(4043, 867, '776U67868U', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `program_reg_mentor`
--

CREATE TABLE `program_reg_mentor` (
  `id` int(11) NOT NULL,
  `program_reg_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_rubric`
--

CREATE TABLE `program_rubric` (
  `id` int(11) NOT NULL,
  `rubric_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `program_sub` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_rubric`
--

INSERT INTO `program_rubric` (`id`, `rubric_id`, `program_id`, `program_sub`) VALUES
(1, 1, 2, 1),
(3, 3, 1, NULL),
(4, 4, 6, NULL),
(5, 5, 3, NULL),
(6, 6, 2, 3),
(7, 7, 2, 2),
(8, 8, 2, 5),
(9, 9, 2, 4),
(10, 10, 2, 7),
(11, 11, 2, 6),
(12, 12, 5, 8),
(13, 13, 5, 9);

-- --------------------------------------------------------

--
-- Table structure for table `program_stage`
--

CREATE TABLE `program_stage` (
  `id` int(11) NOT NULL,
  `stage_name` varchar(255) NOT NULL,
  `program_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_sub`
--

CREATE TABLE `program_sub` (
  `id` int(11) NOT NULL,
  `sub_name` varchar(255) NOT NULL,
  `sub_abbr` varchar(100) DEFAULT NULL,
  `advisor` varchar(255) DEFAULT NULL,
  `program_id` int(11) NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_sub`
--

INSERT INTO `program_sub` (`id`, `sub_name`, `sub_abbr`, `advisor`, `program_id`, `date_start`, `date_end`) VALUES
(1, 'E-Preneur', 'E-Preneur', 'Dr. Fatihah Mohd & Dr. Yusmazida', 2, NULL, NULL),
(2, 'Business Idea Pitching', 'Business Pitching', 'Dr. Noor Raihani Binti Zainol', 2, NULL, NULL),
(3, 'Creative Product Marketing Videos', 'Creative Video', 'Dr. Azira Hanani Binti Ab Rahman', 2, '2024-06-09', '2024-06-09'),
(4, 'Competitive Student Venture', 'Student Venture', 'Dr. Wan Farha Binti Wan Zulkiffli', 2, NULL, NULL),
(5, 'Takaful Product Innovation', 'Takaful', 'Mrs. Farah Hanan Binti Muhamad', 2, NULL, NULL),
(6, 'TaxPro Challenge', 'TaxPro', 'Dr. Amira Binti Jamil', 2, NULL, NULL),
(7, 'Poster Presentation', 'Poster', 'Dr. Siti Fariha Binti Muhamad', 2, NULL, NULL),
(8, 'Operasi Teroka Baru', 'OTB', '', 5, '2024-06-11', '2024-06-12'),
(9, 'Pembentukan Teroka Baru', 'PTB', '', 5, '2024-06-11', '2024-06-12');

-- --------------------------------------------------------

--
-- Table structure for table `questionnaire`
--

CREATE TABLE `questionnaire` (
  `id` int(11) NOT NULL,
  `pre_post` tinyint(1) NOT NULL COMMENT '1=pre 2 post',
  `question_number` int(11) DEFAULT NULL COMMENT 'correspond to answer colum table, may or in order',
  `question_text` text NOT NULL,
  `question_type` tinyint(4) NOT NULL COMMENT '1=,likert,2=open,3=checkbox',
  `question_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questionnaire`
--

INSERT INTO `questionnaire` (`id`, `pre_post`, `question_number`, `question_text`, `question_type`, `question_order`) VALUES
(1, 1, 1, 'How confident do you feel to face challenges as an entrepreneur?', 1, 1),
(2, 1, 2, 'How interested are you in running your business before attending this program?', 1, 2),
(3, 1, 3, 'How confident are you that owning a business was a good career choice before attending this program?', 1, 3),
(4, 1, 4, 'What do you want to learn or achieve in this entrepreneurship event?', 3, 4),
(5, 1, 5, 'What is your biggest challenge in starting your business or entrepreneurial project?', 3, 5),
(6, 2, 1, 'How confident are you in your ability to face challenges in entrepreneurship after following this program?', 1, 1),
(7, 2, 2, 'To what extent do you feel the support and guidance you received after attending this program was sufficient to start a business?How interested were you in running your own business after attending this program?', 1, 2),
(8, 2, 3, 'How confident were you that owning your own business was a good career choice after following this program?', 1, 3),
(9, 2, 4, 'What important knowledge or insight did you gain from this entrepreneurial event?', 3, 4),
(10, 2, 5, 'What are your next steps after attending this entrepreneurship event?', 3, 5);

-- --------------------------------------------------------

--
-- Table structure for table `questionnaire_ans`
--

CREATE TABLE `questionnaire_ans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `q1` int(11) DEFAULT NULL,
  `q2` int(11) DEFAULT NULL,
  `q3` int(11) DEFAULT NULL,
  `q4` int(11) DEFAULT NULL,
  `q5` int(11) DEFAULT NULL,
  `q6` int(11) DEFAULT NULL,
  `q7` text DEFAULT NULL,
  `q8` text DEFAULT NULL,
  `q9` text DEFAULT NULL,
  `sub1` int(11) DEFAULT NULL,
  `sub2` int(11) DEFAULT NULL,
  `sub3` int(11) DEFAULT NULL,
  `sub4` int(11) DEFAULT NULL,
  `sub5` int(11) DEFAULT NULL,
  `sub6` int(11) DEFAULT NULL,
  `sub7` int(11) DEFAULT NULL,
  `sub8` int(11) DEFAULT NULL,
  `sub9` int(11) DEFAULT NULL,
  `sub10` int(11) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questionnaire_ans`
--

INSERT INTO `questionnaire_ans` (`id`, `user_id`, `q1`, `q2`, `q3`, `q4`, `q5`, `q6`, `q7`, `q8`, `q9`, `sub1`, `sub2`, `sub3`, `sub4`, `sub5`, `sub6`, `sub7`, `sub8`, `sub9`, `sub10`, `submitted_at`) VALUES
(29, 336, 4, 4, 4, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, 0, 1, 0, 0, NULL, NULL, '2024-06-06 13:58:26'),
(846, 2203, 5, 5, 5, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, 0, 1, 0, 0, NULL, NULL, '2026-03-11 13:55:25'),
(847, 2204, 4, 5, 5, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, 0, 1, 0, 0, NULL, NULL, '2026-03-11 13:56:39');

-- --------------------------------------------------------

--
-- Table structure for table `questionnaire_ans_post`
--

CREATE TABLE `questionnaire_ans_post` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `q1` int(11) DEFAULT NULL,
  `q2` int(11) DEFAULT NULL,
  `q3` int(11) DEFAULT NULL,
  `q4` int(11) DEFAULT NULL,
  `q5` int(11) DEFAULT NULL,
  `q6` text DEFAULT NULL,
  `q7` text DEFAULT NULL,
  `q8` int(11) DEFAULT NULL,
  `q9` int(11) DEFAULT NULL,
  `q10` int(11) DEFAULT NULL,
  `q11` text DEFAULT NULL,
  `sub1` int(11) DEFAULT NULL,
  `sub2` int(11) DEFAULT NULL,
  `sub3` int(11) DEFAULT NULL,
  `sub4` int(11) DEFAULT NULL,
  `sub5` int(11) DEFAULT NULL,
  `sub6` int(11) DEFAULT NULL,
  `sub7` int(11) DEFAULT NULL,
  `sub8` int(11) DEFAULT NULL,
  `sub9` int(11) DEFAULT NULL,
  `sub10` int(11) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questionnaire_sub`
--

CREATE TABLE `questionnaire_sub` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `question_text` varchar(255) DEFAULT NULL,
  `answer_colum` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questionnaire_sub`
--

INSERT INTO `questionnaire_sub` (`id`, `question_id`, `question_text`, `answer_colum`) VALUES
(1, 4, 'Expand My Network', 'sub1'),
(2, 4, 'Gain Insights from Experts', 'sub2'),
(3, 4, 'Understand Market Trends', 'sub3'),
(4, 4, 'Develop an Entrepreneurial Mindset', 'sub4'),
(5, 5, 'Securing Funding', 'sub5'),
(6, 5, 'Market Research and Understanding', 'sub6'),
(7, 5, 'Marketing and Customer Acquisition', 'sub7'),
(8, 5, 'Managing Finances', 'sub8'),
(9, 9, 'Networking and Relationships', 'sub1'),
(10, 9, 'Real-world Experiences and Lessons\r\nLatest Trends and Innovations\r\nBusiness Strategy and Planning', 'sub2'),
(12, 9, 'Latest Trends and Innovations', 'sub3'),
(13, 9, 'Business Strategy and Planning', 'sub4'),
(14, 10, 'Review and reflect', 'sub5'),
(15, 10, 'Update Your Business Plan', 'sub6'),
(16, 10, 'Implement New Strategies', 'sub7'),
(17, 10, 'Set New Goals', 'sub8');

-- --------------------------------------------------------

--
-- Table structure for table `rubric`
--

CREATE TABLE `rubric` (
  `id` int(11) NOT NULL,
  `rubric_name` varchar(255) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rubric`
--

INSERT INTO `rubric` (`id`, `rubric_name`, `created_at`, `updated_at`) VALUES
(1, 'E-PRENEUR  JUDGING RUBRIC', NULL, NULL),
(2, 'IMPACT RUBRIC STAGE 1', NULL, NULL),
(3, 'IMPACT RUBRIC FINAL STAGE', NULL, NULL),
(4, 'RUBRIC FOR JUDGES JFED 2024', NULL, NULL),
(5, 'JUDGING FORM FOR NeWEEK FEBRUARY 2023/2024', NULL, NULL),
(6, 'JUDGING FORM FOR CREATIVE VIDEO (COMEI)', NULL, NULL),
(7, 'Rubric Business Product Pitching', NULL, NULL),
(8, 'TAKAFUL INNOVATION  RUBRIC JUDGING', NULL, NULL),
(9, 'Rubric Competitive Student Venture', NULL, NULL),
(10, 'MyMFRS POSTER PRESENTATION JUDGING RUBRIC', NULL, NULL),
(11, 'EVALUATION TAXPRO CHALLENGE', NULL, NULL),
(12, 'Entrepreneurial Business Project Judging Rubric (OTB)', NULL, NULL),
(13, 'Entrepreneurial Project Ideation Judging Rubric (PTB)', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rubric_answer`
--

CREATE TABLE `rubric_answer` (
  `id` int(11) NOT NULL,
  `rubric_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `item_no1` int(11) DEFAULT NULL,
  `item_no2` int(11) DEFAULT NULL,
  `item_no3` int(11) DEFAULT NULL,
  `item_no4` int(11) DEFAULT NULL,
  `item_no5` int(11) DEFAULT NULL,
  `item_no6` int(11) DEFAULT NULL,
  `item_no7` int(11) DEFAULT NULL,
  `item_no8` int(11) DEFAULT NULL,
  `item_no9` int(11) DEFAULT NULL,
  `item_no10` int(11) DEFAULT NULL,
  `item_no11` int(11) DEFAULT NULL,
  `item_no12` int(11) DEFAULT NULL,
  `item_no13` int(11) DEFAULT NULL,
  `item_no14` int(11) DEFAULT NULL,
  `item_no15` int(11) DEFAULT NULL,
  `item_no16` int(11) DEFAULT NULL,
  `item_no17` int(11) DEFAULT NULL,
  `item_no18` int(11) DEFAULT NULL,
  `item_no19` int(11) DEFAULT NULL,
  `item_no20` int(11) DEFAULT NULL,
  `item_no21` int(11) DEFAULT NULL,
  `item_no22` int(11) DEFAULT NULL,
  `item_no23` int(11) DEFAULT NULL,
  `item_no24` int(11) DEFAULT NULL,
  `item_no25` int(11) DEFAULT NULL,
  `item_no26` int(11) DEFAULT NULL,
  `item_no27` int(11) DEFAULT NULL,
  `item_no28` int(11) DEFAULT NULL,
  `item_no29` int(11) DEFAULT NULL,
  `item_no30` int(11) DEFAULT NULL,
  `item_text1` text DEFAULT NULL,
  `item_text2` text DEFAULT NULL,
  `item_text3` text DEFAULT NULL,
  `item_text4` text DEFAULT NULL,
  `item_text5` text DEFAULT NULL,
  `item_text6` text DEFAULT NULL,
  `item_text7` text DEFAULT NULL,
  `item_text8` text DEFAULT NULL,
  `item_text9` text DEFAULT NULL,
  `item_text10` text DEFAULT NULL,
  `text_no1` text DEFAULT NULL,
  `text_no2` text DEFAULT NULL,
  `text_no3` text DEFAULT NULL,
  `text_no4` text DEFAULT NULL,
  `text_no5` text DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rubric_answer`
--

INSERT INTO `rubric_answer` (`id`, `rubric_id`, `assignment_id`, `item_no1`, `item_no2`, `item_no3`, `item_no4`, `item_no5`, `item_no6`, `item_no7`, `item_no8`, `item_no9`, `item_no10`, `item_no11`, `item_no12`, `item_no13`, `item_no14`, `item_no15`, `item_no16`, `item_no17`, `item_no18`, `item_no19`, `item_no20`, `item_no21`, `item_no22`, `item_no23`, `item_no24`, `item_no25`, `item_no26`, `item_no27`, `item_no28`, `item_no29`, `item_no30`, `item_text1`, `item_text2`, `item_text3`, `item_text4`, `item_text5`, `item_text6`, `item_text7`, `item_text8`, `item_text9`, `item_text10`, `text_no1`, `text_no2`, `text_no3`, `text_no4`, `text_no5`, `updated_at`, `created_at`, `submitted_at`) VALUES
(50, 11, 28, 8, 9, 8, 8, 8, 9, 9, 8, 9, 10, 2, 2, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-11 16:50:38', '2024-06-10 10:02:57', '2024-06-11 16:50:38'),
(472, 11, 29, 8, 8, 9, 8, 7, 8, 9, 7, 9, 8, 2, 2, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-11 16:51:59', '2024-06-11 14:40:37', '2024-06-11 16:51:59');

-- --------------------------------------------------------

--
-- Table structure for table `rubric_category`
--

CREATE TABLE `rubric_category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `is_recommend` tinyint(1) DEFAULT 0,
  `rubric_id` int(11) NOT NULL,
  `cat_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rubric_category`
--

INSERT INTO `rubric_category` (`id`, `category_name`, `is_recommend`, `rubric_id`, `cat_order`) VALUES
(3, 'CRITERIA BUSINESS PORTAL', 0, 1, 1),
(4, 'CRITERIA ON SOCIAL MEDIA', 0, 1, 3),
(5, 'CRITERIA ON COMPANY PROFILE', 0, 1, 5),
(6, 'RECOMMENDATION', 1, 1, 6),
(7, 'PROJECT BACKGROUND', 0, 2, NULL),
(8, 'PROJECT BACKGROUND', 0, 3, NULL),
(9, 'PROJECT FEASIBILITY', 0, 2, NULL),
(10, 'PROJECT FEASIBILITY', 0, 3, NULL),
(11, 'INNOVATION AND CREATIVITY', 0, 2, NULL),
(12, 'INNOVATION AND CREATIVITY', 0, 3, NULL),
(13, 'Engagement And Collaboration', 0, 2, NULL),
(14, 'Engagement And Collaboration', 0, 3, NULL),
(15, 'Impact and Benefit', 0, 2, NULL),
(16, 'Impact and Benefit', 0, 3, NULL),
(17, 'Project Viability', 0, 2, NULL),
(18, 'Project Viability', 0, 3, NULL),
(19, 'PRESENTATION', 0, 3, NULL),
(21, 'RECOMMENDATION', 1, 2, NULL),
(22, 'RECOMMENDATION', 1, 3, NULL),
(23, 'FRANCHISE PLAN PITCHING', 0, 4, 1),
(24, 'POSTER EVALUATION', 0, 4, 3),
(25, 'RECOMMENDATION', 1, 4, 2),
(26, 'SECTION A: BUSINESS ATTRACTIVENESS', 0, 5, 1),
(27, 'SECTION B: INNOVATIVE PRODUCTS', 0, 5, 3),
(28, 'SECTION C: MARKETING STRATEGIES', 0, 5, 5),
(29, 'RECOMMENDATION (A)', 1, 5, 2),
(30, 'RECOMMENDATION (B)', 1, 5, 4),
(31, 'RECOMMENDATION (C)', 1, 5, 6),
(32, 'RECOMMENDATION', 1, 4, 3),
(33, 'Assessment', 0, 6, NULL),
(34, 'Recommendation', 1, 6, NULL),
(35, 'Recommendation (ecommerce)', 1, 1, 2),
(36, 'Recommendation (Social media)', 1, 1, 4),
(40, 'PRESENTATION: SPEAKING SKILLS ', 0, 7, NULL),
(41, 'PRESENTATION: CONTENTS', 0, 7, NULL),
(42, 'PRESENTATION: OTHER CRITERIA', 0, 7, NULL),
(43, 'BMC: Use of Business Model Canvas Process', 0, 7, NULL),
(44, 'BMC: Testing, Validation, and Customer Interaction', 0, 7, NULL),
(45, 'BMC: Student/Team Business Model Judgment', 0, 7, NULL),
(46, 'BMC: In The Judges\' Expert Opinion', 0, 7, NULL),
(47, 'Assessment', 0, 8, NULL),
(48, 'Recommendation', 1, 8, NULL),
(49, 'Competitive Student Venture', 0, 9, NULL),
(50, 'Presentation', 0, 9, NULL),
(51, 'Recommendation', 1, 9, NULL),
(52, 'Poster Presentation', 0, 10, NULL),
(53, 'Recommendation', 1, 10, NULL),
(54, 'Report', 0, 11, NULL),
(55, 'Presentation', 0, 11, NULL),
(56, 'Recommendation', 1, 11, NULL),
(57, 'Infographic Report', 0, 10, NULL),
(58, 'Recommendation', 1, 10, NULL),
(59, 'Recommendation', 1, 7, NULL),
(60, 'A. CRITERIA FOR PRODUCT/SERVICE', 0, 12, 1),
(61, 'B. PITCHING', 0, 12, 3),
(62, 'Recommendation (Best Product/Service)', 1, 12, 2),
(63, 'Recommendation (Best Presenter)', 1, 12, 4),
(64, 'OVERALL POTENTIAL', 0, 12, 5),
(65, 'Recommendation (Best Young Entrepreneur)', 1, 12, 6),
(66, 'A. CRITERIA FOR PRODUCT/SERVICE IDEATION', 0, 13, NULL),
(67, 'Recommendation (Best Ideation)', 1, 13, NULL),
(68, 'B. PRESENTER', 0, 13, NULL),
(69, 'Recommendation (Best Presenter)', 1, 13, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rubric_item`
--

CREATE TABLE `rubric_item` (
  `id` int(11) NOT NULL,
  `item_text` text NOT NULL,
  `item_description` text DEFAULT NULL,
  `item_short` varchar(100) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `item_type` int(11) DEFAULT NULL,
  `option_number` int(11) DEFAULT NULL,
  `item_order` int(11) DEFAULT NULL,
  `colum_ans` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rubric_item`
--

INSERT INTO `rubric_item` (`id`, `item_text`, `item_description`, `item_short`, `category_id`, `item_type`, `option_number`, `item_order`, `colum_ans`) VALUES
(1, 'Ability to present the business as a ecommerce platform.', 'Able to utilize website application and online platform to present online business site.', NULL, 3, 1, 10, NULL, 'item_no1'),
(2, 'Ability to present the product as e-catalogue.', 'Products are presented in a logical, rational manner.  The online catalog is very organized and user-friendly.', NULL, 3, 1, 10, NULL, 'item_no2'),
(3, 'Website Design.', 'Page contains all necessary navigational tools and buttons. Users can progress intuitively through screens in a logical path to find information.', NULL, 3, 1, 10, NULL, 'item_no3'),
(4, 'Social Media Visibility', 'Well actively updating social status either post text, picture or video.', NULL, 4, 1, 10, NULL, 'item_no4'),
(5, 'Followers', 'The number of followers is achieved to at least minimum 200 followers.', NULL, 4, 1, 10, NULL, 'item_no5'),
(6, 'Marketing and Advertisement.', 'The social media brand pages are well complete as a marketing platform.', NULL, 4, 1, 10, NULL, 'item_no6'),
(7, 'Content', 'Well presented the business information.', NULL, 5, 1, 10, NULL, 'item_no7'),
(8, 'Creativity', 'Well attractive picture, application, video, and graphic.', NULL, 5, 1, 10, NULL, 'item_no8'),
(9, 'Video Business Corporate', 'Effectively delivered the business message', NULL, 5, 1, 10, NULL, 'item_no9'),
(10, 'Recommend for  Corporate Business Award', '', 'Corporate Business Award', 6, 2, 2, NULL, 'item_no10'),
(11, 'Clear and realistic project objectives', '', NULL, 7, 1, 10, NULL, 'item_no1'),
(12, 'Clear and realistic project objectives', '', NULL, 8, 1, 10, NULL, 'item_no1'),
(13, 'Feasible timeline and budget', '', NULL, 9, 1, 10, NULL, 'item_no2'),
(14, 'Feasible timeline and budget', '', NULL, 10, 1, 10, NULL, 'item_no2'),
(15, 'Demonstrated capacity (to) execute the project effectively', '', NULL, 9, 1, 10, NULL, 'item_no3'),
(16, 'Demonstrated capacity (to) execute the project effectively', NULL, NULL, 10, 1, 10, NULL, 'item_no3'),
(17, 'Originality and creativity of the project', '', NULL, 11, 1, 10, NULL, 'item_no4'),
(18, 'Originality and creativity of the project', '', NULL, 12, 1, 10, NULL, 'item_no4'),
(19, 'Innovative approaches to addressing community needs or challenges', '', NULL, 11, 1, 10, NULL, 'item_no5'),
(20, 'Innovative approaches to addressing community needs or challenges', '', NULL, 12, 1, 10, NULL, 'item_no5'),
(21, 'Potential for the project to inspire others or serve as a model for similar initiatives', '', NULL, 11, 1, 10, NULL, 'item_no6'),
(22, 'Potential for the project to inspire others or serve as a model for similar initiatives', '', NULL, 12, 1, 10, NULL, 'item_no6'),
(23, 'Incorporation of unique or unconventional solutions', '', NULL, 11, 1, 10, NULL, 'item_no7'),
(24, 'Incorporation of unique or unconventional solutions', '', NULL, 12, 1, 10, NULL, 'item_no7'),
(25, 'Evidence (potential) of collaboration with community stakeholders, organizations, or local authorities', '', NULL, 13, 1, 10, NULL, 'item_no8'),
(26, 'Evidence (potential) of collaboration with community stakeholders, organizations, or local authorities', '', NULL, 14, 1, 10, NULL, 'item_no8'),
(27, 'Demonstrated (potential) efforts involve and empower community members throughout the project', '', NULL, 13, 1, 10, NULL, 'item_no9'),
(28, 'Demonstrated (potential) efforts involve and empower community members throughout the project', '', NULL, 14, 1, 10, NULL, 'item_no9'),
(29, 'Incorporation of feedback from community members in project design and implementation', '', NULL, 13, 1, 10, NULL, 'item_no10'),
(30, 'Incorporation of feedback from community members in project design and implementation', '', NULL, 14, 1, 10, NULL, 'item_no10'),
(31, 'Evidence (potential) positive impact on the community (e.g., social, economic, environmental)', '', NULL, 15, 1, 10, NULL, 'item_no11'),
(32, 'Evidence (potential) positive impact on the community (e.g., social, economic, environmental)', '', NULL, 16, 1, 10, NULL, 'item_no11'),
(33, 'Clarity of intended outcomes and benefits for target beneficiaries', '', NULL, 15, 1, 10, NULL, 'item_no12'),
(34, 'Clarity of intended outcomes and benefits for target beneficiaries', '', NULL, 16, 1, 10, NULL, 'item_no12'),
(35, 'Potential to address an existing community need or problem effectively', '', NULL, 15, 1, 10, NULL, 'item_no13'),
(36, 'Potential to address an existing community need or problem effectively', '', NULL, 16, 1, 10, NULL, 'item_no13'),
(37, 'Consideration of sustainability factors (e.g., environmental impact, ongoing maintenance etc.)', '', NULL, 17, 1, 10, NULL, 'item_no14'),
(38, 'Consideration of sustainability factors (e.g., environmental impact, ongoing maintenance etc.)', '', NULL, 18, 1, 10, NULL, 'item_no14'),
(39, 'Demonstrate (Potential) for the project to generate long-term benefits or create lasting change', '', NULL, 17, 1, 10, NULL, 'item_no15'),
(40, 'Demonstrate (Potential) for the project to generate long-term benefits or create lasting change', '', NULL, 18, 1, 10, NULL, 'item_no15'),
(41, 'Plans for securing continued funding or support beyond the competition period', '', NULL, 17, 1, 10, NULL, 'item_no16'),
(42, 'Plans for securing continued funding or support beyond the competition period', '', NULL, 18, 1, 10, NULL, 'item_no16'),
(43, 'Integration of strategies to ensure the project\'s sustainability over time', '', NULL, 17, 1, 10, NULL, 'item_no17'),
(44, 'Integration of strategies to ensure the project\'s sustainability over time', '', NULL, 18, 1, 10, NULL, 'item_no17'),
(45, 'Clarity and professionalism of the project presentation', '', NULL, 19, 1, 10, NULL, 'item_no18'),
(46, 'Effectiveness of communication in conveying project objectives, strategies, and potential impact', '', NULL, 19, 1, 10, NULL, 'item_no19'),
(47, 'Ability to engage and inspire the audience', '', NULL, 19, 1, 10, NULL, 'item_no20'),
(48, 'Use visuals, multimedia, or other presentation aids to enhance understanding', '', NULL, 19, 1, 10, NULL, 'item_no21'),
(49, 'Is this project eligible to be awarded as the Best Project Ideation', '', 'Best Project Ideation', 21, 2, 10, NULL, 'item_no22'),
(50, 'Is this project eligible to be awarded as the Best Project Ideation', '', 'Best Project Ideation', 22, 2, 10, NULL, 'item_no22'),
(51, 'Is this project eligible to be awarded as the Best Project Implementation', '', 'Best Project Implementation', 21, 2, 10, NULL, 'item_no23'),
(52, 'Is this project eligible to be awarded as the Best Project Implementation', '', 'Best Project Implementation', 22, 2, 10, NULL, 'item_no23'),
(53, 'Is this project eligible to be awarded as the Best Project Presentation', '', 'Best Project Presentation', 21, 2, 10, NULL, 'item_no24'),
(54, 'Is this project eligible to be awarded as the Best Project Presentation', '', 'Best Project Presentation', 22, 2, 10, NULL, 'item_no24'),
(55, 'VISUAL APPEAL', 'The ability to create an eye-catching poster', NULL, 24, 1, 10, NULL, 'item_no1'),
(56, 'CLARITY', 'Explanation of these elements:\r\n1.  Company and product name\r\n2. Product description\r\n3. Justification or reasoning behind franchise package and product development\r\n4. Method of franchise plan development\r\n5. Novelty or originality or inventiveness\r\n6. Value of the Product\r\n7. Commercialisation or marketability', NULL, 24, 1, 10, NULL, 'item_no2'),
(57, 'INFOGRAPHIC', 'Convey information through combination of text and images about the product and franchise plan package', NULL, 24, 1, 10, NULL, 'item_no3'),
(58, 'CREATIVITY', 'The ability to generate original and innovative poster in terms of:\r\n1. Colour usage\r\n2. Font usage\r\n3. Arrangement of content', NULL, 24, 1, 10, NULL, 'item_no4'),
(59, 'IMPACT', 'The ability to convey messages through one poster even without explanation', NULL, 24, 1, 10, NULL, 'item_no5'),
(60, 'CLARITY', 'Explanation of these elements:\r\n1.  Company and product name\r\n2. Product description\r\n3. Justification or reasoning behind franchise package and product development\r\n4. Method of franchise plan development\r\n5. Novelty or originality or inventiveness\r\n6. Value of the Product\r\n7. Commercialisation or marketability', NULL, 23, 1, 10, NULL, 'item_no6'),
(61, 'FEASIBILITY', 'Explanation on:\r\n1. The way it works\r\n2. Financial viability\r\n3. Potential for scalability', NULL, 23, 1, 10, NULL, 'item_no7'),
(62, 'IMPACT', 'Description of product value and franchise plan package could make a positive impact towards audience', NULL, 23, 1, 10, NULL, 'item_no8'),
(63, 'PERSUASION', 'Considering:\r\n1. Professional or formal language used\r\n2. Convincing technique of pitching', NULL, 23, 1, 10, NULL, 'item_no9'),
(64, 'ADAPTABILITY', 'Response of students to product and franchise package queries and feedback', NULL, 23, 1, 10, NULL, 'item_no10'),
(65, 'DO YOU RECOMMEND THIS GROUP FOR BEST PITCHING AWARD?', '', 'BEST PITCHING AWARD', 25, 2, 2, NULL, 'item_no11'),
(66, 'DO YOU RECOMMEND THIS GROUP FOR BEST POSTER AWARD?', '', 'BEST POSTER AWARD', 32, 2, 2, NULL, 'item_no12'),
(67, 'Display Business Registration No. (SSU or SSM)', '', NULL, 26, 2, 5, NULL, 'item_no1'),
(68, 'The booth clean and tidy', '', NULL, 26, 1, 10, NULL, 'item_no2'),
(69, 'Variety of product display', '', NULL, 26, 1, 10, NULL, 'item_no3'),
(70, 'Well-designed product layout and operational flow', '', NULL, 26, 1, 10, NULL, 'item_no4'),
(71, 'Interactive booth (engagement with customers)', '', NULL, 26, 1, 10, NULL, 'item_no5'),
(72, 'Usefulness in solving problems \r\n', 'The product fulfil the needs/ wants of the target market', NULL, 27, 1, 10, NULL, 'item_no6'),
(73, 'Unique packaging', 'Materials used contribute to green environment', NULL, 27, 1, 10, NULL, 'item_no7'),
(74, 'Creativity of the product', 'The product exhibit originality features/ style from the existing product', NULL, 27, 1, 10, NULL, 'item_no8'),
(75, 'Price is clearly displayed', 'All prices for each product is well displayed', NULL, 28, 1, 10, NULL, 'item_no9'),
(76, 'Availability of any special deals', 'The group provide any coupons/ combo purchases/ discounts/ product giveaway/  etc. to the customers', NULL, 28, 1, 10, NULL, 'item_no10'),
(77, 'Availability of post-purchase experience', 'The group put an effort to build relationship with customers after purchase; platform to share feedback etc.', NULL, 28, 1, 10, NULL, 'item_no11'),
(78, '4. Availability of advertising activities', 'The group provide campaign/ awareness/ promotional of their product to the customers using variety of platforms; attractive banners/ posters via online/ offline, etc.', NULL, 28, 1, 10, NULL, 'item_no12'),
(79, 'Do you recommend this booth to be the most attractive booth?', NULL, 'most attractive booth', 29, 2, 2, NULL, 'item_no13'),
(80, 'State your reason(s) if any', NULL, NULL, 29, 3, 0, NULL, 'text_no1'),
(81, 'Do you recommend this product to be the most innovative product?', NULL, 'most innovative product', 30, 2, 2, NULL, 'item_no14'),
(82, 'State your reason(s) if any', NULL, NULL, 30, 3, 0, NULL, 'text_no2'),
(83, 'Do you recommend this group for the best marketing strategies?', NULL, 'best marketing strategies', 31, 2, 2, NULL, 'item_no15'),
(84, 'State your reason(s) if any', NULL, NULL, 31, 3, 0, NULL, 'text_no3'),
(85, 'State your reason(s) if any', '', NULL, 25, 3, 0, NULL, 'text_no1'),
(86, 'State your reason(s) if any', '', NULL, 32, 3, 0, NULL, 'text_no2'),
(87, 'CREATIVITY', 'How the creative idea is developed. The overall work inspires and surprises the viewers.', NULL, 33, 1, 10, NULL, 'item_no1'),
(88, 'CINEMATOGRAPHY', 'Shot composition and framing that demonstrate creativity and skill.', NULL, 33, 1, 10, NULL, 'item_no2'),
(89, 'STORYLINE', 'Ability to deliver ideas with great clarity.', NULL, 33, 1, 10, NULL, 'item_no3'),
(90, 'CONTENT', 'The narrative captivates the audience from start to finish.\r\n', NULL, 33, 1, 10, NULL, 'item_no4'),
(91, 'Do you recommend this group to be the Best Video Marketing Award?', '', 'Best Video Marketing Award', 34, 2, 2, NULL, 'item_no5'),
(92, 'Recommend for BEST E-COMMERCE AWARD', '', 'BEST E-COMMERCE AWARD', 35, 2, 2, NULL, 'item_no11'),
(93, 'Recommend for Most Social Media Creative Award', '', 'Most Social Media Creative Award', 36, 2, 2, NULL, 'item_no12'),
(94, 'Delivery', 'Presenter doesn’t rush, shows enthusiasm, avoids likes, ums, kind ofs, you knows, etc. Uses complete sentences.', NULL, 40, 1, 10, NULL, 'item_no1'),
(95, 'Eye Contact', 'Presenter keeps head up, does not read, and speaks to whole audience', NULL, 40, 1, 10, NULL, 'item_no2'),
(96, 'Posture', 'Presenter stands up straight, faces audience, and doesn’t fidget', NULL, 40, 1, 10, NULL, 'item_no3'),
(97, 'Volume', 'Presenter can be easily heard by all. No gum, etc.', NULL, 40, 1, 10, NULL, 'item_no4'),
(98, 'Introduction', 'Presentation begins with a clear focus', NULL, 41, 1, 10, NULL, 'item_no5'),
(99, 'Topic Development', 'a) Presentation is clearly organized. Material is logically sequenced, related to topic, and not repetitive.\r\n', NULL, 41, 1, 10, NULL, 'item_no6'),
(100, 'Topic Development', 'b) Presentation shows full grasp and understanding of the material.\r\n', NULL, 41, 1, 10, NULL, 'item_no7'),
(101, 'Conclusion ', 'a) Presentation highlights key ideas and concludes with a strong final statement.\r\n', NULL, 41, 1, 10, NULL, 'item_no8'),
(102, 'Conclusion ', 'b) Presenter fields questions easily.\r\n', NULL, 41, 1, 10, NULL, 'item_no9'),
(103, 'Visual Appeal', 'There are no errors in spelling, grammar and punctuation. Information is clear and concise on each slide. Visually appealing/engaging', NULL, 42, 1, 10, NULL, 'item_no10'),
(104, 'CREATIVITY', 'The participant clearly explored and expressed multiple ideas in a unique way', NULL, 47, 1, 10, NULL, 'item_no1'),
(105, 'ITERATION', 'The participant completes their product, having improved the design and/or aesthetic over time', NULL, 47, 1, 10, NULL, 'item_no2'),
(106, 'INITIATIVE', 'The participant encounters complications with a positive attitude and perseveres to problem-solve independently without needing to seeking assistance', NULL, 47, 1, 10, NULL, 'item_no3'),
(107, 'LEARNING SKILLS', 'The participant attempts multiple new avenue of learning for their project. They clearly demonstrate a synthesis of skills they did not have at the start of the project', NULL, 47, 1, 10, NULL, 'item_no4'),
(108, 'COMMUNITY SPIRIT', 'The participant shares their project and learning with an authentic community in a formal manner', NULL, 47, 1, 10, NULL, 'item_no5'),
(109, 'Recommendation for BEST TAKAFUL LAUREATE AWARD', '', 'BEST TAKAFUL LAUREATE AWARD', 48, 2, 2, NULL, 'item_no6'),
(110, 'Total Sale', 'Total sale made by the company was mentioned clearly and the details was disclosed accordingly.', NULL, 49, 1, 10, NULL, 'item_no1'),
(111, 'Marketing against sale\r\n', 'Description on marketing activity was attractive and convincing.', NULL, 49, 1, 10, NULL, 'item_no2'),
(112, 'Record keeping', 'Software or system used to record daily cash in and cash out was table convenience and systematically. ', NULL, 49, 1, 10, NULL, 'item_no3'),
(113, 'Business operation', 'Description on identifying supplier/getting capital/customer relationship management/risk management are well prepared. ', NULL, 49, 1, 10, NULL, 'item_no4'),
(114, 'Financial aid', 'Details of the financial sources and reasons to use the sources was justify acceptably.', NULL, 49, 1, 10, NULL, 'item_no5'),
(115, 'Teamwork', '', NULL, 50, 1, 10, NULL, 'item_no6'),
(116, 'Non-verbal communication', 'Eye contact, facial expressions, gestures, posture, use of objects and body language', NULL, 50, 1, 10, NULL, 'item_no7'),
(117, 'Confidence and ability to answer questions', '', NULL, 50, 1, 10, NULL, 'item_no8'),
(118, 'Appearance', '', NULL, 50, 1, 10, NULL, 'item_no9'),
(119, 'Appropriate use of visual aid', '', NULL, 50, 1, 10, NULL, 'item_no10'),
(120, 'Recommendation for  Most Viable Student Venture Award', '', 'Most Viable Student Venture Award', 51, 2, 2, NULL, 'item_no11'),
(121, 'INTRODUCTION', 'The overview of the case is clear and well explained by the presenter', NULL, 52, 1, 10, NULL, 'item_no1'),
(122, 'PRESENTATION OF CONTENT', 'The presentation of the content shows high confident level, high level of creativity, full of enthusiasm and attract the attention and participation of audience', NULL, 52, 1, 10, NULL, 'item_no2'),
(123, 'TEAMWORK', 'Great efforts, coordination and participation from all the team members', NULL, 52, 1, 10, NULL, 'item_no3'),
(124, 'IT USAGE', 'Excellent incorporation of IT usage  in the poster creation', NULL, 52, 1, 10, NULL, 'item_no4'),
(125, 'CONCLUSION', 'The conclusion is clear, connected and relevant with the case', NULL, 52, 1, 10, NULL, 'item_no5'),
(126, 'Recommendation for BEST POSTER award ', '', 'BEST POSTER award ', 53, 2, 0, NULL, 'item_no6'),
(127, 'Recommendation for BEST PRESENTATION award ', '', 'BEST PRESENTATION award ', 53, 2, 0, NULL, 'item_no7'),
(128, 'DEPTH OF ANALYSIS', 'Report show extensive exploration of ethical dimensions with insightful insights and critical analysis.', NULL, 54, 1, 10, NULL, 'item_no1'),
(129, 'CLARITY AND ORGANISATION', 'Report is exceptionally well-organized, with a clear and coherent structure that enhances readability and understanding.', NULL, 54, 1, 10, NULL, 'item_no2'),
(130, 'CRITICAL THINKING AND INSIGHT', 'Report offers profound insights and original analysis, showing a sophisticated understanding of ethical considerations and their implications.', NULL, 54, 1, 10, NULL, 'item_no3'),
(131, 'QUALITY OF RECOMMENDATIONS', 'Report presents innovative and comprehensive recommendations that demonstrate a deep understanding of ethical issues and practical solutions.', NULL, 54, 1, 10, NULL, 'item_no4'),
(132, 'USE OF EVIDENCE AND SUPPORT', 'Report presents a wide range of compelling evidence and examples to support assertions, showcasing a thorough and well-researched understanding of the topic.', NULL, 54, 1, 10, NULL, 'item_no5'),
(133, 'CONTENT', 'Presentation show in-depth analysis, comprehensive coverage, and relevant details, showcasing thorough research and understanding of the topic.', NULL, 55, 1, 10, NULL, 'item_no6'),
(134, 'ORGANISATION AND STRUCTURE', 'Presentation is impeccably organized with a clear introduction, coherent development of ideas, and seamless transitions between sections, enhancing overall clarity and understanding.', NULL, 55, 1, 10, NULL, 'item_no7'),
(135, 'DELIVERY AND ENGAGEMENT', 'Presenter captivates the audience with compelling delivery, confident demeanor, and interactive engagement techniques, ensuring active participation and retention of information.', NULL, 55, 1, 10, NULL, 'item_no8'),
(136, 'VISUAL AIDS AND PRESENTATION TOOLS', 'Visuals are professionally designed, creatively used, and seamlessly integrated with the content, maximizing audience engagement and comprehension.', NULL, 55, 1, 10, NULL, 'item_no9'),
(137, 'APPEARANCE', 'Presenter show appearance appropriate to situations and wear proper attire at all times', NULL, 55, 1, 10, NULL, 'item_no10'),
(138, 'Recommendation for THE THINK TANK TROPHY', 'Best report award', 'THE THINK TANK TROPHY', 56, 2, 2, NULL, 'item_no11'),
(139, 'Recommendation for THE STELLAR PERFORMANCE AWARD', 'Best Presentation Award', 'THE STELLAR PERFORMANCE AWARD', 56, 2, 2, NULL, 'item_no12'),
(140, 'Recommendation for THE TAXPRO CHAMPION AWARD', 'Best Overall - Report & Presentation', 'THE TAXPRO CHAMPION AWARD', 56, 2, 2, NULL, 'item_no13'),
(141, 'INTRODUCTION\r\n', 'The overview of the case is clear and well-explained', '', 57, 1, 10, NULL, 'item_no8'),
(142, 'PROBLEMS / ISSUES ', 'All the problems or issues addressed are clear, well-connected and relevant with the case', '', 57, 1, 10, NULL, 'item_no9'),
(143, 'SUGGESTIONS / SOLUTIONS WITH REFERENCE TO RELEVANT ACCOUNTING STANDARD\r\n', 'All the suggestion or solution offered are clear, well-connected and relevant with the case and all the accounting standards referred are relevant with the case', '', 57, 1, 10, NULL, 'item_no10'),
(144, 'CREATIVITY IN IT USAGE', 'High creativity with excellent incorporation of IT usage shown in the infographic report', '', 57, 1, 10, NULL, 'item_no11'),
(145, 'CONCLUSION', 'The conclusion is clear, connected and relevant with the case', '', 57, 1, 10, NULL, 'item_no12'),
(146, 'Recommendation for Best Report award', '', 'Best Report award', 58, 2, 2, NULL, 'item_no13'),
(147, 'Was there evidence that the business model canvas was used to identify and track assumptions?', NULL, NULL, 43, 1, 10, NULL, 'item_no11'),
(148, 'Were assumptions clearly stated?', NULL, NULL, 43, 1, 10, NULL, 'item_no12'),
(149, 'Were high priority or crucial assumptions identified, explained and acted on first (the ones most likely to kill the idea)?', NULL, NULL, 43, 1, 10, NULL, 'item_no13'),
(150, 'Were low cost, rapid, but reliable tests of assumptions developed?', NULL, NULL, 44, 1, 10, NULL, 'item_no14'),
(151, 'Were tests implemented in a reasonable and reliable way', '- did the student or team get out of the building\r\n- who and how many people were interviewed/surveyed\r\n- depth and quality of testing', NULL, 44, 1, 10, NULL, 'item_no15'),
(152, 'Did the individual/team clearly state what they learned, whether it validated an assumption, or what changes or pivots seem appropriate?', '', NULL, 44, 1, 10, NULL, 'item_no16'),
(153, 'Did the individual or team follow the data and evidence to the most logical conclusions and did they explain that well?', '', NULL, 45, 1, 10, NULL, 'item_no17'),
(154, 'Did the individual/team clearly map out their next action steps including any resources needed to implement those steps?', '', NULL, 45, 1, 10, NULL, 'item_no18'),
(155, 'Did the business model is exceptionally attractive in terms of design, layout, and neatness?', '', NULL, 45, 1, 10, NULL, 'item_no19'),
(156, 'Real Life: Based on the persuasiveness of the evidence presented and your own real-life experience, how viable is this product or service?', '', NULL, 46, 1, 10, NULL, 'item_no20'),
(157, 'Recommend for Best Business Product Pitching Award', '', 'Best Business Product Pitching Award', 59, 2, 2, NULL, 'item_no21'),
(158, 'Recommend for Best E-Preneur Award', '', 'Corporate Business Award', 6, 2, 2, NULL, 'item_no11'),
(159, 'The level of originality exhibited in the features and functionalities of the product or service demonstrates creativity and innovation.', '', '', 60, 1, 10, NULL, 'item_no1'),
(160, 'The efficacy of the product or service in addressing customer needs and challenges.', '', '', 60, 1, 10, NULL, 'item_no2'),
(161, 'The potential positive impact of the product or service on society, the economy, or the environment, including its capacity to address significant problems or needs and its sustainability considerations.', '', '', 60, 1, 10, NULL, 'item_no3'),
(162, 'The potential of the product or service to revolutionize or shake up established markets.', '', '', 60, 1, 10, NULL, 'item_no4'),
(163, 'Business Viability - Thorough market research and comprehension of customer requirements, alongside the potential for revenue generation and profitability.', '', '', 60, 1, 10, NULL, 'item_no5'),
(164, 'Recommend for Best Product/Service Award', '', 'Best Product/Service Award', 62, 2, 2, NULL, 'item_no6'),
(165, 'The presenter delivers an engaging, clear, and professional presentation, effectively communicating and generating excitement.', '', '', 61, 1, 10, NULL, 'item_no7'),
(166, 'The presenter instills confidence in potential investors or customers by delivering well-organized content and information in an easy-to-follow manner.', '', '', 61, 1, 10, NULL, 'item_no8'),
(167, 'The presenter excels in delivering a compelling narrative and inspirational speech. They captivate the entire audience through direct eye contact, rarely relying on notes. Their speech is characterized by fluctuations in volume and inflection, ensuring sustained audience interest and emphasizing key points.', '', '', 61, 1, 10, NULL, 'item_no9'),
(168, 'Recommend for Best Presenter Award', '', 'Best Presenter Award', 63, 2, 2, NULL, 'item_no10'),
(169, 'The potential for future growth and success in entrepreneurship, coupled with a visionary outlook for the future of their business and its impact on the industry or society.', '', '', 64, 1, 10, NULL, 'item_no11'),
(170, 'Recommend for Best Young Entrepreneur Award', '', 'Best Young Entrepreneur Award', 65, 2, 2, NULL, 'item_no12'),
(171, 'ORIGINALITY', 'Uniqueness of the idea.', '', 66, 1, 10, NULL, 'item_no1'),
(172, 'FEASIBILITY', 'Practicality of implementation and consideration of resources required.', '', 66, 1, 10, NULL, 'item_no2'),
(173, 'MARKET POTENTIAL', 'Identification of target audience and analysis of market demand.', '', 66, 1, 10, NULL, 'item_no3'),
(174, 'IMPACT', 'Potential positive effects on society, economy, or environment. Addressing a significant problem or need and sustainability considerations.', '', 66, 1, 10, NULL, 'item_no4'),
(175, 'Recommend for Best Ideation Award', '', 'Best Ideation Award', 67, 2, 2, NULL, 'item_no5'),
(176, 'PRESENTATION', 'Clarity and coherence of presentation. The persuasiveness of pitch and professionalism in delivery.', '', 68, 1, 10, NULL, 'item_no6'),
(177, 'Recommend for Best Presenter Award', '', 'Best Presenter Award', 69, 2, 2, NULL, 'item_no7');

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `session_name` varchar(255) NOT NULL,
  `speaker` text DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `program_sub` int(11) DEFAULT NULL,
  `datetime_start` datetime DEFAULT NULL,
  `datetime_end` datetime DEFAULT NULL,
  `token` text DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`id`, `session_name`, `speaker`, `program_id`, `program_sub`, `datetime_start`, `datetime_end`, `token`, `created_at`, `updated_at`) VALUES
(3, 'Program Perasmian I-CREATE 2024', NULL, NULL, NULL, '2024-06-10 08:00:00', '2024-06-10 12:00:00', 'gYIhosqvKXGYA', 1717952249, 1717965285),
(4, 'Sharing Session by Prof. Madya Dato\' Haji Abdul Rahman Badul Razak Shaik (Ahli Lembaga ANGKASA)', NULL, NULL, NULL, '2024-06-10 10:45:00', '2024-06-10 12:15:00', 'QKDmMZS9Uc', 1717980486, 1717980607),
(5, 'Fire Site Chat I ', NULL, NULL, NULL, '2024-06-10 12:15:00', '2024-06-10 13:15:00', 'zxewEmqxcN', 1717980530, NULL),
(6, 'Fire Site Chat II', NULL, NULL, NULL, '2024-06-10 14:30:00', '2024-06-10 17:00:00', 'HVV0RSaauD', 1717980559, NULL),
(7, 'PEMBUKAAN DAN PERASMIAN', NULL, NULL, NULL, '2024-06-11 09:00:00', '2024-06-11 10:00:00', 'eEGHwXGc9P', 1717980739, 1718068518),
(8, 'Talk by ResoMAC', NULL, NULL, NULL, '2024-06-11 09:30:00', '2024-06-11 11:00:00', 'AMsbEAFrTR', 1717980824, 1718068449),
(9, 'Talk by Prof. Madya Ts. Dr. Siti Nurul Huda Mohamad Azmin', NULL, NULL, NULL, '2024-06-11 15:30:00', '2024-06-11 16:30:00', '2SYCJVfK47', 1717981025, 1718074845),
(10, 'Community-Based Economics Development As An Effort To Achieve Sustainable Development Goals (SDGs)', NULL, NULL, NULL, '2024-06-11 10:45:00', '2024-06-11 11:45:00', 'fEF7dIAHIR', 1717981173, 1718066485),
(11, '\"UNITY IN DIVERSITY COMMUNITY OUTREACH INITIATIVES\"', NULL, NULL, NULL, '2024-06-11 14:30:00', '2024-06-11 15:30:00', '0oX7GxE9Zh', 1717981216, 1718086822),
(12, 'Talk by Mdm. Rosnani Binti Seman (Pengerusi PUSPAK)', NULL, NULL, NULL, '2024-06-11 14:30:00', '2024-06-11 15:30:00', 'bsTH8feJFg', 1717981338, 1718055543),
(13, 'STRATEGIC FINANCIAL MANAGEMENT AND CULTIVATING FINANCIAL STABILITY', 'Abdul Rahim bin Ab Rahman (Bank Muamalat) & Muhammad Hilal bin Abdul Karim (ASNB)', 4, NULL, '2024-06-12 08:30:00', '2024-06-12 17:30:00', '0AIYGNwFR3', 1718095690, 1718179099),
(14, 'TRAILBLAZING YUR CAREER - INDUSTRY TRENDS AND TAXATION TIPS', 'Prof. Dr. Hjh Zuraeda binti Ibrahim (UiTM Puncak Alam)', 4, NULL, '2024-06-12 10:45:00', '2024-06-12 18:30:00', 'QpTjdcuuNn', 1718095785, 1718180182),
(15, 'PATHWAY TO BECOME CHARTERED ACCOUNTANTS', 'En. Aliff Ikhwan bin Mohamad & Mohammad Syazni bin Mohd Zaki (Audit Partner & MIA Member)', 4, NULL, '2024-06-12 14:00:00', '2024-06-12 18:00:00', 'FBzDDfOmlg', 1718096341, 1718168283),
(16, 'Talk by Dato\' Azwan bin Abdul Jalil CEO ZAAJ Wealth Management', NULL, 4, NULL, '2024-06-12 15:30:00', '2024-06-12 18:00:00', 'cCS3vnYFeZ', 1718096459, 1718167685),
(17, 'PENDAFTARAN PESERTA JEMPUTAN', NULL, NULL, NULL, '2024-06-13 08:00:00', '2024-06-13 09:30:00', 'tz0lw1UQjn', 1718207936, 1718238458),
(18, 'Sharing Session by Ts. Mohd Norhaizzat Naim bin Mohd Mazlan', NULL, NULL, NULL, '2024-06-13 09:00:00', '2024-06-13 10:30:00', 'MF_RAFdAGa', 1718208012, NULL),
(19, 'Sharing session by Mdm. Eriyca Baiduri @ Madammu (Noir Health & Beauty Sdn Bhd)', NULL, NULL, NULL, '2024-06-13 10:00:00', '2024-06-13 11:30:00', 'Ff_-FJQPiq', 1718208089, NULL),
(20, 'Fire Site Chat III by En. Abdunnoor bin Mohamed Ariff (KNKV Group)', NULL, NULL, NULL, '2024-06-13 11:00:00', '2024-06-13 13:00:00', '9wlF0myTcb', 1718208228, NULL),
(21, 'Majlis Penutup I-CREATE 2024 dan Penyampaian Hadiah', NULL, NULL, NULL, '2024-06-13 12:00:00', '2024-06-13 13:30:00', 'cFsbIhWEEe', 1718208331, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `session_attendance`
--

CREATE TABLE `session_attendance` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `scanned_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session_attendance`
--

INSERT INTO `session_attendance` (`id`, `session_id`, `user_id`, `scanned_at`) VALUES
(8, 3, 336, '2024-06-10 04:34:02'),
(1711, 13, 336, '2024-06-12 09:59:22'),
(2413, 17, 336, '2024-06-13 08:55:58');

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `allow_cert_from` date DEFAULT NULL,
  `allow_edit_reg_until` date DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `allow_cert_from`, `allow_edit_reg_until`, `date_start`, `date_end`) VALUES
(1, '2024-07-16', '2026-07-01', '2025-03-09', '2026-05-24');

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

CREATE TABLE `token` (
  `user_id` int(11) NOT NULL,
  `code` varchar(32) NOT NULL,
  `created_at` int(11) NOT NULL,
  `type` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `fullname` varchar(200) NOT NULL,
  `matric` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_internal` tinyint(1) DEFAULT 1,
  `is_student` tinyint(1) DEFAULT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `password_hash` varchar(60) NOT NULL,
  `auth_key` varchar(32) DEFAULT NULL,
  `confirmed_at` int(11) DEFAULT NULL,
  `unconfirmed_email` varchar(255) DEFAULT NULL,
  `blocked_at` int(11) DEFAULT NULL,
  `registration_ip` varchar(45) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `flags` int(11) NOT NULL DEFAULT 0,
  `last_login_at` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `fullname`, `matric`, `email`, `phone`, `is_internal`, `is_student`, `institution`, `password_hash`, `auth_key`, `confirmed_at`, `unconfirmed_email`, `blocked_at`, `registration_ip`, `created_at`, `updated_at`, `flags`, `last_login_at`, `status`, `password_reset_token`) VALUES
(336, 'zul@umk.edu.my', 'ZUL KARAMI BIN CHE MUSA', '01619A', 'zul@umk.edu.my', '0133671531', 1, 0, '', '$2y$13$Ip63HqC8UNKuts5N7xc.pe9aJTWslYfqDnVxZiTXjC70zc1WoIg06', 'MmlRzXYAUhwI5PikfNLNs11RK_0P7aSg', NULL, NULL, NULL, NULL, 1717574843, 1749086629, 0, NULL, 10, NULL),
(2202, 'aisynuraisya@gmail.com', 'SITI NURAISYA BINTI JOHARI TEE', 'A21A3107', 'aisynuraisya@gmail.com', '01137520617', 1, 1, '', '$2y$13$hUw0DsKqFvl8XkFkyzYf/OqOcRMwE8ftp7Bm1XvFExDCK0yaG/vFW', '3ErGmaFJA9P7pg1p8ara4C7O7704EJzX', NULL, NULL, NULL, NULL, 1772298776, 1772298776, 0, NULL, 10, NULL),
(2203, 'a23a2555@siswa.umk.edu.my', 'HNG ZHI WEI', 'a23a2555', 'a23a2555@siswa.umk.edu.my', '0174068336', 1, 1, '', '$2y$13$DqZpiq.1MEHXb7Z6Vf4dxOxIu4RXXBsYlpG2gWlLpC6FzByRL4RkW', 'Ux6DTk5D9B4tOUgzhJvOknAzErQhujdq', NULL, NULL, NULL, NULL, 1773207848, 1773207848, 0, NULL, 10, NULL),
(2204, 'zhiwei8336@gmail.com', 'HNG ZHI WEI', 'a23a2555', 'zhiwei8336@gmail.com', '0174068336', 1, 1, '', '$2y$13$m3byAhiIkNekZkG1e7B/EuzimT65ZHNXu03uMQG1Cp4gAAr8SFlkC', 'l-b6Sg3evnV5jW8GNd1IayO73b_NhniM', NULL, NULL, NULL, NULL, 1773208589, 1773208589, 0, NULL, 10, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `role_name` varchar(100) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `program_sub` int(11) DEFAULT NULL,
  `committee_id` int(11) DEFAULT NULL,
  `is_leader` tinyint(1) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `request_at` datetime DEFAULT NULL,
  `approve_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id`, `user_id`, `status`, `role_name`, `program_id`, `program_sub`, `committee_id`, `is_leader`, `is_deleted`, `request_at`, `approve_at`) VALUES
(36, 336, 10, 'manager', 2, 1, NULL, NULL, 0, '2024-06-05 16:08:24', NULL),
(86, 336, 10, 'participant', NULL, 0, NULL, NULL, 0, '2024-06-06 13:58:09', NULL),
(94, 336, 10, 'jury', NULL, 0, NULL, NULL, 0, '2024-06-06 14:18:22', NULL),
(95, 336, 10, 'committee', NULL, 0, 28, 2, 0, '2024-06-06 14:18:54', NULL),
(126, 336, 10, 'admin', NULL, NULL, NULL, NULL, 0, '2024-06-05 16:08:24', NULL),
(152, 336, 10, 'manager', 1, NULL, NULL, NULL, 0, '2024-06-06 23:00:31', NULL),
(967, 336, 10, 'manager', 2, 7, NULL, NULL, 0, '2024-06-09 11:03:43', NULL),
(971, 336, 10, 'manager', 4, NULL, NULL, NULL, 0, '2024-06-09 11:36:14', NULL),
(978, 336, 10, 'manager', 6, NULL, NULL, NULL, 0, '2024-06-09 11:52:31', NULL),
(985, 336, 10, 'manager', 2, 2, NULL, NULL, 0, '2024-06-09 12:13:24', NULL),
(993, 336, 10, 'manager', 2, 3, NULL, NULL, 0, '2024-06-09 13:04:46', NULL),
(1000, 336, 10, 'manager', 5, 8, NULL, NULL, 0, '2024-06-09 14:50:42', NULL),
(1009, 336, 10, 'manager', 3, NULL, NULL, NULL, 0, '2024-06-09 15:11:29', NULL),
(1209, 336, 10, 'manager', 5, 9, NULL, NULL, 0, '2024-06-10 09:44:21', NULL),
(2058, 336, 10, 'mentor', NULL, 0, NULL, NULL, 0, '2024-06-12 08:31:42', NULL),
(2431, 2202, 10, 'participant', NULL, NULL, NULL, NULL, 0, '2026-03-01 01:12:56', '2026-03-01 01:12:56'),
(2432, 2202, 0, 'committee', NULL, NULL, 32, NULL, 0, '2026-03-01 01:12:56', NULL),
(2433, 2203, 10, 'participant', NULL, NULL, NULL, NULL, 0, '2026-03-11 13:44:08', '2026-03-11 13:44:08'),
(2434, 2203, 0, 'committee', NULL, NULL, 32, NULL, 0, '2026-03-11 13:44:08', NULL),
(2435, 2204, 10, 'participant', NULL, NULL, NULL, NULL, 0, '2026-03-11 13:56:29', '2026-03-11 13:56:29'),
(2436, 2204, 0, 'committee', NULL, NULL, 32, NULL, 0, '2026-03-11 13:56:29', NULL),
(2437, 336, 10, 'manager', 7, NULL, NULL, NULL, 0, '2026-04-01 01:00:22', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`),
  ADD KEY `auth_assignment_user_id_idx` (`user_id`);

--
-- Indexes for table `auth_item`
--
ALTER TABLE `auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `idx-auth_item-type` (`type`);

--
-- Indexes for table `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);

--
-- Indexes for table `auth_rule`
--
ALTER TABLE `auth_rule`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `cert_tmpl`
--
ALTER TABLE `cert_tmpl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `committee`
--
ALTER TABLE `committee`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `competition_cat`
--
ALTER TABLE `competition_cat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `negeri`
--
ALTER TABLE `negeri`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program`
--
ALTER TABLE `program`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program_achievement`
--
ALTER TABLE `program_achievement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `program_sub` (`program_sub`);

--
-- Indexes for table `program_method`
--
ALTER TABLE `program_method`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `program_reg`
--
ALTER TABLE `program_reg`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_program_reg_program_email` (`program_id`,`contact_email`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `program_sub` (`program_sub`);

--
-- Indexes for table `program_reg_achieve`
--
ALTER TABLE `program_reg_achieve`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_reg_id` (`program_reg_id`),
  ADD KEY `user_id` (`achieve_id`);

--
-- Indexes for table `program_reg_field`
--
ALTER TABLE `program_reg_field`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_program_field` (`program_id`,`field_name`);

--
-- Indexes for table `program_reg_jury`
--
ALTER TABLE `program_reg_jury`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reg_id` (`reg_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `rubric_id` (`rubric_id`);

--
-- Indexes for table `program_reg_member`
--
ALTER TABLE `program_reg_member`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_reg_id` (`program_reg_id`);

--
-- Indexes for table `program_reg_mentor`
--
ALTER TABLE `program_reg_mentor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_reg_id` (`program_reg_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `program_rubric`
--
ALTER TABLE `program_rubric`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `rubric_id` (`rubric_id`),
  ADD KEY `program_sub` (`program_sub`);

--
-- Indexes for table `program_stage`
--
ALTER TABLE `program_stage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `program_sub`
--
ALTER TABLE `program_sub`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `questionnaire`
--
ALTER TABLE `questionnaire`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questionnaire_ans`
--
ALTER TABLE `questionnaire_ans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `questionnaire_ans_post`
--
ALTER TABLE `questionnaire_ans_post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `questionnaire_sub`
--
ALTER TABLE `questionnaire_sub`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `rubric`
--
ALTER TABLE `rubric`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rubric_answer`
--
ALTER TABLE `rubric_answer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rubric_id` (`rubric_id`),
  ADD KEY `assignment_id` (`assignment_id`);

--
-- Indexes for table `rubric_category`
--
ALTER TABLE `rubric_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rubric_id` (`rubric_id`);

--
-- Indexes for table `rubric_item`
--
ALTER TABLE `rubric_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `program_sub` (`program_sub`);

--
-- Indexes for table `session_attendance`
--
ALTER TABLE `session_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `token`
--
ALTER TABLE `token`
  ADD UNIQUE KEY `token_unique` (`user_id`,`code`,`type`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_unique_username` (`username`),
  ADD UNIQUE KEY `user_unique_email` (`email`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `committee_id` (`committee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cert_tmpl`
--
ALTER TABLE `cert_tmpl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `committee`
--
ALTER TABLE `committee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `competition_cat`
--
ALTER TABLE `competition_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `negeri`
--
ALTER TABLE `negeri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `program`
--
ALTER TABLE `program`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `program_achievement`
--
ALTER TABLE `program_achievement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `program_method`
--
ALTER TABLE `program_method`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_reg`
--
ALTER TABLE `program_reg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=868;

--
-- AUTO_INCREMENT for table `program_reg_achieve`
--
ALTER TABLE `program_reg_achieve`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `program_reg_field`
--
ALTER TABLE `program_reg_field`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `program_reg_jury`
--
ALTER TABLE `program_reg_jury`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=913;

--
-- AUTO_INCREMENT for table `program_reg_member`
--
ALTER TABLE `program_reg_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4044;

--
-- AUTO_INCREMENT for table `program_reg_mentor`
--
ALTER TABLE `program_reg_mentor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_rubric`
--
ALTER TABLE `program_rubric`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `program_stage`
--
ALTER TABLE `program_stage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_sub`
--
ALTER TABLE `program_sub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `questionnaire`
--
ALTER TABLE `questionnaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `questionnaire_ans`
--
ALTER TABLE `questionnaire_ans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=848;

--
-- AUTO_INCREMENT for table `questionnaire_ans_post`
--
ALTER TABLE `questionnaire_ans_post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `questionnaire_sub`
--
ALTER TABLE `questionnaire_sub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `rubric`
--
ALTER TABLE `rubric`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `rubric_answer`
--
ALTER TABLE `rubric_answer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=708;

--
-- AUTO_INCREMENT for table `rubric_category`
--
ALTER TABLE `rubric_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `rubric_item`
--
ALTER TABLE `rubric_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `session_attendance`
--
ALTER TABLE `session_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2456;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2205;

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2438;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `program_achievement`
--
ALTER TABLE `program_achievement`
  ADD CONSTRAINT `program_achievement_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`),
  ADD CONSTRAINT `program_achievement_ibfk_2` FOREIGN KEY (`program_sub`) REFERENCES `program_sub` (`id`);

--
-- Constraints for table `program_method`
--
ALTER TABLE `program_method`
  ADD CONSTRAINT `program_method_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`);

--
-- Constraints for table `program_reg`
--
ALTER TABLE `program_reg`
  ADD CONSTRAINT `program_reg_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `program_reg_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`),
  ADD CONSTRAINT `program_reg_ibfk_3` FOREIGN KEY (`program_sub`) REFERENCES `program_sub` (`id`);

--
-- Constraints for table `program_reg_achieve`
--
ALTER TABLE `program_reg_achieve`
  ADD CONSTRAINT `program_reg_achieve_ibfk_1` FOREIGN KEY (`achieve_id`) REFERENCES `program_achievement` (`id`),
  ADD CONSTRAINT `program_reg_achieve_ibfk_2` FOREIGN KEY (`program_reg_id`) REFERENCES `program_reg` (`id`);

--
-- Constraints for table `program_reg_member`
--
ALTER TABLE `program_reg_member`
  ADD CONSTRAINT `program_reg_member_ibfk_1` FOREIGN KEY (`program_reg_id`) REFERENCES `program_reg` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
