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

/**
 * Предотвращение прямого доступа к файлу.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Активация плагина CF7 Search Posts.
 */
function wpcf7sp_init() {
	$wpcf7sp = new WPCF7SP();
	$wpcf7sp->init();
}

add_action( 'plugins_loaded', 'wpcf7sp_init' );

class WPCF7SP {
	
	/**
	 * Наличие плагина Contact Form 7.
	 *
	 * @since 0.1
	 * @var bool
	 */
	private $cf7_available = false;
	
	/**
	 * WPCF7SP constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		$this->cf7_available = function_exists( 'wpcf7' );
	}
	
	/**
	 * Инициализирует функционал плагина.
	 *
	 * @since 0.1
	 */
	public function init() {
		if ( ! $this->cf7_available ) {
			add_action( 'admin_notices', [ $this, 'notice' ] );
			
			return;
		}
		
		if ( wp_doing_ajax() ) {
			//echo '1';
		}
		
		add_filter( 'wpcf7_editor_panels', [ $this, 'add_tab' ] );
	}
	
	/**
	 * Отображает информационное окно, если CF7 не установлен
	 *
	 * @since 0.1
	 */
	function notice() {
		?>
        <div class="error">
            <p>У Вас не установлен плагин <b>Contact Form 7</b></p>
        </div>
		<?php
	}
	
	/**
	 * Добавляет вкладку в панель редактирования формы CF7.
	 *
	 * @since 0.1
	 *
	 * @param array $panels
	 *
	 * @return array
	 */
	function add_tab( $panels ) {
		$panels['posts-panel'] = [
			'title'    => 'Поиск формы в постах',
			'callback' => [ $this, 'render' ],
		];
		
		return $panels;
	}
	
	/**
	 * Выводит на экран контент вкладки.
	 */
	function render() {
		?>
        <h2>Поиск формы в постах</h2>
		<?php
		/**
		 * @var WP_Post_Type $post_type
		 */
		foreach ( $this->allow_post_types() as $post_type ) {
			printf( '<a class="button-primary" href="#%s">%s</a>', $post_type->name, $post_type->label );
		}
	}
	
	/**
	 * Возвращает
	 *
	 * @return array
	 */
	function allow_post_types() {
		$exclude = [
			'attachment'          => 'attachment',
			'revision'            => 'revision',
			'nav_menu_item'       => 'nav_menu_item',
			'custom_css'          => 'custom_css',
			'customize_changeset' => 'customize_changeset',
			'wpcf7_contact_form'  => 'wpcf7_contact_form',
		];
		
		$exclude = array_diff_key( get_post_types( [], 'objects' ), $exclude );
		
		return apply_filters( 'cf7sp_exclude', $exclude );
	}
}
