<?php

/*
Version: 0.4.1809
Requires PHP: 5.4.0
Requires at least: 4.4.0
Plugin Name: Post Field Widget
Github Plugin URI: dimitrov-adrian/post-field-widget
Github Branch: master
Text Domain: post-field-widget
Description: Widget that display field (core, custom or meta) of post
Author: dimitrov.adrian
Author URI: https://github.com/dimitrov-adrian
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


/**
 * Register the Post Content Field Widget.
 */
add_action('widgets_init', function () {


  include __DIR__ . '/fields/core.php';

  if (function_exists('acf')) {
    include __DIR__ . '/fields/acf.php';
  }

  if (defined('WPCF_VERSION')) {
    include __DIR__ . '/fields/wpcf.php';
  }

  if (defined('CMB2_LOADED')) {
    include __DIR__ . '/fields/cmb2.php';
  }

  if (defined('WC_PLUGIN_FILE')) {
    include __DIR__ . '/fields/woocommerce.php';
  }

  register_widget( 'Post_field_Widget' );
});

/**
 * Widget content field
 */
class Post_field_Widget extends WP_Widget
{

  // Multiple instance cache.
  static $fieldsCache = null;

  /**
   * {@inheritdoc}
   */
  function __construct()
  {
    parent::__construct(false, __('Post Content Field', 'post-field-widget') );
  }

  /**
   * Supported field formatters of the widget
   *
   * @return array
   */
  function getAvailableFields()
  {
    if (self::$fieldsCache === null) {
      self::$fieldsCache = apply_filters( 'post_field_widget_fields', [] );
    }
    return self::$fieldsCache;
  }

  /**
   * Widget defaults.
   *
   * @return array
   */
  function defaultSettings()
  {
    return [
      'field'  => '',
      'widget-wrappers' => false,
      'hide-empty' => true,
      'link-to-post' => false,
      'noresult' => '',
      'rewrite'  => '',
      'field_settings' => [],
    ];
  }

  /**
   * Widget settings from.
   *
   * {@inheritdoc}
   */
  function form($instance)
  {

    $instance = wp_parse_args($instance, $this->defaultSettings());
    $js_function_name = 'onChange' . md5($this->id);

    ?>

    <p>
      <label for="<?php echo $this->get_field_id('field') ?>">
        <?php _e('field:', 'post-field-widget') ?>
      </label>
      <select onchange="<?php echo $js_function_name ?>(this)"
              id="<?php echo $this->get_field_id('field') ?>"
              name="<?php echo $this->get_field_name('field') ?>"
              class="widefat">
        <?php foreach ($this->getAvailableFields() as $field_name => $field_data): ?>
          <option <?php selected($instance['field'], $field_name) ?> value="<?php echo esc_attr($field_name) ?>">
            <?php echo $field_data['label'] ?>
          </option>
        <?php endforeach ?>
      </select>
      <script>
        function <?php echo $js_function_name?>(element) {
          document.querySelectorAll('.<?php echo $this->get_field_id('field-args-')?>')
            .forEach(function(lelement) {
              lelement.style.display = 'none';
            });
          document.querySelectorAll('#<?php echo $this->get_field_id('field-args-')?>-' + element.value )
            .forEach(function(element) {
              element.style.display = null;
            });
        }
      </script>
    </p>

    <p>
      <input id="<?php echo $this->get_field_id('widget-wrappers') ?>"
             name="<?php echo $this->get_field_name('widget-wrappers') ?>"
             type="checkbox"
             value="1"
        <?php checked(! empty($instance['widget-wrappers']), true) ?> />
      <label for="<?php echo $this->get_field_id('widget-wrappers') ?>">
        <?php _e('Use Widget HTML wrappers', 'post-field-widget') ?>
      </label>
    </p>

    <p>
      <input id="<?php echo $this->get_field_id('hide-empty') ?>"
             name="<?php echo $this->get_field_name('hide-empty') ?>"
             type="checkbox"
             value="1"
        <?php checked(! empty($instance['hide-empty']), true) ?> />
      <label for="<?php echo $this->get_field_id('hide-empty') ?>">
        <?php _e('Hide widget when empty', 'post-field-widget') ?>
      </label>
    </p>

    <p>
      <input id="<?php echo $this->get_field_id('link-to-post') ?>"
             name="<?php echo $this->get_field_name('link-to-post') ?>"
             type="checkbox"
             value="1"
        <?php checked(! empty($instance['link-to-post']), true) ?> />
      <label for="<?php echo $this->get_field_id('link-to-post') ?>">
        <?php _e('Link this item to the post of context.', 'post-field-widget') ?>
      </label>
    </p>

    <div>
      <?php foreach ($this->getAvailableFields() as $field_name => $field_data):
        $fieldset_style = $instance['field'] != $field_name ? 'style="display:none;"' : '';
        ?>

        <?php if ( ! empty($field_data['args'])): ?>
        <fieldset class="<?php echo $this->get_field_id('field-args-') ?>"
                  id="<?php echo $this->get_field_id('field-args-') ?>-<?php echo esc_attr($field_name) ?>"
          <?php echo $fieldset_style?> >

          <?php foreach ($field_data['args'] as $setting_name => $setting_label): ?>
            <p>
              <label for="<?php echo $this->get_field_id("field-args-{$field_name}-{$setting_name}")?>">
                <?php echo $setting_label ?>
              </label>
              <input id="<?php echo $this->get_field_id("field-args-{$field_name}-{$setting_name}") ?>"
                     name="<?php echo $this->get_field_name('field_args') ?>[<?php echo $field_name ?>][<?php echo $setting_name ?>]"
                     type="text"
                     class="widefat"
                     value="<?php echo empty($instance['field_settings'][$field_name][$setting_name]) ? '' : esc_attr($instance['field_args'][$field_name][$setting_name]) ?>" />
            </p>
          <?php endforeach ?>

        </fieldset>
      <?php endif ?>

      <?php endforeach ?>
    </div>

    <p>
      <label for="<?php echo $this->get_field_id('rewrite') ?>">
        <?php _e('Rewrite content:', 'post-field-widget') ?>
      </label>
      <textarea class="widefat"
                id="<?php echo $this->get_field_id('rewrite') ?>"
                name="<?php echo $this->get_field_name('rewrite') ?>"><?php echo esc_html($instance['rewrite'])?></textarea>
      <?php _e('Custom content to rewrite the value (supported variable tags %value%, %value_plain_text%, %title%, %post_url%). <strong>HTML tags are supported.</strong>','post-field-widget') ?>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('noresult') ?>">
        <?php _e('No result value:', 'post-field-widget') ?>
      </label>
      <textarea class="widefat"
                id="<?php echo $this->get_field_id('noresult') ?>"
                name="<?php echo $this->get_field_name('noresult') ?>"><?php echo esc_html($instance['noresult']) ?></textarea>
    </p>

    <?php
  }

