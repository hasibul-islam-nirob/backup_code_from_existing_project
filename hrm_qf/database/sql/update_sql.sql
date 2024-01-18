-- 22-06-2022 Saurav
ALTER TABLE `hr_emp_personal_details` CHANGE `gender` `geNder` ENUM('Male','Female','Others')
    CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'This column is not valid,now gender is inm employee master table.';

ALTER TABLE `hr_employees` ADD `gender` ENUM('Male','Female','Others') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
    COMMENT 'this column is active' AFTER `department_id`;

UPDATE `hr_employees` as em SET em.`gender`= (select ep.gender from hr_emp_personal_details as ep where ep.emp_id = em.id);


-- 27-07-22 ---- Tuli

ALTER TABLE `acc_year_end_balance_d`
ADD `cumulative_debit_amount` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `ft_credit`,
ADD `cumulative_credit_amount` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `cumulative_debit_amount`,
ADD `cumulative_balance_amount` DECIMAL(20,2) NOT NULL DEFAULT '0.00' AFTER `cumulative_credit_amount`,
ADD `cumulative_cash_debit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `cumulative_balance_amount`,
ADD `cumulative_cash_credit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `cumulative_cash_debit`,
ADD `cumulative_bank_debit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `cumulative_cash_credit`,
ADD `cumulative_bank_credit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `cumulative_bank_debit`,
ADD `cumulative_jv_debit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `cumulative_bank_credit`,
ADD `cumulative_jv_credit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `cumulative_jv_debit`,
ADD `cumulative_ft_debit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `cumulative_jv_credit`,
ADD `cumulative_ft_credit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `cumulative_ft_debit`;

-- ALTER TABLE `acc_year_end_balance_d`
-- ADD `cumulative_balance_amount` DECIMAL(20,2) NOT NULL DEFAULT '0.00' AFTER `cumulative_credit_amount`;




UPDATE `acc_year_end_balance_d` SET `cumulative_debit_amount` = `debit_amount`;
UPDATE `acc_year_end_balance_d` SET `cumulative_credit_amount` = `credit_amount`;
UPDATE `acc_year_end_balance_d` SET `cumulative_cash_debit` = `cash_debit`;
UPDATE `acc_year_end_balance_d` SET `cumulative_cash_credit` = `cash_credit`;
UPDATE `acc_year_end_balance_d` SET `cumulative_bank_debit` = `bank_debit`;
UPDATE `acc_year_end_balance_d` SET `cumulative_bank_credit` = `bank_credit`;
UPDATE `acc_year_end_balance_d` SET `cumulative_jv_debit` = `jv_debit`;
UPDATE `acc_year_end_balance_d` SET `cumulative_jv_credit` = `jv_credit`;
UPDATE `acc_year_end_balance_d` SET `cumulative_ft_debit` = `ft_debit`;
UPDATE `acc_year_end_balance_d` SET `cumulative_ft_credit` = `ft_credit`;
UPDATE `acc_year_end_balance_d` SET `cumulative_balance_amount` = `balance_amount`;




