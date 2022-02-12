<?php

namespace Hupa\TeamFreeMembers;

use DOMDocument;

use Hupa_Teams_Free;
use stdClass;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use WP_Query;

defined('ABSPATH') or die();

/**
 * ADMIN Gutenberg Sidebar
 * @package Hummelt & Partner WordPress-Plugin
 * Copyright 2022, Jens Wiecker
 * License: Commercial - goto https://www.hummelt-werbeagentur.de/
 *
 * @Since 1.0.0
 */
class Free_Render_Callback_Templates
{

    protected Hupa_Teams_Free $main;
    /**
     * TWIG autoload for PHP-Template-Engine
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Environment $twig TWIG autoload for PHP-Template-Engine
     */
    protected Environment $twig;

    /**
     * TRAIT of Default Settings.
     * @since    1.0.0
     */
    use Hupa_Teams_Free_Members_Defaults_Trait;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $basename The ID of this plugin.
     */
    private string $basename;
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
     * @param Environment $twig
     */
    public function __construct(string $plugin_name, string $version, Hupa_Teams_Free $main, Environment $twig)
    {
        $this->basename = $plugin_name;
        $this->version = $version;
        $this->main = $main;
        $this->twig = $twig;

    }

    /**
     * @param string $block_content
     * @param array $block
     * @return string
     */
    public static function render_core_team_free_members_callback(string $block_content, array $block): string
    {
        if ($block['blockName'] === 'hupa/team-free-members-block' && !is_admin() && !wp_is_json_request()) {
            return str_replace('wp-block-columns', '', $block_content);;
        }
        return $block_content;
    }

    /**
     */
    public function render_callback_free_template($attributes)
    {

        isset($attributes['className']) ? $class = $attributes['className'] : $class = '';
        isset($attributes['selectedTemplate']) && !empty($attributes['selectedTemplate']) ? $template = $attributes['selectedTemplate'] : $template = '';
        isset($attributes['selectedCategory']) && !empty($attributes['selectedCategory']) ? $selectedCategory = $attributes['selectedCategory'] : $selectedCategory = '';

        if (!$selectedCategory) {
            return '';
        }

        $args = [
            'post_type' => 'team_free_members',
            'tax_query' => [
                [
                    'taxonomy' => 'team_free_members_category',
                    'field' => 'term_id',
                    'terms' => $selectedCategory
                ]
            ]
        ];

        $queryObj = new WP_Query($args);
        $twigData = new stdClass();

        switch ($template) {
            case '1':
                $tmpName = 'TemplateOne.twig';
                break;
            default:
                $tmpName = 'TemplateOne.twig';
        }

        $dataArr = [];
        $count = count($queryObj->posts);
        $plusBox = 0;
        foreach ($queryObj->posts as $tmp) {
            $imgMeta = get_post_meta($tmp->ID, '_member_free_cover_image_meta', true);
            $showMember = get_post_meta($tmp->ID, '_team_free_member_show', true);
            if (!(int)$showMember) {
                $count--;
                continue;
            }

            $newCount = 0;
            if($count % 3 != 0){
                $newCount += $count +1;
                if($newCount % 3 == 0){
                    $plusBox = 1;
                } else {
                    $plusBox = 2;
                }
            }

            if ($imgMeta) {
                $imgData = json_decode($imgMeta);
                $imgUrl = $imgData->url;
            } else {
                $imgUrl = plugins_url($this->basename) . '/admin/images/box-platzhalter.jpeg';
            }
            if(!$imgUrl){
                $imgUrl = plugins_url($this->basename) . '/admin/images/box-platzhalter.jpeg';;
            }
            $content = preg_replace(array('/<!--(.*)-->/Uis', "/[[:blank:]]+/"), array('', ' '), str_replace(array("\n", "\r", "\t"), '', $tmp->post_content));

            $data_item = [
                'rand' => $this->generateRandomId(5, 0, 2),
                'img_url' => $imgUrl,
                'content' => $content,
                'name' => get_post_meta($tmp->ID, '_team_free_member_name', true),
                'subName' => get_post_meta($tmp->ID, '_team_free_member_subtitle', true),
                'show' => true,
                'scrollAktiv' => (bool) get_post_meta($tmp->ID, '_team_free_member_scroll_top', true),
                'scrollOffset' =>  abs(get_post_meta($tmp->ID, '_team_free_member_scroll_offset', true))
            ];
            $dataArr[] = $data_item;
        }
        if( $plusBox != 0) {
            for ($i = 0; $i <= $plusBox; $i++) {
                $data_item = [
                    'rand' => '',
                    'img_url' => '',
                    'content' => '',
                    'name' => '',
                    'subName' => '',
                    'show' => false,
                    'scrollAktiv' => false,
                    'scrollOffset' => 0
                ];
                $dataArr[] = $data_item;
            }
        }

        $twigData->data = $dataArr;
        $twigData->parentRand = $this->generateRandomId(5, 0, 2);
        try {
            echo $this->twig->render($tmpName, ['data' => $twigData]);
        } catch (LoaderError | SyntaxError | RuntimeError $e) {
            // $e->getMessage();
            echo '';
        } catch (Throwable $e) {
            //$e->getMessage();
            echo '';
        }
    }

    /**
     * @param int $passwordlength
     * @param int $numNonAlpha
     * @param int $numNumberChars
     * @param bool $useCapitalLetter
     * @return string
     */
    private function generateRandomId(int $passwordlength = 12, int $numNonAlpha = 1, int $numNumberChars = 4, bool $useCapitalLetter = true): string
    {
        $numberChars = '123456789';
        //$specialChars = '!$&?*-:.,+@_';
        $specialChars = '!$%&=?*-;.,+~@_';
        $secureChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz';
        $stack = $secureChars;
        if ($useCapitalLetter) {
            $stack .= strtoupper($secureChars);
        }
        $count = $passwordlength - $numNonAlpha - $numNumberChars;
        $temp = str_shuffle($stack);
        $stack = substr($temp, 0, $count);
        if ($numNonAlpha > 0) {
            $temp = str_shuffle($specialChars);
            $stack .= substr($temp, 0, $numNonAlpha);
        }
        if ($numNumberChars > 0) {
            $temp = str_shuffle($numberChars);
            $stack .= substr($temp, 0, $numNumberChars);
        }

        return str_shuffle($stack);
    }
}
