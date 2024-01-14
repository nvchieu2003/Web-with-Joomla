CREATE TABLE IF NOT EXISTS `#__invoicing_invoices` (
	`invoicing_invoice_id` bigint(20) unsigned NOT NULL auto_increment,
	`order_number` bigint(20) NOT NULL,
    `invoice_number` bigint(20),
	`user_id` int(11) NOT NULL,
    `vendor_id` int(11) NOT NULL,
    `subject` VARCHAR(255) NULL,

    `status` VARCHAR(16) NOT NULL,

	`created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_by` int(11) NOT NULL DEFAULT 0,
	`due_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

	`notes` TEXT,

	`processor` varchar(255) NOT NULL,
	`processor_key` varchar(255) NOT NULL,

	`net_subamount` FLOAT NOT NULL,
	`tax_subamount` FLOAT NOT NULL,
	`gross_subamount` FLOAT NOT NULL,

	`custom_discount` FLOAT NOT NULL DEFAULT 0,
	`net_discount_amount` FLOAT NULL,
	`gross_discount_amount` FLOAT NULL,
    `tax_discount_amount` FLOAT NULL,

	`coupon_id` int(11),
    `coupon_type` varchar(255) NULL DEFAULT NULL,
	`discount_type` VARCHAR(255) NOT NULL DEFAULT '', 
    `discount_value`  FLOAT NOT NULL DEFAULT 0,

	`net_amount` FLOAT NOT NULL,
	`tax_amount` FLOAT NOT NULL,
	`gross_amount` FLOAT NOT NULL,

    `currency_id` int(11),

    `language` VARCHAR(6) NOT NULL, 
    `ip_address` VARCHAR(39) NULL DEFAULT NULL,

    `generator` varchar(255) NULL DEFAULT NULL,
	`generator_key` varchar(255) NULL DEFAULT NULL,

    `params` TEXT DEFAULT NULL,

  PRIMARY KEY (`invoicing_invoice_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__invoicing_quotes` (
	`invoicing_quote_id` bigint(20) unsigned NOT NULL auto_increment,
    `quote_number` bigint(20),
	`user_id` int(11) NOT NULL,
    `vendor_id` int(11) NOT NULL,
    `subject` VARCHAR(255) NULL,

	`created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_by` int(11) NOT NULL DEFAULT 0,
	`due_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

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
    `coupon_type` varchar(255) NULL DEFAULT NULL,
	`discount_type` VARCHAR(255) NOT NULL DEFAULT '', 
    `discount_value`  FLOAT NOT NULL DEFAULT 0,

	`net_amount` FLOAT NOT NULL,
	`tax_amount` FLOAT NOT NULL,
	`gross_amount` FLOAT NOT NULL,

    `currency_id` int(11),

    `language` VARCHAR(6) NOT NULL, 

    `ip_address` VARCHAR(39) NULL DEFAULT NULL,
    
    `generator` varchar(255) NULL DEFAULT NULL,
	`generator_key` varchar(255) NULL DEFAULT NULL,
	
    `params` TEXT DEFAULT NULL,

  PRIMARY KEY (`invoicing_quote_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__invoicing_references` (
	`invoicing_reference_id` bigint(20) unsigned NOT NULL auto_increment,
	
	`name` TEXT,
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

CREATE TABLE IF NOT EXISTS `#__invoicing_invoice_items` (
	`invoicing_invoice_item_id` bigint(20) unsigned NOT NULL auto_increment,
	`invoice_id` bigint(20) NOT NULL,

	`name` TEXT,
	`description` TEXT,
    `quantity` FLOAT NOT NULL,

	`gross_unit_price` FLOAT NOT NULL,
	`tax` FLOAT NOT NULL,
	`net_unit_price` FLOAT NOT NULL,

    `net_amount` FLOAT NOT NULL,
    `gross_amount` FLOAT NOT NULL,

    `source` varchar(255) NULL,
	`source_key` varchar(255) NULL DEFAULT '',

	`ordering` bigint(20) unsigned NOT NULL,
    `params` TEXT,

  PRIMARY KEY (`invoicing_invoice_item_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__invoicing_quote_items` (
	`invoicing_quote_item_id` bigint(20) unsigned NOT NULL auto_increment,
	`quote_id` bigint(20) NOT NULL,

	`name` TEXT,
	`description` TEXT,
    `quantity` FLOAT NOT NULL,

	`gross_unit_price` FLOAT NOT NULL,
	`tax` FLOAT NOT NULL,
	`net_unit_price` FLOAT NOT NULL,

    `net_amount` FLOAT NOT NULL,
    `gross_amount` FLOAT NOT NULL,

    `source` varchar(255) NULL,
	`source_key` varchar(255) NULL DEFAULT '',

	`ordering` bigint(20) unsigned NOT NULL,
    `params` TEXT,

  PRIMARY KEY (`invoicing_quote_item_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__invoicing_coupons` (
	`invoicing_coupon_id` bigint(20) unsigned NOT NULL auto_increment,
	`title` varchar(255) NOT NULL,
	`code` varchar(255) NOT NULL,

	`publish_up` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`publish_down` datetime NULL DEFAULT NULL,

	`apply_on` VARCHAR(255) NULL,

	`user` int(10) DEFAULT NULL,
	`params` TEXT,

	`hitslimit` BIGINT(20) unsigned NULL,
	`userhitslimit` BIGINT(20) unsigned NULL,

	`valuetype` varchar(255) NOT NULL DEFAULT 'value',
	`value` FLOAT NOT NULL DEFAULT 0.0,
	
	`enabled` tinyint(1) NOT NULL DEFAULT '1',
	`ordering` bigint(20) unsigned NOT NULL,
	`created_on` datetime NOT NULL default CURRENT_TIMESTAMP,
	`created_by` int(11) NOT NULL DEFAULT 0,
	`modified_on` datetime NULL DEFAULT NULL,
	`modified_by` int(11) NOT NULL DEFAULT 0,
	`locked_on` datetime NULL DEFAULT NULL,
	`locked_by` int(11) NOT NULL DEFAULT 0,

	`hits` BIGINT(20) unsigned NOT NULL default 0,
	PRIMARY KEY ( `invoicing_coupon_id` )
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__invoicing_vendors` (
	`invoicing_vendor_id` int(11) NOT NULL auto_increment,
	`contact_name` varchar(50) NOT NULL,  
	`company_name` varchar(70) NOT NULL,
	`company_email` varchar(70) NOT NULL,
	`company_phone` varchar(70) NOT NULL,
	`company_url` varchar(255) NOT NULL,
	`logo` varchar(255) NOT NULL,
    `filename` varchar(255) NOT NULL,
	`address1` VARCHAR(255) NULL,
	`address2` VARCHAR(255) NULL,
	`city` VARCHAR(255) NULL,
	`state` VARCHAR(255) NULL,
	`zip` VARCHAR(255) NULL,
	`country` VARCHAR(255) NOT NULL DEFAULT 'XX',
    `params` TEXT,
	`notes` TEXT,
	PRIMARY KEY  (`invoicing_vendor_id`)
) ; 

CREATE TABLE IF NOT EXISTS `#__invoicing_users` (
	`invoicing_user_id` bigint(20) unsigned NOT NULL auto_increment,
	`user_id` bigint(20) unsigned NOT NULL,
	`isbusiness` TINYINT(1) NOT NULL DEFAULT '0',
	`businessname` VARCHAR(255) NULL,
	`firstname` CHAR(255) NOT NULL DEFAULT 'XX',
	`lastname` CHAR(255) NOT NULL DEFAULT 'XX',
	`occupation` VARCHAR(255) NULL,
	`vatnumber` VARCHAR(255) NULL,
	`viesregistered` TINYINT(1) NOT NULL DEFAULT '0',
	`taxauthority` VARCHAR(255) NULL,
	`address1` VARCHAR(255) NULL,
	`address2` VARCHAR(255) NULL,
	`city` VARCHAR(255) NULL,
	`state` VARCHAR(255) NULL,
	`zip` VARCHAR(255) NULL,
	`country` CHAR(255) NOT NULL DEFAULT 'XX',
	`mobile` CHAR(255) NOT NULL DEFAULT 'XX',
	`landline` CHAR(255) NOT NULL DEFAULT 'XX',
	`params` TEXT,
	`notes` TEXT,	
	PRIMARY KEY ( `invoicing_user_id` )
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__invoicing_taxes` (
	`invoicing_tax_id` bigint(20) unsigned NOT NULL auto_increment,
	`taxrate` FLOAT NOT NULL DEFAULT '20',
	`enabled` tinyint(1) NOT NULL DEFAULT '1',
	`ordering` bigint(20) unsigned NOT NULL,
	`created_on` datetime NOT NULL default CURRENT_TIMESTAMP,
	`created_by` int(11) NOT NULL DEFAULT 0,
	`modified_on` datetime NULL DEFAULT NULL,
	`modified_by` int(11) NOT NULL DEFAULT 0,
	`locked_on` datetime NULL DEFAULT NULL,
	`locked_by` int(11) NOT NULL DEFAULT 0,
	PRIMARY KEY ( `invoicing_tax_id` )
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__invoicing_currencies` (
	`invoicing_currency_id` bigint(20) unsigned NOT NULL auto_increment,
	`symbol` VARCHAR(20) NOT NULL DEFAULT '€',
	`code`   VARCHAR(20) NOT NULL DEFAULT 'EUR',
	`symbol_position` VARCHAR(20) NOT NULL DEFAULT 'after',
    `number_decimals` int(11) NOT NULL DEFAULT 2,
	`decimal_separator` VARCHAR(20) NOT NULL DEFAULT '.',
	`thousand_separator` VARCHAR(20) NOT NULL DEFAULT ' ',
	`enabled` tinyint(1) NOT NULL DEFAULT '1',
	`ordering` bigint(20) unsigned NOT NULL,
	`created_on` datetime NOT NULL default CURRENT_TIMESTAMP,
	`created_by` int(11) NOT NULL DEFAULT 0,
	`modified_on` datetime NULL DEFAULT NULL,
	`modified_by` int(11) NOT NULL DEFAULT 0,
	`locked_on` datetime NULL DEFAULT NULL,
	`locked_by` int(11) NOT NULL DEFAULT 0,
	PRIMARY KEY ( `invoicing_currency_id` )
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__invoicing_emails` (
  `invoicing_email_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `body` text,
  `description` text,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `pdf` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`invoicing_email_id`)
) DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `#__invoicing_templates` (
  `invoicing_template_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text,
  `htmlcontent` text,
  `pdfcontent` text,
  `usehtmlforpdf` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`invoicing_template_id`)
) DEFAULT CHARSET=utf8 ;

INSERT IGNORE INTO `#__invoicing_templates` (`invoicing_template_id`, `description`, `htmlcontent`, `pdfcontent`, `usehtmlforpdf`) VALUES
(1, 'INVOICING_TEMPLATE_ORDER', '', '', 1),
(2, 'INVOICING_TEMPLATE_INVOICE', '', '', 1),
(3, 'INVOICING_TEMPLATE_QUOTE', '', '', 1);

INSERT IGNORE INTO `#__invoicing_emails` (`invoicing_email_id`, `subject`, `body`, `published`, `description`,`pdf`) VALUES
(1, 'Order confirmation','Write here your own content for order confirmation, you can use predefined tags in the right', 1, 'INVOICING_INVOICE_MAIL_DESCRIPTION_NEW',1),
(3, 'Payment confirmation', 'Write here your own content for payment confirmation, you can use predefined tags in the right', 1, 'INVOICING_INVOICE_MAIL_DESCRIPTION_PAID',1),
(4, 'Payment request', 'Write here your own content for payment request, you can use predefined tags in the right', 1, 'INVOICING_INVOICE_MAIL_DESCRIPTION_PENDING',1),
(5, '[Admin] Order confirmation', '[Admin] Write here your own content for order confirmation, you can use predefined tags in the right ', 1, 'INVOICING_INVOICE_MAIL_DESCRIPTION_NEW_ADMIN',1),
(6, '[Admin] Payment confirmation', '[Admin] Write here your own content for payment confirmation, you can use predefined tags in the right ', 1, 'INVOICING_INVOICE_MAIL_DESCRIPTION_PAID_ADMIN',1),
(7, '[Admin] Payment request', '[Admin] Write here your own content for payment request, you can use predefined tags in the right ', 1, 'INVOICING_INVOICE_MAIL_DESCRIPTION_PENDING_ADMIN',1),
(8, 'Quote', 'Write here your own content for quote, you can use predefined tags in the right', 1, 'INVOICING_QUOTE_MAIL_DESCRIPTION',1),
(9, '[Admin] Quote', '[admin] Write here your own content for quote, you can use predefined tags in the right', 1, 'INVOICING_QUOTE_MAIL_DESCRIPTION_ADMIN',1),
(10, 'Offline payment completed', 'Write here your own content for the reception of an offline payment, you can use predefined tags in the right', 1, 'INVOICING_OFFLINE_PAYMENT_MAIL_DESCRIPTION',1),
(11, '[Admin] Offline payment completed', '[admin] Write here your own content for the reception of an offline payment, you can use predefined tags in the right', 1, 'INVOICING_OFFLINE_PAYMENT_MAIL_DESCRIPTION_ADMIN',1),
(12, 'Offline2 payment completed', 'Write here your own content for the reception of an offline2 payment, you can use predefined tags in the right', 1, 'INVOICING_OFFLINE2_PAYMENT_MAIL_DESCRIPTION',1),
(13, '[Admin] Offline2 payment completed', '[admin] Write here your own content for the reception of an offline2 payment, you can use predefined tags in the right', 1, 'INVOICING_OFFLINE2_PAYMENT_MAIL_DESCRIPTION_ADMIN',1);

INSERT IGNORE INTO `#__invoicing_currencies` (
`invoicing_currency_id` ,
`symbol` ,
`code` ,
`symbol_position` ,
`decimal_separator` ,
`thousand_separator` ,
`enabled` ,
`ordering` ,
`created_on` ,
`created_by` ,
`modified_on` ,
`modified_by` ,
`locked_on` ,
`locked_by`
)
VALUES (
1 , '€', 'EUR', 'after', '.', ' ', '1', '', CURRENT_TIMESTAMP, '0', NULL, '0', NULL, '0'
);
