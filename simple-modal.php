<?php
/**
 * Plugin Name: Simple Modal
 * Plugin URI: http://claudiosmweb.com/
 * Description: Displays a modal content in your WordPress
 * Author: claudiosanches
 * Author URI: http://claudiosmweb.com/
 * Version: 1.0
 * License: GPLv2 or later
 * Text Domain: simplemodal
 * Domain Path: /languages/
 */

class Simple_Modal {

    /**
     * Construct.
     *
     * @return void
     */
    public function __construct() {

        add_action( 'plugins_loaded', array( &$this, 'languages' ), 0 );

        // Default options.
        register_activation_hook( __FILE__, array( &$this, 'default_settings' ) );

        // Add menu.
        add_action( 'admin_menu', array( &$this, 'menu' ) );

        // Init plugin options form.
        add_action( 'admin_init', array( &$this, 'plugin_settings' ) );

        // Load scripts in front-end.
        add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ), 999 );

        // Load admin scripts.
        add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts' ) );

        // Display the modal.
        add_action( 'wp_footer', array( &$this, 'display_modal' ), 9999 );

        // Modal js vars.
        add_action( 'wp_head', array( &$this, 'modal_js_vars' ), 9999 );
    }

    /**
     * Load translations.
     *
     * @return void
     */
    public function languages() {
        load_plugin_textdomain( 'simplemodal', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Load admin scripts.
     *
     * @return void
     */
    function admin_scripts() {
        // Color Picker.
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );

        // Theme Options.
        wp_register_script( 'simplemodal-admin', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), null, true );
        wp_enqueue_script( 'simplemodal-admin' );
    }

    /**
     * Enqueue plugin scripts.
     *
     * @return void
     */
    public function enqueue_scripts() {
        // Get plugin settings.
        $settings = get_option( 'simplemodal_settings' );

        if ( $this->is_visible( $settings ) ) {

            wp_register_script( 'simplemodal', plugins_url( 'assets/js/simplemodal.js', __FILE__ ), array( 'jquery' ), null, true );
            wp_enqueue_script( 'simplemodal' );

            wp_register_style( 'simplemodal', plugins_url( 'assets/css/simplemodal.css', __FILE__ ), array(), null, 'all' );
            wp_enqueue_style( 'simplemodal' );
        }
    }

    /**
     * Sets the modal visibility.
     *
     * @param  array $settings Plugin settings.
     *
     * @return bool
     */
    private function is_visible( $settings ) {
        $show = false;

        if ( isset( $settings['active'] ) && 1 == $settings['active'] ) {
            $show = true;
        }

        if ( isset( $settings['only_home'] ) && 1 == $settings['only_home'] && ! is_home() && ! is_front_page() ) {

            $show = false;
        }

        return $show;
    }

    /**
     * Set default settings.
     *
     * @return void.
     */
    public function default_settings() {

        $default = array(
            'active'     => '0',
            'time'       => '1',
            'only_home'  => '0',
            'background' => '#000000',
            'width'      => '500',
            'height'     => '300',
            'content'    => ''
        );

        add_option( 'simplemodal_settings', $default );
    }

    /**
     * Add menu.
     *
     * @return void.
     */
    public function menu() {
        add_options_page(
            __( 'Simple Modal', 'simplemodal' ),
            __( 'Simple Modal', 'simplemodal' ),
            'manage_options',
            'simplemodal',
            array( $this, 'settings_page' )
        );
    }

    /**
     * Built the options page.
     *
     * @return string Settings page HTML.
     */
    public function settings_page() {
        ?>

            <div class="wrap">
                <div class="icon32" id="icon-options-general"><br /></div>
                <h2><?php _e( 'Simple Modal', 'simplemodal' ); ?></h2>

                <form method="post" action="options.php">

                    <?php
                        settings_fields( 'simplemodal_settings' );
                        do_settings_sections( 'simplemodal_settings' );

                        submit_button();
                    ?>

                </form>

            </div>

        <?php
    }

    /**
     * Plugin settings form fields.
     *
     * @return void.
     */
    public function plugin_settings() {
        $option = 'simplemodal_settings';

        // Create option in wp_options.
        if ( false == get_option( $option ) ) {
            add_option( $option );
        }

        // Set settings section.
        add_settings_section(
            'settings_section',
            __( 'Settings:', 'simplemodal' ),
            array( &$this, 'callback_section' ),
            $option
        );

        // Display the Modal.
        add_settings_field(
            'active',
            __( 'Display the modal:', 'simplemodal' ),
            array( &$this, 'callback_checkbox' ),
            $option,
            'settings_section',
            array(
                'tab' => $option,
                'id' => 'active',
                'description' => __( 'Enable to display the modal.', 'simplemodal' )
            )
        );

        // Cookie expiration.
        add_settings_field(
            'time',
            __( 'Cookie expiration:', 'simplemodal' ),
            array( &$this, 'callback_text' ),
            $option,
            'settings_section',
            array(
                'tab' => $option,
                'id' => 'time',
                'description' => __( 'Days of the cookie will be valid until the modal view again.', 'simplemodal' )
            )
        );

        // Display only in homepage.
        add_settings_field(
            'only_home',
            __( 'Display only in homepage:', 'simplemodal' ),
            array( &$this, 'callback_checkbox' ),
            $option,
            'settings_section',
            array(
                'tab' => $option,
                'id' => 'only_home',
                'description' => __( 'View the modal only on homepage.', 'simplemodal' )
            )
        );

        // Set design section.
        add_settings_section(
            'design_section',
            __( 'Design:', 'simplemodal' ),
            array( &$this, 'callback_section' ),
            $option
        );

        // Background.
        add_settings_field(
            'background',
            __( 'Background:', 'simplemodal' ),
            array( &$this, 'callback_color' ),
            $option,
            'design_section',
            array(
                'tab' => $option,
                'id' => 'background',
                'description' => ''
            )
        );

        // Width
        add_settings_field(
            'width',
            __( 'Width:', 'simplemodal' ),
            array( &$this, 'callback_text' ),
            $option,
            'design_section',
            array(
                'tab' => $option,
                'id' => 'width',
                'description' => ''
            )
        );

        // Height.
        add_settings_field(
            'height',
            __( 'Height:', 'simplemodal' ),
            array( &$this, 'callback_text' ),
            $option,
            'design_section',
            array(
                'tab' => $option,
                'id' => 'height',
                'description' => ''
            )
        );

        // Set content section.
        add_settings_section(
            'content_section',
            __( 'Content:', 'simplemodal' ),
            array( &$this, 'callback_section' ),
            $option
        );

        // Address Autocomplete option.
        add_settings_field(
            'content',
            __( 'Content:', 'simplemodal' ),
            array( &$this, 'callback_editor' ),
            $option,
            'content_section',
            array(
                'tab' => $option,
                'id' => 'content',
                'description' => '',
                'options' => ''
            )
        );

        // Register settings.
        register_setting( $option, $option, array( $this, 'validate_input' ) );
    }

    /**
     * Get Option.
     *
     * @param  string $tab     Tab that the option belongs.
     * @param  string $id      Option ID.
     * @param  string $default Default option.
     *
     * @return array           Item options.
     */
    protected function get_option( $tab, $id, $default = '' ) {
        $options = get_option( $tab );

        if ( isset( $options[$id] ) ) {
            $default = $options[$id];
        }

        return $default;

    }

    /**
     * Section null fallback.
     *
     * @return void.
     */
    public function callback_section() {

    }

    /**
     * Checkbox field callback.
     *
     * @param array $args Arguments from the option.
     */
    public function callback_checkbox( $args ) {
        $tab = $args['tab'];
        $id  = $args['id'];

        // Sets current option.
        $current = esc_html( $this->get_option( $tab, $id ) );

        $html = sprintf( '<input type="checkbox" id="%1$s" name="%2$s[%1$s]" value="1"%3$s />', $id, $tab, checked( 1, $current, false ) );

        // Displays option description.
        if ( $args['description'] ) {
            $html .= sprintf( '<label for="%s"> %s</label>', $id, $args['description'] );
        }

        echo $html;
    }

    /**
     * Text field callback.
     *
     * @param array $args Arguments from the option.
     */
    public function callback_text( $args ) {
        $tab = $args['tab'];
        $id  = $args['id'];

        // Sets current option.
        $current = esc_html( $this->get_option( $tab, $id ) );

        // Sets input size.
        $size = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

        $html = sprintf( '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="%4$s-text" />', $id, $tab, $current, $size );

        // Displays option description.
        if ( $args['description'] ) {
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );
        }

        echo $html;
    }

    /**
     * Color field callback.
     *
     * @param array $args Arguments from the option.
     */
    public function callback_color( $args ) {
        $tab = $args['tab'];
        $id  = $args['id'];

        // Sets current option.
        $current = esc_html( $this->get_option( $tab, $id ) );

        $html = sprintf( '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="simplemodal-color-field" />', $id, $tab, $current );

        // Displays option description.
        if ( $args['description'] ) {
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );
        }

        echo $html;
    }

    /**
     * Editor field callback.
     *
     * @param array $args Arguments from the option.
     */
    public function callback_editor( $args ) {
        $tab     = $args['tab'];
        $id      = $args['id'];
        $options = $args['options'];

        // Sets current option.
        $current = esc_html( $this->get_option( $tab, $id ) );

        // Sets input size.
        $size = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : '600px';

        // Set default options.
        if ( empty( $options ) ) {
            $options = array( 'textarea_rows' => 10 );
        }

        echo '<div style="width: ' . $size . ';">';

            wp_editor( $current, $tab . '[' . $id . ']', $options );

        echo '</div>';

        // Displays option description.
        if ( $args['description'] ) {
            echo sprintf( '<p class="description">%s</p>', $args['description'] );
        }
    }

    /**
     * Sanitization fields callback.
     *
     * @param  string $input The unsanitized collection of options.
     *
     * @return string        The collection of sanitized values.
     */
    public function validate_input( $input ) {

        // Create our array for storing the validated options
        $output = array();

        // Loop through each of the incoming options
        foreach ( $input as $key => $value ) {

            // Check to see if the current option has a value. If so, process it.
            if ( isset( $input[$key] ) ) {

                // Filter for validation.
                $output[$key] = apply_filters( 'simplemodal_validate_settings', $value );
            }
        }

        return $output;
    }

    /**
     * Insert the modal js vars.
     *
     * @return string HTML and JavaScript.
     */
    public function modal_js_vars() {
        // Get plugin settings.
        $settings = get_option( 'simplemodal_settings' );

        if ( $this->is_visible( $settings ) ) {

            $time = isset( $settings['time'] ) ? $settings['time'] : '1';

            $js = '<script type="text/javascript">' . PHP_EOL;
            $js .= '/* <![CDATA[ */' . PHP_EOL;
            $js .= 'var simplemodal_params = {"time": "' . $time. '"};' . PHP_EOL;
            $js .= '/* ]]> */' . PHP_EOL;
            $js .= '</script>' . PHP_EOL;

            echo $js;
        }
    }

    /**
     * Display the modal.
     *
     * @return string HTML of the modal.
     */
    public function display_modal() {
        // Get plugin settings.
        $settings = get_option( 'simplemodal_settings' );

        if ( $this->is_visible( $settings ) ) {

            $background = isset( $settings['background'] ) ? $settings['background'] : '#000000';
            $width = isset( $settings['width'] ) ? $settings['width'] : '500';
            $height = isset( $settings['height'] ) ? $settings['height'] : '300';
            $margin = sprintf( '-%spx 0 0 -%spx', ( ( $height + 10 ) / 2 ), ( ( $width + 10 ) / 2 ) );
            $content = isset( $settings['content'] ) ? apply_filters( 'the_content', $settings['content'] ) : '';

            $html = sprintf( '<div id="simplemodal" class="simplemodal" style="background: %s">', $background );
            $html .= '</div>';
            $html .= sprintf( '<div id="simplemodal-content" class="simplemodal" style="width: %spx; height: %spx; margin: %s;">', $width, $height, $margin );
            $html .= '<div id="simplemodal-close"></div>';
            $html .= $content;
            $html .= '</div>';

            echo $html;
        }
    }

}

$simple_modal = new Simple_Modal;
