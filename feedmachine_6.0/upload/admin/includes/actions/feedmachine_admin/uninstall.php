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

  $configuration_keys = [
    'FM_SAVE_LOCATION',
    'FM_OPTION_SEPARATOR',
    'FM_OPTION_VALUE_SEPARATOR',
    'FM_SQL_BIG_SELECTS',
    'FM_MEMORY_LIMIT',
    'FM_TIME_LIMIT',
    'FM_AUTO_REPORTING',
    'FM_AUTO_TEST_MODE_LEVEL',
    'FM_AUTO_REPORT_EMAIL_ADDRESS',
    'FM_AUTO_TEST_EMAIL_ADDRESS',
    'FM_AUTO_TEST_MODE_LEVEL',
    'FM_AUTO_GRACE_PERIOD_MINUTES',
    'FM_AUTO_DATE_FORMAT',
    'FM_BACK_UP_OLD_FEEDS',
    ];

  $GLOBALS['db']->query("DELETE FROM configuration WHERE configuration_key IN ('" . implode("', '", $configuration_keys) . "')");
  $GLOBALS['db']->query("DELETE FROM configuration_group WHERE configuration_group_title = 'Feedmachine'");

  $GLOBALS['db']->query("DROP TABLE IF EXISTS feedmachine");

  return $GLOBALS['Admin']->link('feedmachine_admin.php');
