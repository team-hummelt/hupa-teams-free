<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wwdh.de
 * @since      1.0.0
 *
 * @package    Hupa_Teams
 * @subpackage Hupa_Teams/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Hupa_Teams
 * @subpackage Hupa_Teams/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Hupa_Teams_Free_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
           self::hupa_register_team_free_members();
           self::hupa_register_team_free_members_taxonomies();
           flush_rewrite_rules();
    }

    /**
     * Register Custom Post-Type Team-Members.
     *
     * @since    1.0.0
     */
    public static function hupa_register_team_free_members(): void
    {
        register_post_type(
            'team_free_members',
            array(
                'labels' => array(
                    'name' => __('Team Members Free', 'hupa-teams'),
                    'singular_name' => __('Team Members Posts', 'hupa-teams'),
                    'edit_item' => __('Edit Team Members Post', 'hupa-teams'),
                    'items_list_navigation' => __('Team Members Posts navigation', 'hupa-teams'),
                    'add_new_item' => __('Add new post', 'hupa-teams'),
                    'archives' => __('Team Members Posts Archives', 'hupa-teams'),
                ),
                'public' => true,
                'publicly_queryable' => true,
                'show_in_rest' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'has_archive' => true,
                'query_var' => true,
                'show_in_nav_menus' => true,
                'exclude_from_search' => false,
                'hierarchical' => true,
                'capability_type' => 'post',
                'menu_icon' => 'dashicons-groups',
                'menu_position' => 3,
                'supports' => array(
                    'title', 'excerpt', 'page-attributes', 'author', 'editor', 'thumbnail','custom-fields'
                ),
                'taxonomies' => array('team_free_members_category'),
            )
        );
    }

    /**
     * Register Custom Taxonomies for Team-Members Post-Type.
     *
     * @since    1.0.0
     */
    public static function hupa_register_team_free_members_taxonomies(): void
    {
        $labels = array(
            'name' => __('Team Members Categories', 'hupa-teams'),
            'singular_name' => __('Team Members Category', 'hupa-teams'),
            'search_items' => __('Search Team Members Categories', 'hupa-teams'),
            'all_items' => __('All Team Members Categories', 'hupa-teams'),
            'parent_item' => __('Parent Team Members Category', 'hupa-teams'),
            'parent_item_colon' => __('Parent Team Members Category:', 'hupa-teams'),
            'edit_item' => __('Edit Team Members Category', 'hupa-teams'),
            'update_item' => __('Update Team Members Category', 'hupa-teams'),
            'add_new_item' => __('Add New Team Members Category', 'hupa-teams'),
            'new_item_name' => __('New Team Members Category', 'hupa-teams'),
            'menu_name' => __('Members Categories', 'hupa-teams'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'show_ui' => true,
            'sort' => true,
            'show_in_rest' => true,
            'query_var' => true,
            'args' => array('orderby' => 'term_order'),
            'rewrite' => array('slug' => 'team_free_members_category'),
            'show_admin_column' => true
        );
        register_taxonomy('team_free_members_category', array('team_free_members'), $args);

        if (!term_exists('Members General', 'team_free_members_category')) {
            wp_insert_term(
                'Members General',
                'team_free_members_category',
                array(
                    'description' => __('Standard category for Team Members posts', 'hupa-teams'),
                    'slug' => 'team-free-members-posts'
                )
            );
        }
    }
}
