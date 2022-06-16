<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="mkd-post-content">
		<div class="mkd-audio-image-holder">
			<?php hue_mikado_get_module_template_part('templates/single/parts/image', 'blog'); ?>

			<div class="mkd-audio-player-holder">
				<?php hue_mikado_get_module_template_part('templates/parts/audio', 'blog'); ?>
			</div>
		</div>
		<div class="mkd-post-text">
			<div class="mkd-post-text-inner clearfix">
				<?php hue_mikado_get_module_template_part('templates/single/parts/title', 'blog'); ?>
				<?php the_content(); ?>
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
	<?php do_action('hue_mikado_before_blog_article_closed_tag'); ?>
</article>