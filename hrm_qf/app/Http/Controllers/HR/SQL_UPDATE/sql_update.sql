
----------------------------------------------
-- Software Update date is 31-10-2023 Finished
----------------------------------------------
ALTER TABLE `hr_app_movements` CHANGE `application_for` `application_for` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'late, absent, early';


ALTER TABLE `hr_app_movements` CHANGE `appl_date` `appl_date` DATE NULL DEFAULT NULL COMMENT 'deprecate column', CHANGE `exp_effective_date` `exp_effective_date` DATE NULL DEFAULT NULL COMMENT 'deprecate column', CHANGE `effective_date` `effective_date` DATE NULL DEFAULT NULL COMMENT 'deprecate column';


ALTER TABLE `hr_app_movements` CHANGE `reason` `reason` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Application Purpose / Reason';
ALTER TABLE `hr_app_movements` DROP `application_reason`;
