<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0');

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles()
{

    wp_enqueue_style(
        'hello-elementor-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        [
            'hello-elementor-theme-style',
        ],
        HELLO_ELEMENTOR_CHILD_VERSION
    );
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20);

function update_SiteMeta($post_id)
{

    if (get_post_type($post_id) == 'timeline') {

        $values = get_fields($post_id);
        if (isset($values['ct']) && is_array($values['ct'])) {

            foreach ($values['ct'] as $layout) {
                if (isset($layout['acf_fc_layout']) && $layout['acf_fc_layout'] === 'ct-site') {
                    $url = isset($layout['ct-site-url']) ? $layout['ct-site-url'] : '';
                    // Obtém o conteúdo HTML da URL
                    $html = file_get_contents($url);

                    if ($html !== false) {
                        // Parse HTML usando DOMDocument
                        $dom = new DOMDocument();
                        @$dom->loadHTML($html);

                        // Procura pela tag meta com name="description"
                        $metaTags = $dom->getElementsByTagName('meta');
                        $description = '';
                        foreach ($metaTags as $tag) {
                            if ($tag->getAttribute('name') == 'description') {
                                $description = $tag->getAttribute('content');
                                break;
                            }
                        }

                        // Procura pela tag meta com name="title"
                        $title = $dom->getElementsByTagName('title')->item(0)->textContent;

                        // Procura pela tag meta com property="og:image" ou name="twitter:image"
                        $imageUrl = '';
                        foreach ($metaTags as $tag) {
                            if ($tag->getAttribute('property') == 'og:image' || $tag->getAttribute('name') == 'twitter:image') {
                                $imageUrl = $tag->getAttribute('content');
                                break;
                            }
                        }

                        // Atualiza os campos.
                        update_field('ct_0_ct-site-title', $title, $post_id);
                        update_field('ct_0_ct-site-description', $description, $post_id);
                        update_field('ct_0_ct-site-image_url', $imageUrl, $post_id);
                    }
                    break;
                }
            }
        }
    }
}


add_action('acf/save_post', 'update_SiteMeta');

add_action('rest_api_init', function () {
    register_rest_field(
        'timeline',
        'content',
        array(
            'get_callback'    => 'api_rest_customization',
            'update_callback' => null,
            'schema'          => null,
        )
    );
});

function api_rest_customization($post)
{

    return is_array($content = get_field('ct', $post['id'])) ? $content[0] : null;
}