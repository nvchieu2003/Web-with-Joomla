ALTER TABLE `#__invoicing_invoices` ADD COLUMN `net_subamount` FLOAT NOT NULL;
ALTER TABLE `#__invoicing_invoices` ADD COLUMN `tax_subamount` FLOAT NOT NULL;
ALTER TABLE `#__invoicing_invoices` ADD COLUMN `gross_subamount` FLOAT NOT NULL;

ALTER TABLE `#__invoicing_invoices` ADD COLUMN `net_discount_amount` FLOAT NOT NULL;
ALTER TABLE `#__invoicing_invoices` ADD COLUMN `gross_discount_amount` FLOAT NOT NULL;
ALTER TABLE `#__invoicing_invoices` ADD COLUMN `tax_discount_amount` FLOAT NOT NULL;

ALTER TABLE `#__invoicing_invoices` DROP COLUMN `discount_amount`;

ALTER TABLE `#__invoicing_invoices` ADD COLUMN `discount_type` varchar(255) NOT NULL DEFAULT 'value';
ALTER TABLE `#__invoicing_invoices` ADD COLUMN `discount_value` FLOAT NOT NULL DEFAULT 0;

ALTER TABLE `#__invoicing_vendors` ADD COLUMN `filename` varchar(32) NOT NULL;

ALTER TABLE `#__invoicing_currencies` ADD COLUMN `number_decimals` int(11) NOT NULL DEFAULT 2;

CREATE TABLE IF NOT EXISTS `#__invoicing_quotes` (
	`invoicing_quote_id` bigint(20) unsigned NOT NULL auto_increment,
        `quote_number` bigint(20),
	`user_id` int(11) NOT NULL,
    	`vendor_id` int(11) NOT NULL,

	`created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    	`created_by` int(11) NOT NULL DEFAULT 0,
	`due_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',

	`notes` TEXT,

	`processor` varchar(255) NOT NULL,
	`processor_key` varchar(255) NOT NULL,

	`net_subamount` FLOAT NOT NULL,
	`tax_subamount` FLOAT NOT NULL,
	`gross_subamount` FLOAT NOT NULL,

	`custom_discount` FLOAT NOT NULL DEFAULT 0,
	`net_discount_amount` FLOAT NOT NULL,
	`gross_discount_amount` FLOAT NOT NULL,
        `tax_discount_amount` FLOAT NOT NULL,

	`coupon_id` int(11),
    `coupon_type` varchar(255) NOT NULL,
	`discount_type` VARCHAR(255) NOT NULL DEFAULT '', 
        `discount_value`  FLOAT NOT NULL DEFAULT 0,

	`net_amount` FLOAT NOT NULL,
	`tax_amount` FLOAT NOT NULL,
	`gross_amount` FLOAT NOT NULL,

    	`currency_id` int(11),

    	`language` VARCHAR(6) NOT NULL, 

    	`ip_address` VARCHAR(39) NOT NULL,

    	`generator` varchar(255) NOT NULL,
	`generator_key` varchar(255) NOT NULL,

    	`params` TEXT,

  PRIMARY KEY (`invoicing_quote_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__invoicing_quote_items` (
	`invoicing_quote_item_id` bigint(20) unsigned NOT NULL auto_increment,
	`quote_id` bigint(20) NOT NULL,

	`description` TEXT,
        `quantity` FLOAT NOT NULL,

	`gross_unit_price` FLOAT NOT NULL,
	`tax` FLOAT NOT NULL,
	`net_unit_price` FLOAT NOT NULL,

        `net_amount` FLOAT NOT NULL,
        `gross_amount` FLOAT NOT NULL,

    	`source` varchar(255) NOT NULL,
	`source_key` varchar(255) NOT NULL,

	`ordering` bigint(20) unsigned NOT NULL,
    	`params` TEXT,

  PRIMARY KEY (`invoicing_quote_item_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__invoicing_references` (
	`invoicing_reference_id` bigint(20) unsigned NOT NULL auto_increment,
	
	`description` TEXT,
        `quantity` FLOAT NOT NULL,

	`gross_unit_price` FLOAT NOT NULL,
	`tax` FLOAT NOT NULL,
	`net_unit_price` FLOAT NOT NULL,

        `net_amount` FLOAT NOT NULL,
        `gross_amount` FLOAT NOT NULL,

    	`source` varchar(255) NOT NULL,
	`source_key` varchar(255) NOT NULL,

	`ordering` bigint(20) unsigned NOT NULL,
    	`params` TEXT,

  PRIMARY KEY (`invoicing_reference_id`)
) DEFAULT CHARSET=utf8;