ALTER TABLE `acc_year_end_balance_m`
ADD `total_cumulative_debit_amount` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `total_ft_credit`,
ADD `total_cumulative_credit_amount` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `total_cumulative_debit_amount`,
ADD `total_cumulative_cash_debit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `total_cumulative_credit_amount`,
ADD `total_cumulative_cash_credit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `total_cumulative_cash_debit`,
ADD `total_cumulative_bank_debit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `total_cumulative_cash_credit`,
ADD `total_cumulative_bank_credit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `total_cumulative_bank_debit`,
ADD `total_cumulative_jv_debit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `total_cumulative_bank_credit`,
ADD `total_cumulative_jv_credit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `total_cumulative_jv_debit`,
ADD `total_cumulative_ft_debit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `total_cumulative_jv_credit`,
ADD `total_cumulative_ft_credit` DECIMAL(20,2) NOT NULL DEFAULT '0.00'  AFTER `total_cumulative_ft_debit`;




UPDATE `acc_year_end_balance_m` SET `total_cumulative_debit_amount` = `total_debit_amount`;
UPDATE `acc_year_end_balance_m` SET `total_cumulative_credit_amount` = `total_credit_amount`;
UPDATE `acc_year_end_balance_m` SET `total_cumulative_cash_debit` = `total_cash_debit`;
UPDATE `acc_year_end_balance_m` SET `total_cumulative_cash_credit` = `total_cash_credit`;
UPDATE `acc_year_end_balance_m` SET `total_cumulative_bank_debit` = `total_bank_debit`;
UPDATE `acc_year_end_balance_m` SET `total_cumulative_bank_credit` = `total_bank_credit`;
UPDATE `acc_year_end_balance_m` SET `total_cumulative_jv_debit` = `total_jv_debit`;
UPDATE `acc_year_end_balance_m` SET `total_cumulative_jv_credit` = `total_jv_credit`;
UPDATE `acc_year_end_balance_m` SET `total_cumulative_ft_debit` = `total_ft_debit`;
UPDATE `acc_year_end_balance_m` SET `total_cumulative_ft_credit` = `total_ft_credit`;



-- 04-12-2022 Tuli
INSERT INTO `gnl_terms_type` (`id`, `type_title`) VALUES (NULL, 'MIP/SS Certificate Conditions (For Front Print)');
INSERT INTO `gnl_terms_type` (`id`, `type_title`) VALUES (NULL, 'MIP/SS Certificate Instructions (For Back Print)');

--07-02-2023 Rana
UPDATE `mfn_config` SET `content` = '{\"memberCodeLengthItSelf\":\"4\",\"mraCodeMaxLength\":\"20\",\"minAge\":\"18\",\"maxAge\":\"60\",\"admissionFee\":\"0\",\"passbookFee\":\"10\",\"countryCode\":\"+88\",\"mobileNoLength\":\"11\",\"passportLength\":\"10\",\"nationalIdLength\":[\"10\",\"13\",\"17\"],\"profileImageSize\":\"400:300\",\"signatureImageSize\":\"150:100\",\"isProfileImageMandatory\":\"no\",\"isSignatureImageMandatory\":\"no\"}' WHERE `mfn_config`.`id` = 4;
ALTER TABLE `mfn_member_details` ADD `passbookFee` DECIMAL(8,2) NULL DEFAULT NULL AFTER `admissionFee`;
UPDATE `mfn_config` SET `content` = '{\"memberCodeLengthItSelf\":\"4\",\"mraCodeMaxLength\":\"20\",\"minAge\":\"18\",\"maxAge\":\"60\",\"admissionFee\":\"0\",\"passbookFee\":\"10\",\"closingFee\":\"20\",\"countryCode\":\"+88\",\"mobileNoLength\":\"11\",\"passportLength\":\"10\",\"nationalIdLength\":[\"10\",\"13\",\"17\"],\"profileImageSize\":\"400:300\",\"signatureImageSize\":\"150:100\",\"isProfileImageMandatory\":\"no\",\"isSignatureImageMandatory\":\"no\"}' WHERE `mfn_config`.`id` = 4;
ALTER TABLE `mfn_member_closings` ADD `closingFee` DECIMAL(8,2) NULL DEFAULT NULL AFTER `closingBalance`;

--12-02-2023 Rana
UPDATE `mfn_config` SET `content` = '{\r\n    \"samityId\": \"required\",\r\n    \"name\": \"required\",\r\n    \"surName\": \"not-required\",\r\n    \"gender\": \"required\",\r\n    \"maritalStatusId\": \"required\",\r\n    \"educationLevelId\": \"required\",\r\n    \"dateOfBirth\": \"required\",\r\n    \"member_age\": \"not-required\",\r\n    \"fatherName\": \"required\",\r\n    \"motherName\": \"required\",\r\n    \"sonName\": \"not-required\",\r\n    \"spouseName\": \"required\",\r\n    \"nationalityId\": \"required\",\r\n    \"mobileNo\": \"required\",\r\n    \"email\": \"not-required\",\r\n    \"formApplicationNo\": \"not-required\",\r\n    \"firstEvidenceTypeId\": \"required\",\r\n    \"firstEvidence\": \"required\",\r\n    \"firstEvidenceIssuerCountryId\": \"not-required\",\r\n    \"secondEvidenceTypeId\": \"not-required\",\r\n    \"secondEvidenceIssuerCountryId\": \"not-required\",\r\n    \"secondEvidence\": \"not-required\",\r\n    \"secondEvidenceValidTill\": \"not-required\",\r\n    \"admissionDate\": \"required\",\r\n    \"admissionFee\": \"not-required\",\r\n    \"primaryProductId\": \"required\",\r\n    \"admissionNo\": \"not-required\",\r\n    \"preDivisionId\": \"not-required\",\r\n    \"preDistrictId\": \"not-required\",\r\n    \"preUpazilaId\": \"not-required\",\r\n    \"preUnionId\": \"not-required\",\r\n    \"preVillageId\": \"not-required\",\r\n    \"preStreetHolding\": \"not-required\",\r\n    \"familyContactNumber\": \"not-required\",\r\n    \"perDivisionId\": \"required\",\r\n    \"perDistrictId\": \"required\",\r\n    \"perUpazilaId\": \"required\",\r\n    \"perUnionId\": \"required\",\r\n    \"perVillageId\": \"not-required\",\r\n    \"perStreetHolding\": \"not-required\",\r\n    \"nomineeNames\": \"not-required\",\r\n    \"nomineeMobileNos\": \"not-required\",\r\n    \"nomineeRelationships\": \"not-required\",\r\n    \"nomineeShares\": \"not-required\",\r\n    \"referenceNames\": \"not-required\",\r\n    \"referenceRelationships\": \"not-required\",\r\n    \"referenceOrganizations\": \"not-required\",\r\n    \"referenceDesignations\": \"not-required\",\r\n    \"referenceMobileNos\": \"not-required\",\r\n    \"professionId\": \"required\",\r\n    \"religionId\": \"required\",\r\n    \"numberOfFamilyMember\": \"not-required\",\r\n    \"yearlyIncome\": \"not-required\",\r\n    \"landArea\": \"not-required\"\r\n\r\n}' WHERE `mfn_config`.`id` = 14;

-- 05-02-2023 Tuli
ALTER TABLE `mfn_member_primary_product_transfers` ADD `isAuthorized` TINYINT(1) NOT NULL DEFAULT '0' AFTER `note`;



INSERT INTO `mfn_auto_voucher_components` (`id`, `title`, `voucherType`, `relatedTo`, `otherHandRelatedTo`, `isOtherHandCommon`, `note`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `is_delete`) VALUES (NULL, 'Member Passbook', 'Credit', 'loanProduct', 'loanProduct', '0', 'it relates to the \'passbook\' of \'mfn_member_details\' table', '1', '1', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '0');

INSERT INTO `mfn_auto_voucher_components` (`id`, `title`, `voucherType`, `relatedTo`, `otherHandRelatedTo`, `isOtherHandCommon`, `note`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `is_delete`) VALUES (NULL, 'Member Closing Fee', 'Credit', 'loanProduct', 'loanProduct', '0', 'it relates to the \'closingFee\' of \'mfn_member_details\' table', '1', '1', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '0');
--07-03-2023 Rana
UPDATE `mfn_config` SET `content` = '{\"memberCodeLengthItSelf\":\"4\",\"mraCodeMaxLength\":\"20\",\"minAge\":\"18\",\"maxAge\":\"60\",\"admissionFee\":\"10\",\"passbookFee\":\"10\",\"closingFee\":\"20\",\"countryCode\":\"+88\",\"mobileNoLength\":\"11\",\"passportLength\":\"10\",\"nationalIdLength\":[\"10\",\"13\",\"17\"],\"profileImageSize\":\"400:300\",\"signatureImageSize\":\"150:100\",\"isProfileImageMandatory\":\"no\",\"isSignatureImageMandatory\":\"no\"}' WHERE `mfn_config`.`id` = 4;
-- 11-02-2023 Tuli
ALTER TABLE `mfn_savings_provision`  ADD `productTypeId` INT(11) NOT NULL  AFTER `branchId`;
ALTER TABLE `mfn_savings_provision_details` ADD `productTypeId` INT(11) NOT NULL AFTER `branchId`;
ALTER TABLE `mfn_savings_provision_details` ADD `productId` INT(11) NOT NULL AFTER `productTypeId`;
ALTER TABLE `gnl_company_config` CHANGE `form_id` `form_id` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

--25-03-2023 Rana
 ALTER TABLE `gnl_branchs` ADD `independent_branch_date` DATE NOT NULL DEFAULT '0000-00-00' AFTER `is_approve`;

--25-03-2023 Rana
 ALTER TABLE `gnl_branchs` ADD `independent_branch_date` DATE NOT NULL DEFAULT '0000-00-00' AFTER `is_approve`;
--17-04-2023 Rana
ALTER TABLE `gnl_regions` DROP `zone_arr`, DROP `branch_arr`;
ALTER TABLE `gnl_zones` DROP `branch_arr` , DROP `area_arr`;
ALTER TABLE `gnl_zones` ADD `region_arr` TEXT NULL DEFAULT NULL AFTER `zone_code`;

--29-04-2023 Rana
ALTER TABLE `gnl_branchs` ADD `area_id` INT(11) NULL DEFAULT NULL AFTER `is_approve`;
ALTER TABLE `gnl_branchs` ADD `region_id` INT(11) NULL DEFAULT NULL AFTER `area_id`;
ALTER TABLE `gnl_branchs` ADD `zone_id` INT(11) NULL DEFAULT NULL AFTER `region_id`;

--25-03-2023 Rana
 ALTER TABLE `gnl_branchs` ADD `independent_branch_date` DATE NOT NULL DEFAULT '0000-00-00' AFTER `is_approve`;

--03-04-2023 Rana
ALTER TABLE `mfn_provision_config` CHANGE `effectiveDate` `effectiveDateStart` DATE NULL DEFAULT NULL;
ALTER TABLE `mfn_provision_config` ADD `effectiveDateEnd` DATE NULL DEFAULT NULL AFTER `effectiveDateStart`;

--06-05-2023 Rana
ALTER TABLE `mfn_loan_rebates` ADD `isAuthorized` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_delete`;
ALTER TABLE `mfn_loan_waivers` ADD `isAuthorized` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_delete`;
ALTER TABLE `mfn_loan_writeoffs` ADD `isAuthorized` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_delete`;
ALTER TABLE `mfn_member_samity_transfers` ADD `isAuthorized` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_delete`;

-- 19-10-23 Tuli
ALTER TABLE `hr_employees`  ADD `emp_type` INT NULL DEFAULT NULL COMMENT '1 = House Tutor, \r\n2 = Others'  AFTER `department_id`;
ALTER TABLE `hms_seat`  ADD `emp_id` INT(11) NULL DEFAULT NULL COMMENT 'Employee Id of House Tutor'  AFTER `ht_id`;

------------------ 17-10-23 Tuli
-- Add 4 new COlumn to the adjustment table
ALTER TABLE `mfn_loan_adjustments`  ADD `loanProductId` INT(11) NULL DEFAULT NULL  AFTER `isAuthorized`,
ADD `principalAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `loanProductId`,
ADD `interestAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `principalAmount`,
ADD `savingsProductId` INT(11) NULL DEFAULT NULL  AFTER `interestAmount`;

-- 19-10-23 Arnab
ALTER TABLE `hms_house_tutor` ADD `gender` VARCHAR(50) NULL DEFAULT NULL AFTER `mobile_no`, ADD `emp_id` INT(11) NULL DEFAULT NULL AFTER `gender`;

------------------ 17-10-23 Tuli
-- Add 4 new COlumn to the adjustment table
ALTER TABLE `mfn_loan_adjustments`  ADD `loanProductId` INT(11) NULL DEFAULT NULL  AFTER `isAuthorized`,
ADD `principalAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `loanProductId`,
ADD `interestAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `principalAmount`,
ADD `savingsProductId` INT(11) NULL DEFAULT NULL  AFTER `interestAmount`;

ALTER TABLE `mfn_loan_adjustments`  ADD `loanProductId` INT(11) NULL DEFAULT NULL  AFTER `isAuthorized`,
ADD `principalAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `loanProductId`,
ADD `interestAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `principalAmount`,
ADD `savingsProductId` INT(11) NULL DEFAULT NULL  AFTER `interestAmount`;

ALTER TABLE `mfn_loan_adjustments`  ADD `loanProductId` INT(11) NULL DEFAULT NULL  AFTER `isAuthorized`,
ADD `principalAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `loanProductId`,
ADD `interestAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `principalAmount`,
ADD `savingsProductId` INT(11) NULL DEFAULT NULL  AFTER `interestAmount`;

-- 19-10-23 Arnab
ALTER TABLE `hms_house_tutor` ADD `gender` VARCHAR(50) NULL DEFAULT NULL AFTER `mobile_no`, ADD `emp_id` INT(11) NULL DEFAULT NULL AFTER `gender`;

------------------ 17-10-23 Tuli
-- Add 4 new COlumn to the adjustment table
-- ALTER TABLE `mfn_loan_adjustments`  ADD `loanProductId` INT(11) NULL DEFAULT NULL  AFTER `isAuthorized`,
-- ADD `principalAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `loanProductId`,
-- ADD `interestAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `principalAmount`,
-- ADD `savingsProductId` INT(11) NULL DEFAULT NULL  AFTER `interestAmount`;

-- ALTER TABLE `mfn_loan_adjustments`  ADD `loanProductId` INT(11) NULL DEFAULT NULL  AFTER `isAuthorized`,
-- ADD `principalAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `loanProductId`,
-- ADD `interestAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `principalAmount`,
-- ADD `savingsProductId` INT(11) NULL DEFAULT NULL  AFTER `interestAmount`;

-- ALTER TABLE `mfn_loan_adjustments`  ADD `loanProductId` INT(11) NULL DEFAULT NULL  AFTER `isAuthorized`,
-- ADD `principalAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `loanProductId`,
-- ADD `interestAmount` DECIMAL(13,5) NOT NULL DEFAULT '0.00'  AFTER `principalAmount`,
-- ADD `savingsProductId` INT(11) NULL DEFAULT NULL  AFTER `interestAmount`;

