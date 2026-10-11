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

  if( !empty($_POST['feeds_update']) && is_array($_POST['feeds_update']) ) {
    foreach( $_POST['feeds_update'] as $config_filename => $config ) {
      $data = ['filename' => Text::prepare($config['filename']),
                    'ftp_status' => Text::prepare($config['ftp_status']),
                    'ftp_server' => Text::prepare($config['ftp_server']),
                    'ftp_path' => Text::prepare($config['ftp_path']),
                    'ftp_username' => Text::prepare($config['ftp_username']),
                    'ftp_password' => Text::prepare($config['ftp_password']),
                    'ftp_upload_period' => Text::prepare($config['ftp_upload_period']),
                    'language_code' => Text::prepare($config['language_code']),
                    'currency_code' => Text::prepare($config['currency_code']),
                    'countries_iso_2' => Text::prepare($config['countries_iso_2']),
                    'url_parameters' => Text::prepare($config['url_parameters']),
                    'ftp_port' => (!Text::is_empty($config['ftp_port'])? (int)$config['ftp_port'] : '')];
        $GLOBALS['db']->perform('feedmachine', $data, 'update', 'config_filename = \'' . $config_filename . '\'');
    }
    $messageStack->add_session('Feed settings updated!', 'success');
  }

  return $GLOBALS['Admin']->link('feedmachine_admin.php');
