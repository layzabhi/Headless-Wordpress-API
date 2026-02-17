<?php
/**
 * ACF Fields Setup
 * 
 * Programmatic registration of Advanced Custom Fields
 * Keeps field definitions in version control
 */

class Headless_ACF_Setup {
    
    public static function register_field_groups() {
        if (!function_exists('acf_add_local_field_group')) return;
        
        self::register_project_fields();
        self::register_team_fields();
        self::register_testimonial_fields();
        self::register_site_options();
    }
    
    private static function register_project_fields() {
        acf_add_local_field_group(array(
            'key' => 'group_projects',
            'title' => 'Project Details',
            'fields' => array(
                array(
                    'key' => 'field_client',
                    'label' => 'Client',
                    'name' => 'client',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_year',
                    'label' => 'Year',
                    'name' => 'year',
                    'type' => 'number',
                    'default_value' => date('Y'),
                ),
                array(
                    'key' => 'field_url',
                    'label' => 'Project URL',
                    'name' => 'project_url',
                    'type' => 'url',
                ),
                array(
                    'key' => 'field_featured',
                    'label' => 'Featured Project',
                    'name' => 'featured',
                    'type' => 'true_false',
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_technologies',
                    'label' => 'Technologies',
                    'name' => 'technologies',
                    'type' => 'select',
                    'choices' => array(
                        'wordpress' => 'WordPress',
                        'react' => 'React',
                        'nextjs' => 'Next.js',
                        'php' => 'PHP',
                        'javascript' => 'JavaScript',
                        'typescript' => 'TypeScript',
                    ),
                    'multiple' => 1,
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_gallery',
                    'label' => 'Gallery',
                    'name' => 'gallery',
                    'type' => 'gallery',
                    'return_format' => 'array',
                ),
                array(
                    'key' => 'field_challenges',
                    'label' => 'Challenges',
                    'name' => 'challenges',
                    'type' => 'wysiwyg',
                ),
                array(
                    'key' => 'field_solution',
                    'label' => 'Solution',
                    'name' => 'solution',
                    'type' => 'wysiwyg',
                ),
                array(
                    'key' => 'field_results',
                    'label' => 'Results',
                    'name' => 'results',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_metric',
                            'label' => 'Metric',
                            'name' => 'metric',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_value',
                            'label' => 'Value',
                            'name' => 'value',
                            'type' => 'text',
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'project',
                    ),
                ),
            ),
            'show_in_rest' => 1,
        ));
    }
    
    private static function register_team_fields() {
        acf_add_local_field_group(array(
            'key' => 'group_team',
            'title' => 'Team Member Details',
            'fields' => array(
                array(
                    'key' => 'field_position',
                    'label' => 'Position',
                    'name' => 'position',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_email',
                    'label' => 'Email',
                    'name' => 'email',
                    'type' => 'email',
                ),
                array(
                    'key' => 'field_social',
                    'label' => 'Social Links',
                    'name' => 'social_links',
                    'type' => 'group',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_linkedin',
                            'label' => 'LinkedIn',
                            'name' => 'linkedin',
                            'type' => 'url',
                        ),
                        array(
                            'key' => 'field_github',
                            'label' => 'GitHub',
                            'name' => 'github',
                            'type' => 'url',
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'team_member',
                    ),
                ),
            ),
            'show_in_rest' => 1,
        ));
    }
    
    private static function register_testimonial_fields() {
        acf_add_local_field_group(array(
            'key' => 'group_testimonials',
            'title' => 'Testimonial Details',
            'fields' => array(
                array(
                    'key' => 'field_author_name',
                    'label' => 'Author Name',
                    'name' => 'author_name',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_company',
                    'label' => 'Company',
                    'name' => 'company',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_rating',
                    'label' => 'Rating',
                    'name' => 'rating',
                    'type' => 'range',
                    'min' => 1,
                    'max' => 5,
                    'default_value' => 5,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'testimonial',
                    ),
                ),
            ),
            'show_in_rest' => 1,
        ));
    }
    
    private static function register_site_options() {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page(array(
                'page_title' => 'Site Settings',
                'menu_slug' => 'site-settings',
                'capability' => 'manage_options',
            ));
        }
        
        acf_add_local_field_group(array(
            'key' => 'group_options',
            'title' => 'Site Options',
            'fields' => array(
                array(
                    'key' => 'field_hero_title',
                    'label' => 'Hero Title',
                    'name' => 'hero_title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_hero_subtitle',
                    'label' => 'Hero Subtitle',
                    'name' => 'hero_subtitle',
                    'type' => 'textarea',
                ),
                array(
                    'key' => 'field_contact_email',
                    'label' => 'Contact Email',
                    'name' => 'contact_email',
                    'type' => 'email',
                ),
                array(
                    'key' => 'field_facebook',
                    'label' => 'Facebook URL',
                    'name' => 'facebook_url',
                    'type' => 'url',
                ),
                array(
                    'key' => 'field_linkedin',
                    'label' => 'LinkedIn URL',
                    'name' => 'linkedin_url',
                    'type' => 'url',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'site-settings',
                    ),
                ),
            ),
            'show_in_rest' => 1,
        ));
    }
}