-- Update LoanProductId
UPDATE `mfn_loan_adjustments` as adj SET adj.`loanProductId`= (
    SELECT ml.productId FROM `mfn_loans` as ml WHERE adj.loanId = ml.id AND ml.is_delete = 0
)

-- UPDATE `mfn_loan_adjustments` as adj SET adj.`loanProductId`= (
--     SELECT sw.primaryProductId FROM `mfn_savings_withdraw` as sw WHERE adj.withdrawId = sw.id AND sw.is_delete = 0
--     AND sw.transactionTypeId = 10 AND adj.date = sw.date
-- )

-- Update SavingsProductId
UPDATE `mfn_loan_adjustments` as adj SET adj.savingsProductId = (
    SELECT acc.savingsProductId FROM `mfn_savings_accounts` as acc WHERE adj.accountId = acc.id AND acc.is_delete = 0
)
-- Update Principal Amount
UPDATE `mfn_loan_adjustments` as adj SET adj.`principalAmount`= (
    SELECT mlc.principalAmount FROM `mfn_loan_collections` as mlc WHERE adj.collectionId = mlc.id
)

-- Update Interest Amount
UPDATE `mfn_loan_adjustments` as adj SET adj.`interestAmount`= (
    SELECT mlc.interestAmount FROM `mfn_loan_collections` as mlc WHERE adj.collectionId = mlc.id
)
----------- 17-10-23 END

