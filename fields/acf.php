<?php

/**
 * Register ACF fields.
 */
add_filter('post_field_widget_fields', function ($elements) {

  $acf_groups = apply_filters('acf/get_field_groups', []);

  foreach ($acf_groups as $acf) {
    foreach (apply_filters('acf/field_group/get_fields', [], $acf['id']) as $field) {
      $elements[$field['id']] = [
        'label' => sprintf('(%s) %s', 'ACF', implode('/' , [ $acf['title'], $field['label'], $field['type'] ])),
        'callback' => 'post_field_widget_formatter_acf',
        'args' => $field,
      ];
    }
  }

  return $elements;
});


/**
 * ACF Formatter
 */
function post_field_widget_formatter_acf($args) {
  if (!empty($args['name']) && ($field = get_field($args['name']))) {
    // id
    // label
    // class
    if ($args['type'] == 'image') {
      $alt = empty($field['alt']) ? $field['title'] : $field['alt'];
      printf('<img src="%s" alt="%s" width="%s" height="%s" />', $field['url'], $alt, $field['width'], $field['height']);
    }
    //elseif ($args['type'] == 'file') {
    //}
    else {
      the_field($args['name']);
    }
  }
}
