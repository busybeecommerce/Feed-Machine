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


  Google Product Search base feed configuration for The Feedmachine Solution
  ----------------------------
  This is quite a basic feed configuration and can be used to learn how to configure new feeds.

  This should produce a valid Google Product Search feed, however you might require one that
  is more sophistcated, in which case you should search "Feedmachine Configuration"
  in the Phoenix Add-ons section (https://phoenixcart.org/forum/addons/)
  or contact me for customized configurations:
  raiwa@phoenixcartaddons.com
  www.phoenixcartaddons.com

  Since configuring feeds is mostly intuative, without further ado here is the configuration followed
  by some notes.
*/

$feed_config = [
  'name' => 'google_base',
  'authors' => '@raiwa', //list of authors
  'filename' => 'mgoogle_base.txt', //default feed filename (this can be over-ridden by the user in the admin section)
  'schema_version' => '2.0', //always set as '2.0' - this is set just to future-proof configurations in case the schema ever changes
  //the following section of the feed configuration is the most important. It determines the fields that are output.
  'fields' => [
    'id' => [
      'output' => 'products_id',
      'type' => 'DB',
            ],
    'title' => [
      'output' => 'FM_UF_google_limit_title', // limits title to allowed 150 characters
      'type' => 'FUNCTION',
      'options' => ['STRIP_HTML', 'REMOVE_HTML_ENTITIES', 'STRIP_CRLF'],
               ],
    'description' => [
      'output' => 'FM_UF_google_limit_description', // limits description to allowed 5000 characters
      'type' => 'FUNCTION',
      'options' => ['STRIP_HTML', 'REMOVE_HTML_ENTITIES', 'STRIP_CRLF'],
                     ],
    'price' => [
      'output' => 'FINAL_PRICE_WITHOUT_TAX',
      'type' => 'KEYWORD'
               ],
    'sale_price' => [
      'output' => 'SPECIAL_PRICE',
      'type' => 'KEYWORD'
                    ],
    'brand' => [
      'output' => 'manufacturers_name',
      'type' => 'DB',
      'options' => ['STRIP_HTML', 'REMOVE_HTML_ENTITIES', 'STRIP_CRLF'],
               ],
    'mpn' => [
      'output' => 'products_model',
      'type' => 'DB'
             ],
    'product_type' => [
      'output' => 'CATEGORY_TREE',
      'type' => 'KEYWORD',
      'options' => ['STRIP_HTML', 'STRIP_CRLF'],
                      ],
    'weight' => [
      'output' => 'products_weight',
      'type' => 'DB'
                ],
    'link' => [
      'output' => 'PRODUCTS_URL',
      'type' => 'KEYWORD'
              ],
    'availability' => [
      'output' => 'IS_IN_STOCK',
      'type' => 'KEYWORD'
                      ],
    'image_link' => [
      'output' => 'IMAGE_URL',
      'type' => 'KEYWORD'
                    ],
    'condition' => [
      'output' => 'new', //change to 'used' or 'refurbished' if needed
      'type' => 'VALUE'
                   ],
    'max_handling_time' =>  [
      'output' => '1',
      'type' => 'VALUE'
                            ],
    'min_handling_time' => [
      'output' => '0',
      'type' => 'VALUE'
                           ],
    'included_destination' =>	[
      'output' => 'Shopping_ads, Display_ads, Free_listings, Free_local_listings, Local_inventory_ads',
      'type' => 'VALUE'
                              ],
    'google_product_category' => [
      'output' => 'FM_UF_google_categories',  //change the name to the function name used in this configuration if needed
      'type' => 'FUNCTION'
                                 ],
    'online_only' => [
      'output' => 'y', //change to 'n' if your shop is physical
      'type' => 'VALUE'
                     ],
                 ],
  'currency_decimal_override' => false,
  'currency_thousands_override' => false,
  'add_field_names' => true,
  'category_tree_seperator' => ' » ',
  'seperator' => "\t",
  'text_qualifier' => '',
  'newline' => "\n",
  'encoding' => 'false',
  'include_record_function' => ''
               ];

//FEED FUNCTIONS BEGIN

/*
The following include function (see explaination below) checks whether the product's quantity is greater than 0
This function is not used by default because many people do no have their stores configured for stock
To use this function, set include_record_function in feed configuration above to 'FM_UF_lm_stock_check'
*/


/*
  Use the following function only if you need different categories mapping than
  in the generic function FM_UF_google_categories defined in feedmachine_loadingbay.php
  Then replace  above in line 104 the function name to the one below.
  Check the following google categories taxonomy resource:
  https://support.google.com/merchants/answer/6324436?hl=en&sjid=16635037559001871930-EU
  Download Google taxonomy spreadsheet:
  https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.xls
  NOte: Google categories can be entered here in ID Numbers or Names.
*/
function FM_UF_google_categories_base($product) { //change the name to a unique name for each configuration and update line 104
	$output_field_category = ($product['parent_id'] > 0) ? $product['parent_id'] : $product['categories_id'];

  switch( $output_field_category  ) {
    case '1':
    case '3':
      return 'Food, Beverages & Tobacco > Food Items > Fruits & Vegetables > Fresh & Frozen Fruits';
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

//FEED FUNCTIONS END

/*
Some notes about configuring feeds
----------------------------

You can start to create a new feed configuration by copying and modifying this file.

Field Output Types:

DB - A field from the database. You can specify any field from the products or products_description table as the output.
VALUE - A constant value. For example all of your products might be 'new', so you would want to output 'new' under 'condition' for every product
KEYWORD - See below
FUNCTION - Specify a user function for the output (see below)

KEYWORD Field Type:

When 'type' is set to 'KEYWORD'...

Keywords are used to generate important product information

PRODUCTS_URL - URL generation is done by osC, so you get to keep your SEO urls!
IMAGE_URL
CATEGORY - The name of the category in which the product resides
CATEGORY_PARENT - The name of the parent category of the product's category, or if there is no parent, the name of the product's category
CATEGORY_ROOT - The name of the root (i.e. top) category
CATEGORY_TREE - Nested category path (e.g. TopCat > SubCat > SubSubCat, etc) - The seperator (' > ' in this example) is determined by 'category_tree_seperator' in the feed configuration
BLANK
PRICE *
PRICE_WITH_TAX
PRICE_WITHOUT_TAX
FINAL_PRICE * (the product's price or if the product is in specials, it's special price)
FINAL_PRICE_WITH_TAX
FINAL_PRICE_WITHOUT_TAX
SPECIAL_PRICE * (returns the special price if it exists, otherwise returns blank)
SPECIAL_PRICE_WITH_TAX
SPECIAL_PRICE_WITHOUT_TAX

* Tax is added depending on whether osC is set to display product prices with tax and whether tax is configured. 99% of the time these
are the keywords to use for price since they will handle tax according to the store set-up.

User Functions:

For custom field output, you can define your own functions that take an array (called $product)
containing all database fields for a product (e.g. $product['products_name'] is the product's name') as their parameter.
The function should return the output you want.

To set one of the fields to use a user defined function do the following in the field's configuration:

      'title' => [
        'output' => 'FM_UF_lm_product_title',  // where 'FM_UF_lm_product_title' is the name of the user function
        'type' => 'FUNCTION'
                 ],

You should define user functions specific to a feed in that feed's configuration file with a name that
will be unique (Note: Two different functions are not allowed to have the same name).

It is recommended that all user functions begin with "FM_UF_[author_initials]".

Example user function definition:

function FM_UF_lm_product_title($product) {
  return $product['products_name'] . ' by CoolBrand';
}


Include Record Function (a type of "User Function"):

Set using 'include_record_function' in the feed configuration...

If set, Feedmachine will use this function to decide whether a product should be included.
The "Include Record Function" takes the $product array (as described above) as its parameter and should return true or false.


Filters (optional)

Setting the filters array for a field runs the function preg_replace (a PHP function that replaces strings matching patterns) with
the patterns and replacements arrays on the field's output. E.g.

    'title' => [
      'output' => 'products_name',
       'type' => 'DB',
       'options' => ['STRIP_HTML', 'HTML_ENTITIES', 'STRIP_CRLF'],
       'filters' => ['patterns' => ['#badword#i'],
                     'replacements' => ['goodword']]
               ],

You can read about preg_replace here:
http://uk.php.net/manual/en/function.preg-replace.php

and about the pattern syntax here:
http://uk.php.net/manual/en/reference.pcre.pattern.syntax.php

--------------------
*/
?>