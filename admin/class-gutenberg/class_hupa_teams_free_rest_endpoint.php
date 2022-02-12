<?php

namespace Hupa\TeamFreeMembers;

use Hupa_Teams_Free;
use stdClass;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined('ABSPATH') or die();

/**
 * ADMIN Gutenberg Sidebar
 * @package Hummelt & Partner WordPress-Plugin
 * Copyright 2022, Jens Wiecker
 * License: Commercial - goto https://www.hummelt-werbeagentur.de/
 *
 * @Since 1.0.0
 */
class Hupa_Teams_Free_Rest_Endpoint
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

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_free_routes()
    {
        $version = '1';
        $namespace = 'wp-team-free-members/v' . $version;
        $base = '/';

        @register_rest_route(
            $namespace,
            $base . 'get-items/(?P<method>[\S]+)',

            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'hupa_team_free_members_endpoint_get_response'),
                'permission_callback' => array($this, 'permissions_check')
            )
        );

        @register_rest_route(
            $namespace,
            $base . 'get-team-data/(?P<post_id>[\d]+)',
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'hupa_get_team_data_endpoint'),
                'permission_callback' => array($this, 'permissions_check')
            )
        );
    }

    /**
     * Get one item from the collection.
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function hupa_team_free_members_endpoint_get_response(WP_REST_Request $request)
    {

        $method = (string)$request->get_param('method');
        if (!$method) {
            return new WP_Error(404, ' Method failed');
        }

        return $this->get_method_item($method);

    }

    /**
     * GET Post Meta BY ID AND Field
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_method_item($method)
    {
        if (!$method) {
            return new WP_Error(404, ' Method failed');

        }
        $tempArr = [];
        $response = new stdClass();
        switch ($method) {
            case 'get-team-members-block':

                $templates = apply_filters($this->basename . '/get_template_select','');
                if($templates){
                    foreach ($templates as $tmp) {
                        $temp_item = [
                            'id' => $tmp['id'],
                            'name' => $tmp['name']
                        ];
                        $tempArr[] = $temp_item;
                    }
                }

                $terms = apply_filters($this->basename.'/get_custom_terms','team_free_members_category');
                $catArr = [];

                if($terms->status){
                    foreach ($terms->terms as $tmp){
                        $cat_item = [
                            'id' => $tmp->term_id,
                            'name' => $tmp->name
                        ];
                        $catArr[] = $cat_item;
                    }
                }

                $response->templates = $tempArr;
                $response->categories = $catArr;
                break;
        }
        return new WP_REST_Response($response, 200);
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_Error|WP_REST_Response
     */
    public function hupa_get_team_data_endpoint(WP_REST_Request $request)
    {

        $post_id = (int)$request->get_param('post_id');

        if (!$post_id) {
            return new WP_Error(404, ' Method failed');
        }
        //$meta = get_post_meta($post_id ,'_member_cover_image_meta', true);
        //  update_post_meta($post_id,'_member_cover_image_media_id', 0);
        $response = new stdClass();
        $images = [];

        $response->images = $images;
        return new WP_REST_Response($response, 200);

    }

    /**
     * Get a collection of items.
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return void
     */
    public function get_items(WP_REST_Request $request)
    {


    }

    /**
     * Check if a given request has access.
     *
     * @return bool
     */
    public function permissions_check(): bool
    {
        return current_user_can('edit_posts');
    }

    /**
     * @param string $taxonomy
     * @return object
     */
    public function team_free_members_get_custom_terms(string $taxonomy): object
    {
        $return = new  stdClass();
        $return->status = false;
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'parent' => 0,
            'hide_empty' => false,
        ));

        if (!$terms) {
            return $return;
        }
        $return->status = true;
        $return->terms = $terms;
        return $return;
    }
}
