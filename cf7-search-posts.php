<?php
/*
Plugin Name: CF7 Search Posts
Plugin URI: https://github.com/campusboy87/cf7-search-posts
Description: Добавляет закладку в редактор формы для поиска посков, содержащих редактируемую форму.
Version: 0.1
Author: campusboy
Author URI: https://wp-plus.ru/
License: MIT
*/


function wpcf7sp_init() {
	if ( function_exists( 'wpcf7' ) ) {
		add_filter( 'wpcf7_editor_panels', 'wpcf7sp_work' );
	} else {
		add_action( 'admin_notices', function () {
			?>
            <div class="error">
                <p>У Вас не установлен плагин <b>Contact Form 7</b></p>
            </div>
			<?php
		} );
	}
}

add_action( 'plugins_loaded', 'wpcf7sp_init', 20 );


function wpcf7sp_work( $panels ) {
	$panels['posts-panel'] = [
		'title'    => 'Посты',
		'callback' => 'wpcf7sp_editor_panel_posts',
	];
	
	return $panels;
}

function wpcf7sp_editor_panel_posts() {
	?>
    <h2>Посты с участием данной формы</h2>
	<?php
	var_dump( get_post_types() );
}