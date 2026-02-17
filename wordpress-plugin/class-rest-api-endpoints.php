<?php
/**
 * Custom REST API Endpoints
 * 
 * Extends WordPress REST API with custom endpoints for:
 * - Aggregated homepage data
 * - Cross-post-type search
 * - Menu data in JSON format
 * - Site configuration
 */

class Headless_REST_API {
    
    public static function register_routes() {
        $namespace = 'headless/v1';
        
        // Homepage data aggregation
        register_rest_route($namespace, '/homepage', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_homepage_data'),
            'permission_callback' => '__return_true',
        ));
        
        // Global search
        register_rest_route($namespace, '/search', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'search_content'),
            'permission_callback' => '__return_true',
            'args' => array(
                'query' => array(
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));
        
        // Menu by location
        register_rest_route($namespace, '/menus/(?P<location>[a-zA-Z0-9-_]+)', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_menu'),
            'permission_callback' => '__return_true',
        ));
        
        // Site settings
        register_rest_route($namespace, '/settings', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_site_settings'),
            'permission_callback' => '__return_true',
        ));
        
        // Featured projects
        register_rest_route($namespace, '/featured-projects', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_featured_projects'),
            'permission_callback' => '__return_true',
        ));
        
        // Contact form
        register_rest_route($namespace, '/contact', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'handle_contact_form'),
            'permission_callback' => '__return_true',
            'args' => array(
                'name' => array('required' => true, 'sanitize_callback' => 'sanitize_text_field'),
                'email' => array('required' => true, 'sanitize_callback' => 'sanitize_email'),
                'message' => array('required' => true, 'sanitize_callback' => 'sanitize_textarea_field'),
            ),
        ));
    }
    
    public static function get_homepage_data($request) {
        $data = array();
        
        // Featured posts
        $featured_posts = get_posts(array(
            'post_type' => 'post',
            'posts_per_page' => 3,
            'meta_key' => 'featured',
            'meta_value' => '1',
        ));
        $data['featured_posts'] = array_map(array(__CLASS__, 'format_post'), $featured_posts);
        
        // Recent projects
        $recent_projects = get_posts(array(
            'post_type' => 'project',
            'posts_per_page' => 6,
        ));
        $data['recent_projects'] = array_map(array(__CLASS__, 'format_post'), $recent_projects);
        
        // Testimonials
        $testimonials = get_posts(array(
            'post_type' => 'testimonial',
            'posts_per_page' => -1,
        ));
        $data['testimonials'] = array_map(array(__CLASS__, 'format_post'), $testimonials);
        
        // Hero section from ACF options
        if (function_exists('get_field')) {
            $data['hero_section'] = array(
                'title' => get_field('hero_title', 'option'),
                'subtitle' => get_field('hero_subtitle', 'option'),
                'cta_text' => get_field('hero_cta_text', 'option'),
                'cta_link' => get_field('hero_cta_link', 'option'),
            );
        }
        
        return rest_ensure_response($data);
    }
    
    public static function search_content($request) {
        $query = $request->get_param('query');
        
        $search_query = new WP_Query(array(
            'post_type' => array('post', 'project', 'team_member'),
            'posts_per_page' => 20,
            's' => $query,
        ));
        
        $results = array();
        if ($search_query->have_posts()) {
            while ($search_query->have_posts()) {
                $search_query->the_post();
                $results[] = self::format_post(get_post());
            }
            wp_reset_postdata();
        }
        
        return rest_ensure_response(array(
            'results' => $results,
            'total' => $search_query->found_posts,
            'query' => $query,
        ));
    }
    
    public static function get_menu($request) {
        $location = $request->get_param('location');
        $locations = get_nav_menu_locations();
        
        if (!isset($locations[$location])) {
            return new WP_Error('menu_not_found', 'Menu not found', array('status' => 404));
        }
        
        $menu_items = wp_get_nav_menu_items($locations[$location]);
        if (!$menu_items) return rest_ensure_response(array());
        
        return rest_ensure_response(self::build_menu_tree($menu_items));
    }
    
    private static function build_menu_tree($items, $parent_id = 0) {
        $branch = array();
        
        foreach ($items as $item) {
            if ($item->menu_item_parent == $parent_id) {
                $menu_item = array(
                    'id' => $item->ID,
                    'title' => $item->title,
                    'url' => $item->url,
                );
                
                $children = self::build_menu_tree($items, $item->ID);
                if ($children) $menu_item['children'] = $children;
                
                $branch[] = $menu_item;
            }
        }
        
        return $branch;
    }
    
    public static function get_site_settings($request) {
        $settings = array(
            'site_title' => get_bloginfo('name'),
            'site_description' => get_bloginfo('description'),
            'site_url' => get_site_url(),
        );
        
        if (function_exists('get_field')) {
            $settings['social_links'] = array(
                'facebook' => get_field('facebook_url', 'option'),
                'twitter' => get_field('twitter_url', 'option'),
                'linkedin' => get_field('linkedin_url', 'option'),
            );
            
            $settings['contact_info'] = array(
                'email' => get_field('contact_email', 'option'),
                'phone' => get_field('contact_phone', 'option'),
            );
        }
        
        return rest_ensure_response($settings);
    }
    
    public static function get_featured_projects($request) {
        $projects = get_posts(array(
            'post_type' => 'project',
            'posts_per_page' => -1,
            'meta_key' => 'featured',
            'meta_value' => '1',
        ));
        
        return rest_ensure_response(array_map(array(__CLASS__, 'format_post'), $projects));
    }
    
    public static function handle_contact_form($request) {
        $name = $request->get_param('name');
        $email = $request->get_param('email');
        $message = $request->get_param('message');
        
        $to = get_option('admin_email');
        $subject = sprintf('[%s] Contact Form', get_bloginfo('name'));
        $body = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}";
        
        $sent = wp_mail($to, $subject, $body, array('Reply-To: ' . $email));
        
        if ($sent) {
            return rest_ensure_response(array(
                'success' => true,
                'message' => 'Thank you! We will get back to you soon.',
            ));
        }
        
        return new WP_Error('email_failed', 'Failed to send message', array('status' => 500));
    }
    
    private static function format_post($post) {
        $data = array(
            'id' => $post->ID,
            'title' => get_the_title($post->ID),
            'slug' => $post->post_name,
            'content' => apply_filters('the_content', $post->post_content),
            'excerpt' => get_the_excerpt($post->ID),
            'date' => get_the_date('c', $post->ID),
            'type' => $post->post_type,
        );
        
        if (has_post_thumbnail($post->ID)) {
            $data['featured_image'] = wp_get_attachment_image_url(get_post_thumbnail_id($post->ID), 'large');
        }
        
        if (function_exists('get_fields')) {
            $data['acf'] = get_fields($post->ID);
        }
        
        return $data;
    }
}