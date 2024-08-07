<?php
/*
 * No direct access to this file
 */

use WpAssetCleanUp\Misc;

if (! isset($data)) {
	exit;
}

if ($data['for'] === 'homepage') {
	?>
	<p>This applies to the main page of the website: <a target="_blank" href="<?php echo esc_url($data['site_url']); ?>"><?php echo esc_url($data['site_url']); ?></a></p>
	<?php
} elseif ($data['for'] === 'posts') {
	$recentPosts = wp_get_recent_posts( array( 'numberposts' => '1', 'post_status' => 'publish' ) );
	$permalinkExample = false;

	if (isset($recentPosts[0]['ID']) && is_int($recentPosts[0]['ID'])) {
		$permalinkExample = get_permalink($recentPosts[0]['ID']);
	}

    if ( $data['show_no_records_notice'] ) {
        ?>
        <div class="wpacu-notice wpacu-warning" style="font-style: italic; font-size: inherit; margin-bottom: 20px;">
            <span class="dashicons dashicons-info" style="color: orange !important;"></span>&nbsp; Currently, no published posts (articles) were found. Once you publish at least one, any critical CSS set below will take effect.
        </div>
        <?php
    }
	?>
    <p><strong>This applies to all the singular posts</strong> from <span class="dashicons dashicons-admin-post"></span> <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url(admin_url('edit.php')); ?>">"Posts" -&gt; "All Posts"</a>
		<?php if ($permalinkExample) { ?>
			- Page Example: <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url($permalinkExample); ?>"><?php echo esc_url(urldecode($permalinkExample)); ?></a>
		<?php } ?>
	</p>
	<?php
} elseif ($data['for'] === 'pages') {
	$recentPages = get_posts( array( 'numberposts' => '1', 'post_status' => 'publish', 'post_type' => 'page', 'orderby' => 'post_date', 'order' => 'DESC' ) );
	$permalinkExample = false;

	if (isset($recentPages[0]->ID) && is_int($recentPages[0]->ID)) {
		$permalinkExample = get_permalink($recentPages[0]->ID);
	}

    if ( $data['show_no_records_notice'] ) {
        ?>
        <div class="wpacu-notice wpacu-warning" style="font-style: italic; font-size: inherit; margin-bottom: 20px;">
            <span class="dashicons dashicons-info" style="color: orange !important;"></span>&nbsp; Currently, no published posts (articles) were found. Once you publish at least one, any critical CSS set below will take effect.
        </div>
        <?php
    }
	?>
    <p><strong>This applies to all the singular pages</strong> from <span class="dashicons dashicons-admin-page"></span> <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url(admin_url('edit.php?post_type=page')); ?>">"Pages" -&gt; "All Pages"</a>
		<?php if ($permalinkExample) { ?>
			- Page Example: <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url($permalinkExample); ?>"><?php echo esc_url(urldecode($permalinkExample)); ?></a>
		<?php } ?>
	</p>
	<?php
} elseif ($data['for'] === 'custom_post_types') {
    if ( ! empty($data['custom_post_types_list']) ) {
        $postTypeObj = get_post_type_object($data['chosen_post_type']);
        $postTypeObjLabels = $postTypeObj->labels;

        if (isset($postTypeObjLabels->name, $postTypeObjLabels->all_items)) {
            $recentCustomPostType = get_posts( array( 'numberposts' => '1', 'post_status' => 'publish', 'post_type' => $data['chosen_post_type'], 'orderby' => 'post_date', 'order' => 'DESC' ) );
            $permalinkExample = false;

            if (isset($recentCustomPostType[0]->ID) && is_int($recentCustomPostType[0]->ID)) {
                $permalinkExample = get_permalink($recentCustomPostType[0]->ID);
            }
        ?>
            <p><strong>This applies to all the custom post type pages</strong> from <span class="dashicons dashicons-admin-post"></span> <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url(admin_url('edit.php?post_type='.$data['chosen_post_type'])); ?>">"<?php echo esc_html($postTypeObjLabels->name); ?>" -&gt; "<?php echo esc_html($postTypeObjLabels->all_items); ?>"</a>
                <?php if ($permalinkExample) { ?>
                    - Page Example: <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url($permalinkExample); ?>"><?php echo esc_url(urldecode($permalinkExample)); ?></a>
                <?php } ?>
            </p>
        <?php
        }
    } else {
    ?>
        <div class="wpacu-notice wpacu-warning" style="font-style: italic; font-size: inherit; margin-bottom: 20px;">
            <span class="dashicons dashicons-info" style="color: orange !important;"></span>&nbsp; There are no custom post types detected. Perhaps you just installed WordPress, or do not need any custom post type (e.g. WooCommerce "product"), besides the in built WordPress ones.
        </div>
    <?php
    }
} elseif ($data['for'] === 'media_attachment') {
    $recentPages = get_posts( array( 'numberposts' => '1', 'post_status' => null, 'post_type' => 'attachment', 'orderby' => 'post_date', 'order' => 'DESC' ) );
    $permalinkExample = false;

	if (isset($recentPages[0]->ID) && is_int($recentPages[0]->ID)) {
		$permalinkExample = get_permalink($recentPages[0]->ID);
	}
	?>
	<p>This applies to all the media attachment singular pages from <span class="dashicons dashicons-admin-media"></span> <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url(admin_url('upload.php')); ?>">"Media" -&gt; "Library"</a>
		<?php if ($permalinkExample) { ?>
            - Page Example: <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url($permalinkExample); ?>"><?php echo esc_url(urldecode($permalinkExample)); ?></a>
		<?php } ?>
	</p>
    <div style="padding: 8px; background: white; margin: 0 0 20px; border-left: 1px solid #cdcdcd;">
        <p style="margin-top: 0;"><strong>Note:</strong> It's rare for these kind of pages to be public (e.g. in a Sitemap) so that they will be indexed by search engines, so you might not need to use any critical CSS for them if it's only you who views them.</p>
        <p style="margin-bottom: 0;">If you're using a plugin such as Yoast SEO that redirects the attachment's permalink to the actual file URL, then critical CSS in this area becomes irrelevant and you can leave it turned off.</p>
    </div>
    <?php
} elseif ($data['for'] === 'category') {
	$categories = get_categories(array('term_order' => 'term_id', 'order' => 'DESC'));
    ?>
    <p>This applies to all the category pages viewed (list of posts belonging to the category) from <span class="dashicons dashicons-admin-post"></span> <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=category')); ?>">"Posts" -&gt; "Categories"</a>
      <?php
        $permalinkExample = false;

        if (isset($categories[0]->term_id) && is_int($categories[0]->term_id)) {
            $permalinkExample = get_term_link($categories[0]->term_id);
        }
        if ($permalinkExample) { ?>
            - Page Example: <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url($permalinkExample); ?>"><?php echo esc_url(urldecode($permalinkExample)); ?></a>
		<?php } ?>
    </p>
    <?php
} elseif ($data['for'] === 'tag') {
    $categories = get_tags(array('term_order' => 'term_id', 'order' => 'DESC'));
    ?>
    <p>This applies to all the tag pages (list of tagged posts) from <span class="dashicons dashicons-admin-post"></span> <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=post_tag')); ?>">"Posts" -&gt; "Tags"</a>
        <?php
        $permalinkExample = false;

        if (isset($categories[0]->term_id) && is_int($categories[0]->term_id)) {
            $permalinkExample = get_term_link($categories[0]->term_id);
        }
        if ($permalinkExample) { ?>
            - Page Example: <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url($permalinkExample); ?>"><?php echo esc_url(urldecode($permalinkExample)); ?></a>
        <?php } ?>
    </p>
<?php
} elseif ($data['for'] === 'custom_taxonomies') {
    if ( ! empty($data['custom_taxonomies_list']) ) {
        $taxonomyTermsList = get_terms(array('taxonomy' => $data['chosen_taxonomy'], 'term_order' => 'term_id', 'order' => 'DESC', 'hide_empty' => false));
        global $wp_taxonomies;

        $postTypeAssocTax = isset($wp_taxonomies[$data['chosen_taxonomy']]->object_type[0]) ? $wp_taxonomies[$data['chosen_taxonomy']]->object_type[0] : '';
        $postTypeObj = get_post_type_object($postTypeAssocTax);
        $postTypeObjLabels = $postTypeObj->labels;

        $taxMenuName = isset($wp_taxonomies[$data['chosen_taxonomy']]->labels->menu_name) ? $wp_taxonomies[$data['chosen_taxonomy']]->labels->menu_name : '';

        if (isset($postTypeObjLabels->name) && $taxMenuName) {

            $postTypeIcon = '';

            if ($postTypeAssocTax === 'download') {
                $postTypeIcon = '<span class="dashicons dashicons-download"></span>';
            }
        ?>
            <p>This applies to all the taxonomy pages from <?php echo wp_kses($postTypeIcon, array('span' => array('class'))); ?> <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy='.$data['chosen_taxonomy'].'&post_type='.$postTypeAssocTax)); ?>">"<?php echo esc_html($postTypeObjLabels->name); ?>" -&gt; "<?php echo esc_html($taxMenuName); ?>"</a>
                <?php
                $permalinkExample = false;
                $itHasTaxonomies = isset($taxonomyTermsList[0]->term_id) && is_int($taxonomyTermsList[0]->term_id);

                if ($itHasTaxonomies) {
                    $permalinkExample = get_term_link($taxonomyTermsList[0]->term_id);
                }
                if ($permalinkExample) { ?>
                    - Page Example: <a style="text-decoration: none;" target="_blank" href="<?php echo esc_url($permalinkExample); ?>"><?php echo esc_url(urldecode($permalinkExample)); ?></a>
                <?php }

                if (! $itHasTaxonomies) {
                    ?>
                    - it looks like there are no taxonomies added there at this time, thus, it makes the critical CSS addition irrelevant
                    <?php
                }
                ?>
            </p>
        <?php
        }
    } else {
        ?>
        <div class="wpacu-notice wpacu-warning" style="font-style: italic; font-size: inherit; margin-bottom: 20px;">
            <span class="dashicons dashicons-info" style="color: orange !important;"></span>&nbsp; There are no custom taxonomies detected. Perhaps you just installed WordPress, or do not need any custom taxonomies (e.g. WooCommerce Product Category, "product_cat"), besides the in built WordPress ones (Posts Categories' &amp; Tags).
        </div>
        <?php
    }
} elseif ($data['for'] === 'search') {
    $searchUrl = get_site_url().'/?s=keyword-here';
    ?>
    <p style="margin-bottom: 20px;">This applies to the search page (if the website needs it) whenever a URL like the following is accessed: <a target="_blank" href="<?php echo esc_url($searchUrl); ?>"><?php echo esc_url($searchUrl); ?></a> / For more information about WordPress Search Page, you can check the Codex: <a target="_blank" href="https://codex.wordpress.org/Creating_a_Search_Page">https://codex.wordpress.org/Creating_a_Search_Page</a></p>
    <?php
} elseif ($data['for'] === 'author') {
	$authorUrl = '';
    $users = get_users();

    if (isset($users[0]->data->ID) && $users[0]->data->ID) {
	    $authorUrl = get_author_posts_url($users[0]->data->ID);
    }
    ?>
    <p>This applies to archive pages that belong to an author page (with the list of posts assigned to that author). The URL can be access by going to <a href="<?php echo esc_url(admin_url('users.php')); ?>">"Users" -&gt; "All Users"</a>, then clicking on "View" when hovering over an author.</p>
    <?php if ($authorUrl) { ?>
        <p style="margin-bottom: 20px;">Example: <a href="<?php echo esc_url($authorUrl); ?>" target="_blank"><?php echo esc_url($authorUrl); ?></a></p>
    <?php } ?>
    <?php
} elseif ($data['for'] === 'date') {
    $dateArchivesOutput = trim(wp_get_archives(array('echo' => 0, 'post_type' => 'post', 'show_post_count' => true, 'format' => '')));
	$dateArchiveUrl     = '';

    if (strpos($dateArchivesOutput, '<a href=') !== false) {
        if (strpos($dateArchivesOutput, "\n") !== false) {
            $allCounts = array();

            $allLinks = explode("\n", $dateArchivesOutput);

            foreach ($allLinks as $link) {
                list(,$totalPosts) = explode('&nbsp;', $link);
                $totalPosts = trim($totalPosts, '()');
                $allCounts[] = $totalPosts;
            }

            arsort($allCounts);

            $keyTargeted = Misc::arrayKeyFirst($allCounts);

            $dateArchiveUrl = Misc::extractBetween($allLinks[$keyTargeted], '<a href=', '>');
            $dateArchiveUrl = trim($dateArchiveUrl, "'");

            } else {
            $dateArchiveUrl = Misc::extractBetween($dateArchivesOutput, '<a href=', '>');
            $dateArchiveUrl = trim($dateArchiveUrl, "'");
        }
    }

    $dateUriExample = '/2015/11/'; // default example

    if ($dateArchiveUrl) {
        $dateUriExample = str_replace(get_site_url(), '', $dateArchiveUrl);
    }
    ?>
        <p>This applies to the date archive pages (if used on the website) that have the URI like: <?php echo $dateUriExample; ?></p>

    <?php if ($dateArchiveUrl) { ?>
        <p style="margin-bottom: 20px;">Example: <a target="_blank" href="<?php echo esc_url($dateArchiveUrl); ?>"><?php echo esc_url($dateArchiveUrl); ?></a></p>
    <?php
    }
} elseif ($data['for'] === '404_not_found') {
	$random404Page = get_site_url().'/mistyped-post-slug-that-does-not-exist--'.uniqid('', true);
	?>
    <p style="margin-bottom: 20px;">This applies to all pages returning 404 not found whenever a non-existent (perhaps expired a long time ago) URL is accessed - Example: <a target="_blank" href="<?php echo esc_url($random404Page); ?>"><?php echo esc_url($random404Page); ?></a></p>
	<?php
}