--------Rana 30-10-23---------
UPDATE `mfn_savings_provision` SET `productId`= 7 WHERE `productId` is null
-------------------------------------------------
------------------ 30-10-23 Tuli
UPDATE `mfn_savings_deposit` SET `is_delete` = '1' WHERE `mfn_savings_deposit`.`id` = 1130409;
UPDATE `mfn_savings_deposit` SET `is_delete` = '1' WHERE `mfn_savings_deposit`.`id` = 1130427;

SELECT * FROM `mfn_loan_adjustments` WHERE `loanId` = 38266 AND savingsProductId = 6

-- Loan Adjustment
SELECT mla.* , ml.primaryProductId as loanPrimary, msw.primaryProductId as withdrawPrimary, sum(mla.amount)
FROM `mfn_loan_adjustments` as mla
join mfn_loans as ml on (ml.id = mla.loanId)
join mfn_savings_withdraw as msw on (msw.id = mla.withdrawId)
WHERE mla.is_delete = 0
and ml.primaryProductId <> msw.primaryProductId
-- GROUP BY mla.loanId
-------------Rana 31-10-2023---------------
ALTER TABLE `mfn_loan_adjustments` ADD `savingsPrimaryProductId` INT(11) NULL DEFAULT NULL AFTER `savingsProductId`;

