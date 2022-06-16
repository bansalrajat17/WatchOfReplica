<?php
	/*
	Template Name: Blog: Masonry Full Width
	*/
?>
<?php get_header(); ?>

<?php hue_mikado_get_title(); ?>
<?php get_template_part('slider'); ?>

	<div class="mkd-full-width">
		<div class="mkd-full-width-inner clearfix">
			<?php the_content(); ?>
			<?php do_action('hue_mikado_page_after_content'); ?>
			<?php hue_mikado_get_blog('masonry-full-width'); ?>
		</div>
	</div>
<?php get_footer(); ?>