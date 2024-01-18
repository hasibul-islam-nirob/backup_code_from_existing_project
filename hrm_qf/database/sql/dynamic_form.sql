--
-- Table structure for table `gnl_dynamic_form`
--

CREATE TABLE `gnl_dynamic_form` (
  `id` int(11) NOT NULL,
  `uid` varchar(50) NOT NULL,
  `type_id` int(11) DEFAULT NULL COMMENT 'primary key (id)\r\n of Type table ',
  `name` varchar(200) DEFAULT NULL,
  `module_id` varchar(20) DEFAULT NULL,
  `input_type` varchar(50) DEFAULT NULL,
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
-- Indexes for table `gnl_dynamic_form`
--
ALTER TABLE `gnl_dynamic_form`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_id` (`type_id`,`uid`,`module_id`) USING BTREE;

--
-- AUTO_INCREMENT for table `gnl_dynamic_form`
--
ALTER TABLE `gnl_dynamic_form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- Dumping data for table `gnl_dynamic_form`
--

INSERT INTO `gnl_dynamic_form` (`id`, `uid`, `type_id`, `name`, `module_id`, `input_type`, `order_by`, `note`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 'GCONF.5', 2, 'Actions For Menu', '1', 'text', 1, NULL, 1, 0, '2022-01-12 16:23:54', 1, '2022-01-12 04:23:54', NULL),
(2, 'HMS.1', 10, 'Status', '14', 'select', 1, 'Student status', 1, 0, '2023-07-16 11:50:25', 1, '2023-07-15 23:50:25', NULL),
(3, 'HMS.2', 10, 'Promotion Status', '14', 'select', 2, 'example- honors 1st,,2nd year, honors completed etc.', 1, 0, '2023-07-24 10:18:22', 1, '2023-07-23 22:18:22', NULL),
(4, 'HMS.3', 10, 'Certificates', '14', 'checkbox', 3, NULL, 1, 0, '2023-07-31 12:22:46', 1, '2023-07-31 00:23:20', 1),
(5, 'GCONF.2', 5, 'Product Name (Login Page)', '1', 'text', 4, 'Website Product Name In Login Page', 1, 0, '2023-08-02 09:54:35', 1, '2023-08-20 22:33:39', 1),
(6, 'HMS.1', 1, 'Is Authorization required for student seat assign', '14', 'radio', 1, NULL, 1, 0, '2023-08-13 09:30:50', 1, '2023-08-12 21:30:50', NULL),
(7, 'HMS.4', 10, 'Exam Title', '14', 'select', 1, 'ssc, hsc etc', 1, 0, '2023-08-23 15:30:05', 1, '2023-08-23 03:30:05', NULL),
(8, '2', 1, 'Company Type', '1', 'select', 0, 'Type of Company use software', 1, 0, '2020-10-11 18:13:29', 1, '2023-02-25 23:12:27', 1),
(9, '1', 1, 'Print Type', '2', 'select', 1, 'Bill printing paper size', 1, 0, '2020-10-11 17:45:01', 1, '2022-05-29 16:12:20', 1),
(10, '2', 1, 'Company Type', '2', 'select', 0, 'Type of Company use software', 1, 0, '2020-10-11 18:13:29', 1, '2022-05-29 16:12:52', 1),
(11, '3', 1, 'Max bill print', '2', 'text', 3, 'Maximum allow sales invoice print', 1, 0, '2021-03-09 17:52:11', 1, '2022-05-29 16:13:26', 1),
(12, '4', 1, 'Return policy', '2', 'text', 4, 'Shop sales product retun policy (Date)', 1, 0, '2021-03-09 17:52:43', 1, '2022-01-29 18:38:54', 1),
(13, '5', 1, 'Vat', '2', 'text', 5, 'Company vat percentage by default into sales bill', 1, 0, '2021-03-09 17:53:09', 1, '2022-01-29 18:39:00', 1),
(14, '6', 1, 'Acc Fiscal year start', '2', 'select', 6, 'Company fiscal year policy', 1, 0, '2021-03-09 17:54:37', 1, '2022-01-29 18:39:10', 1),
(15, '7', 1, 'Acc Fiscal year end', '2', 'select', 7, 'Company fiscal year policy', 1, 0, '2021-03-09 17:55:00', 1, '2022-01-29 18:39:19', 1),
(16, '8', 1, 'Processing fee editable', '2', 'radio', 8, 'Company wise processing fee edit or not', 1, 0, '2021-03-09 17:55:43', 1, '2022-01-29 18:39:25', 1),
(17, '9', 1, 'Offline Pos', '2', 'radio', 9, 'POS office system use or not', 1, 0, '2021-04-26 15:33:16', 1, '2022-01-29 18:39:30', 1),
(18, '10', 1, 'Company Prefix', '2', 'text', 2, 'Comapany prefix for barcode', 1, 0, '2021-06-02 12:29:45', 1, '2022-01-29 18:39:36', 1),
(19, '11', 1, 'Processing Fee Type', '2', 'select', 10, 'processing fee applicable into bill or product wise', 1, 0, '2021-06-12 11:56:37', 1, '2022-01-29 18:39:43', 1),
(20, '12', 1, 'Payroll', '13', 'radio', 11, 'payroll active or not', 1, 0, '2021-08-01 15:19:03', 1, '2022-01-29 18:39:49', 1),
(21, '13', 1, 'Transaction Authorization', '2', 'checkbox', 12, NULL, 1, 0, '2021-08-02 13:56:13', 1, '2022-01-29 18:39:57', 1),
(22, '14', 1, 'Signator Name', '2', 'radio', 1, 'Signature name show into report footer', 1, 0, '2021-10-09 17:15:02', 1, '2022-01-29 18:40:06', 1),
(23, '1', 3, 'Leave Type', '13', 'text', 1, NULL, 1, 0, '2021-11-29 09:45:15', 1, '2021-11-28 15:45:15', NULL),
(24, '2', 3, 'Benefit Type', '13', 'text', 1, NULL, 1, 0, '2022-01-30 11:52:42', 1, '2022-01-29 18:32:25', 1),
(25, '1', 4, 'Product Type', '2', 'text', 1, 'Product type for POS', 1, 0, '2022-01-17 12:43:44', 1, '2022-01-16 18:43:44', NULL),
(26, '3', 3, 'Fixed Positions', '13', 'select', 1, 'Designation fixed for HR', 1, 0, '2022-02-06 14:34:36', 1, '2022-02-05 21:04:15', 1),
(27, '15', 1, 'Product Stock Wise Load', '2', 'radio', 15, 'company wise', 1, 0, '2022-02-07 12:50:23', 1, '2022-02-06 19:07:28', 1),
(28, 'GCONF.4', 5, 'Product Name (Dashboard)', '1', 'text', 1, 'Website Product Name', 1, 0, '2022-04-06 12:28:24', 1, '2023-08-07 22:45:58', 1),
(29, 'GCONF.3', 5, 'Product Logo (Dashboard)', '1', 'text', 3, 'Software Product Name', 1, 0, '2022-04-06 12:29:19', 1, '2023-08-07 22:46:24', 1),
(30, 'GCONF.1', 5, 'Product Logo (Login Page)', '1', 'text', 4, 'Website Product Logo', 1, 0, '2022-04-10 09:43:51', 1, '2023-08-07 22:46:45', 1),
(31, '20', 1, 'Check Cash & Bank Balance', '5', 'radio', 1, 'Cash & bank balance can not zero during transaction', 1, 0, '2022-04-24 09:50:16', 1, '2022-04-23 15:50:16', NULL),
(32, '24', 1, 'Check Cash & Bank Balance', '3', 'radio', 1, 'Cash & bank balance can not zero during day end execution', 1, 0, '2022-04-24 09:50:41', 1, '2022-05-22 21:40:32', 1),
(33, '16', 1, 'Product Negative Sales', '2', 'radio', 16, 'Stock negative sales allow or not', 1, 0, '2022-04-12 12:37:47', 1, '2022-04-11 18:37:47', NULL),
(34, '17', 1, 'Supplier Payment Type', '2', 'select', 17, 'Vendor wise or bill wise payment', 1, 0, '2022-04-16 13:47:05', 1, '2022-04-15 19:47:05', NULL),
(35, '18', 1, 'Collection Type', '2', 'select', 18, 'Customer wise or bill wise payment', 1, 0, '2022-04-16 13:49:27', 1, '2022-04-15 19:49:27', NULL),
(36, '19', 1, 'Autovoucher Must Config', '2', 'radio', 19, 'have to config auto voucher or not check', 1, 0, '2022-04-20 10:32:12', 1, '2022-04-19 16:32:12', NULL),
(37, '22', 1, 'Autovoucher Must Config', '10', 'radio', 1, 'have to config auto voucher or not check', 1, 0, '2022-05-18 13:40:55', 1, '2022-05-17 19:40:55', NULL),
(38, '23', 1, 'Autovoucher Must Config', '5', 'radio', 1, 'have to config auto voucher or not check', 1, 0, '2022-05-22 09:49:45', 1, '2022-05-21 15:49:45', NULL),
(39, '25', 1, 'Mandatory Requisition', '2', 'radio', 20, 'Requisition Must needed or not', 1, 0, '2022-08-28 11:08:51', 1, '2022-08-27 17:08:51', NULL),
(40, '26', 1, 'Mandatory Order', '2', 'radio', 21, 'Order needed must or not', 1, 0, '2022-08-28 11:09:27', 1, '2022-08-27 17:09:27', NULL),
(41, '27', 1, 'Branch Approve Issue&Transfer', '2', 'radio', 22, 'Branch Approve on or off for Issue&Transfer', 1, 0, '2022-09-04 16:04:12', 1, '2022-09-03 22:04:12', NULL),
(42, '28', 1, 'Change schedule date for holiday', '5', 'select', 4, 'Jodi schedule date a holiday pore tahole next working day nibe or previous working day ta schedule date hisebe count hobe seta select korte hobe.', 1, 0, '2022-10-23 15:57:37', 1, '2022-10-22 21:57:37', NULL),
(43, '29', 1, 'MIS & AIS Date Difference Check', '2', 'radio', 23, 'POS & ACC Date difference maximum 1 day.', 1, 0, '2023-01-23 10:54:22', 1, '2023-01-22 16:54:22', NULL),
(44, 'POS.1', 4, 'MIS Type for POS', '2', 'select', 2, 'mis type for pos loading into mis configuration', 1, 0, '2023-02-07 16:11:06', 1, '2023-02-06 22:11:06', NULL),
(45, 'MFN.1', 8, 'Provision Frequency', '5', 'select', 1, 'Frequency for Provision Configuration', 1, 0, '2023-06-11 17:55:44', 1, '2023-06-11 05:55:44', NULL),
(46, 'MFN.2', 8, 'Interest Generate Method', '5', 'select', 2, NULL, 1, 0, '2023-06-11 18:01:20', 1, '2023-06-11 06:01:20', NULL),
(47, 'MFN.3', 8, 'Interest Rate', '5', 'select', 3, NULL, 1, 0, '2023-06-11 18:03:24', 1, '2023-06-11 06:03:24', NULL),
(48, 'MFN.4', 8, 'Provision Generate Interest Rate For Due Member', '5', 'select', 4, NULL, 1, 0, '2023-06-12 10:45:07', 1, '2023-06-11 22:45:07', NULL),
(49, 'MFN.5', 8, 'Savings Accounts Min Duration For Provision (Month)', '5', 'text', 5, NULL, 1, 0, '2023-06-12 10:49:23', 1, '2023-06-12 03:53:40', 1),
(50, 'MFN.6', 8, 'Minimum Account Balance For Provision', '5', 'text', 6, NULL, 1, 0, '2023-06-12 10:58:08', 1, '2023-06-11 22:58:39', 1),
(51, 'MFN.7', 8, 'Provision Generate', '5', 'select', 7, NULL, 1, 0, '2023-06-12 10:59:52', 1, '2023-06-11 22:59:52', NULL),
(52, 'MFN.8', 8, 'Provision Calculation From', '5', 'select', 8, NULL, 1, 0, '2023-06-12 11:05:23', 1, '2023-06-11 23:05:23', NULL),
(53, 'MFN.9', 8, 'Check Provision Generate Before Day End', '5', 'select', 9, NULL, 1, 0, '2023-06-12 11:08:01', 1, '2023-06-11 23:08:01', NULL),
(54, 'MFN.10', 8, 'Interest Payment', '5', 'select', 10, NULL, 1, 0, '2023-06-12 11:09:46', 1, '2023-06-11 23:09:46', NULL),
(55, 'POS.4', 1, 'Office copy show in invoice ?', '2', 'select', 9, 'Multiple copy disable/enable in invoice/chalan/bill/memo', 1, 0, '2023-07-11 09:25:03', 1, '2023-07-10 21:25:03', NULL),
(56, 'HR.1', 3, 'Employee Type', '13', 'select', 1, 'Employee Type', 1, 0, '2023-06-21 09:44:10', 1, '2023-07-15 22:25:36', 1),
(57, 'HR.2', 3, 'Salary Method', '13', 'select', 1, 'Salary Method', 1, 0, '2023-06-21 09:49:57', 1, '2023-07-15 22:25:52', 1),
(58, 'HR.3', 3, 'Payroll Setting', '13', 'checkbox', 1, 'Payroll Setting Data', 1, 0, '2023-06-21 09:53:32', 1, '2023-06-20 21:53:32', NULL),
(59, 'POS.5', 1, 'POS Nature', '2', 'select', 1, 'Warehouse / Single Outlet', 1, 0, '2023-08-14 11:18:55', 1, '2023-08-13 23:20:05', 1),
(60, 'POS.2', 4, 'Sales Type', '2', 'select', 1, 'Which type of sales - cash/installment/due/online', 1, 0, '2023-08-14 11:27:50', 1, '2023-08-13 23:27:50', NULL),
(61, 'POS.3', 4, 'Sales Return Type', '2', 'select', 1, 'Return type Sales Return/Sales Replacement', 1, 0, '2023-08-14 11:32:27', 1, '2023-08-13 23:32:27', NULL),
(62, 'MFN.11', 8, 'Day of Month Year', '5', 'select', 11, NULL, 1, 0, '2023-06-12 11:09:46', 1, '2023-06-11 23:09:46', NULL);
COMMIT;

-- Dynamic form type, form, value table merge on 10/10/2023 for all module.
-- Merged by Md Mozahid Hossain

----------------------------------------------------
        --   Modifcation After Merging (10/10/2023)
----------------------------------------------------
