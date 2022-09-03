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
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-people" viewBox="0 0 16 16">
                         <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                         </svg>';
        $people = 'data:image/svg+xml;base64,' . base64_encode($icon);
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
                'menu_icon' => $people,
                'menu_position' => 104,
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
