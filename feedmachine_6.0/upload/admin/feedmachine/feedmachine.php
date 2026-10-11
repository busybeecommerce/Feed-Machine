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

//I put in a check for this variable into application_top that prevents a session from starting
//when Feedmachine is ran - you can do the same (it's not important though)
  $start_session = false;

/*
This program uses the catalog's application_top (as oppose to admin's) in case any alternative url generator is used.
*/
  if( !isset($feedmachine_auto) ) {
    $installation_path = dirname(__FILE__) . '/';
    require_once($installation_path . 'feedmachine_config.php');

    $catalog_path = DIR_FS_CATALOG;
    chdir($catalog_path);
    $catalog_path = getcwd() . '/';
    require_once('includes/application_top.php');
    Session::destroy();

    require_once($installation_path . 'feedmachine_loader.php');
  }
  require_once($installation_path . 'feedmachine_loadingbay.php');

  if( isset($_SERVER['HTTP_HOST']) ) echo '<pre>';

  echo 'catalog path: ' . $catalog_path . "\n\n";

  function field_generator($db_field_name) {
    global $product, $current_product_prices, $cur_feed, $currencies_info, $countries, $categories;

    switch( $db_field_name  ) {
      case 'PRODUCTS_URL':
      $additional_params = [];
      if( $cur_feed['currency_code'] != DEFAULT_CURRENCY ) $additional_params['currency'] = $cur_feed['currency_code'];
      if( $cur_feed['language_code'] != DEFAULT_LANGUAGE ) $additional_params['language'] = $cur_feed['language_code'];
      if( !empty($cur_feed['url_parameters']) ) {
        $cur_feed_params = explode('&', $cur_feed['url_parameters']);
        $parameter_array = [];
        foreach ($cur_feed_params as $param) {
          $cur_feed_param = explode('=', $param);
          $additional_params[$cur_feed_param[0]] = $cur_feed_param[1];
        }
      }
      $additional_params_string = !empty($additional_params) ? '&' . implode('&', $additional_params) : '';
      $output_field_value = $GLOBALS['Linker']->build('product_info.php', $additional_params_string)->set_parameter('products_id', $product['products_id']);

     break;
    case 'IMAGE_URL':
      $output_field_value = $product['products_image'] ? HTTP_SERVER . DIR_WS_CATALOG . 'images/' . $product['products_image']  : '';
      break;
     case 'IMAGE_URL_FEEDS':
      $output_field_value = $product['products_image']  ? HTTP_SERVER . DIR_WS_CATALOG . 'images/feeds/' . $product['products_image']  : '';
      break;
    case 'CATEGORY':
      $output_field_value = !empty($product['categories_id']) ? $product['categories_name'] : '';
        break;
      case 'CATEGORY_PARENT':
        $output_field_value = '';
        if( !empty($product['categories_id']) ) {
          $tree_size = sizeof($categories[ $product['categories_id'] ][ $cur_feed['language_id'] ]);
          $output_field_value = $tree_size > 1 ? $categories[ $product['categories_id'] ][ $cur_feed['language_id'] ][$tree_size-2] : $product['categories_name'];
        }
        break;
      case 'CATEGORY_ROOT':
        $output_field_value = !empty($product['categories_id']) ? $categories[ $product['categories_id'] ][ $cur_feed['language_id'] ][0] : '';
        break;
      case 'CATEGORY_TREE':
        $output_field_value = !empty($product['categories_id']) ? implode($cur_feed['category_tree_seperator'], $categories[ $product['categories_id'] ][ $cur_feed['language_id'] ]) : '';
        break;
      case 'PRICE':
        $output_field_value = $current_product_prices['products_price'];
        break;
      case 'FINAL_PRICE':
        $output_field_value = $current_product_prices['final_price'];
        break;
      case 'SPECIAL_PRICE':
        $output_field_value = !empty($product['specials_new_products_price']) ? $current_product_prices['specials_new_products_price'] : '';
        break;
      case 'PRICE_WITHOUT_TAX':
        $output_field_value = $current_product_prices['products_price_without_tax'];
        break;
      case 'FINAL_PRICE_WITHOUT_TAX':
        $output_field_value = $current_product_prices['final_price_without_tax'];
        break;
      case 'SPECIAL_PRICE_WITHOUT_TAX':
        $output_field_value = !empty($product['specials_new_products_price']) ? $current_product_prices['specials_new_products_price_without_tax'] : '';
        break;
      case 'PRICE_WITH_TAX':
        $output_field_value = $current_product_prices['products_price_with_tax'];
        break;
      case 'FINAL_PRICE_WITH_TAX':
        $output_field_value = $current_product_prices['final_price_with_tax'];
        break;
      case 'SPECIAL_PRICE_WITH_TAX':
        $output_field_value = !empty($product['specials_new_products_price']) ? $current_product_prices['specials_new_products_price_with_tax'] : '';
        break;
      case 'UNIQUE_ID':
        //kept for configuration backwards compatibility
        $output_field_value = $product['products_id'];
        break;
      case 'IS_IN_STOCK':
          $output_field_value = $product['products_quantity'] > 0 ? 'in stock' : 'out of stock';
          break;
      case 'BLANK':
        default:
        $output_field_value = '';
        break;
    }

    return $output_field_value;

  }

  function process_field_value($field_value, &$field_options) {

    if( in_array('STRIP_XHTML', $field_options) ) {
      $field_value = preg_replace('#<([^\s]*+).*?>(.*?)</\1>#i', '$2', $field_value);
      $field_value = preg_replace('#<.*? />#i', ' ', $field_value);
    } elseif( in_array('STRIP_HTML', $field_options) ) {
      $field_value = preg_replace('#</?.*?>#i', ' ', $field_value);
    }
    if( in_array('HTML_ENTITIES', $field_options) ) {
      $field_value = htmlentities(html_entity_decode($field_value));
    }
    if( in_array('STRIP_CRLF', $field_options) ) {
      $field_value = preg_replace('#[\040\011]*+[\n\r]+[\040\011]*#s', ' ', $field_value);
    }

    return $field_value;

  }

  function category_path($categories_id, &$language_id, &$cat_path) {

    $query = $GLOBALS['db']->query('
SELECT c.parent_id, cd.categories_name
  FROM categories c, categories_description cd
  WHERE c.categories_id = cd.categories_id
    AND c.categories_id = \'' . (int)$categories_id . '\'
    AND cd.language_id = \'' . (int)$language_id . '\'');
    $cat_info = $query->fetch_assoc();

    if( [] !== $cat_path ) {
      $cat_path = array_merge([$cat_info['categories_name']], $cat_path);
    }

    if( !empty($cat_info['parent_id']) && $cat_info['parent_id'] ) {
      return category_path($cat_info['parent_id'], $language_id, $cat_path);
    } else {
      return $cat_path;
    }

  }

  function combinations(&$options, $options_seperator = '|', $pos = 0, $combinations = false) {

    $new_combinations = [];
    if( $combinations ) {
      foreach( $options[$pos]['values'] as $key1=>$value1 ) {
        foreach( $combinations as $key2=>$value2 ) {
          $new_combinations[$key2 . $options_seperator . $key1]['comb_name'] = $value2['comb_name'] . '; ' . $options[$pos]['comb_name'] . ': ' . $value1;
          $new_combinations[$key2 . $options_seperator . $key1]['comb_price'] = $value2['comb_price'] + ( $options[$pos]['price_prefix'][$key1] == '+' ? 1 : (-1) ) * $options[$pos]['options_values_price'][$key1];
          $new_combinations[$key2 . $options_seperator . $key1]['option_value'] = $value2['option_value'];
          $new_combinations[$key2 . $options_seperator . $key1]['option_value'][$options[$pos]['comb_name']] = $value1;
        }
      }
    } else { //This is a modification of the above for the first pass
      foreach( $options[$pos]['values'] as $key=>$value ) {
        $new_combinations[$key]['comb_name'] = $options[$pos]['comb_name'] . ': ' . $value;
        $new_combinations[$key]['comb_price'] = ( $options[$pos]['price_prefix'][$key] == '+' ? 1 : (-1) ) * $options[$pos]['options_values_price'][$key];
        $new_combinations[$key]['option_value'][$options[$pos]['comb_name']] = $value;
      }
    }

    if( ++$pos < sizeof($options) ) {
      return combinations($options, $options_seperator, $pos, $new_combinations);
    } else {
      return $new_combinations;
    }

  }

  if (defined('FM_TIME_LIMIT') && !Text::is_empty(FM_TIME_LIMIT) ) {
    set_time_limit(FM_TIME_LIMIT);
  }
  if (defined('FM_MEMORY_LIMIT') && !Text::is_empty(FM_MEMORY_LIMIT) ) {
    ini_set('memory_limit', FM_MEMORY_LIMIT);
  }

  $option_value_seperator = FM_OPTION_VALUE_SEPARATOR;
  $options_seperator = FM_OPTION_SEPARATOR;

  $save_path = $catalog_path . FM_SAVE_LOCATION;

  if( !file_exists($save_path) ) {
    @mkdir($save_path);
    if( !file_exists($save_path) ) {
      echo 'The FM_SAVE_LOCATION directory: ' . FM_SAVE_LOCATION . ' did not exist in ' . $catalog_path . '
  and Feedmachine could not create this directory. Please create this directory manually or change
  FM_SAVE_LOCATION to an existing directory. Note: the FM_SAVE_LOCATION should have permissions set to 777.';
      exit();
    }
  }

  //Build Languages Array
  $languages = [];
  $languages_query = $db->query('SELECT * FROM languages');
  while( $languages_row = $languages_query->fetch_assoc() ) {
    $languages[ $languages_row['code'] ] = $languages_row;
  }
  $languages_query->free_result();
  echo 'languages array built' . "\n";
  @ob_flush();

  //Which languages are needed for the feeds?
  //If only some are needed, this will save time in the master query
  $languages_used = [];
  foreach( $feeds as $key=>$feed ) {
    if( isset($feed['language_code']) && isset($languages[ $feed['language_code'] ]['languages_id']) ) {
    $feeds[$key]['language_id'] = $languages[ $feed['language_code'] ]['languages_id'];
    }

    if( isset($feeds[$key]['language_id']) ) {
      $languages_used[ $feeds[$key]['language_id'] ] = $feeds[$key]['language_id'];
    }
  }
  $languages_list = implode(',', $languages_used);

  //Build Currencies Array
  $currencies_info = [];
  $currencies_info_query = $db->query('SELECT * FROM currencies');
  while( $currencies_info_row = $currencies_info_query->fetch_assoc() ) {
    $currencies_info[ $currencies_info_row['code'] ] = $currencies_info_row;
  }
  $currencies_info_query->free_result();
  echo 'currencies array built' . "\n";
  @ob_flush();

  //Establish feed currency's decimal and thousand points
  foreach( $feeds as $cur_feed_id => $cur_feed ) {
    $feeds[$cur_feed_id]['currency_decimal'] = $feeds[$cur_feed_id]['currency_decimal_override'] !== false ? $feeds[$cur_feed_id]['currency_decimal_override'] : $currencies_info[ $cur_feed['currency_code'] ]['decimal_point'];
    $feeds[$cur_feed_id]['currency_thousands'] = $feeds[$cur_feed_id]['currency_thousands_override'] !== false ? $feeds[$cur_feed_id]['currency_thousands_override'] : $currencies_info[ $cur_feed['currency_code'] ]['thousands_point'];
  }

  //Get Countries All-Zones Tax Rates
  $taxes = [];
  $taxes_query = $db->query('
SELECT *
  FROM countries c, zones_to_geo_zones z2gz, tax_rates tr
  WHERE c.countries_id = z2gz.zone_country_id
  AND ( z2gz.zone_id = 0
    OR z2gz.zone_id IS NULL ' . ( defined('STORE_ZONE') && STORE_ZONE != '' ? '
    OR z2gz.zone_id = ' . STORE_ZONE : '' ) . ' )
    AND z2gz.geo_zone_id = tr.tax_zone_id');
  while( $taxes_row = $taxes_query->fetch_assoc() ) {
    $taxes[ $taxes_row['countries_iso_code_2'] ][ $taxes_row['tax_class_id'] ] = $taxes_row['tax_rate'];
  }
  $taxes_query->free_result();
  echo 'taxes array built' . "\n";
  @ob_flush();

  //Build Countries Array
  $countries = [];
  $countries_query = $db->query('SELECT * FROM countries');
  while( $countries_row = $countries_query->fetch_assoc() ) {
    $countries[ $countries_row['countries_iso_code_2'] ] = $countries_row;
  }
  $countries_query->free_result();
  echo 'countries array built' . "\n";
  @ob_flush();

  //Build Categories
  $categories = [];
  $categories_query = $db->query('SELECT * FROM categories c, categories_description cd WHERE c.categories_id = cd.categories_id');
  while( $categories_row = $categories_query->fetch_assoc() ) {
    $cat_path = [];
    $categories[ $categories_row['categories_id'] ][ $categories_row['language_id'] ] = category_path($categories_row['categories_id'], $categories_row['language_id'], $cat_path);
  }
  $categories_query->free_result();
  echo 'categories array built' . "\n";
  @ob_flush();

  if( defined('FM_SQL_BIG_SELECTS') && FM_SQL_BIG_SELECTS ) {
    $db->query('SET sql_big_selects = 1');
  }

  $count_run = true;
  $begin_output_run = true;

  $cycle_length = floor(1000/sizeof($languages_used))*sizeof($languages_used);

  $count = 0;
  $counter = 0;

  while( true ) {
    ++$counter;
    $master_query = $db->query('
SELECT ' . ( $count_run ? 'COUNT(*) as count' : 's.*, cd.*, c.*, ptc.*, mi.*, m.*, pd.*, p.*, IF(s.status, s.specials_new_products_price, p.products_price) as final_price' ) . '
  FROM products p
    LEFT JOIN products_description pd ON p.products_id = pd.products_id
    LEFT JOIN manufacturers m ON p.manufacturers_id = m.manufacturers_id
    LEFT JOIN manufacturers_info mi ON m.manufacturers_id = mi.manufacturers_id AND pd.language_id = mi.languages_id
    LEFT JOIN products_to_categories ptc ON pd.products_id = ptc.products_id
    LEFT JOIN specials s ON ptc.products_id = s.products_id
    LEFT JOIN categories c ON ptc.categories_id = c.categories_id
    LEFT JOIN categories_description cd ON c.categories_id = cd.categories_id AND pd.language_id = cd.language_id
  WHERE pd.language_id IN(' . $languages_list . ')
    AND p.products_status = 1
  ORDER BY p.products_id' . ( $count > 0 ? '
  LIMIT ' . (($counter-1)*$cycle_length) . ', ' . $cycle_length : '' ));

    if( $count_run ) {
      $master_query_row = $master_query->fetch_assoc();
      $count = $master_query_row['count'];
      echo "\n" . 'number of products: ' . ($count/sizeof($languages_used)) . "\n" . 'number of cycles: ' . ceil($count/$cycle_length) . "\n";
      @ob_flush();
      $count_run = false;
      --$counter;
      continue;
    }

    if( $begin_output_run ) {
      //Open Files and Check configurations
      $fps = [];
      foreach( $feeds as $cur_feed_id => $cur_feed ) {
        $file = $save_path . $feeds[$cur_feed_id]['filename'];
        if( file_exists($file) ) {
          if( defined('FM_BACK_UP_OLD_FEEDS') && FM_BACK_UP_OLD_FEEDS ) {
            $old_file_last_modified = date('Y-m-d-H.i.s', filemtime($file));
            $file_info = [];
            preg_match('#^(.*?)(\..*?)?$#', $feeds[$cur_feed_id]['filename'], $file_info);
            rename($file, $save_path . $file_info[1] . '-' . $old_file_last_modified . ( isset($file_info[2]) ? $file_info[2] : '' ));
          } else {
            unlink($file);
          }
        }

        $fps[$cur_feed_id]['fp'] = fopen($file, 'a');
        chmod($file, 0777);
        $fps[$cur_feed_id]['file'] = $file;
      }

      $i=0;
      $fields = [];
      while( $i < mysqli_num_fields($master_query) ) {
        ++$i;
        $cur_field = mysqli_fetch_field($master_query);
        $fields[] = $cur_field->name;
      }

      foreach( $feeds as $cur_feed_id => $cur_feed ) {
        $output_line = '';
        foreach( $cur_feed['fields'] as $output_field_name => $db_field ) {
          if( $db_field['type'] == 'DB' && !in_array($db_field['output'], $fields) ) {
            echo 'The field ' . $db_field['output'] . ' specified in the ' . $cur_feed['filename'] . ' feed does not exist in any of the queried tables.
                  Please ensure you have entered the correct field name and that it exists in one of the tables queried in $master_query (see code)';
            exit;
          } elseif( $cur_feed['add_field_names'] ) {
            $output_line .= $cur_feed['seperator'] . $cur_feed['text_qualifier'] . $output_field_name . $cur_feed['text_qualifier'];
          }
        }
        $additional_header_lines = '';
        if( !empty($cur_feed['additional_header_lines']) && is_array($cur_feed['additional_header_lines']) ) {
          $additional_header_lines = implode($cur_feed['newline'], $cur_feed['additional_header_lines']) . $cur_feed['newline'];
        }
        $output_line = substr($output_line, strlen($cur_feed['seperator'])) . ( $cur_feed['add_field_names'] ? $cur_feed['newline'] : '' );
        $output = $additional_header_lines . $output_line;
        fwrite($fps[$cur_feed_id]['fp'], $output);
      }
      echo "\n" . 'feed configurations checked... output begun.' . "\n";
      @ob_flush();

      $begin_output_run = false;
      --$counter;
      continue;
    }

    echo "\n" . 'cycle: ' . $counter . "\n";
    @ob_flush();

    //Build Products Array
    $products = [];
    $products_id_low = 0;
    $products_id_high = 0;
    while( $master_query_row = $master_query->fetch_assoc() ) {
      if( $products_id_low == 0 ) $products_id_low = $master_query_row['products_id'];
      $products_id_high = $master_query_row['products_id'];
      $products[$master_query_row['language_id']][$master_query_row['products_id']] = $master_query_row;
    }
    //
    //make products defined by attributes
    //
    $master_query->free_result();

    echo "\n" . 'products array built' . "\n";
    @ob_flush();

    //Build Attributes Array
    $products_attributes = [];
    $i_rows = [];

    if( !defined('PERFORMANCE_SKIP_LOAD_ATTRIBUTES') || ( defined('PERFORMANCE_SKIP_LOAD_ATTRIBUTES') && !PERFORMANCE_SKIP_LOAD_ATTRIBUTES ) ) {

      $products_attributes_query = $db->query('
SELECT *
  FROM products_attributes pa LEFT JOIN products_options po ON pa.options_id = po.products_options_id
		LEFT JOIN products_options_values pov ON pa.options_values_id = pov.products_options_values_id AND po.language_id = pov.language_id
  WHERE pa.products_id BETWEEN ' . (int)$products_id_low . '
    AND ' . (int)$products_id_high);

      if( mysqli_num_rows($products_attributes_query) > 0 ) {
        while( $products_attributes_row = $products_attributes_query->fetch_assoc() ) {
          if( isset($products[ $products_attributes_row['language_id'] ][ $products_attributes_row['products_id'] ]) ) {
            $products_attributes[ $products_attributes_row['language_id'] ][ $products_attributes_row['products_id'] ][ $products_attributes_row['options_id'] ][] = $products_attributes_row;
          }
        }

        //check if i_rows required
        $i_rows_required = false;
        foreach( $feeds as $feed ) {
          if( !empty($feed['attributes_handling']) && $feed['attributes_handling'] == 2 ) {
            $i_rows_required = true;
            break;
          }
        }

        //"i_rows" - individual rows, for feeds where individual records are needed for each option variation
        if( $i_rows_required ) {
          foreach( $products_attributes as $language_id => $value ) {
            foreach( $value as $products_id => $options_values ) {
              $options = [];
              $i = 0;
              ksort($options_values);
              foreach( $options_values as $products_options_id => $values ) {
                $options[$i]['comb_name'] = $values[0]['products_options_name'];
              ksort($values);
              foreach( $values as $products_options_values ) {
                $options[$i]['values'][ $products_options_id . $option_value_seperator . $products_options_values['products_options_values_id'] ] = $products_options_values['products_options_values_name'];
                $options[$i]['price_prefix'][ $products_options_id . $option_value_seperator . $products_options_values['products_options_values_id'] ] = $products_options_values['price_prefix'];
                $options[$i]['options_values_price'][ $products_options_id . $option_value_seperator . $products_options_values['products_options_values_id'] ] = $products_options_values['options_values_price'];
              }
              ++$i;
              }
              $combinations = combinations($options, $options_seperator);

              foreach( $combinations as $combination_id => $combination ) {
                $combination['combination_id'] = $combination_id;
                $i_rows[ $language_id ][ $products_id ][] = array_merge($combination, $products[ $language_id ][ $products_id ]);
              }
            }
          }
        }

      }

      echo 'products attributes array built' . "\n";
      @ob_flush();
      $products_attributes_query->free_result();

    }

    echo 'feed generation in process...' . "\n";
    @ob_flush();
    foreach( $feeds as $cur_feed_id => $cur_feed ) {
      foreach( $products[$cur_feed['language_id']] as $row ) {
        $rows = [];
        if( isset($i_rows[ $cur_feed['language_id'] ][ $row['products_id'] ]) && $cur_feed['attributes_handling'] == 2 ) {
          $rows =& $i_rows[$cur_feed['language_id']][ $row['products_id'] ];
        } else {
          $rows[] =& $row;
        }

        foreach( $rows as $product ) {

          if( !empty($cur_feed['include_record_function']) ) {
            $include_record = call_user_func_array($cur_feed['include_record_function'], [&$product]);
          if( !$include_record ) continue;
          }

          //Calculate Prices in Feed Currency and Prices with Tax
          $tax_multiplier = isset($taxes[ $cur_feed['countries_iso_2'] ][ $product['products_tax_class_id'] ]) ? (1+$taxes[ $cur_feed['countries_iso_2'] ][ $product['products_tax_class_id'] ]/100) : 1;

          $current_product_prices = [];
          $currency_conversion_field_list = ['products_price', 'final_price', 'specials_new_products_price', 'comb_price'];
          foreach( $currency_conversion_field_list as $field_name ) {

            $current_product_prices[$field_name . '_raw'] = isset($product[$field_name]) ? ( $product[$field_name] * ( isset($cur_feed['currency_code']) ? $currencies_info[ $cur_feed['currency_code'] ]['value'] : 1 ) ) : '';
            $current_product_prices[$field_name]                  = !empty($current_product_prices[$field_name . '_raw']) && isset($taxes[ $cur_feed['countries_iso_2'] ][ $product['products_tax_class_id'] ]) ? number_format((Tax::add((float)$current_product_prices[$field_name . '_raw'], (float)$taxes[ $cur_feed['countries_iso_2'] ][ $product['products_tax_class_id'] ])), $currencies_info[ $cur_feed['currency_code'] ]['decimal_places'], $cur_feed['currency_decimal'], $cur_feed['currency_thousands']) : '';
            $current_product_prices[$field_name . '_without_tax'] = !empty($current_product_prices[$field_name . '_raw']) ? number_format($current_product_prices[$field_name . '_raw'], $currencies_info[ $cur_feed['currency_code'] ]['decimal_places'], $cur_feed['currency_decimal'], $cur_feed['currency_thousands']) : '';
            $current_product_prices[$field_name . '_with_tax']    = !empty($current_product_prices[$field_name . '_raw']) ? number_format($tax_multiplier * $current_product_prices[$field_name . '_raw'], $currencies_info[ $cur_feed['currency_code'] ]['decimal_places'], $cur_feed['currency_decimal'], $cur_feed['currency_thousands']) : '';
          }

          $output_line = '';

          foreach( $cur_feed['fields'] as $output_field_name=>$db_field ) {
            $output_field_value = '';
            switch( $db_field['type'] ) {
              case 'DB':
              $output_field_value = $product[$db_field['output']];
              break;
              case 'KEYWORD':
                $output_field_value = field_generator($db_field['output']);
              break;
              case 'LOGIC':
                eval($db_field['output']);
              break;
              case 'FUNCTION':
                $params = explode('|', $db_field['output']);
                $function = $params[0];
                unset($params[0]);
                $params[0] = &$product;
                ksort($params);
                $output_field_value = call_user_func_array($function, $params);
                  break;
                case 'VALUE':
                default:
                $output_field_value = $db_field['output'];
              break;
            }

            if( isset($db_field['options']) ) $output_field_value = process_field_value($output_field_value, $db_field['options']);

            if( isset($db_field['filters']) && is_array($db_field['filters']) ) {
              $output_field_value = preg_replace($db_field['filters']['patterns'], $db_field['filters']['replacements'], $output_field_value);
            }

            $output_field_value = empty($cur_feed['text_qualifier']) ? preg_replace('#[' . $cur_feed['seperator'] . $cur_feed['newline'] . ']+#s', ' ', $output_field_value) : preg_replace('#' . $cur_feed['text_qualifier'] . '#s', $cur_feed['text_qualifier'] . $cur_feed['text_qualifier'], $output_field_value);

            $output_line .= $cur_feed['seperator'] . $cur_feed['text_qualifier'] . trim($output_field_value) . $cur_feed['text_qualifier'];
          }
          $output_line = substr($output_line, strlen($cur_feed['seperator'])) . $cur_feed['newline'];
          if( !empty($feeds[$cur_feed_id]['encoding']) && $feeds[$cur_feed_id]['encoding'] !== false ) {
            switch( $feeds[$cur_feed_id]['encoding'] ) {
              case 'utf8':
                $output_line = utf8_encode($output_line);
              break;
            }
          }
          fwrite($fps[$cur_feed_id]['fp'], $output_line);
        }
      }
    }

    //clean up
    unset($products);

    if( ($counter*$cycle_length) >= $count ) break;

  }//end while

  foreach( $fps as $cur_feed_id => $fp ) {
    fclose($fp['fp']);

    if( isset($feeds[$cur_feed_id]['copies']) && is_array($feeds[$cur_feed_id]['copies']) ) {
      foreach( $feeds[$cur_feed_id]['copies'] as $location ) {
        copy($fp['file'], $location);
        chmod($location, 0777);
      }
    }

    if( isset($feeds[$cur_feed_id]['compression_compress']) && $feeds[$cur_feed_id]['compression_compress'] ) {

      $compression_filename = $save_path . $feeds[$cur_feed_id]['compression_filename'];
      if( empty($feeds[$cur_feed_id]['compression_type']) ) $feeds[$cur_feed_id]['compression_type'] = 'gzip';

      switch( $feeds[$cur_feed_id]['compression_type'] ) {
        case 'zip':
          $zip = new ZipArchive();
          $zip->open($compression_filename);
          $zip->addFile($fp['file'], $feeds[$cur_feed_id]['filename']);
          $zip->close();
        break;
        default:
          $compression_level = isset($feeds[$cur_feed_id]['compression_level']) && is_numeric($feeds[$cur_feed_id]['compression_level']) && $feeds[$cur_feed_id]['compression_level'] < 10 ? (int)$feeds[$cur_feed_id]['compression_level'] : 9;
          $compressed_data = gzencode(file_get_contents($fp['file']), $compression_level);
          $comp_fp = fopen($compression_filename, 'w');
          fwrite($comp_fp, $compressed_data);
          fclose($comp_fp);
          chmod($compression_filename, 0777);
          unset($compressed_data);
        break;
      }

      if( isset($feeds[$cur_feed_id]['compression_delete_original']) && $feeds[$cur_feed_id]['compression_delete_original'] ) {
        unlink($fp['file']);
      }

      if( isset($feeds[$cur_feed_id]['compression_copies']) && is_array($feeds[$cur_feed_id]['compression_copies']) ) {
        foreach( $feeds[$cur_feed_id]['compression_copies'] as $location ) {
          copy($compression_filename, $location);
          chmod($location, 0777);
        }
      }
    }

  }

  echo "\n" . 'done.' . "\n\n";

  if( isset($_SERVER['HTTP_HOST']) ) echo '</pre>';

  if( !isset($feedmachine_auto) ) exit();

?>
