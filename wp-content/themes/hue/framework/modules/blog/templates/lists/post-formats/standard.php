<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="mkd-post-content">
		<?php hue_mikado_get_module_template_part('templates/lists/parts/image', 'blog'); ?>
		<div class="mkd-post-text">
			<div class="mkd-post-text-inner">
				<?php hue_mikado_get_module_template_part('templates/lists/parts/title', 'blog'); ?>
				<div class="mkd-post-info">
					<?php
					hue_mikado_post_info(array(
						'date' => 'yes',
						'author' => 'yes',
						'category' => 'no',
						'comments' => (hue_mikado_options()->getOptionValue('blog_single_comments') == 'yes') ? 'yes' : 'no',
						'like' => hue_mikado_show_likes() ? 'yes' : 'no'
					)); ?>
				</div>
				<?php
				hue_mikado_excerpt($excerpt_length);
				$args_pages = array(
					'before' => '<div class="mkd-single-links-pages"><div class="mkd-single-links-pages-inner">',
					'after' => '</div></div>',
					'link_before' => '<span>',
					'link_after' => '</span>',
					'pagelink' => '%'
				);

				wp_link_pages($args_pages);
				?>
			</div>
			<div class="mkd-category-share-holder clearfix">
				<div class="mkd-categories-list">
					<?php hue_mikado_get_module_template_part('templates/parts/post-info-category', 'blog'); ?>
				</div>
				<div class="mkd-share-icons">
					<?php $post_info_array['share'] = hue_mikado_options()->getOptionValue('enable_social_share') == 'yes'; ?>
					<?php if ($post_info_array['share'] == 'yes'): ?>
						<span class="mkd-share-label"><?php esc_html_e('Share', 'hue'); ?></span>
					<?php endif; ?>
					<?php echo hue_mikado_get_social_share_html(array(
						'type' => 'list',
						'icon_type' => 'normal'
					)); ?>
				</div>
			</div>
		</div>
	</div>
</article>