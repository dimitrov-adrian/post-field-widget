<?php


add_filter('post_field_widget_fields', function ($elements) {
  $elements = array_replace_recursive($elements, [
    'Global' => [
      'empty'          => [
        'callback' => '__return_null',
        'label'    => __('Empty Value', 'post-content-widget'),
      ],
    ],
    'Post' => [
      'post_name'      => [
        'label'    => __('Post Name', 'post-content-widget'),
      ],
      'post_title'      => [
        'label'    => __('Post Title', 'post-content-widget'),
      ],
      'post_thumbnail'  => [
        'label'    => __('Post Thumbnail', 'post-content-widget'),
        'settings' => [
          'size' => __('Size string or dimensions', 'post-content-widget'),
        ],
      ],
      'post_thumbnail_url'  => [
        'label'    => __('Post Thumbnail URL only', 'post-content-widget'),
        'settings' => [
          'size' => __('Size string or dimensions', 'post-content-widget'),
        ],
      ],
      'post_excerpt'    => [
        'label'    => __('Post Excerpt', 'post-content-widget'),
      ],
      'post_content'    => [
        'label'    => __('Post Content', 'post-content-widget'),
      ],
      'post_custom_fields' => [
        'label'    => __('Post Custom Meta', 'post-content-widget'),
        'settings' => [
          'post_field_name'  => __('Field Name', 'post-content-widget'),
          'post_field_label' => __('Override Label', 'post-content-widget'),
        ],
      ],
      'post_date'      => [
        'label'    => __('Post Date', 'post-content-widget'),
      ],
      'comments_block'  => [
        'label'    => __('Comments block', 'post-content-widget'),
      ],
      'post_info'       => [
        'label'    => __('Post Info', 'post-content-widget'),
      ],
      'post_author_bio' => [
        'label'    => __('Post author bio', 'post-content-widget'),
      ],
    ],
  ]);

  $public_taxonomies = get_taxonomies(['public' => true], 'names');
  if ($public_taxonomies) {
    $elements['Taxonomy'] = [];
    foreach ($public_taxonomies as $taxonomy) {
      $elements['Taxonomy']['taxonomy-' . $taxonomy] = [
        'label'    => sprintf(__('Taxonomy - %s', 'post-content-widget'), $taxonomy),
        'callback' => 'post_field_widget_formatter_taxonomy',
        'args'     => [
          'taxonomy' => $taxonomy
        ],
      ];
    }
  }

  return $elements;
});

/**
 * Title formatter.
 */
function post_field_widget_formatter_post_name()
{
  $post = get_post();
  if ($post) {
    echo $post->post_name;
  }
}

/**
 * Title formatter.
 */
function post_field_widget_formatter_post_title()
{
  if ( ! get_the_title()) {
    return;
  }

  if ( is_singular() || in_the_loop() ) {
    if (defined('WOOCOMMERCE_VERSION') && is_woocommerce() && is_product()) {
      woocommerce_template_single_title();
    } else {
      echo '<h1 class="entry-title">' . get_the_title() . '</h1>';
    }
  } else {
    echo '<h1 class="entry-title">';
    if (defined('WOOCOMMERCE_VERSION') && is_woocommerce()) {
      woocommerce_page_title();
    } else {
      post_type_archive_title();
    }
    echo '</h1>';
  }
}

/**
 * Post thumbnail
 */
function post_field_widget_formatter_post_thumbnail($instance)
{
  if (has_post_thumbnail()) {
    $size = (is_single() ? 'large' : 'medium');
    if ( ! empty($instance['field_settings']['size'])) {
      if (strpos($instance['field_settings']['size'], ',')) {
        $size = explode(',', $size);
      } else {
        $size = $instance['field_settings']['size'];
      }
    }
    ?>
    <div class="entry-thumbnail-wrapper">
      <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute() ?>">
        <?php the_post_thumbnail($size, 'class=thumbnail') ?>
      </a>
    </div>
    <?php
  }
}

/**
 * Post thumbnail
 */
function post_field_widget_formatter_post_thumbnail_url($instance)
{
  if (has_post_thumbnail()) {
    $size = (is_single() ? 'large' : 'medium');
    if ( ! empty($instance['field_settings']['size'])) {
      if (strpos($instance['field_settings']['size'], ',')) {
        $size = explode(',', $size);
      } else {
        $size = $instance['field_settings']['size'];
      }
    }
    echo get_the_post_thumbnail_url(null, $size);
  }
}

/**
 * Excerpt formatter.
 */
function post_field_widget_formatter_excerpt()
{
  if (get_the_excerpt()) {
    ?>
    <div class="entry-summary teaser">
      <?php the_excerpt() ?>
    </div>
    <?php
  }
}

/**
 * Meta formatter.
 */
function post_field_widget_formatter_post_info()
{
  ?>
  <div class="entry-header-meta">
    <time class="entry-header-meta-time" datetime="<?php the_time('c') ?>" pubdate="pubdate">
      <?php the_time(get_option('date_format')) ?>
    </time>
    <span class="entry-header-meta-comments">
        <?php comments_number('0', '1', '%') ?>
      </span>
    <span class="entry-header-meta-author">
        <?php the_author() ?>
      </span>
  </div>
  <?php
}

/**
 * Date formatter.
 */
function post_field_widget_formatter_post_date()
{
  ?>
  <time class="entry-header-meta-time" datetime="<?php the_time('c') ?>" pubdate="pubdate">
    <?php the_time(get_option('date_format')) ?>
  </time>
  <?php
}

/**
 * Taxonomy formatter.
 */
function post_field_widget_formatter_taxonomy($instance)
{
  if ($terms = get_the_terms(get_the_ID(), $instance['field_info']['args']['taxonomy'])) {
    ?>
    <div class="field-taxonomy-terms">
      <?php foreach ($terms as $term): ?>
        <a rel="nofollow" href="<?php echo get_term_link($term) ?>"
           title="<?php echo $term->name ?>"
           class="taxonomy-term-<?php echo $term->term_id ?>">
          <?php echo $term->name ?>
        </a>
      <?php endforeach ?>
    </div>
    <?php
  }
}

/**
 * Custom post field formatter.
 */
function post_field_widget_formatter_post_custom_field($instance)
{
  if ( ! empty($instance['field_settings']['post_field_name'])) {
    return;
  }
  $post_meta = get_post_meta(get_the_ID(), $instance['field_settings']['post_field_name'], false);
  if (empty($post_meta)) {
    return;
  }
  ?>
  <div class="post-custom-meta-field post-custom-meta-field-<?php echo esc_attr($instance['field_settings']['post_field_name']) ?>">
    <span class="post-custom-meta-label">
      <?php echo($instance['field_settings']['post_field_label']
        ? $instance['field_settings']['field_settings']['post_field_label']
        : $instance['field_settings']['post_field_name']) ?>
    </span>
    <span class="post-custom-meta-value"> <?php echo implode(', ', $post_meta) ?> </span>
  </div>
  <?php
}

/**
 * Content formatter.
 */
function post_field_widget_formatter_content()
{
  if (get_the_content()) {
    ?>
    <div class="entry-content">
      <?php
        the_content();
        posts_nav_link();
      ?>
    </div>
    <?php
  }
}

/**
 * Comments block
 */
function post_field_widget_formatter_comments_block()
{
  $result          = [];
  $result['title'] = get_comments_number_text();
  comments_template();
}

/**
 * Post author bio
 */
function post_field_widget_formatter_post_author_bio()
{
  get_template_part('templates/author-bio');
}
