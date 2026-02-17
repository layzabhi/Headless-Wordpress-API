<?php
/**
 * Custom Post Types Handler
 * 
 * Registers custom post types for portfolio content:
 * - Projects: Portfolio items with case studies
 * - Team Members: Staff profiles and bios
 * - Testimonials: Client reviews and feedback
 */

class Headless_Custom_Post_Types {
    
    public static function register_post_types() {
        self::register_projects();
        self::register_team_members();
        self::register_testimonials();
    }
    
    private static function register_projects() {
        $labels = array(
            'name' => 'Projects',
            'singular_name' => 'Project',
            'add_new_item' => 'Add New Project',
            'edit_item' => 'Edit Project',
            'all_items' => 'All Projects',
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-portfolio',
            'menu_position' => 5,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'),
            'show_in_rest' => true,
            'rest_base' => 'projects',
            'rewrite' => array('slug' => 'projects'),
            'taxonomies' => array('project_category', 'project_tag'),
        );
        
        register_post_type('project', $args);
    }
    
    private static function register_team_members() {
        $labels = array(
            'name' => 'Team Members',
            'singular_name' => 'Team Member',
            'add_new_item' => 'Add Team Member',
            'edit_item' => 'Edit Team Member',
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-groups',
            'menu_position' => 6,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'show_in_rest' => true,
            'rest_base' => 'team',
            'rewrite' => array('slug' => 'team'),
        );
        
        register_post_type('team_member', $args);
    }
    
    private static function register_testimonials() {
        $labels = array(
            'name' => 'Testimonials',
            'singular_name' => 'Testimonial',
            'add_new_item' => 'Add Testimonial',
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-format-quote',
            'menu_position' => 7,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'show_in_rest' => true,
            'rest_base' => 'testimonials',
        );
        
        register_post_type('testimonial', $args);
    }
}