
-- Mozahid Hossain - HR - 23-08-2023
INSERT INTO `gnl_user_permissions` (`name`, `route_link`, `page_title`, `bn_name`, `method_name`, `menu_id`, `order_by`, `set_status`, `notes`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`)
VALUES ('New Entry', 'hr/relation/add', 'Entry', NULL, 'add', '641', '1', '1', NULL, '1', '0', '2023-08-23 14:47:58', '1', NULL, NULL),
('Edit', 'hr/relation/edit', 'Update', NULL, 'edit', '641', '2', '2', NULL, '1', '0', '2023-08-23 14:47:58', '1', NULL, NULL);

INSERT INTO `gnl_user_permissions` (`name`, `route_link`, `page_title`, `bn_name`, `method_name`, `menu_id`, `order_by`, `set_status`, `notes`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`)
VALUES ('Delete', 'hr/relation/delete', NULL, NULL, 'delete', '641', '6', '6', NULL, '1', '0', '2023-08-23 14:47:58', '1', NULL, NULL),
('All Data', 'hr/relation', NULL, NULL, 'all_data', '641', '8', '8', NULL, '1', '0', '2023-08-23 14:47:58', '1', NULL, NULL),
('Publish', 'hr/relation/publish', NULL, NULL, 'isActive', '641', '14', '4', NULL, '0', '0', '2023-08-23 14:47:58', '1', NULL, NULL),
('Unpublish', 'hr/relation/publish', NULL, NULL, 'isActive', '641', '15', '5', NULL, '0', '0', '2023-08-23 14:47:58', '1', NULL, NULL);


-- Hasibul Islam Nirob - HR - 16/09/2023
ALTER TABLE `gnl_user_permissions` ADD `module_id` INT NULL AFTER `method_name`; 