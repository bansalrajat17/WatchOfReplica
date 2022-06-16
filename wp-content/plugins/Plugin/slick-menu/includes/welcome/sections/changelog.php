<?php
$changelog = slick_menu_remote_get_data('changelog');

echo '
<a class="xt-refresh-link" href="'.$this->get_section_url('changelog', array('nocache'=>'1')).'">
	<span class="dashicons dashicons-image-rotate"></span> '.esc_html__('Refresh').'
</a>';

echo $changelog;