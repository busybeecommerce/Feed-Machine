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

  LOADING BAY
  This is the place to load general user functions, classes and extra arrays that your
  configurations can access as the feeds are built.

  It is recommended that user functions begin with "FM_UF_" to ensure there are no conflicts.
  User Functions take the $product array containing all database fields for that product
  as their only parameter and should return the output field.
  "Include Record Function" decides whether the record (product or product variation) should
  be included and should return true or false.
  Note: The $product array is loaded by reference for performance reasons - you can also
  use this to your advantage
*/

// Google limits products names to 150 characters
function FM_UF_google_limit_title($product) {
  return substr($product['products_name'], 0,149) ;
}
// Google limits products descriptions to 5000 characters
function FM_UF_google_limit_description($product) {
  return substr($product['products_description'], 0,4999) ;
}

/*
  Check the following google categories taxonomy resource:
  https://support.google.com/merchants/answer/6324436?hl=en&sjid=16635037559001871930-EU
  Download Google taxonomy spreadsheet:
  https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.xls
  NOte: Google categories should be entered here in ID Numbers rather than names, so they can serve independant of the language.
*/
function FM_UF_google_categories($product) { //change the name to a unique name for each language and country
	$output_field_category = ($product['parent_id'] > 0) ? $product['parent_id'] : $product['categories_id'];

  switch( $output_field_category  ) {
    case '1':
    case '3':
      return '5795';
      break;
    case '2':
      return '5793';
      break;
    case '4':
      return '503795';
      break;
    default:
      return '422';
  }
}

//END OF LOADINGBAY
///////////////////////////////////////////////////////////////////////////////////////

?>