  /**
   * Widget renderer
   *
   * {@inheritdoc}
   */
  function widget($args, $instance)
  {

    $instance = wp_parse_args($instance, $this->defaultSettings());

    // Set the context to current post.
    $post = get_post();

    if ( ! $post) {
      return;
    }

    $fields = $this->getAvailableFields();

    if ( ! $instance['field'] ) {
      return;
    }

    if ( empty($fields[$instance['field']]['callback']) ) {
      $fields[$instance['field']]['callback'] = 'post_field_widget_formatter_' . $instance['field'];
    }

    ob_start();

    call_user_func($fields[$instance['field']]['callback'], $instance['field_settings']);

    $plain_text_allowed_tags = '<img><iframe><picture><figure><object>';

    $result = [
      '%value%' => ob_get_clean(),
    ];
    $result['%value_plain_text%'] = trim(strip_tags($result['%value%'], $plain_text_allowed_tags));


    if ( empty($result['%value_plain_text%']) ) {
      if ( ! empty($instance['hide-empty']) ) {
        return;
      }
      $result['%value%'] = $instance['noresult'];
      $result['%value_plain_text%'] = trim(strip_tags($result['%value%'], $plain_text_allowed_tags));
    }

    $result['%post_url%'] = esc_attr(get_the_permalink($post));

    // Do the rewrite.
    if ( ! empty( $instance['rewrite'] )) {
      $result['%value%'] = strtr($instance['rewrite'], $result);
    }

    // If there is link, use it.
    if ( ! empty( $instance['link-to-post'] )) {
      $pattern = '/<a(.*)href=(")?([a-zA-Z]+)"? ?(.*)>(.*)<\/a>/i';
      if (preg_match($pattern, $result['%value%'])) {
        $result['%value%'] = preg_replace($pattern, $result['%post_url%'], $result['%value%']);
      } else {
        $result['%value%'] = '<a href="' . $result['%post_url%'] . '">' . $result['%value%'] . '</a>';
      }
    }

    // Reserved setting name widgettitle
    if ( ! empty( $instance['field_settings']['widgettitle'] )) {
      $result['%value%'] = $args['before_title'] . $instance['field_settings']['widgettitle'] . $args['after_title'] . $result['%value%'];
    }

    // Render the field.
    if ( empty($instance['widget-wrappers']) ) {
      echo $result['%value%'];
    } else {
      echo $args['before_widget'], $result['%value%'], $args['after_widget'];
    }

  }

}
