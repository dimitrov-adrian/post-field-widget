<?php

/**
 * Register WPCF fields.
 */
add_filter('post_field_widget_fields', function ($elements) {
  foreach (wpcf_admin_fields_get_fields() as $field) {
    $elements[$field['meta_key']] = [
      'label' => sprintf('(%s) %s', 'ACF', implode('/' , [ $field['title'], $field['type'] ])),
      'callback' => 'post_field_widget_formatter_wpcf',
      'args' => $field,
    ];
  }
  return $elements;
});

/**
 * WPCF Formatter
 */
function post_field_widget_formatter_wpcf($args) {
  // File
  if ($args['type'] == 'file') {
    $urls = explode(' ', types_render_field($args['slug'], [ 'output' => 'raw' ]));
    $multiple = count($urls) > 1;
    if ($multiple) {
      echo '<ul>';
    }
    foreach ($urls as $url) {
      if ($multiple) {
        printf('<li><a href="%s" target="_blank">%s</a></li>', $url, basename($url));
      }
      else {
        printf('<a href="%s" target="_blank">%s</a>', $url, basename($url));
      }
    }
    if ($multiple) {
      echo '</ul>';
    }
  }
  // All others
  else {
    echo types_render_field($args['slug']);
  }
}