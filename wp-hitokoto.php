<?php
/*
Plugin Name: wp-hitokoto
Plugin URI: https://github.com/moycat/wp-hitokoto
Description: 由hitokoto本地源改制的WordPress插件
Version: 0.1
Author: Moycat via hitokoto
Author URI: http://hitokoto.us/
*/

$hitokoto_db = null;
$hitokoto_now = null;

/**
 * Class HitokotoWidget
 */
class HitokotoWidget extends WP_Widget {
    protected $default_format = '{hitokoto} <code style="white-space: nowrap;">{source}</code>';

    function __construct() {
        parent::__construct(
            'hitokoto_widget',
            __('Hitokoto 一言', 'wp-hitokoto' ),
            array (
                'description' => __( '由hitokoto本地源改制的WordPress侧边栏插件', 'wp-hitokoto' )
            )
        );
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        if (empty($instance['format'])) {
            $format = $this->default_format;
        } else {
            $format = $instance['format'];
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                <?php _e('Title:'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('format'); ?>">
                <?php _e('Content:'); ?>
                <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format'); ?>"><?php echo $format; ?></textarea>
            </label>
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        if (current_user_can('unfiltered_html')) {
            $instance['format'] = $new_instance['format'];
        } else {
            $instance['format'] = wp_kses_post($new_instance['format']);
        }
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    function widget($args, $instance) {
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $format = empty($instance['format']) ? $this->default_format : $instance['format'];
        $hitokoto = hitokoto_full();

        $patterns = [];
        $replacements = [];
        foreach ($hitokoto as $key => $value) {
            $patterns[] = '/\{'.$key.'\}/';
            $replacements[] = $value;
        }
        $output = preg_replace($patterns, $replacements, $format);

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        } ?>
        <div class="textwidget"><?php echo !empty( $instance['filter'] ) ? wpautop($output) : $output; ?></div>
        <?php
        echo $args['after_widget'];
    }
}

/**
 * Register the widget.
 */
function hitokoto_register_widget() {
    register_widget( 'HitokotoWidget' );
}
add_action( 'widgets_init', 'hitokoto_register_widget' );

/**
 * Get a certain part of a piece.
 * @param $type
 * @param bool $print
 * @return string
 */
function hitokoto($type, $print = true)
{
    global $hitokoto_now;

    if ($hitokoto_now === null) {
        hitokoto_read();
    }

    $rt = '';

    if (isset($hitokoto_now[$type])) {
        $rt = $hitokoto_now[$type];
    }

    if ($print) {
        echo $rt;
        return strlen($rt);
    } else {
        return $rt;
    }
}

/**
 * Fetch a new piece to $hitokoto_now;
 */
function hitokoto_read()
{
    global $hitokoto_now;

    if (!hitokoto_read_json()) {
        $hitokoto_now = [];
        return;
    }

    $hitokoto_now = hitokoto_random();
}

/**
 * Init hitokoto.
 */
function hitokoto_read_json()
{
    static $read = -1;

    if ($read > -1) {
        return $read;
    }

    global $hitokoto_db;

    $data  = dirname(__FILE__) . '/hitokoto.json';
    $json  = file_get_contents($data);
    $hitokoto_db = json_decode($json, true);
    if (!$hitokoto_db) {
        return $read = 0;
    }
    return $read = count($hitokoto_db);
}

/**
 * Print the content of some piece only.
 */
function hitokoto_single()
{
    if (!hitokoto_read_json()) {
        echo '';
        return;
    }

    echo hitokoto_random()['hitokoto'];
}

/**
 * Get an array of some piece.
 * @return null|array
 */
function hitokoto_full()
{
    if (!hitokoto_read_json()) {
        return null;
    }

    return hitokoto_random();
}

function hitokoto_random()
{
    global $hitokoto_db;

    $hitokoto = $hitokoto_db[array_rand($hitokoto_db)];

    foreach ($hitokoto as $key => $value) {
        if ($value === '') {
            $hitokoto[$key] = 'Unknown';
        }
    }

    return $hitokoto;
}