--
-- Table structure for table `gnl_dynamic_form_value`
--

CREATE TABLE `gnl_dynamic_form_value` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL COMMENT 'primary key (id)\r\n of Type table ',
  `form_id` varchar(50) DEFAULT NULL COMMENT '(uid) of form table ',
  `name` varchar(200) DEFAULT NULL,
  `value_field` varchar(200) DEFAULT NULL,
  `order_by` int(11) DEFAULT NULL,
  `note` text,
  `is_active` tinyint(2) NOT NULL DEFAULT '1',
  `is_delete` tinyint(2) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `gnl_dynamic_form_value`
--
ALTER TABLE `gnl_dynamic_form_value`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_id` (`type_id`,`form_id`,`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gnl_dynamic_form_value`
--
ALTER TABLE `gnl_dynamic_form_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Dumping data for table `gnl_dynamic_form_value`
--

INSERT INTO `gnl_dynamic_form_value` (`id`, `uid`, `type_id`, `form_id`, `name`, `value_field`, `order_by`, `note`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 1, 5, 'GCONF.3', 'assets/images/advanee_icon_white.png', NULL, 2, NULL, 1, 0, '2022-04-06 12:31:32', 1, '2023-08-13 03:40:54', 1),
(2, 1, 5, 'GCONF.1', 'assets/images/advanceLogo/Advance.png', NULL, 1, NULL, 1, 0, '2022-04-10 09:45:24', 1, '2023-08-13 03:41:08', 1),
(3, 4, 1, '2', 'Others', '4', 4, NULL, 1, 0, '2023-07-10 11:15:46', 1, '2023-07-10 05:15:46', NULL),
(4, 1, 10, 'HMS.1', 'Resident', '1', 1, NULL, 1, 0, '2023-07-16 11:51:37', 1, '2023-09-11 09:41:23', 1),
(5, 2, 10, 'HMS.1', 'Non-Resident', '2', 2, NULL, 1, 0, '2023-07-16 11:51:58', 1, '2023-09-11 09:41:26', 1),
(6, 3, 10, 'HMS.1', 'Alumni', '3', 3, NULL, 1, 0, '2023-07-16 11:52:13', 1, '2023-07-16 05:52:13', NULL),
(7, 1, 10, 'HMS.2', 'Honors 1st Year', 'h1', 1, NULL, 1, 0, '2023-07-24 10:19:18', 1, '2023-07-24 04:19:18', NULL),
(8, 2, 10, 'HMS.2', 'Honors 2nd Year', 'h2', 2, NULL, 1, 0, '2023-07-24 10:19:44', 1, '2023-07-24 04:19:44', NULL),
(9, 3, 10, 'HMS.2', 'Honors 3rd Year', 'h3', 3, NULL, 1, 0, '2023-07-24 10:20:08', 1, '2023-07-24 04:20:08', NULL),
(10, 4, 10, 'HMS.2', 'Honors 4th Year', 'h4', 4, NULL, 1, 0, '2023-07-24 10:20:51', 1, '2023-07-24 04:20:51', NULL),
(11, 5, 10, 'HMS.2', 'Honors 5th Year', 'h5', 5, NULL, 1, 0, '2023-07-24 10:21:16', 1, '2023-07-24 04:21:16', NULL),
(12, 6, 10, 'HMS.2', 'Honors Completed', 'hcom', 6, NULL, 1, 0, '2023-07-24 10:22:35', 1, '2023-07-24 04:22:35', NULL),
(13, 7, 10, 'HMS.2', 'Masters 1st Year', 'ms1', 7, NULL, 1, 0, '2023-07-24 10:24:41', 1, '2023-07-24 04:24:41', NULL),
(14, 8, 10, 'HMS.2', 'Masters 2nd Year', 'ms2', 8, NULL, 1, 0, '2023-07-24 10:25:10', 1, '2023-07-24 04:25:10', NULL),
(15, 9, 10, 'HMS.2', 'Masters Completed', 'mscom', 9, NULL, 1, 0, '2023-07-24 10:26:08', 1, '2023-07-24 04:26:08', NULL),
(16, 1, 10, 'HMS.3', 'Honors Certificate', 'hons', 1, NULL, 1, 0, '2023-07-31 12:23:29', 1, '2023-07-31 06:23:41', 1),
(17, 2, 10, 'HMS.3', 'Masters Certificate', 'msc', 2, NULL, 1, 0, '2023-07-31 12:24:02', 1, '2023-07-31 06:24:02', NULL),
(18, 1, 1, 'HMS.1', 'Yes', 'yes', 1, NULL, 0, 0, '2023-08-13 09:31:25', 1, '2023-08-13 03:31:25', NULL),
(19, 2, 1, 'HMS.1', 'No', 'no', 2, NULL, 0, 0, '2023-08-13 09:31:46', 1, '2023-08-13 03:31:46', NULL),
(20, 1, 10, 'HMS.4', 'SSC', 'ssc', 1, NULL, 1, 0, '2023-08-23 15:30:26', 1, '2023-08-23 09:30:26', NULL),
(21, 2, 10, 'HMS.4', 'HSC', 'hsc', 2, NULL, 1, 0, '2023-08-23 15:30:47', 1, '2023-08-23 09:30:47', NULL),
(22, 3, 10, 'HMS.4', 'Hons', 'hons', 3, NULL, 1, 0, '2023-08-23 15:31:18', 1, '2023-08-23 09:31:18', NULL),
(23, 4, 10, 'HMS.4', 'MSC', 'msc', 4, NULL, 1, 0, '2023-08-23 15:31:41', 1, '2023-08-23 09:31:41', NULL),
(24, 5, 10, 'HMS.4', 'M.Phill', 'phill', 5, NULL, 1, 0, '2023-08-23 15:32:10', 1, '2023-08-23 09:32:10', NULL),
(25, 6, 10, 'HMS.4', 'PHD', 'phd', 6, NULL, 1, 0, '2023-08-23 15:32:26', 1, '2023-08-23 09:32:26', NULL),
(26, 4, 10, 'HMS.1', 'Non Resident [Seat Cancel]', '4', 4, NULL, 1, 0, '2023-09-11 12:28:16', 1, '2023-09-11 09:40:35', 1),
(27, 5, 10, 'HMS.1', 'Resident [Seat Empty]', '5', 5, NULL, 1, 0, '2023-09-11 15:41:12', 1, '2023-09-11 09:45:23', 1),
(28, 6, 3, '3', 'Regional Manager', 'RM', 6, NULL, 1, 0, '2022-06-25 12:09:33', 1, '2023-07-12 12:17:50', 1),
(29, 1, 1, '1', 'POS', 'POS', 1, NULL, 1, 0, '2021-03-21 13:31:54', 1, '2022-01-30 00:41:04', 1),
(30, 2, 1, '1', 'A4', 'A4', 2, NULL, 1, 0, '2021-03-21 13:32:29', 1, '2022-01-30 00:41:06', 1),
(31, 1, 1, '2', 'NGO', '2', 1, NULL, 1, 0, '2021-03-21 13:33:48', 1, '2022-01-30 00:41:07', 1),
(32, 2, 1, '2', 'Fashion', '3', 2, NULL, 1, 0, '2021-03-21 13:34:13', 1, '2022-01-30 00:41:09', 1),
(33, 3, 1, '2', 'Supper Shop', '4', 3, NULL, 1, 0, '2021-03-21 13:34:35', 1, '2022-01-30 00:41:11', 1),
(34, 1, 1, '6', '01-07', '01-07', 1, NULL, 1, 0, '2021-03-22 14:24:58', 1, '2022-01-30 00:58:49', 1),
(35, 1, 1, '7', '30-06', '30-06', 1, NULL, 1, 0, '2021-03-22 14:25:14', 1, '2022-01-30 00:59:06', 1),
(36, 1, 1, '11', 'Product Wise', 'product', 1, NULL, 1, 0, '2021-06-12 11:57:28', 1, '2022-01-30 01:00:07', 1),
(37, 2, 1, '11', 'Bill wise', 'bill', 2, NULL, 1, 0, '2021-06-12 11:57:51', 1, '2022-01-30 01:00:11', 1),
(38, 1, 1, '13', 'None', 'none', 1, NULL, 1, 0, '2021-08-02 13:56:47', 1, '2022-01-30 01:01:09', 1),
(39, 2, 1, '13', 'Collection', 'collection', 2, NULL, 1, 0, '2021-08-02 13:57:13', 1, '2022-01-30 01:01:14', 1),
(40, 3, 1, '13', 'Sales', 'sales', 3, NULL, 1, 0, '2021-08-02 16:27:34', 1, '2022-01-30 01:01:19', 1),
(41, 4, 1, '13', 'Sales Return', 'salesreturn', 4, NULL, 1, 0, '2021-08-02 16:27:59', 1, '2022-01-30 01:01:23', 1),
(42, 5, 1, '13', 'Transfer', 'transfer', 5, NULL, 1, 0, '2021-08-02 16:28:27', 1, '2022-01-30 01:01:28', 1),
(43, 6, 1, '13', 'Waiver', 'waiver', 6, NULL, 1, 0, '2021-08-02 16:28:47', 1, '2022-01-30 01:01:35', 1),
(44, 7, 1, '13', 'Issue Return', 'issuereturn', 7, NULL, 1, 0, '2021-08-02 16:29:10', 1, '2022-01-30 01:01:40', 1),
(45, 1, 3, '1', 'Pay', 'pay', 1, NULL, 1, 0, '2021-11-29 09:47:02', 1, '2022-01-30 01:02:12', 1),
(46, 2, 3, '1', 'Non Pay', 'nonpay', 2, NULL, 1, 0, '2021-11-29 09:47:31', 1, '2022-01-30 01:02:15', 1),
(47, 3, 3, '1', 'Earn Leave', 'earn', 3, NULL, 1, 0, '2021-11-29 09:50:49', 1, '2022-01-30 01:02:19', 1),
(48, 4, 3, '1', 'Parental Leave', 'PL', 4, NULL, 1, 0, '2021-12-05 09:54:05', 1, '2022-01-30 01:02:23', 1),
(49, 1, 3, '2', 'Type A', 'a', 1, NULL, 1, 0, '2022-01-30 11:54:21', 1, '2022-01-30 00:54:39', 1),
(50, 2, 3, '2', 'Type B', 'b', 1, NULL, 1, 0, '2022-01-30 11:55:20', 1, '2022-01-30 00:54:47', 1),
(51, 1, 2, 'GCONF.5', 'Add', '1', 1, NULL, 1, 0, '2022-01-12 16:24:39', 1, '2022-01-11 22:24:39', NULL),
(52, 2, 2, 'GCONF.5', 'Edit', '2', 2, NULL, 1, 0, '2022-01-12 16:25:53', 1, '2022-01-11 22:25:53', NULL),
(53, 3, 2, 'GCONF.5', 'View', '3', 3, NULL, 1, 0, '2022-01-12 16:26:07', 1, '2022-01-11 22:26:07', NULL),
(54, 4, 2, 'GCONF.5', 'Active', '4', 4, NULL, 1, 0, '2022-01-12 16:26:39', 1, '2022-01-11 22:26:39', NULL),
(55, 5, 2, 'GCONF.5', 'In-Active', '5', 5, NULL, 1, 0, '2022-01-12 16:26:57', 1, '2022-01-11 22:26:57', NULL),
(56, 6, 2, 'GCONF.5', 'Delete', '6', 6, NULL, 1, 0, '2022-01-12 16:27:21', 1, '2022-01-11 22:27:21', NULL),
(57, 7, 2, 'GCONF.5', 'Approve', '7', 7, NULL, 1, 0, '2022-01-12 16:27:42', 1, '2022-01-11 22:27:42', NULL),
(58, 8, 2, 'GCONF.5', 'Change Password', '9', 9, NULL, 1, 0, '2022-01-12 16:28:36', 1, '2022-01-11 22:28:36', NULL),
(59, 9, 2, 'GCONF.5', 'Permission', '10', 10, NULL, 1, 0, '2022-01-12 16:28:54', 1, '2022-01-11 22:28:54', NULL),
(60, 10, 2, 'GCONF.5', 'Print', '11', 11, NULL, 1, 0, '2022-01-12 16:29:21', 1, '2022-01-11 22:29:21', NULL),
(61, 11, 2, 'GCONF.5', 'Print pdf', '12', 12, NULL, 1, 0, '2022-01-12 16:29:40', 1, '2022-01-11 22:29:40', NULL),
(62, 12, 2, 'GCONF.5', 'Force Delete', '13', 13, NULL, 1, 0, '2022-01-12 16:29:57', 1, '2022-01-11 22:29:57', NULL),
(63, 13, 2, 'GCONF.5', 'Permission Folder', '14', 14, NULL, 1, 0, '2022-01-12 16:30:13', 1, '2022-01-11 22:30:13', NULL),
(64, 14, 2, 'GCONF.5', 'Execute', '15', 15, NULL, 1, 0, '2022-01-12 16:30:30', 1, '2022-01-11 22:30:30', NULL),
(65, 15, 2, 'GCONF.5', 'Send', '16', 16, NULL, 1, 0, '2022-01-12 16:30:45', 1, '2022-01-11 22:30:45', NULL),
(66, 17, 2, 'GCONF.5', 'All Data for Permitted Branches', '101', 101, 'All Data for Permitted Branches', 1, 0, '2022-01-12 16:30:59', 1, '2023-09-17 12:14:59', 1),
(67, 18, 2, 'GCONF.5', 'All Branch Data Without HO', '102', 102, 'All Branch Data Without HO', 1, 0, '2022-01-12 16:31:19', 1, '2023-09-17 12:15:27', 1),
(68, 19, 2, 'GCONF.5', 'All Data Only HO', '103', 103, 'All Data Only HO', 1, 0, '2022-01-12 16:31:51', 1, '2023-09-17 12:15:42', 1),
(69, 1, 4, '1', 'Ready Items', 'ready', 1, NULL, 1, 0, '2022-01-17 12:45:05', 1, '2022-01-16 18:45:05', NULL),
(70, 2, 4, '1', 'Raw Materials', 'raw', 2, NULL, 0, 0, '2022-01-17 12:45:27', 1, '2022-09-12 22:01:19', 1),
(71, 3, 4, '1', 'Gold Items', 'gold', 3, NULL, 1, 0, '2022-01-17 12:45:45', 1, '2022-01-16 18:45:45', NULL),
(72, 4, 4, '1', 'Others', 'others', 4, NULL, 0, 0, '2022-01-17 12:46:00', 1, '2022-09-12 22:01:29', 1),
(73, 1, 3, '3', 'Credit Officer', 'CO', 1, NULL, 1, 0, '2022-02-06 15:05:02', 1, '2022-02-06 03:05:02', NULL),
(74, 2, 3, '3', 'Accountant', 'AC', 2, NULL, 1, 0, '2022-02-06 15:05:41', 1, '2022-02-06 03:05:41', NULL),
(75, 3, 3, '3', 'Branch Manager', 'BM', 3, NULL, 1, 0, '2022-02-06 15:06:12', 1, '2022-02-06 03:06:12', NULL),
(76, 4, 3, '3', 'Area Manager', 'AM', 5, NULL, 1, 0, '2022-02-06 15:06:39', 1, '2022-02-06 03:06:39', NULL),
(77, 5, 3, '3', 'Zonal Manager', 'ZM', 5, NULL, 1, 0, '2022-02-06 15:07:11', 1, '2022-02-06 03:07:11', NULL),
(78, 1, 5, 'GCONF.4', 'Advance', 'Advance', 1, NULL, 1, 0, '2022-04-06 12:30:36', 1, '2022-07-26 00:16:06', 1),
(79, 1, 1, '17', 'Bill Wise', 'bill', 1, NULL, 1, 0, '2022-04-16 13:47:35', 1, '2022-04-16 01:47:35', NULL),
(80, 2, 1, '17', 'Supplier Wise', 'supplier', 2, NULL, 1, 0, '2022-04-16 13:48:56', 1, '2022-04-16 01:48:56', NULL),
(81, 1, 1, '18', 'Bill Wise', 'bill', 1, NULL, 1, 0, '2022-04-16 13:49:50', 1, '2022-04-16 01:49:50', NULL),
(82, 2, 1, '18', 'Customer Wise', 'customer', 2, NULL, 1, 0, '2022-04-16 13:50:20', 1, '2022-04-16 01:50:20', NULL),
(83, 1, 1, '25', 'Generate Auto Provision', '1', 1, NULL, 1, 0, '2022-06-22 17:29:01', 1, '2022-06-22 05:42:21', 1),
(84, 3, 3, '2', 'Type C', 'c', 3, NULL, 1, 0, '2022-08-16 16:51:16', 1, '2022-08-19 19:26:38', 1),
(85, 1, 1, '28', 'Next Working Date', 'next', 1, NULL, 1, 0, '2022-10-23 15:58:19', 1, '2022-10-22 21:58:19', NULL),
(86, 2, 1, '28', 'Previous Working Date', 'previous', 2, NULL, 1, 0, '2022-10-23 15:58:49', 1, '2022-10-22 21:58:49', NULL),
(87, 1, 4, 'POS.1', 'Purchase', 'purchase', 1, NULL, 1, 0, '2023-02-07 16:11:34', 1, '2023-02-07 04:11:34', NULL),
(88, 2, 4, 'POS.1', 'Purchase Return', 'purchase_return', 2, NULL, 1, 0, '2023-02-07 16:12:07', 1, '2023-02-07 04:12:07', NULL),
(89, 3, 4, 'POS.1', 'Supplier Payment', 'payment', 3, NULL, 1, 0, '2023-02-07 16:13:03', 1, '2023-02-07 04:13:03', NULL),
(90, 4, 4, 'POS.1', 'Issue', 'issue', 4, NULL, 1, 0, '2023-02-07 16:14:26', 1, '2023-02-07 04:14:26', NULL),
(91, 5, 4, 'POS.1', 'Issue Return', 'issue_return', 5, NULL, 1, 0, '2023-02-07 16:14:48', 1, '2023-02-07 04:14:48', NULL),
(92, 6, 4, 'POS.1', 'Sales (Cash sales / Shops)', 'sales_cash_shop', 6, NULL, 1, 0, '2023-02-07 16:16:30', 1, '2023-02-07 04:16:30', NULL),
(93, 7, 4, 'POS.1', 'Sales (Installment)', 'sales_installment', 7, NULL, 1, 0, '2023-02-07 16:17:29', 1, '2023-02-07 04:17:29', NULL),
(94, 8, 4, 'POS.1', 'Sales (Due Sales)', 'sales_due', 8, NULL, 1, 0, '2023-02-07 16:22:38', 1, '2023-02-07 04:22:38', NULL),
(95, 9, 4, 'POS.1', 'Collection (Installment / Due Sales)', 'collection', 9, NULL, 1, 0, '2023-02-07 16:23:46', 1, '2023-02-07 04:23:46', NULL),
(96, 10, 4, 'POS.1', 'Sales Return', 'sales_return', 10, NULL, 1, 0, '2023-02-07 16:24:49', 1, '2023-02-07 04:24:49', NULL),
(97, 11, 4, 'POS.1', 'Transfer', 'transfer', 11, NULL, 1, 0, '2023-02-07 16:25:33', 1, '2023-02-07 04:25:33', NULL),
(98, 1, 8, 'MFN.1', 'Daily', 'daily', 1, NULL, 1, 0, '2023-06-11 17:57:23', 1, '2023-06-11 11:57:23', NULL),
(99, 2, 8, 'MFN.1', 'Monthly', '1', 2, NULL, 1, 0, '2023-06-11 17:58:00', 1, '2023-06-11 11:58:00', NULL),
(100, 3, 8, 'MFN.1', 'Quarterly', '3', 3, NULL, 1, 0, '2023-06-11 17:58:36', 1, '2023-06-11 11:58:36', NULL),
(101, 4, 8, 'MFN.1', 'Half Yearly', '6', 4, NULL, 1, 0, '2023-06-11 17:59:04', 1, '2023-06-11 11:59:04', NULL),
(102, 5, 8, 'MFN.1', 'Yearly', '12', 5, NULL, 1, 0, '2023-06-11 17:59:36', 1, '2023-06-11 11:59:36', NULL),
(103, 1, 8, 'MFN.2', 'Daily Balance', 'dailyBasis', 1, NULL, 1, 0, '2023-06-11 18:02:09', 1, '2023-06-11 12:02:09', NULL),
(104, 2, 8, 'MFN.2', 'Daily Average', 'dailyAverage', 2, NULL, 1, 0, '2023-06-11 18:02:42', 1, '2023-06-12 03:32:54', 1),
(105, 1, 8, 'MFN.3', 'Flat', 'flat', 1, NULL, 1, 0, '2023-06-11 18:06:41', 1, '2023-06-11 12:06:41', NULL),
(106, 2, 8, 'MFN.3', 'Variable', 'variable', 2, NULL, 1, 0, '2023-06-11 18:08:59', 1, '2023-06-11 12:08:59', NULL),
(107, 3, 8, 'MFN.2', 'Average', 'monthlyAverage', 3, NULL, 1, 0, '2023-06-12 09:33:05', 1, '2023-06-12 03:33:05', NULL),
(108, 1, 8, 'MFN.4', 'Yes', 'yes', 1, NULL, 1, 0, '2023-06-12 10:54:01', 1, '2023-06-12 04:54:01', NULL),
(109, 2, 8, 'MFN.4', 'No', 'no', 2, NULL, 1, 0, '2023-06-12 10:54:17', 1, '2023-06-12 04:54:17', NULL),
(110, 1, 8, 'MFN.7', 'Monthly Wise', 'monthly_wise', 1, NULL, 1, 0, '2023-06-12 11:01:17', 1, '2023-06-12 05:01:17', NULL),
(111, 2, 8, 'MFN.7', 'Frequency Wise', 'frequency_wise', 2, NULL, 1, 0, '2023-06-12 11:01:56', 1, '2023-06-12 05:01:56', NULL),
(112, 3, 8, 'MFN.7', 'Mature Period Wise', 'mature_period_wise', 3, NULL, 1, 0, '2023-06-12 11:02:54', 1, '2023-06-12 05:02:54', NULL),
(113, 4, 8, 'MFN.7', 'Provision Period Wise', 'provision_period_wise', 4, NULL, 1, 0, '2023-06-12 11:03:24', 1, '2023-06-12 05:03:24', NULL),
(114, 1, 8, 'MFN.8', 'Current Fiscal Year', 'current_fiscal_year', 1, NULL, 1, 0, '2023-06-12 11:06:02', 1, '2023-06-12 05:06:02', NULL),
(115, 2, 8, 'MFN.8', 'Account Opening Date', 'acc_opening_date', 2, NULL, 1, 0, '2023-06-12 11:06:28', 1, '2023-06-12 05:06:28', NULL),
(116, 1, 8, 'MFN.9', 'Yes', 'yes', 1, NULL, 1, 0, '2023-06-12 11:08:22', 1, '2023-06-12 05:08:22', NULL),
(117, 2, 8, 'MFN.9', 'No', 'no', 2, NULL, 1, 0, '2023-06-12 11:08:38', 1, '2023-06-12 05:08:38', NULL),
(118, 1, 8, 'MFN.10', 'Cash Withdraw', 'cash_withdraw', 1, NULL, 1, 0, '2023-06-12 11:10:12', 1, '2023-06-12 05:10:12', NULL),
(119, 2, 8, 'MFN.10', 'Deposit', 'deposit', 2, NULL, 1, 0, '2023-06-12 11:11:22', 1, '2023-06-12 05:11:22', NULL),
(120, 1, 1, 'POS.4', 'Enable', 'yes', 1, NULL, 1, 0, NULL, NULL, '2023-07-11 04:27:26', NULL),
(121, 2, 1, 'POS.4', 'Disable', 'no', 2, NULL, 1, 0, NULL, NULL, '2023-07-11 04:27:26', NULL),
(122, 1, 5, 'GCONF.2', 'Advance', 'Advance', 4, NULL, 1, 0, '2023-08-01 15:35:01', 1, '2023-08-08 05:51:15', 1),
(123, 1, 3, 'HR.1', 'Permanent', 'permanent', 1, NULL, 1, 0, '2023-06-21 09:45:27', 1, '2023-06-21 03:45:27', NULL),
(124, 2, 3, 'HR.1', 'Non Permanent', 'nonpermanent', 2, NULL, 1, 0, '2023-06-21 09:46:41', 1, '2023-07-19 03:22:14', 1),
(125, 1, 3, 'HR.2', 'Auto', 'auto', 1, NULL, 1, 0, '2023-06-21 09:51:12', 1, '2023-07-16 04:29:54', 1),
(126, 2, 3, 'HR.2', 'Manual', 'manual', 2, NULL, 0, 0, '2023-06-21 09:51:40', 1, '2023-07-19 03:25:52', 1),
(127, 1, 3, 'HR.3', 'PF', 'pf', 1, NULL, 1, 0, '2023-06-21 09:55:00', 1, '2023-06-21 03:55:00', NULL),
(128, 2, 3, 'HR.3', 'WF', 'wf', 2, NULL, 1, 0, '2023-06-21 09:55:21', 1, '2023-06-21 03:55:21', NULL),
(129, 3, 3, 'HR.3', 'EPS', 'eps', 3, NULL, 1, 0, '2023-06-21 09:56:17', 1, '2023-06-21 03:56:17', NULL),
(130, 4, 3, 'HR.3', 'OSF', 'osf', 4, NULL, 1, 0, '2023-06-21 09:56:51', 1, '2023-06-21 03:56:51', NULL),
(131, 5, 3, 'HR.3', 'Insurance', 'inc', 5, NULL, 1, 0, '2023-06-21 09:57:37', 1, '2023-06-21 04:01:47', 1),
(132, 6, 3, 'HR.3', 'Gratuity', 'gat', 6, NULL, 1, 0, '2023-06-21 09:58:40', 1, '2023-06-21 03:58:40', NULL),
(133, 7, 3, 'HR.3', 'Bonus', 'bonus', 7, NULL, 1, 0, '2023-06-21 09:59:27', 1, '2023-06-21 03:59:27', NULL),
(134, 8, 3, 'HR.3', 'Loan', 'loan', 8, NULL, 1, 0, '2023-06-21 10:00:05', 1, '2023-06-21 04:00:05', NULL),
(135, 9, 3, 'HR.3', 'Security Money', 'sm', 9, NULL, 1, 0, '2023-06-21 10:00:43', 1, '2023-06-21 04:00:43', NULL),
(136, 3, 3, 'HR.2', 'Both', 'both', 3, NULL, 0, 0, '2023-07-16 10:30:25', 1, '2023-07-19 03:25:59', 1),
(137, 1, 1, 'POS.5', 'With Warehouse', '1', 1, NULL, 1, 0, '2023-08-14 11:22:04', 1, '2023-08-14 05:22:04', NULL),
(138, 2, 1, 'POS.5', 'Without Warehouse / Single Outlet', '2', 2, NULL, 1, 0, '2023-08-14 11:22:49', 1, '2023-08-14 05:22:49', NULL),
(139, 1, 4, 'POS.2', 'Cash / Retail Sales', '1', 1, NULL, 1, 0, '2023-08-14 11:28:44', 1, '2023-08-14 05:28:44', NULL),
(140, 2, 4, 'POS.2', 'Installment Sales', '2', 2, NULL, 1, 0, '2023-08-14 11:29:05', 1, '2023-08-14 05:29:05', NULL),
(141, 3, 4, 'POS.2', 'Whole / Due / Credit Sales', '3', 3, NULL, 1, 0, '2023-08-14 11:29:29', 1, '2023-08-14 05:29:29', NULL),
(142, 4, 4, 'POS.2', 'Online / E-com Sales', '4', 4, NULL, 1, 0, '2023-08-14 11:29:53', 1, '2023-08-14 05:29:53', NULL),
(143, 1, 4, 'POS.3', 'Sales Return', '1', 1, NULL, 1, 0, '2023-08-14 11:33:14', 1, '2023-08-14 05:33:14', NULL),
(144, 2, 4, 'POS.3', 'Sales Replacement', '2', 2, NULL, 1, 0, '2023-08-14 11:33:50', 1, '2023-08-14 05:33:50', NULL),
(145, 1, 8, 'MFN.11', 'Real Time', 'real_time', 1, NULL, 1, 0, '2023-08-21 10:07:01', 1, '2023-08-20 22:07:01', NULL),
(146, 2, 8, 'MFN.11', 'Month 30 Days & Year 360 Days', '360', 2, NULL, 1, 0, '2023-08-21 10:08:00', 1, '2023-08-20 22:23:29', 1),
(147, 20, 2, 'GCONF.5', 'All data for own department of permitted branches', '104', 104, 'All data for own department of permitted branches', 1, 0, '2023-08-28 13:00:17', 1, '2023-09-17 12:16:32', 1),
(148, 26, 2, 'GCONF.5', 'All Data (Only permitted Samity)', '108', 108, NULL, 1, 0, '2023-08-28 13:01:16', 1, '2023-08-28 07:01:16', NULL),
(149, 23, 2, 'GCONF.5', 'All Data (Only permitted Project)', '107', 107, NULL, 1, 0, '2023-08-28 15:24:11', 1, '2023-08-28 09:24:11', NULL),
(150, 16, 2, 'GCONF.5', 'Own Data', '100', 100, 'Only own Data', 1, 0, '2023-09-16 09:52:33', 1, '2023-09-17 10:52:39', 1),
(151, 21, 2, 'GCONF.5', 'All data for own department of all branches without HO', '105', 105, 'All data for own department of all branches without HO', 1, 0, '2023-09-17 18:18:12', 1, '2023-09-17 12:18:12', NULL),
(152, 22, 2, 'GCONF.5', 'All data for own department only HO', '106', 106, 'All data for own department only HO', 1, 0, '2023-09-17 18:19:39', 1, '2023-09-17 12:19:39', NULL);
COMMIT;

-- Dynamic form type, form, value table merge on 10/10/2023 for all module.
-- Merged by Md Mozahid Hossain

----------------------------------------------------
        --   Modifcation After Merging (10/10/2023)
----------------------------------------------------