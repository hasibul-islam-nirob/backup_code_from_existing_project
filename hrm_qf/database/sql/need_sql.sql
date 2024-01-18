-----------------------------------------------------------------------------------------------------
-- -- -- Arrange auto increment primary key
-----------------------------------------------------------------------------------------------------
SET  @num := 0;
UPDATE acc_voucher_details SET id = @num := (@num+1);
ALTER TABLE acc_voucher_details AUTO_INCREMENT =1;

Delete from acc_voucher_details where not exists (select 1 from acc_voucher where acc_voucher_details.voucher_id = acc_voucher.id)

-----------------------------------------------------------------------------------------------------
-- Voucher Primary & foriegn Key Collumn asc
-----------------------------------------------------------------------------------------------------

ALTER TABLE `acc_voucher` ADD `id_new` INT NOT NULL AFTER `id`;
SET  @num := 0;
UPDATE acc_voucher SET `id_new` = @num := (@num+1);

ALTER TABLE `acc_voucher_details` ADD `voucher_id_new` INT NOT NULL AFTER `id`;
UPDATE `acc_voucher_details` SET `voucher_id_new`= (select id_new from acc_voucher where acc_voucher_details.voucher_id = acc_voucher.id)

ALTER TABLE `acc_voucher_details` DROP `voucher_id`;
ALTER TABLE `acc_voucher_details` CHANGE `voucher_id_new` `voucher_id` INT(11) NOT NULL;
ALTER TABLE `acc_voucher` DROP `id`;
ALTER TABLE `acc_voucher` CHANGE `id_new` `id` INT(11) NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`);
------------------------------------- End ----------------------

-----------------------------------------------------------------------------------------------------
        --   Dynamic form table start
-----------------------------------------------------------------------------------------------------
delete t1 from gnl_dynamic_form as t1
INNER JOIN gnl_dynamic_form as t2
WHERE
t1.id < t2.id AND
t1.uid = t2.uid AND
t1.type_id = t2.type_id AND
t1.name = t2.name AND
t1.module_id = t2.module_id AND
t1.input_type = t2.input_type AND
t1.is_active = t2.is_active AND
t1.is_delete = t2.is_delete;



SELECT  *, count(*) FROM gnl_dynamic_form
GROUP BY
     uid,
     type_id,
     name,
     module_id,
     input_type,
     is_active,
     is_delete
HAVING
     count(*) > 1;


delete t1 from gnl_dynamic_form_value as t1
INNER JOIN gnl_dynamic_form_value as t2
WHERE
t1.id < t2.id AND
t1.uid = t2.uid AND
t1.type_id = t2.type_id AND
t1.form_id = t2.form_id AND
t1.name = t2.name AND
t1.value_field = t2.value_field AND
t1.is_active = t2.is_active AND
t1.is_delete = t2.is_delete;

SELECT  *, count(*) FROM gnl_dynamic_form_value
GROUP BY
     uid,
     type_id,
     form_id,
     name,
     value_field,
     is_active,
     is_delete
HAVING
     count(*) > 1;


SET  @num := 0;
UPDATE gnl_dynamic_form_value SET id = @num := (@num+1);
ALTER TABLE gnl_dynamic_form_value AUTO_INCREMENT =1;

select t1.* FROM `gnl_dynamic_form_value` as t1 where t1.form_id not in (SELECT t2.uid from gnl_dynamic_form as t2 where t2.type_id = t1.type_id);

UPDATE `gnl_dynamic_form_value` SET `name` = 'assets/images/advanceLogo/Advance.png' WHERE `form_id` LIKE 'GCONF.1';
UPDATE `gnl_dynamic_form_value` SET `name` = 'assets/images/advanee_icon_white.png' WHERE `form_id` LIKE 'GCONF.3';

-----------------------------------------------------------------------------------------------------
        --   Dynamic form table End
-----------------------------------------------------------------------------------------------------

-----------------------------------------------------------------------------------------------------
        --   POS Product Settings
-----------------------------------------------------------------------------------------------------

DELETE FROM `pos_p_brands` WHERE `id` > 1;
UPDATE `pos_products` SET `prod_brand_id` = 1;

DELETE FROM `pos_p_colors` WHERE `id` > 1;
UPDATE `pos_p_colors` SET `prod_group_id` = NULL, `prod_cat_id` = NULL, `prod_sub_cat_id` = NULL, `updated_at` = NULL WHERE `pos_p_colors`.`id` = 1;
UPDATE `pos_products` SET `prod_color_id` = 1;

DELETE FROM `pos_p_sizes` WHERE `id` > 1;
UPDATE `pos_p_sizes` SET `prod_group_id` = NULL, `prod_cat_id` = NULL, `prod_sub_cat_id` = NULL, `updated_at` = NULL WHERE `pos_p_sizes`.`id` = 1;
UPDATE `pos_products` SET `prod_size_id` = 1;

UPDATE `pos_p_uoms` SET `id` = '22', `uom_name` = 'N/A', `updated_at` = NULL WHERE `pos_p_uoms`.`id` = 2;
UPDATE `pos_p_uoms` SET `id` = '2' WHERE `pos_p_uoms`.`id` = 1;
UPDATE `pos_p_uoms` SET `id` = '1' WHERE `pos_p_uoms`.`id` = 22;
UPDATE `pos_p_uoms` SET `is_delete` = '0' WHERE `pos_p_uoms`.`id` = 1;
UPDATE `pos_products` SET `prod_uom_id` = 2;

DELETE FROM `pos_p_models` WHERE `id` NOT IN (1,8,11,13,14,17,18,21,22,24,25,34);
UPDATE `pos_p_models` SET `prod_group_id` = NULL, `prod_cat_id` = NULL, `prod_sub_cat_id` = NULL, `model_name` = 'N/A', `updated_at` = NULL WHERE `pos_p_models`.`id` = 1;
UPDATE `pos_products` SET `prod_model_id` = 1 WHERE `prod_model_id` NOT IN (1,8,11,13,14,17,18,21,22,24,25,34);


SELECT *  FROM `pos_products` WHERE `prod_model_id` NOT IN (1,8,11,13,14,17,18,21,22,24,25,34);

-------------------------------
-- SELECT REPLACE("SQL Tutorial", "SQL", "HTML");
-- SELECT *  FROM `acc_account_ledger` WHERE `branch_arr` LIKE '%3-0%'

UPDATE `acc_account_ledger` SET `branch_arr`= (select REPLACE(branch_arr, "3-0,", "1-0,")) WHERE `branch_arr` LIKE '3-0,%';

-- UPDATE `acc_account_ledger` SET `project_arr`= 0 WHERE
-- (`project_arr` LIKE '0,%' OR `project_arr` LIKE '%,0,%' OR `project_arr` LIKE '%,0' OR `project_arr` LIKE '0');


UPDATE `pos_shop_sales_m` as pom SET pom.`total_cost_amount`= (select ifnull(sum(pod.total_cost_price), 0) from pos_shop_sales_d as pod where pod.sales_bill_no = pom.sales_bill_no) WHERE pom.`total_cost_amount` = 0

SELECT *  FROM `pos_sales_m`
WHERE `sales_type` LIKE '2'
	AND `sales_date` < '2021-02-28'
	AND (`complete_date` IS NULL OR `complete_date` >= '2021-02-28')
	AND `is_active` = 1
	AND `is_delete` = 0
	AND (
        `sales_date` NOT BETWEEN '2021-02-01' AND '2021-02-28' OR `installment_type` <> 1
    )

-- Query script for Double fund transfer transection remove for head office
-- Start

-------------------
SELECT id, voucher_code, voucher_date, ft_id, ft_from, branch_id, is_active, is_delete
    FROM `acc_voucher`
    WHERE id in (SELECT av.id
                    FROM `acc_voucher` as av
                    join acc_voucher_details as avd on (av.id = avd.voucher_id and avd.debit_acc = avd.credit_acc)
                    WHERE av.voucher_type_id = 5
                        and av.branch_id = 1
                        and av.is_active = 1
                        -- and av.is_delete= 0
                        and av.ft_id in (SELECT ft_id
                                        FROM `acc_voucher`
                                        WHERE `voucher_type_id` = 5
                                            AND `branch_id` = 1
                                            AND `is_active` = 1
                                            -- AND `is_delete` = 0
                                        GROUP BY ft_id
                                        HAVING count(id) > 1
                                        )
                )
              and is_delete = 0;
--------------------------
SELECT GROUP_CONCAT(id)
    FROM `acc_voucher`
    WHERE id in (SELECT av.id
                    FROM `acc_voucher` as av
                    join acc_voucher_details as avd on (av.id = avd.voucher_id and avd.debit_acc = avd.credit_acc)
                    WHERE av.voucher_type_id = 5
                        and av.branch_id = 1
                        and av.is_active = 1
                        and av.is_delete= 0
                        and av.ft_id in (SELECT ft_id
                                        FROM `acc_voucher`
                                        WHERE `voucher_type_id` = 5
                                            AND `branch_id` = 1
                                            AND `is_active` = 1
                                            AND `is_delete` = 0
                                        GROUP BY ft_id
                                        HAVING count(id) > 1
                                        )
                )
              and is_delete = 0;
-----------------------------------------
UPDATE `acc_voucher` SET `is_delete`= 2 WHERE `v_generate_type` = 0 AND `branch_id` = 1 AND `is_active` = 1 AND `is_delete` = 0

-----------------------
-- for acc check query
SELECT sum(avd.amount) FROM `acc_voucher_details` as avd
join acc_account_ledger as aal on (avd.credit_acc = aal.id and aal.`acc_type_id` = 5 AND aal.`is_group_head` = 0 AND aal.`is_active` = 1 AND aal.`is_delete` = 0)
WHERE avd.voucher_id in (SELECT av.id FROM `acc_voucher` as av WHERE av.`voucher_date` BETWEEN '2020-09-01' AND '2020-09-30' AND av.`voucher_status` > 0 AND av.`branch_id` = 1 AND av.`is_active` = 1 AND av.`is_delete` = 0
-- and av.voucher_type_id = 5
)

----------------------------------------------------- Offline POS problem---------------

UPDATE `pos_issues_d` SET `product_id`= (product_id - 1) where product_id > 1354;

UPDATE `pos_purchases_d` SET `product_id`= (product_id - 1) where product_id > 1354;
UPDATE `pos_purchases_r_d` SET `product_id`= (product_id - 1) where product_id > 1354;

---------------------------------- End -------------------

-- mfn all active members list (jader mobile no ache)
-- (last one year member with mobile no)
SELECT mb.id, mbd.mobileNo, mb.admissionDate
    FROM `mfn_members` as mb
    join mfn_member_details as mbd on mbd.memberId = mb.id
    WHERE mb.is_delete = 0
        and mb.closingDate LIKE "%0000-00-00"
        and mbd.mobileNo IS NOT NULL
        -- and mb.admissionDate > "2020-01-01";

--- member with loan (with mobile no is not null) 4 is the monthly loan 2 weekly
SELECT mb.id, mbd.mobileNo
	FROM `mfn_members` as mb

    JOIN mfn_loans as ml
    	on (ml.memberId = mb.id)
        and ml.is_delete = 0
        and ml.loanCompleteDate LIKE "0000-00-00"
        -- and ml.repaymentFrequencyId = 4

	join mfn_member_details as mbd on mbd.memberId = mb.id and mbd.mobileNo IS NOT NULL
    WHERE mb.is_delete = 0
    	and mb.closingDate LIKE "0000-00-00"
        -- and mb.admissionDate > "2020-01-01"
    -- GROUP BY mb.id

SELECT mb.id, mbd.mobileNo
	FROM `mfn_members` as mb

    JOIN mfn_loans as ml
     on (ml.memberId = mb.id)
       and ml.is_delete = 0
       and ml.loanCompleteDate LIKE "0000-00-00"
       and ml.repaymentFrequencyId = 4

    JOIN mfn_savings_accounts as msa
    on (msa.memberId = mb.id)
    and msa.is_delete = 0
    and msa.closingDate LIKE "0000-00-00"

	join mfn_member_details as mbd on mbd.memberId = mb.id and mbd.mobileNo IS NOT NULL
    WHERE mb.is_delete = 0
    	and mb.closingDate LIKE "0000-00-00"
        and mb.admissionDate > "2020-01-01"
     GROUP BY mb.id


     ------------------------------
SELECT
 -- mb.id,
 DISTINCT mbd.mobileNo
	FROM `mfn_members` as mb

    -- left JOIN mfn_loans as ml
     -- on (ml.memberId = mb.id)

    JOIN mfn_savings_accounts as msa
     on (msa.memberId = mb.id)

	join mfn_member_details as mbd on mbd.memberId = mb.id and mbd.mobileNo IS NOT NULL
    WHERE mb.is_delete = 0
    	and mb.closingDate LIKE "0000-00-00"
        and mb.admissionDate > "2020-01-01"

        -- and ml.is_delete = 0
       -- and ml.loanCompleteDate LIKE "0000-00-00"
       -- and ml.repaymentFrequencyId = 4

       and msa.is_delete = 0
      and msa.closingDate LIKE "0000-00-00"


    -- GROUP BY mb.id

-- sms send kora hoyeche
    SELECT
 -- mb.id,
 DISTINCT mbd.mobileNo
	FROM `mfn_members` as mb

     JOIN mfn_loans as ml
      on (ml.memberId = mb.id)

    -- JOIN mfn_savings_accounts as msa
     -- on (msa.memberId = mb.id)

	join mfn_member_details as mbd on mbd.memberId = mb.id and mbd.mobileNo IS NOT NULL
    WHERE mb.is_delete = 0
    	and mb.closingDate LIKE "0000-00-00"
        and mb.admissionDate > "2020-01-01"

         and ml.is_delete = 0
        and ml.loanCompleteDate LIKE "0000-00-00"
        and ml.repaymentFrequencyId = 4

       -- and msa.is_delete = 0
      -- and msa.closingDate LIKE "0000-00-00"

     GROUP BY mbd.mobileNo

------------------------------------------------------
-- voucehr check
SELECT
SUM(IFNULL(CASE
when avd.debit_acc = 412 then avd.amount
end, 0)) as debit_amount,

SUM(IFNULL(CASE
when avd.credit_acc = 412 then avd.amount
end, 0)) as credit_amount,

(
    SUM(IFNULL(CASE when avd.debit_acc = 412 then avd.amount end, 0))
	-
	SUM(IFNULL(CASE when avd.credit_acc = 412 then avd.amount end, 0))
) as cash_book

 FROM `acc_voucher_details` as avd
 WHERE (avd.`debit_acc` = 412 OR avd.`credit_acc` = 412)
 and avd.voucher_id IN ( SELECT av.id from acc_voucher as av
						WHERE av.branch_id = 10 and av.voucher_date <= "2021-09-15"
						and av.voucher_status in (1,2)
						and av.is_delete = 0)

    ----------------------------------------------
    SELECT * FROM `mfn_savings_interest` where is_delete = 0 and id in (SELECT interestId FROM `mfn_savings_interest_details` WHERE isOpening = 0)

----------------------------- update provision and interest table
UPDATE `mfn_savings_provision` as msp SET msp.`isOpening`= (select mspd.isOpening FROM mfn_savings_provision_details as mspd where msp.id = mspd.provisionId GROUP BY mspd.provisionId);


-- SELECT REPLACE("SQL Tutorial", "SQL", "HTML");
-- SELECT INSERT("W3Schools.com", 3, 9, "Example");
-- SELECT *  FROM `acc_account_ledger` WHERE `branch_arr` LIKE '%3-0%'
-- UPDATE `mfn_savings_provision` SET `provisionCode`= (select REPLACE(provisionCode, "PV.", "PV")) WHERE `isOpening` = 1;

UPDATE `mfn_savings_provision` as msp SET msp.`provisionCode`= (select INSERT(msp.provisionCode, 3, 0, ".")) WHERE msp.`isOpening` = 1;

UPDATE `mfn_savings_provision` as msp SET msp.`provisionCode`= (select INSERT(msp.provisionCode, 3, 0, (select gb.branch_code from gnl_branchs as gb where msp.branchId = gb.id))) WHERE msp.`isOpening` = 1;

UPDATE `mfn_savings_provision` as msp SET msp.`provisionCode`= (select INSERT(msp.provisionCode, 7, 0, ".")) WHERE msp.`isOpening` = 1;

-- UPDATE `mfn_savings_interest` SET `interestCode`= (select REPLACE(interestCode, "IN.", "IN")) WHERE `isOpening` = 1;


SELECT
	GROUP_CONCAT(CASE
        WHEN debit_acc IN (412, 1587)
        THEN credit_acc
    END) as receipt_ledgers,
    GROUP_CONCAT(CASE
    	WHEN credit_acc IN (412, 1587)
    	THEN debit_acc
    END) as payment_ledgers
FROM `acc_voucher_details`
WHERE
 `voucher_id` in (SELECT id FROM `acc_voucher` WHERE `voucher_date` BETWEEN '2021-07-01' AND '2021-10-13' AND `voucher_status` = 1 AND `branch_id` = 10 AND `is_active` = 1 AND `is_delete` = 0)
and (credit_acc in (412, 1587) OR debit_acc in (412, 1587) )
-- and debit_acc not in (412, 1587)

---------------------------------------------------------

SELECT
he.id as emp_t_id, gs.`emp_id`,
he.`employee_no`, gs.`employee_no`,
he.`emp_code`, gs.`username`,
he.`emp_name`,
he.`user_id`, gs.id as user_t_id,
he.`branch_id`, gs.`branch_id`,
he.`status`,
he.`is_active`, gs.`is_active`,
he.`is_delete`, gs.`is_delete`,
he.`designation_id`, gs.`sys_user_role_id`
FROM `hr_employees` as he
left join `gnl_sys_users` as gs on gs.id = he.`user_id`
WHERE he.`is_delete` = 0 and gs.emp_id is null and gs.employee_no is null

SELECT
he.id as emp_t_id, gs.`emp_id` as sys_emp_id,
he.`employee_no`, gs.`employee_no` as sys_employee_no,
he.`emp_code`, gs.`username`,
he.`emp_name`, gs.`full_name`,
he.`user_id`, gs.id as user_t_id,
he.`branch_id`, gs.`branch_id` as sys_branch_id,
he.`is_active`, gs.`is_active` as sys_is_active,
he.`is_delete`, gs.`is_delete` as sys_is_delete,
he.`status`,
he.`designation_id`, gs.`sys_user_role_id`
FROM `gnl_sys_users` as gs
left join `hr_employees` as he  on (gs.`emp_id` = he.`id` or gs.`employee_no` = he.`employee_no`)
WHERE gs.emp_id is not null and gs.employee_no is not null
and gs.`is_delete` = 1

SELECT emp_code, COUNT(emp_code) as count_code FROM `hr_employees` GROUP by emp_code HAVING count_code > 1
-----------------------------------------------------------------
SELECT avd.*, av.voucher_type_id, av.v_generate_type, av.branch_id, av.ft_id, av.voucher_date
    FROM `acc_voucher_details` as avd
    join acc_voucher as av on (avd.voucher_id = av.id and av.is_delete = 0 and av.voucher_status = 1)
    WHERE avd.`debit_acc` = 94 || avd.credit_acc = 94

UPDATE `acc_voucher_details` as avd
    join acc_voucher as av on (avd.voucher_id = av.id and av.is_delete = 0 and av.voucher_status = 1 and av.voucher_type_id = 5)
    SET avd.`debit_acc`=201
    WHERE avd.`debit_acc` = 94

UPDATE `acc_voucher_details` as avd
    join acc_voucher as av on (avd.voucher_id = av.id and av.is_delete = 0 and av.voucher_status = 1 and av.voucher_type_id = 5)
    SET avd.`credit_acc`=201
    WHERE avd.`credit_acc` = 94


-------------------------------------------------------------

    SELECT msc.accountId, msc.memberId, msc.samityId, msc.branchId, msc.closingDate, msc.isFromMemberClosing,
    (SELECT sum(msd.amount)
            FROM `mfn_savings_deposit` as msd
            WHERE msd.`accountId` = msc.accountId
                AND msd.`is_delete` = 0
    ) as deposit_amount,

    (SELECT SUM(msw.amount)
            FROM `mfn_savings_withdraw` as msw
            WHERE msw.`accountId` = msc.accountId
                AND msw.`is_delete` = 0
    ) as withdraw_amount,

    ((SELECT sum(msd.amount)
            FROM `mfn_savings_deposit` as msd
            WHERE msd.`accountId` = msc.accountId
                AND msd.`is_delete` = 0
    ) -
    (SELECT SUM(msw.amount)
            FROM `mfn_savings_withdraw` as msw
            WHERE msw.`accountId` = msc.accountId
                AND msw.`is_delete` = 0
    )) as diff
    FROM `mfn_savings_closings` as msc
    WHERE msc.`is_delete` = 0
    GROUP BY msc.accountId
    having diff > 0

    SELECT msc.*, count(msc.accountId) FROM `mfn_savings_closings` as msc WHERE msc.`is_delete` = 0 GROUP BY msc.accountId having count(msc.accountId) > 1

    SELECT id FROM `mfn_members` where id in (SELECT id FROM `mfn_savings_accounts` WHERE `branchId` = 17 AND `savingsProductId` = 4 AND `is_delete` = 0 ) and is_delete = 0 and primaryProductId = 1 and gender = "Female"

    SELECT sum(msd.amount)
	FROM `mfn_savings_deposit` as msd
	WHERE msd.`branchId` = 17
		AND msd.`primaryProductId` = 1
		AND msd.`savingsProductId` = 4
		AND msd.`date` BETWEEN '2021-02-01' AND '2021-02-28'
		AND msd.`is_delete` = 0
		and msd.memberId in (
			SELECT mm.id
			FROM `mfn_members` as mm
			where mm.is_delete = 0
            and mm.gender = "Female"
        )
        and msd.transactionTypeId = 1

SELECT sum(amount)
	FROM `mfn_savings_withdraw` as msd
    WHERE msd.`branchId` = 17
		AND msd.`primaryProductId` = 1
		AND msd.`savingsProductId` = 4
		AND msd.`date` BETWEEN '2021-02-01' AND '2021-02-28'
		AND msd.`is_delete` = 0
		and msd.memberId in (
			SELECT mm.id
			FROM `mfn_members` as mm
			where mm.is_delete = 0
            and mm.gender = "Female"
        )
         and msd.transactionTypeId in (1,7)

SELECT msd.*
	FROM `mfn_savings_deposit` as msd
    WHERE msd.`branchId` = 17
    	AND msd.`date` <= '2022-05-31'
        AND msd.`is_delete` = 0
        and msd.memberId in (SELECT mm.id FROM `mfn_members` as mm WHERE mm.`branchId` = 17 AND mm.`primaryProductId` = 1 AND mm.`is_delete` = 0)
        and msd.accountId in (
            SELECT msa.id
			FROM `mfn_savings_accounts` as msa
    		WHERE msa.`branchId` = 17
                AND msa.`savingsProductId` = 4
                AND msa.`is_delete` = 0
                and (msa.closingDate like "0000-00-00" or msa.closingDate > "2022-05-31")
        )

SELECT sum(msd.amount) FROM `mfn_savings_withdraw` as msd
	WHERE msd.`branchId` = 17
    	AND msd.`date` <= '2022-05-31'
        AND msd.`is_delete` = 0
        and msd.memberId in (SELECT mm.id FROM `mfn_members` as mm WHERE mm.`branchId` = 17 AND mm.`primaryProductId` = 1 AND mm.`is_delete` = 0)
        and msd.accountId in (
            SELECT msa.id
			FROM `mfn_savings_accounts` as msa
    		WHERE msa.`branchId` = 17
                AND msa.`savingsProductId` = 4
                AND msa.`is_delete` = 0
                and (msa.closingDate like "0000-00-00" or msa.closingDate > "2022-05-31")
        )

select (SELECT sum(msd.amount)
	FROM `mfn_savings_deposit` as msd
    WHERE msd.`branchId` = 17
    	AND msd.`date` <= '2022-05-31'
        AND msd.`is_delete` = 0
        and msd.memberId in (SELECT mm.id FROM `mfn_members` as mm WHERE mm.`branchId` = 17 AND mm.`primaryProductId` = 1 AND mm.`is_delete` = 0)
        and msd.accountId in (
            SELECT msa.id
			FROM `mfn_savings_accounts` as msa
    		WHERE msa.`branchId` = 17
                AND msa.`savingsProductId` = 4
                AND msa.`is_delete` = 0
                and (msa.closingDate like "0000-00-00" or msa.closingDate > "2022-05-31")
        ) )
        -
        (SELECT sum(msd.amount) FROM `mfn_savings_withdraw` as msd
	WHERE msd.`branchId` = 17
    	AND msd.`date` <= '2022-05-31'
        AND msd.`is_delete` = 0
        and msd.memberId in (SELECT mm.id FROM `mfn_members` as mm WHERE mm.`branchId` = 17 AND mm.`primaryProductId` = 1 AND mm.`is_delete` = 0)
        and msd.accountId in (
            SELECT msa.id
			FROM `mfn_savings_accounts` as msa
    		WHERE msa.`branchId` = 17
                AND msa.`savingsProductId` = 4
                AND msa.`is_delete` = 0
                and (msa.closingDate like "0000-00-00" or msa.closingDate > "2022-05-31")
        )) as diff


SELECT * FROM `mfn_savings_deposit` where is_delete = 0 and accountId in (SELECT id FROM `mfn_savings_accounts` where is_delete = 1)

-----------------------------------------------------------------------------------------------------------------------------------
------------- POS

ALTER TABLE `pos_p_groups` ADD `id_new` INT NOT NULL AFTER `id`;
UPDATE `pos_p_groups` SET `id_new`= id + 1;
ALTER TABLE `pos_p_groups` DROP `id`;
ALTER TABLE `pos_p_groups` CHANGE `id_new` `id` INT NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`);
INSERT INTO `pos_p_groups` (`id`, `company_id`, `branch_id`, `group_name`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`)
VALUES ('1', '1', '1', 'N/A', '1', '0', NULL, NULL, NULL, NULL);

ALTER TABLE `pos_p_categories` ADD `id_new` INT NOT NULL AFTER `id`;
UPDATE `pos_p_categories` SET `id_new`= id + 1;
ALTER TABLE `pos_p_categories` DROP `id`;
ALTER TABLE `pos_p_categories` CHANGE `id_new` `id` INT NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`);
INSERT INTO `pos_p_categories` (`id`, `company_id`, `branch_id`, `prod_group_id`, `prod_type_id`, `cat_name`, `other_cost`, `processing_fee`, `serial_barcode`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`)
    VALUES ('1', '1', '1', NULL, '1', 'N/A', '0.00', '1', '0', '1', '0', NULL, NULL, NULL, NULL);
UPDATE `pos_p_categories` SET `prod_group_id`= `prod_group_id` + 1;

ALTER TABLE `pos_p_subcategories` ADD `id_new` INT NOT NULL AFTER `id`;
UPDATE `pos_p_subcategories` SET `id_new`= id + 1;
ALTER TABLE `pos_p_subcategories` DROP `id`;
ALTER TABLE `pos_p_subcategories` CHANGE `id_new` `id` INT NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`);
INSERT INTO `pos_p_subcategories` (`id`, `company_id`, `branch_id`, `prod_group_id`, `prod_cat_id`, `sub_cat_name`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`)
    VALUES (1, '1', '1', NULL, NULL, 'N/A', '1', '0', NULL, NULL, NULL, NULL);
UPDATE `pos_p_subcategories` SET `prod_group_id`= (`prod_group_id` + 1),`prod_cat_id`= (`prod_cat_id` + 1);

INSERT INTO `pos_p_models` (`id`, `company_id`, `branch_id`, `prod_group_id`, `prod_cat_id`, `prod_sub_cat_id`, `prod_brand_id`, `model_name`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`)
    VALUES ('1', '1', '1', NULL, NULL, NULL, NULL, 'N/A', '1', '0', NULL, NULL, NULL, NULL);
