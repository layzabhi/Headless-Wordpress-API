<?php
/**
 * Custom Taxonomies Handler
 * 
 * Organizes content with custom classification systems:
 * - Project Categories: Hierarchical project types
 * - Project Tags: Flat technology/feature tags
 * - Skills: Team member expertise areas
 */

class Headless_Custom_Taxonomies {
    
    public static function register_taxonomies() {
        self::register_project_categories();
        self::register_project_tags();
        self::register_skills();
    }
    
    private static function register_project_categories() {
        $args = array(
            'labels' => array(
                'name' => 'Project Categories',
                'singular_name' => 'Project Category',
            ),
            'hierarchical' => true,
            'public' => true,
            'show_in_rest' => true,
            'rest_base' => 'project-categories',
            'rewrite' => array('slug' => 'project-category'),
        );
        
        register_taxonomy('project_category', array('project'), $args);
    }
    
    private static function register_project_tags() {
        $args = array(
            'labels' => array(
                'name' => 'Project Tags',
                'singular_name' => 'Project Tag',
            ),
            'hierarchical' => false,
            'public' => true,
            'show_in_rest' => true,
            'rest_base' => 'project-tags',
            'rewrite' => array('slug' => 'project-tag'),
        );
        
        register_taxonomy('project_tag', array('project'), $args);
    }
    
    private static function register_skills() {
        $args = array(
            'labels' => array(
                'name' => 'Skills',
                'singular_name' => 'Skill',
            ),
            'hierarchical' => false,
            'public' => true,
            'show_in_rest' => true,
            'rest_base' => 'skills',
        );
        
        register_taxonomy('skill', array('team_member'), $args);
    }
}