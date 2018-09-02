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

  foreach (CMB2_Boxes::get_all() as $metabox) {
    foreach ($metabox->prop('fields') as $field_args) {
      if (in_array($field_args['type'], $unsupported)) {
        continue;
      }
      $elements[$metabox->cmb_id . '_' . $field_args['name']] = array(
        'label'    => sprintf('(%s) %s', 'CMB2', implode('/', [ $metabox->prop('title'), $field_args['name'] ])),
        'callback' => 'post_field_widget_formatter_acf',
        'args'     => [
          'field'   => $field_args,
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
function cmb2_render_field($args, $options = []) {
  $value = cmb2_get_field_value($args['metabox'], $args['field']);
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
