<?php
declare(strict_types=1);

require dirname(__DIR__) . '/feedmachine_6.0/upload/admin/feedmachine/fm-feed-configs/google_base.php';

$cur_feed = ['currency_code' => 'GBP', 'countries_iso_2' => 'GB'];
$currencies_info = ['GBP' => ['value' => 1], 'EUR' => ['value' => 1.25]];
$taxes = ['GB' => [1 => 20]];
$product = ['products_id' => 1, 'products_price' => '10.00', 'products_tax_class_id' => 1,
            'status' => 1, 'specials_new_products_price' => '8.00'];
$checks = 0;

function expect_price(string $expected, string $actual): void {
  global $checks;
  if( $expected !== $actual ) {
    throw new RuntimeException('Expected ' . $expected . ', received ' . $actual);
  }
  ++$checks;
}

function expect_invalid_price(array $product): void {
  global $checks;
  try {
    FM_UF_google_price($product);
  } catch( UnexpectedValueException $error ) {
    ++$checks;
    return;
  }
  throw new RuntimeException('Invalid price was accepted');
}

// Invoke the configured callbacks, as both the manual and cron generators do.
expect_price('12.00 GBP', $feed_config['fields']['price']['output']($product));
expect_price('9.60 GBP', $feed_config['fields']['sale_price']['output']($product));
expect_price('1200.00 GBP', FM_UF_google_price(array_replace($product, ['products_price' => 1000])));
expect_price('0.00 GBP', FM_UF_google_price(array_replace($product, ['products_price' => 0])));
expect_price('10.00 GBP', FM_UF_google_price(array_replace($product, ['products_tax_class_id' => 0])));
expect_price('', FM_UF_google_sale_price(array_replace($product, ['status' => 0])));
expect_price('', FM_UF_google_sale_price(array_replace($product, ['specials_new_products_price' => 10])));
expect_price('', FM_UF_google_sale_price(array_replace($product, ['specials_new_products_price' => 11])));
unset($product['specials_new_products_price']);
expect_price('', FM_UF_google_sale_price($product));
$cur_feed['currency_code'] = 'EUR';
expect_price('15.00 EUR', FM_UF_google_price($product));
$cur_feed['currency_code'] = 'GBP';
expect_invalid_price(array_replace($product, ['products_price' => null]));
expect_invalid_price(array_replace($product, ['products_price' => -1]));
expect_invalid_price(array_replace($product, ['products_price' => 'invalid']));
$currencies_info['GBP']['value'] = 0;
expect_invalid_price($product);
$currencies_info['GBP']['value'] = 1;
$cur_feed['currency_code'] = 'invalid';
expect_invalid_price($product);
echo $checks . " Google price checks passed\n";
