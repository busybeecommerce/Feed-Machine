<?php
/*
  $Id$

  Feedmachine 6.0.0

  by @raiwa
  raiwa@phoenixcartaddons.com
  www.phoenixcartaddons.com

  Copyright (c) 2023 Rainer Schmied

  Released under commercial License
  
  Generate feeds for any product search engine, e.g. Google Product Search, PriceGrabber, BizRate,
  DealTime, mySimon, Shopping.com, Yahoo! Shopping, PriceRunner.
  copyright Copyright 2005-, Lech Madrzyk
  original author Lech Madrzyk

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart
*/

  $GLOBALS['db']->query("
INSERT INTO configuration_group (configuration_group_title, configuration_group_description, sort_order, visible)
  VALUES ('Feedmachine', 'Feedmachine Configuration Settings', '', '1');
 ");
  $group_id_query = ($GLOBALS['db']->fetch_all("
SELECT configuration_group_id 
  FROM configuration_group 
  ORDER BY configuration_group_id DESC 
  LIMIT 1
 "));
  $group_id = $group_id_query[0]['configuration_group_id'];

  $GLOBALS['db']->query("
INSERT INTO configuration 
  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 
  VALUES (
    'Save Location Relative to Catalog Directory', 
    'FM_SAVE_LOCATION', 
    'fm-feeds/', 
    'The directory where your feeds will be saved. It is created during Feedmachine installation.<br>
     You can change the name and location if needed. Then update this setting to the name of this directory followed by a foward slash (/)', 
    '" . $group_id . "', 
    '1', 
    '',
    NOW());
    ");
  $GLOBALS['db']->query("
INSERT INTO configuration 
  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 
  VALUES (
    'Option Separator', 
    'FM_OPTION_SEPARATOR', 
    '|', 
    'Default is \"|\". The The character used to separate Options.
     You should not need to change it.', 
    '" . $group_id . "', 
    '2', 
    '',
    NOW());
    ");
  $GLOBALS['db']->query("
INSERT INTO configuration 
  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 
  VALUES (
    'Option Value Separator', 
    'FM_OPTION_VALUE_SEPARATOR', 
    ':', 
    'Default is \":\". The The character used to separate Option Values.
     You should not need to change it.', 
    '" . $group_id . "', 
    '3', 
    '',
    NOW());
    ");
  $GLOBALS['db']->query("
INSERT INTO configuration 
  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 
  VALUES (
    'SQL Big Selects', 
    'FM_SQL_BIG_SELECTS', 
    'false', 
    'If you are getting MYSQL error 1104, try setting the following to true', 
    '" . $group_id . "', 
    '4', 
    'Config::select_one([\'true\', \'false\'], ',
    NOW());
    ");
  $GLOBALS['db']->query("
INSERT INTO configuration 
  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 
  VALUES (
    'Memory Limit', 
    'FM_MEMORY_LIMIT', 
    '', 
    'Default is empty. If you are getting Memory limit error, try setting the following to a higher value for example: \"256M\" to overwrite your server setting.', 
    '" . $group_id . "', 
    '5', 
    '',
    NOW());
    ");
  $GLOBALS['db']->query("
INSERT INTO configuration 
  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 
  VALUES (
    'Time Limit', 
    'FM_TIME_LIMIT', 
    '', 
    'Default is empty. If you are getting Time limit error, try setting the following to a higher value for example: \"2400\" (2400seconds) to overwrite your server setting.', 
    '" . $group_id . "', 
    '6', 
    '',
    NOW());
    ");
  $GLOBALS['db']->query("
INSERT INTO configuration 
  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 
  VALUES (
    'Auto Reporting', 
    'FM_AUTO_REPORTING', 
    'true', 
    'Default is \"true\". Send report to the store owners or auto reporting e-mail address.', 
    '" . $group_id . "', 
    '7', 
    'Config::select_one([\'true\', \'false\'], ',
    NOW());
    ");
  $GLOBALS['db']->query("
INSERT INTO configuration 
  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 
  VALUES (
    'Auto Report E-Mail address', 
    'FM_AUTO_REPORT_EMAIL_ADDRESS', 
    '', 
    'Default is empty. Send report to this e-mail address. Leave empty to use the Store Owner E-Mail address.', 
    '" . $group_id . "', 
    '8', 
    '',
    NOW());
    ");
  $GLOBALS['db']->query("
INSERT INTO configuration 
  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 
  VALUES (
    'Auto Test E-Mail Address', 
    'FM_AUTO_TEST_EMAIL_ADDRESS', 
    '', 
    'Default is empty. Send report to this e-mail address when Test Mode Level is set to 1-3', 
    '" . $group_id . "', 
    '9', 
    '',
    NOW());
    ");
  $GLOBALS['db']->query("
INSERT INTO configuration 
  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 
  VALUES (
    'Auto Test Mode Level', 
    'FM_AUTO_TEST_MODE_LEVEL', 
    '0', 
    'Default is \"0\".<br>
    \"0\": No report is sent to the Auto Test E-Mail address<br>
    \"1\": Report is sent to the Auto Test E-Mail Address if an error occurs.<br>
    \"2\": Report is always sent to the Auto Test E-Mail Address.', 
    '" . $group_id . "', 
    '10', 
    'Config::select_one([\'0\', \'1\', \'2\'], ',
    NOW());
    ");
  $GLOBALS['db']->query("
INSERT INTO configuration 
  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 
  VALUES (
    'Grace Period Minutes', 
    'FM_AUTO_GRACE_PERIOD_MINUTES', 
    '60', 
    'Default is \"60\". The time which must have passed since the last product, special or category has been updated.<br>
     This prevents the feeds to be generated during catalog updates.', 
    '" . $group_id . "', 
    '11', 
    '',
    NOW());
    ");
  $GLOBALS['db']->query("
INSERT INTO configuration 
  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 
  VALUES (
    'Reports Date format', 
    'FM_AUTO_DATE_FORMAT', 
    'Y-m-d H:i:s T', 
    'Default is \"Y-m-d H:i:s T\". The date format used in the Feedmachine Reports for start/end date and time.', 
    '" . $group_id . "', 
    '12', 
    '',
    NOW());
    ");
  $GLOBALS['db']->query("
INSERT INTO configuration 
  (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 
  VALUES (
    'Backup Old Feeds', 
    'FM_BACK_UP_OLD_FEEDS', 
    'false', 
    'Default is \"false\". Make Backups of old feeds.', 
    '" . $group_id . "', 
    '13', 
    'Config::select_one([\'true\', \'false\'], ',
    NOW());
    ");

  if( mysqli_num_rows($db->query('show tables like \'feedmachine\'')) == 0 ) {
    $db->query('CREATE TABLE `feedmachine` (
                  `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
                  `config_filename` VARCHAR( 64 ) NOT NULL ,
                  `filename` VARCHAR( 64 ) NOT NULL ,
                  `ftp_status` TINYINT( 1 ) NOT NULL DEFAULT \'0\',
                  `ftp_server` VARCHAR( 128 ) NOT NULL ,
                  `ftp_port` INT( 16 ) NOT NULL ,
                  `ftp_path` VARCHAR( 128 ) NOT NULL ,
                  `ftp_username` VARCHAR( 64 ) NOT NULL ,
                  `ftp_password` VARCHAR( 128 ) NOT NULL ,
                  `ftp_upload_period` INT( 11 ) NOT NULL ,
                  `language_code` CHAR( 2 ) NOT NULL ,
                  `currency_code` CHAR( 3 ) NOT NULL ,
                  `countries_iso_2` CHAR( 2 ) NOT NULL ,
                  `url_parameters` VARCHAR( 255 ) NOT NULL ,
                  PRIMARY KEY ( `id` ),
                  UNIQUE ( `config_filename` )
                  )');
  } elseif (mysqli_num_rows($GLOBALS['db']->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='". DB_DATABASE . "' AND TABLE_NAME='feedmachine' AND COLUMN_NAME LIKE 'ftp_port'")) != 1 ) {
    $GLOBALS['db']->query("ALTER TABLE feedmachine ADD COLUMN ftp_port INT( 16 ) NOT NULL");
  }

  // make the fm-feeds directory
  if (!is_dir(DIR_FS_CATALOG . 'fm-feeds')) {
    mkdir(DIR_FS_CATALOG . 'fm-feeds', 0775);
  }

  return $GLOBALS['Admin']->link('feedmachine_admin.php');