UPDATE `pos_p_models` SET `prod_group_id`= (`prod_group_id` + 1),`prod_cat_id`= (`prod_cat_id` + 1),`prod_sub_cat_id`= (`prod_sub_cat_id` + 1);

INSERT INTO `pos_p_brands` (`id`, `brand_name`, `company_id`, `branch_id`, `prod_group_id`, `prod_cat_id`, `prod_sub_cat_id`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`)
    VALUES ('1', 'N/A', '1', '1', NULL, NULL, NULL, '1', '0', NULL, NULL, NULL, NULL);

TRUNCATE `pos_p_colors`;
INSERT INTO `pos_p_colors` (`id`, `company_id`, `branch_id`, `color_name`, `prod_group_id`, `prod_cat_id`, `prod_sub_cat_id`, `prod_brand_id`, `prod_model_id`, `prod_size_id`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`)
    VALUES ('1', '1', '1', 'N/A', NULL, NULL, NULL, NULL, NULL, NULL, '1', '0', NULL, NULL, NULL, NULL);

INSERT INTO `pos_p_sizes` (`id`, `company_id`, `branch_id`, `prod_group_id`, `prod_cat_id`, `prod_sub_cat_id`, `prod_brand_id`, `prod_model_id`, `size_name`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`)
    VALUES ('1', '1', '1', NULL, NULL, NULL, NULL, NULL, 'N/A', '1', '0', NULL, NULL, NULL, NULL);
UPDATE `pos_p_sizes` SET `prod_group_id`= (`prod_group_id` + 1),`prod_cat_id`= (`prod_cat_id` + 1),`prod_sub_cat_id`= (`prod_sub_cat_id` + 1);

INSERT INTO `pos_p_uoms` (`id`, `company_id`, `branch_id`, `uom_name`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`)
    VALUES ('1', '1', '1', 'N/A', '1', '0', NULL, NULL, NULL, NULL);

UPDATE `pos_products` SET `prod_group_id`= (`prod_group_id` + 1),`prod_cat_id`= (`prod_cat_id` + 1),`prod_sub_cat_id`= (`prod_sub_cat_id` + 1), `prod_color_id` = 1;
UPDATE `pos_products` SET `prod_size_id`=1 WHERE `prod_size_id` not in (47,48,54,60,61,79,80,84,90,107,125);
DELETE FROM `pos_p_sizes` WHERE id not in (1, 47,48,54,60,61,79,80,84,90,107,125);

------------------------------------------------------------------------------

UPDATE `pos_products` SET `prod_barcode`= (select REPLACE(prod_barcode, "BnF", "BNF")), `sys_barcode`= (select REPLACE(sys_barcode, "BnF", "BNF"));
