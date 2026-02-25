-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 25, 2026 at 01:13 PM
-- Server version: 5.7.44
-- PHP Version: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fkpportalumkedu_icreatedb2025`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_assignment`
--

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_item`
--

CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_item_child`
--

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_rule`
--

CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
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
  `name_mt` double DEFAULT '10',
  `name_size` double DEFAULT '10',
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
  `set_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=preset,2=custom_html',
  `custom_html` text,
  `template_file` text,
  `updated_at` datetime DEFAULT NULL,
  `published` tinyint(1) DEFAULT '0',
  `is_portrait` tinyint(1) DEFAULT '1',
  `published_at` datetime DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `align` tinyint(1) NOT NULL DEFAULT '3' COMMENT '1=left,2=right,3=center'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `committee`
--

CREATE TABLE `committee` (
  `id` int(11) NOT NULL,
  `com_name` varchar(200) DEFAULT NULL,
  `is_jawatankuasa` int(1) DEFAULT NULL,
  `is_student` tinyint(1) DEFAULT '0',
  `com_name_en` varchar(200) DEFAULT NULL,
  `is_pengarah` tinyint(1) NOT NULL DEFAULT '0',
  `can_approve` tinyint(1) NOT NULL DEFAULT '0',
  `cert_only` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `competition_cat`
--

CREATE TABLE `competition_cat` (
  `id` int(11) NOT NULL,
  `cat_name` varchar(100) DEFAULT NULL,
  `lecturer` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `negeri`
--

CREATE TABLE `negeri` (
  `negeri_name` varchar(15) DEFAULT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE `program` (
  `id` int(11) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `program_abbr` varchar(50) DEFAULT NULL,
  `reg_info` text,
  `payment_info` text NOT NULL,
  `payment_short` varchar(255) DEFAULT NULL,
  `has_sub` tinyint(1) DEFAULT '0' COMMENT '1=yes,2=no',
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `program_type` tinyint(4) DEFAULT '1' COMMENT '1=has competition, 2 = sharing -attendance'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `program_achievement`
--

CREATE TABLE `program_achievement` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `program_sub` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `program_method`
--

CREATE TABLE `program_method` (
  `id` int(11) NOT NULL,
  `method_name` varchar(255) NOT NULL,
  `program_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `nric` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `group_code` varchar(100) DEFAULT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `booth_number` varchar(100) DEFAULT NULL,
  `participant_cat_local` tinyint(1) DEFAULT '1' COMMENT '1=local,2=int',
  `participant_cat_group` tinyint(1) DEFAULT NULL COMMENT '1=local,2 international',
  `participant_cat_umk` tinyint(1) DEFAULT NULL COMMENT '1=umk 2 non umk',
  `participant_mode` tinyint(1) DEFAULT NULL COMMENT '1=physical 2 online',
  `participant_program` int(11) DEFAULT NULL,
  `other_program` varchar(255) DEFAULT NULL,
  `advisor` varchar(255) DEFAULT NULL,
  `advisor_dropdown` int(11) DEFAULT NULL,
  `institution` text,
  `project_desc` text,
  `competition_type` tinyint(1) DEFAULT NULL COMMENT '1=ideation, 2 implementation',
  `poster_file` text,
  `payment_file` text,
  `score` decimal(11,2) DEFAULT NULL,
  `award` tinyint(4) DEFAULT NULL,
  `flag` tinyint(1) DEFAULT '0',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `program_reg_achieve`
--

CREATE TABLE `program_reg_achieve` (
  `id` int(11) NOT NULL,
  `program_reg_id` int(11) NOT NULL,
  `achieve_id` int(11) DEFAULT NULL,
  `achieved_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `program_reg_jury`
--

CREATE TABLE `program_reg_jury` (
  `id` int(11) NOT NULL,
  `reg_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) DEFAULT '0',
  `score` decimal(11,2) DEFAULT '0.00',
  `stage` int(11) DEFAULT NULL,
  `method` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `rubric_id` int(11) DEFAULT NULL,
  `note` text,
  `link` text,
  `is_nullified` tinyint(1) NOT NULL DEFAULT '0',
  `reason_nullified` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `program_reg_member`
--

CREATE TABLE `program_reg_member` (
  `id` int(11) NOT NULL,
  `program_reg_id` int(11) NOT NULL,
  `member_name` varchar(255) DEFAULT NULL,
  `member_matric` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `program_reg_mentor`
--

CREATE TABLE `program_reg_mentor` (
  `id` int(11) NOT NULL,
  `program_reg_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `program_rubric`
--

CREATE TABLE `program_rubric` (
  `id` int(11) NOT NULL,
  `rubric_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `program_sub` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `program_stage`
--

CREATE TABLE `program_stage` (
  `id` int(11) NOT NULL,
  `stage_name` varchar(255) NOT NULL,
  `program_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `q7` text,
  `q8` text,
  `q9` text,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `q6` text,
  `q7` text,
  `q8` int(11) DEFAULT NULL,
  `q9` int(11) DEFAULT NULL,
  `q10` int(11) DEFAULT NULL,
  `q11` text,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `questionnaire_sub`
--

CREATE TABLE `questionnaire_sub` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `question_text` varchar(255) DEFAULT NULL,
  `answer_colum` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rubric`
--

CREATE TABLE `rubric` (
  `id` int(11) NOT NULL,
  `rubric_name` varchar(255) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `item_text1` text,
  `item_text2` text,
  `item_text3` text,
  `item_text4` text,
  `item_text5` text,
  `item_text6` text,
  `item_text7` text,
  `item_text8` text,
  `item_text9` text,
  `item_text10` text,
  `text_no1` text,
  `text_no2` text,
  `text_no3` text,
  `text_no4` text,
  `text_no5` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rubric_category`
--

CREATE TABLE `rubric_category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `is_recommend` tinyint(1) DEFAULT '0',
  `rubric_id` int(11) NOT NULL,
  `cat_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rubric_item`
--

CREATE TABLE `rubric_item` (
  `id` int(11) NOT NULL,
  `item_text` text NOT NULL,
  `item_description` text,
  `item_short` varchar(100) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `item_type` int(11) DEFAULT NULL,
  `option_number` int(11) DEFAULT NULL,
  `item_order` int(11) DEFAULT NULL,
  `colum_ans` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `session_name` varchar(255) NOT NULL,
  `speaker` text,
  `program_id` int(11) DEFAULT NULL,
  `program_sub` int(11) DEFAULT NULL,
  `datetime_start` datetime DEFAULT NULL,
  `datetime_end` datetime DEFAULT NULL,
  `token` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `session_attendance`
--

CREATE TABLE `session_attendance` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `scanned_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

CREATE TABLE `token` (
  `user_id` int(11) NOT NULL,
  `code` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL,
  `type` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `matric` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_internal` tinyint(1) DEFAULT '1',
  `is_student` tinyint(1) DEFAULT NULL,
  `institution` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_hash` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `confirmed_at` int(11) DEFAULT NULL,
  `unconfirmed_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `blocked_at` int(11) DEFAULT NULL,
  `registration_ip` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `flags` int(11) NOT NULL DEFAULT '0',
  `last_login_at` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `role_name` varchar(100) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `program_sub` int(11) DEFAULT NULL,
  `committee_id` int(11) DEFAULT NULL,
  `is_leader` tinyint(1) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `request_at` datetime DEFAULT NULL,
  `approve_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `committee`
--
ALTER TABLE `committee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competition_cat`
--
ALTER TABLE `competition_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `negeri`
--
ALTER TABLE `negeri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program`
--
ALTER TABLE `program`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_achievement`
--
ALTER TABLE `program_achievement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_method`
--
ALTER TABLE `program_method`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_reg`
--
ALTER TABLE `program_reg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_reg_achieve`
--
ALTER TABLE `program_reg_achieve`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_reg_jury`
--
ALTER TABLE `program_reg_jury`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_reg_member`
--
ALTER TABLE `program_reg_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_reg_mentor`
--
ALTER TABLE `program_reg_mentor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_rubric`
--
ALTER TABLE `program_rubric`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_stage`
--
ALTER TABLE `program_stage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_sub`
--
ALTER TABLE `program_sub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questionnaire`
--
ALTER TABLE `questionnaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questionnaire_ans`
--
ALTER TABLE `questionnaire_ans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questionnaire_ans_post`
--
ALTER TABLE `questionnaire_ans_post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questionnaire_sub`
--
ALTER TABLE `questionnaire_sub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rubric`
--
ALTER TABLE `rubric`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rubric_answer`
--
ALTER TABLE `rubric_answer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rubric_category`
--
ALTER TABLE `rubric_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rubric_item`
--
ALTER TABLE `rubric_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_attendance`
--
ALTER TABLE `session_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- Constraints for table `program_reg_jury`
--
ALTER TABLE `program_reg_jury`
  ADD CONSTRAINT `program_reg_jury_ibfk_1` FOREIGN KEY (`reg_id`) REFERENCES `program_reg` (`id`),
  ADD CONSTRAINT `program_reg_jury_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `program_reg_jury_ibfk_3` FOREIGN KEY (`rubric_id`) REFERENCES `rubric` (`id`);

--
-- Constraints for table `program_reg_member`
--
ALTER TABLE `program_reg_member`
  ADD CONSTRAINT `program_reg_member_ibfk_1` FOREIGN KEY (`program_reg_id`) REFERENCES `program_reg` (`id`);

--
-- Constraints for table `program_reg_mentor`
--
ALTER TABLE `program_reg_mentor`
  ADD CONSTRAINT `program_reg_mentor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `program_rubric`
--
ALTER TABLE `program_rubric`
  ADD CONSTRAINT `program_rubric_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`),
  ADD CONSTRAINT `program_rubric_ibfk_2` FOREIGN KEY (`rubric_id`) REFERENCES `rubric` (`id`),
  ADD CONSTRAINT `program_rubric_ibfk_3` FOREIGN KEY (`program_sub`) REFERENCES `program_sub` (`id`);

--
-- Constraints for table `program_stage`
--
ALTER TABLE `program_stage`
  ADD CONSTRAINT `program_stage_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`);

--
-- Constraints for table `program_sub`
--
ALTER TABLE `program_sub`
  ADD CONSTRAINT `program_sub_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`);

--
-- Constraints for table `questionnaire_ans`
--
ALTER TABLE `questionnaire_ans`
  ADD CONSTRAINT `questionnaire_ans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `questionnaire_ans_post`
--
ALTER TABLE `questionnaire_ans_post`
  ADD CONSTRAINT `questionnaire_ans_post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `questionnaire_sub`
--
ALTER TABLE `questionnaire_sub`
  ADD CONSTRAINT `questionnaire_sub_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questionnaire` (`id`);

--
-- Constraints for table `rubric_answer`
--
ALTER TABLE `rubric_answer`
  ADD CONSTRAINT `rubric_answer_ibfk_1` FOREIGN KEY (`rubric_id`) REFERENCES `rubric` (`id`),
  ADD CONSTRAINT `rubric_answer_ibfk_2` FOREIGN KEY (`assignment_id`) REFERENCES `program_reg_jury` (`id`);

--
-- Constraints for table `rubric_category`
--
ALTER TABLE `rubric_category`
  ADD CONSTRAINT `rubric_category_ibfk_1` FOREIGN KEY (`rubric_id`) REFERENCES `rubric` (`id`);

--
-- Constraints for table `rubric_item`
--
ALTER TABLE `rubric_item`
  ADD CONSTRAINT `rubric_item_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `rubric_category` (`id`);

--
-- Constraints for table `session`
--
ALTER TABLE `session`
  ADD CONSTRAINT `session_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`),
  ADD CONSTRAINT `session_ibfk_2` FOREIGN KEY (`program_sub`) REFERENCES `program_sub` (`id`);

--
-- Constraints for table `session_attendance`
--
ALTER TABLE `session_attendance`
  ADD CONSTRAINT `session_attendance_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`),
  ADD CONSTRAINT `session_attendance_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`committee_id`) REFERENCES `committee` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
