--
-- Table structure for table `gnl_dynamic_form_type`
--

CREATE TABLE `gnl_dynamic_form_type` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `is_active` tinyint(2) NOT NULL DEFAULT '1',
  `is_delete` tinyint(2) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `gnl_dynamic_form_type`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `gnl_dynamic_form_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
-- COMMIT;

--
-- Dumping data for table `gnl_dynamic_form_type`
--
INSERT INTO `gnl_dynamic_form_type` (`id`, `name`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 'Company Configuration', 1, 0, '2020-10-11 17:02:45', 1, '2020-10-11 05:17:09', 1),
(2, 'General Configuration', 1, 0, '2020-10-11 17:17:42', 1, '2020-10-11 05:17:42', NULL),
(3, 'Static Data (HR)', 1, 0, '2021-11-28 16:21:59', 1, '2021-12-04 22:12:11', 1),
(4, 'Static Data(POS)', 1, 0, '2022-01-30 13:03:01', 1, '2022-01-30 01:03:01', NULL),
(5, 'Website Information', 1, 0, '2022-04-06 12:27:43', 1, '2022-04-06 00:27:43', NULL),
(6, 'Static Data(ACC)', 1, 0, '2023-02-07 16:09:29', 1, '2023-02-07 04:09:29', NULL),
(7, 'Static Data(INV)', 1, 0, '2023-02-07 16:09:38', 1, '2023-02-07 04:09:38', NULL),
(8, 'Static Data(MFN)', 1, 0, '2023-02-07 16:09:50', 1, '2023-02-07 04:09:50', NULL),
(9, 'Static Data(FAM)', 1, 0, '2023-02-07 16:10:10', 1, '2023-02-07 04:10:10', NULL),
(10, 'Static Data (HMS)', 1, 0, '2023-07-09 14:53:08', 1, '2023-07-09 08:53:08', NULL);
COMMIT;

-- Dynamic form type, form, value table merge on 10/10/2023 for all module.
-- Merged by Md Mozahid Hossain

----------------------------------------------------
        --   Modifcation After Merging (10/10/2023)
----------------------------------------------------


