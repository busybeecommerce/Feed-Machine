<?php
/*
  The Feedmachine Solution

  Generate feeds for any product search engine, e.g. Google Product Search, PriceGrabber, BizRate,
  DealTime, mySimon, Shopping.com, Yahoo! Shopping, PriceRunner.
  
  Version 6
  
  Updated and refractored for Phoenixcart by @raiwa
  raiwa@phoenixcartaddons.com
  www.phoenixcartaddons.com

  Copyright (c) 2023 Rainer Schmied

  Released under commercial License

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org
  
  Copyright (c) 2021 Phoenix Cart

  copyright Copyright 2005-, Lech Madrzyk
  original author Lech Madrzyk
 */
  
  //Get store country code
  $store_country_code_array = $db->fetch_all('
SELECT countries_iso_code_2 AS code 
  FROM countries 
  WHERE countries_id = ' . (int)STORE_COUNTRY);
  $store_country_code = $store_country_code_array[0]['code'];
  
  $fm_installed_configs = [];
  $fm_installed_configs_query = $db->query('SELECT * FROM feedmachine');
  if( mysqli_num_rows($fm_installed_configs_query) > 0 ) {
    while( $fm_installed_configs_array = $fm_installed_configs_query->fetch_assoc() ) {
      if( file_exists($installation_path . 'fm-feed-configs/' . $fm_installed_configs_array['config_filename']) ) {
        $fm_installed_configs[ $fm_installed_configs_array['config_filename'] ] = $fm_installed_configs_array;
      } else {
        $db->query('DELETE FROM feedmachine WHERE config_filename = \'' . $db->escape($fm_installed_configs_array['config_filename']) . '\'');
      }
    }
  }
  
  $fm_configs = [];
  $dir = @dir($installation_path . 'fm-feed-configs');
  while( $file = $dir->read() ) {
    $feeds = [];
    if( !is_dir($installation_path . 'fm-feed-configs/' . $file) ) {
      include($installation_path . 'fm-feed-configs/' . $file);
      if( isset($feed_config) ) {
        if( !isset($fm_installed_configs[ $file ]) ) {
          $feed_name = current(explode('.php', $file));
          $data = ['config_filename' => $file,
                   'filename' => $feed_config['filename'],
                   'language_code' => DEFAULT_LANGUAGE,
                   'currency_code' => DEFAULT_CURRENCY,
                   'countries_iso_2' => $store_country_code,
                   'ftp_upload_period' => '30',
                   'url_parameters' => ( 'utm_source=' . urlencode($feed_name) . '&utm_medium=product_search&utm_campaign=' . urlencode($feed_name) )];
          $db->perform('feedmachine', $data);
          $fm_installed_configs[ $file ] = $data;
        }
        $fm_configs[ $file ] = array_merge($feed_config, $fm_installed_configs[ $file ]);
      }
    }
  }
  
  $feeds = $fm_configs;

?>