<?php

/**
 * Register CMB2 fields.
 */
add_filter('post_field_widget_fields', function ($elements) {
  $unsupported = [
    'group',
    'colorpicker',
    'taxonomy_multicheck',
    'multicheck',
    'oembed'
  ];

  if (CMB2_Boxes::get_all()) {
    $elements['CMB2'] = [];
  }

  foreach (CMB2_Boxes::get_all() as $metabox) {
    foreach ($metabox->prop('fields') as $field_settings) {
      if (in_array($field_settings['type'], $unsupported)) {
        continue;
      }
      $elements['CMB2'][$metabox->cmb_id . '_' . $field_settings['name']] = array(
        'label'    => sprintf('(%s) %s', 'CMB2', implode('/', [ $metabox->prop('title'), $field_settings['name'] ])),
        'callback' => 'post_field_widget_formatter_acf',
        'args'     => [
          'field'   => $field_settings,
          'metabox' => $metabox->cmb_id
        ],
      );
    }
  }
  return $elements;
});

/**
 * CMB2 Formatter
 */
function cmb2_render_field($instance) {
  $value = cmb2_get_field_value($instance['field_info']['args']['metabox'], $instance['field_info']['args']['field']);
  if (!$value) {
    return;
  }
  if ($args['field']['type'] == 'file_list') {
    echo '<ul>';
    foreach ($value as $file) {
      echo '<li><a href="' . esc_attr($file) . '" rel="nofollow" target="_blank">' . basename($file) . '</a></li>';
    }
    echo '</ul>';
  }
  elseif ($args['field']['type'] == 'file') {
    echo '<a href="' . esc_attr($value) . '" rel="nofollow" target="_blank">' . basename($value) . '</a>';
  }
  elseif ($args['field']['type'] == 'image') {
    printf('<img src="%s" alt="%s" width="%s" height="%s" />', $value, esc_attr(basename($value)));
  }
  elseif ($args['field']['type'] == 'text_date_timestamp') {
    echo date(get_option('date_format'), $value);
  }
  elseif (is_scalar($value)) {
    echo $value;
  }
}
