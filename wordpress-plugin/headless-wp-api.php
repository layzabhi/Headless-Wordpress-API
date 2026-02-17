<?php
/**
 * Plugin Name: Headless WordPress API
 * Description: Custom REST API implementation for headless WordPress architecture with Next.js frontend
 * Version: 1.0.0
 * Author: Abhishek Gupta
 * License: GPL v2 or later
 * Text Domain: headless-wp-api
 */

if (!defined('ABSPATH')) exit;

// Plugin constants
define('HEADLESS_WP_VERSION', '1.0.0');
define('HEADLESS_WP_DIR', plugin_dir_path(__FILE__));
define('HEADLESS_WP_URL', plugin_dir_url(__FILE__));

class Headless_WP_API {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        $includes = HEADLESS_WP_DIR . 'includes' . DIRECTORY_SEPARATOR;
        
        require_once $includes . 'class-custom-post-types.php';
        require_once $includes . 'class-custom-taxonomies.php';
        require_once $includes . 'class-rest-api-endpoints.php';
        require_once $includes . 'class-jwt-auth.php';
        require_once $includes . 'class-acf-setup.php';
    }
    
    private function init_hooks() {
        // Register custom content types
        add_action('init', array('Headless_Custom_Post_Types', 'register_post_types'));
        add_action('init', array('Headless_Custom_Taxonomies', 'register_taxonomies'));
        
        // Initialize REST API
        add_action('rest_api_init', array('Headless_REST_API', 'register_routes'));
        add_action('rest_api_init', array($this, 'add_cors_support'));
        
        // Setup authentication and custom fields
        Headless_JWT_Auth::init();
        add_action('acf/init', array('Headless_ACF_Setup', 'register_field_groups'));
        
        // Enhance API responses
        add_filter('rest_prepare_post', array($this, 'enhance_api_response'), 10, 3);
        add_filter('rest_prepare_project', array($this, 'enhance_api_response'), 10, 3);
    }
    
    public function add_cors_support() {
        $allowed_origins = array(
            'http://localhost:3000',
            'http://localhost:3001',
        );
        
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        
        if (in_array($origin, $allowed_origins)) {
            header("Access-Control-Allow-Origin: {$origin}");
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce');
            header('Access-Control-Expose-Headers: X-WP-Total, X-WP-TotalPages');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            status_header(200);
            exit;
        }
    }
    
    public function enhance_api_response($response, $post, $request) {
        // Add ACF fields
        if (function_exists('get_fields')) {
            $acf_fields = get_fields($post->ID);
            if ($acf_fields) {
                $response->data['acf'] = $acf_fields;
            }
        }
        
        // Add featured image in multiple sizes
        if (has_post_thumbnail($post->ID)) {
            $thumbnail_id = get_post_thumbnail_id($post->ID);
            $sizes = array('thumbnail', 'medium', 'large', 'full');
            $images = array();
            
            foreach ($sizes as $size) {
                $image = wp_get_attachment_image_src($thumbnail_id, $size);
                if ($image) {
                    $images[$size] = array(
                        'url' => $image[0],
                        'width' => $image[1],
                        'height' => $image[2]
                    );
                }
            }
            
            $response->data['featured_image'] = $images;
            $response->data['featured_image_alt'] = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
        }
        
        // Add author details
        $author_id = $post->post_author;
        $response->data['author_details'] = array(
            'id' => $author_id,
            'name' => get_the_author_meta('display_name', $author_id),
            'avatar' => get_avatar_url($author_id),
            'description' => get_the_author_meta('description', $author_id)
        );
        
        return $response;
    }
}

// Initialize plugin
function headless_wp_init() {
    return Headless_WP_API::get_instance();
}
headless_wp_init();

// Activation hook - flush rewrite rules
register_activation_hook(__FILE__, function() {
    flush_rewrite_rules();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});