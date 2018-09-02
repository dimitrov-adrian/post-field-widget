<?php

/**
 * Register WPCF fields.
 */
add_filter('post_field_widget_fields', function ($elements) {
  $elements['wc_product_title'] = [
    'label' => __('WooCommerce Product: Title', 'post-field-widget'),
  ];
  $elements['wc_product_teaser_image'] = [
    'label' => __('WooCommerce Product Teaser: Images', 'post-field-widget'),
  ];
  $elements['wc_product_teaser_info'] = [
    'label' => __('WooCommerce Product Teaser: Info', 'post-field-widget'),
  ];
  $elements['wc_product_related'] = [
    'label' => __('WooCommerce Product Related', 'post-field-widget'),
  ];
  $elements['wc_product_info'] = [
    'label' => __('WooCommerce Product Tabs', 'post-field-widget'),
  ];

  $product_tabs = apply_filters('woocommerce_product_tabs', []);
  foreach ($product_tabs as $key => $tab) {
    $elements['wc_product_tab_' . $key] = [
      'label' => sprintf(__('WooCommerce Product Tab: %s', 'post-field-widget'), $tab['title']),
      'callback' => 'post_field_widget_formatter_wc_product_tab',
      'args' => [
        'title' => $tab['title'],
        'tab' => $key
      ],
    ];
  }

  return $elements;
});


/**
 * Product teaser
 * @see woocommerce/templates/content-single-product.php
 */
function post_field_widget_formatter_wc_product_title() {

  if (!is_product()) {
    return;
  }

  woocommerce_template_single_title();
}

/**
 * Product teaser
 * @see woocommerce/templates/content-single-product.php
 */
function post_field_widget_formatter_wc_product_teaser_image() {

  if (!is_product()) {
    return;
  }

  woocommerce_show_product_images();
}

/**
 * Product teaser
 * @see woocommerce/templates/content-single-product.php
 */
function post_field_widget_formatter_wc_product_teaser_info() {

  if (!is_product()) {
    return;
  }

  woocommerce_template_single_title();
  woocommerce_template_single_rating();
  woocommerce_template_single_price();
  woocommerce_template_single_excerpt();
  woocommerce_template_single_add_to_cart();
  woocommerce_template_single_meta();
  woocommerce_template_single_sharing();

  $wc_sdata = new WC_Structured_Data();
  $wc_sdata->generate_product_data();
  $wc_sdata->output_structured_data();
}

/**
 * Product related products
 * @see woocommerce/templates/content-single-product.php
 */
function post_field_widget_formatter_wc_product_related() {
  if (!is_product()) {
    return;
  }
  woocommerce_upsell_display();
  woocommerce_output_related_products();
}

/**
 * Product tabs
 */
function post_field_widget_formatter_wc_product_tab($args) {
  if (!is_product()) {
    return;
  }
  woocommerce_output_product_data_tabs();
  static $tabs = array();
  if (!$tabs) {
    $tabs = apply_filters('woocommerce_product_tabs', array());
  }
  if (empty($tabs[$args['tab']])) {
    return;
  }
  call_user_func($tabs[$args['tab']]['callback']);
  return $args;
}