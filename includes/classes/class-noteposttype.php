<?php 
namespace ConverseLab;

class NotePostType{
    public static function init(){
        add_action('init',[__CLASS__, 'register_cpt']);
        add_action('init',[__CLASS__, 'register_meta']);
        add_action('add_meta_boxes',[__CLASS__, 'add_meta_boxes']);
        add_action('save_post',[__CLASS__, 'save_meta']);
    }

    public static function register_cpt(){
        $labels=[
            'name'              => 'Converselab Notes',
            'singular_name'     => 'Note',
            'menu_name'         => 'Converselab Notes',
            'add_new'           => 'Add new note',
            'add_new_item'      => 'Add new Converselab Note',
            'edit_item'         => 'Edit note',
            'search_item'       => 'Search Notes',
            'not_found'         => 'No notes found',
            'not_found_in_trash'=> 'No notes found in trash',
        ];
        $args = [
            'labels'              => $labels,
            'public'              => false,  
            'show_ui'             => true,   
            'show_in_menu'        => true, 
            'show_in_rest'        => true,  
            'menu_icon'           => 'dashicons-format-aside', 
            'capability_type'     => 'post', 
            'map_meta_cap'        => true,
            'supports'            => [ 'title', 'editor', 'custom-fields' ], 
            'rewrite'             => false,
        ];

        register_post_type('converselab_note', $args);
    }

    public static function register_meta(){
        register_post_meta('converselab_note', 'converselab_note_priority',[
            'show_in_rest'      => true,
            'single'            =>true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        register_post_meta('converselab_note', 'converselab_note_source_url',[
            'show_in_rest'      => true,
            'single'            =>true,
            'type'              => 'string',
            'sanitize_callback' => 'esc_url_raw',
        ]);
    }
    
    public static function add_meta_boxes(){
        add_meta_box(
            'converselab_meta_box',
            'Note Details',
            [__CLASS__, 'render_meta_box'],
            'converselab_note',
            'side',
            'default'
        );
    }

    public static function render_meta_box($post){
        $priority = get_post_meta($post->ID, 'converselab_note_priority', true);
        $url = get_post_meta($post->ID, 'converselab_note_source_url', true);

        wp_nonce_field('converselab_save_note_meta', 'converselab_note_nonce');

        ?>
        <p>
            <label for="converselab_note_priority"> <strong?>Priority: </strong></label>
            <select for="converselab_note_priority" id="converselab_note_priority" style="width=100%">
                <option value="low" <?php selected($priority, 'low');?> > Low</option>
                <option value="meduim" <?php selected($priority, 'medium');?> > Medium</option>
                <option value="high" <?php selected($priority, 'high');?> > High</option>
            </select>
        </p>
        <p>
            <label for="converselab_note_source_url"> <strong?>Source URL: </strong></label>
            <br>
            <input type="url" name="converselab_note_source_url" id="converselab_note_source_url"
                value="<?php echo esc_attr( $url ); ?>" style="width:100%" placeholder="https://..." />
        </p>
        <?php
    }

    public static function save_meta($post_id){
            if(!isset($_POST['converselab_note_nonce']) ||
               ! wp_verify_nonce($_POST['converselab_note_nonce'],'converselab_save_note_meta')){
                return;
               }

            if(isset($_POST['converselab_note_priority'])){
               $priority = sanitize_text_field( $_POST['converselab_note_priority'] ); 

               if(in_array($priority,['low','medium','high'])){
                update_post_meta($post_id,'converselab_note_priority', $priority);
               }
            }

            if(isset($_POST['converselab_note_source_url'])){
                $url=esc_url_raw($_POST['converselab_note_source_url']);
                update_post_meta($post_id,'converselab_note_source_url', $url);
            }
    }
}


