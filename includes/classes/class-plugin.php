<?php
namespace ConverseLab;

if(!defined('ABSPATH')){
    exit;
}

class Plugin{
    public static function init(){
        add_action('admin_menu',[__CLASS__, 'register_menu']);
        add_action('admin_enqueue_scripts',[__CLASS__,'enqueue_admin_assests']);
    }
    /**
     * register menu
     */

    public static function register_menu(){
        add_menu_page(
            'Converselab',
            'Converselab',
            'manage_options',
            'converselab',
            [__CLASS__, 'render_settings_page'],
            'dashicons-admin-generic'
        );

        add_submenu_page(
            'converselab',
            'Note App',
            'Notes App',
            'manage_options',
            'converselab-react-app',
            [__CLASS__,'render_react_page']
        );
    }

    /**
     * Set default options on plugin activation
     */
    public static function set_defaults() {
        if (get_option('converselab_notes_enabled') === false) {
            update_option('converselab_notes_enabled', 0);
        }

        if (get_option('converselab_notes_default_count') === false) {
            update_option('converselab_notes_default_count', 5);
        }

        if (get_option('converselab_notes_allowed_roles') === false) {
            update_option('converselab_notes_allowed_roles', []);
        }
    }

    public static function deactivate() {
        //
    }
    
    /**
     * render setting page
     */

    public static function render_settings_page(){
        if(!current_user_can('manage_options')){
            return;
        }

        $error_messages=[];

    // Handle form submission
        if(isset($_POST['converselab_settings_submit'])){

            if(!isset($_POST['converselab_nonce']) ||
                !wp_verify_nonce($_POST['converselab_nonce'],'converselab_save_settings')){
                $error_messages[] = __('Invalid security token.', 'converselab');
    ;
            }

            // Checkbox
            $notes_enabled = isset($_POST['converselab_notes_enabled']) ? 1 : 0;

            // Integer
            if (isset($_POST['converselab_notes_default_count'])) {
                    $default_count = intval($_POST['converselab_notes_default_count']);
                    if ($default_count < 1) {
                        $error_messages[] = __('Default Notes Count must be at least 1.', 'converselab');
                        $default_count = get_option('converselab_notes_default_count', 5);
                    }
                } else {
                    $default_count = get_option('converselab_notes_default_count', 5);
                }

            // Roles
            if(isset($_POST['converselab_notes_allowed_roles'])){
                $allowed_roles = $_POST['converselab_notes_allowed_roles'];

                if(!is_array($allowed_roles)){
                    $allowed_roles = [];
                }

                foreach($allowed_roles as $key => $role){
                    $allowed_roles[$key] = sanitize_text_field($role);
                }
            } else{
                $allowed_roles = [];
            }

            if (empty($error_messages)) {
                    update_option('converselab_notes_enabled', $notes_enabled);
                    update_option('converselab_notes_default_count', $default_count);
                    update_option('converselab_notes_allowed_roles', $allowed_roles);

                    echo '<div class="updated notice"><p>' . esc_html__('Settings saved.', 'converselab') . '</p></div>';
                } else{
                    foreach ($error_messages as $error){
                        echo '<div class="error notice"><p>' . esc_html($error) . '</p></div>';
                    }
                }
        }

    $notes_enabled = get_option( 'converselab_notes_enabled', 0 );
    $default_count = get_option( 'converselab_notes_default_count', 5 );
    $allowed_roles = get_option( 'converselab_notes_allowed_roles', [] );

    
    require CONVERSELAB_PATH . 'includes/views/settings-page.php';
    }

    public static function render_react_page(){
        echo '<div class="wrap"><div id="converselab-admin-app">Loading App....</div></div>';
    }

    public static function enqueue_admin_assests($hook){
        if($hook !=='converselab_page_converselab-react-app'){
            return;
        }

        $asset_path = CONVERSELAB_PATH . 'build/index.asset.php';

        if(!file_exists($asset_path)){
            return;
        }
        $asser_file = include($asset_path);
        $plugin_url = defined('CONVERSELAB_URL')?CONVERSELAB_URL : plugin_dir_url(dirname(__FILE__)); 

        wp_enqueue_script(
            'converselab-admin-js',
            $plugin_url.'build/index.js',
            $asser_file['dependencies'],
            $asser_file['version'],
            true
        );
        wp_localize_script('converselab-admin-js','converselabSettings',[
            'restUrl'=> esc_url_raw(rest_url('converselab/v1/notes')),
            'nonce'=> wp_create_nonce('wp_rest')
        ]);

    }

}


    // public static function load_textdomain(): void{
    //     load_plugin_textdomain(
    //         CONVERSELAB_TEXT_DOMAIN,
    //         false,
    //         dirname(plugin_basename(CONVERSELAB_PATH . 'converselab.php')) . '/languages'
    //     );
    // }
