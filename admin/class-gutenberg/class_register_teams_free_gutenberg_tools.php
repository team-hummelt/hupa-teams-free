<?php

namespace Hupa\TeamFreeMembers;

use Hupa_Teams_Free;
defined('ABSPATH') or die();

/**
 * ADMIN Gutenberg Sidebar
 * @package Hummelt & Partner WordPress-Plugin
 * Copyright 2022, Jens Wiecker
 * License: Commercial - goto https://www.hummelt-werbeagentur.de/
 *
 * @Since 1.0.0
 */
class Register_Teams_Free_Gutenberg_Tools
{

    protected Hupa_Teams_Free $main;
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $basename The ID of this plugin.
     */
    private string $basename;

    /**
     * TRAIT of Default Settings.
     * @since    1.0.0
     */
    use Hupa_Teams_Free_Members_Defaults_Trait;


    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private string $version;

    /**
     * @param string $plugin_name
     * @param string $version
     * @param Hupa_Teams_Free $main
     */
    public function __construct(string $plugin_name, string $version, Hupa_Teams_Free $main)
    {

        $this->basename = $plugin_name;
        $this->version = $version;
        $this->main = $main;

    }

    public function team_free_member_posts_sidebar_meta_fields(): void
    {
        register_meta(
            'post',
            '_member_free_cover_image_meta',
            array(
                'type' => 'string',
                'object_subtype' => 'team_free_members',
                'single' => true,
                'show_in_rest' => true,
                'default' => json_encode(['id' => 0, 'url' => '', 'width' => '', 'height' => '']),
                // 'sanitize_callback' => 'sanitize_text_field',
                'auth_callback' => array($this, 'sidebar_permissions_check')
            )
        );

        register_meta(
            'post',
            '_member_free_detail_image_meta',
            array(
                'type' => 'string',
                'object_subtype' => 'team_free_members',
                'single' => true,
                'show_in_rest' => true,
                'default' => json_encode(['id' => 0, 'url' => '', 'width' => '', 'height' => '']),
                // 'sanitize_callback' => 'sanitize_text_field',
                'auth_callback' => array($this, 'sidebar_permissions_check')
            )
        );

        register_meta(
            'post',
            '_team_free_member_name',
            array(
                'type' => 'string',
                'object_subtype' => 'team_free_members',
                'single' => true,
                'show_in_rest' => true,
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
                'auth_callback' => array($this, 'sidebar_permissions_check')
            )
        );

        register_meta(
            'post',
            '_team_free_member_subtitle',
            array(
                'type' => 'string',
                'object_subtype' => 'team_free_members',
                'single' => true,
                'show_in_rest' => true,
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
                'auth_callback' => array($this, 'sidebar_permissions_check')
            )
        );

        register_meta(
            'post',
            '_team_free_member_show',
            array(
                'type'              => 'boolean',
                'object_subtype'    => 'team_free_members',
                'single'            => true,
                'show_in_rest'      => true,
                'default'           => 1,
                'auth_callback'     => array($this, 'sidebar_permissions_check')
            )
        );

        register_meta(
            'post',
            '_team_free_member_scroll_top',
            array(
                'type'              => 'boolean',
                'object_subtype'    => 'team_free_members',
                'single'            => true,
                'show_in_rest'      => true,
                'default'           => 0,
                'auth_callback'     => array($this, 'sidebar_permissions_check')
            )
        );

        register_meta(
            'post',
            '_team_free_member_scroll_offset',
            array(
                'type'              => 'number',
                'object_subtype'    => 'team_free_members',
                'single'            => true,
                'show_in_rest'      => true,
                'default'           => 0,
                'auth_callback'     => array($this, 'sidebar_permissions_check')
            )
        );
    }

    /**
     * Register WP_TEAM GUTENBERG SCRIPTS
     *
     * @since    1.0.0
     */
    public function wp_team_free_members_register_sidebar(): void
    {

        $plugin_asset = require HUPA_TEAMS_FREE_SIDEBAR_BUILD_DIR . 'index.asset.php';
        wp_register_script(
            'wp-team-free-members-sidebar',
            plugins_url($this->basename) . '/admin/gutenberg-sidebar/sidebar-react/build/index.js',
            $plugin_asset['dependencies'], $plugin_asset['version'], true
        );

        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations('wp-team-free-members-sidebar', 'hupa-teams', HUPA_TEAMS_FREE_GUTENBERG_LANGUAGE);
        }

        wp_register_script('wp-team-free-members-js-localize', '', [], $plugin_asset['version'], true);
        wp_enqueue_script('wp-team-free-members-js-localize');
        wp_localize_script('wp-team-free-members-js-localize',
            'TeamFreeRestObj',
            array(
                'url' => esc_url_raw(rest_url('wp-team-free-members/v1/')),
                'nonce' => wp_create_nonce('wp_rest')
            )
        );
    }

    public function team_free_members_sidebar_script_enqueue()
    {
        wp_enqueue_script('wp-team-free-members-sidebar');
        wp_enqueue_style('wp-team-free-members-sidebar-style');
        wp_enqueue_style(
            'wp-team-free-members-sidebar-style',
            plugins_url($this->basename) . '/admin/gutenberg-sidebar/sidebar-react/build/index.css', array(), $this->version);
    }

    /**
     * Register TAM MEMBERS REGISTER GUTENBERG BLOCK TYPE
     *
     * @since    1.0.0
     */
    public function register_team_free_members_block_type()
    {
        register_block_type('hupa/team-free-members-block', array(
            'render_callback' => [Teams_Free_Members_Block_Callback::class, 'callback_team_free_members_block_type'],
            'editor_script' => 'team-free-members-gutenberg-block',
        ));

        //add_filter( 'gutenberg_google_rezension_api_render', 'gutenberg_block_google_rezension_api_render_filter', 10, 20 );
    }

    /**
     * REGISTER TEAM MEMBERS GUTENBERG SCRIPTS
     *
     * @since    1.0.0
     */
    public function team_free_members_block_type_scripts(): void
    {

        $plugin_asset = require HUPA_TEAMS_FREE_BLOCK_BUILD_DIR . 'index.asset.php';

        wp_enqueue_script(
            'team-free-members-gutenberg-block',
            plugins_url($this->basename) . '/admin/gutenberg-block/build/index.js',
            $plugin_asset['dependencies'], $plugin_asset['version'], true
        );

        wp_enqueue_style(
            'team-free-members-gutenberg-block',
            plugins_url($this->basename) . '/admin/gutenberg-block/build/index.css', array(), $this->version
        );
    }

    /**
     * Check if a given request has access.
     *
     * @return bool
     */
    public function sidebar_permissions_check(): bool
    {
        return current_user_can('edit_posts');
    }
}