UPDATE `mfn_loan_adjustments` as adj SET adj.savingsPrimaryProductId = (
    SELECT acc.primaryProductId FROM `mfn_savings_withdraw` as acc WHERE adj.withdrawId = acc.id AND acc.is_delete = 0
);
-----------------------------------------------

-------- Nirob - 13-11-2023 -------------------
UPDATE `hr_app_movements`
SET
    `start_time` = DATE_FORMAT(DATE_ADD(`start_time`, INTERVAL 12 HOUR), '%Y-%m-%d %H:%i:%s'),
    `application_for` = 'early'
WHERE HOUR(`start_time`) BETWEEN 1 AND 6;
-----------------------------------------------

----------------- Nirob - 20-11-2023 -----------------------------
/** This query for get Action for Root Menu Select All Item */
SELECT gnl_sys_menus.*, gnl_user_permissions.*
FROM gnl_sys_menus JOIN gnl_user_permissions ON gnl_sys_menus.id = gnl_user_permissions.menu_id
WHERE gnl_sys_menus.module_id = 13 AND gnl_sys_menus.parent_menu_id = 0

/** This query for get Action for Root Menu */
SELECT gnl_user_permissions.* FROM gnl_sys_menus
JOIN gnl_user_permissions ON gnl_sys_menus.id = gnl_user_permissions.menu_id
WHERE
    gnl_sys_menus.module_id = 13
AND gnl_sys_menus.parent_menu_id = 0
AND gnl_sys_menus.id <> 471
ORDER BY `menu_id` DESC;

/*
Not Use it => // This query for delete Action for Root Menu
DELETE gnl_user_permissions
FROM gnl_sys_menus
JOIN gnl_user_permissions ON gnl_sys_menus.id = gnl_user_permissions.menu_id
WHERE gnl_sys_menus.module_id = 13 AND gnl_sys_menus.parent_menu_id = 0;
*/

/* This is final Query  */
/** This query for delete Action for Root Menu - With avoid specific id */
DELETE gnl_user_permissions
FROM gnl_sys_menus
JOIN gnl_user_permissions ON gnl_sys_menus.id = gnl_user_permissions.menu_id
WHERE gnl_sys_menus.module_id = 13
  AND gnl_sys_menus.parent_menu_id = 0
  AND gnl_sys_menus.id <> 471;

----------------------------------------

DELETE FROM `gnl_user_permissions` where menu_id in (SELECT DISTINCT parent_menu_id FROM `gnl_sys_menus` where parent_menu_id <> 0);
