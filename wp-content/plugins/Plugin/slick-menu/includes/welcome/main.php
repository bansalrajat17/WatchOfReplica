<?php
/**
 * Welcome Dashboard Page
 */

/** WordPress Administration Bootstrap */


?>
<div class="wrap about-wrap <?php echo $this->parent->plugin_slug("welcome-wrap"); ?>">
	
	<div class="about-text">
		<img class="slick-menu-logo" src="<?php echo esc_url( $this->parent->url. 'assets/images/logo.png' ); ?>" class="image-50" />
	</div>
	
	<h1><?php echo sprintf(esc_html__( 'Welcome to %s %s', 'slick-menu'  ), $this->parent->plugin_name(), $this->parent->plugin_version()); ?></h1>
	
	<div class="about-text">
		<?php echo esc_html_e('Slick Menu is more than just a menu plugin. It can be used to create unlimited multi level push menus or content sidebars with rich content, multiple style options and animation effects. Every menu level is customizable featuring background colors, images, overlays, patterns, videos, custom fonts and much more.', 'slick-menu' ); ?>
	</div>
		
	<script type="text/javascript" src="//xplodedthemes.com/widgets/xt-follow/xt-follow-min.js"></script> 
	<script type="text/javascript">
		XT_FOLLOW.init({
			'facebook': {'name': 'Facebook', 'url': 'https://facebook.com/slickmenu/'},
		});
	</script> 	
			
	<?php $this->show_nav(); ?>
		
	<div class="sm-welcome-section <?php echo $this->parent->plugin_slug($this->get_section_id()); ?>-section">
		
		<?php $this->show_section(); ?>
		
	</div>

</div>
