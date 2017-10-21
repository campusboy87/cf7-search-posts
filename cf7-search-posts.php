<?php
/*
Plugin Name: CF7 Search Posts
Plugin URI: https://github.com/campusboy87/cf7-search-posts
Description: Добавляет закладку в редактор формы для поиска посnов, содержащих редактируемую форму.
Version: 0.4
Author: campusboy
Author URI: https://wp-plus.ru/
License: MIT
*/

/**
 * Предотвращает прямой доступ к файлу.
 *
 * @since 0.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Путь к папке плагина c закрывающим слэшем.
 *
 * @since 0.2
 * @var string
 */
define( 'CF7SP_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Ссылка на папку с плагином с закрывающем слэшем.
 *
 * @since 0.2
 * @var string
 */
define( 'CF7SP_URL', plugin_dir_url( __FILE__ ) );

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
	public function __construct(){
		$this->cf7_available = function_exists( 'wpcf7' );
	}
	
	/**
	 * Инициализирует функционал плагина.
	 *
	 * @since 0.1
	 */
	public function init(){
		if ( ! $this->cf7_available ) {
			add_action( 'admin_notices', [ $this, 'notice' ] );
			
			return;
		}
		
		$this->hooks();
	}
	
	/**
	 * Подключает хуки.
	 *
	 * @since 0.2
	 */
	function hooks(){
		add_action( 'admin_menu', [ $this, 'menu' ] );
		add_filter( 'wpcf7_editor_panels', [ $this, 'add_tab' ] );
		
		$form_new  = 'contact-form-7_page_wpcf7-new';
		$form_edit = 'toplevel_page_wpcf7';
		
		add_action( "admin_print_scripts-{$form_new}", [ $this, 'assets' ] );
		add_action( "admin_print_scripts-{$form_edit}", [ $this, 'assets' ] );
	}
	
	/**
	 * Подключает CSS и JS.
	 *
	 * @since 0.2
	 */
	function assets(){
		wp_enqueue_script( 'cf7sp', CF7SP_URL . 'assets/cf7sp-admin.js', [ 'jquery-ui-tabs' ] );
		wp_enqueue_style( 'cf7sp', CF7SP_URL . 'assets/cf7sp-admin.css' );
	}
	
	/**
	 * Отображает информационное окно, если CF7 не установлен.
	 *
	 * @since 0.1
	 */
	function notice(){
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
	function add_tab( $panels ){
		$panels[ 'posts-panel' ] = [
			'title'    => 'Поиск формы в постах',
			'callback' => [ $this, 'render' ],
		];
		
		return $panels;
	}
	
	/**
	 * Выводит на экран контент вкладки.
	 *
	 * @since 0.1
	 */
	function render_tab(){
		
		include CF7SP_PATH . 'templates' . DIRECTORY_SEPARATOR . 'tab-template.php';
		
	}
	
	/**
	 * Возвращает список типов постов, доступных для поиска
	 *
	 * @since 0.2
	 * @return array
	 */
	function allow_post_types(){
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
	
	/**
	 * Возвращает посты, где встречается текущий шоткод
	 *
	 * @since 0.3
	 *
	 * @param string $post_type
	 *
	 * @return array|bool
	 */
	function shortcode_in_posts( $post_type ){
		$post = empty( $_GET[ 'post' ] ) ? false : get_post( $_GET[ 'post' ] );
		
		if ( ! $post ) {
			return false;
		}
		
		$posts = get_posts( [
			'numberposts' => - 1,
			'post_type'   => $post_type,
			's'           => $this->build_shortcode( $post ),
		] );
		
		return $posts;
	}
	
	/**
	 * Возвращает шоткод на основе данных поста.
	 *
	 * @since 0.4
	 *
	 * @param int|WP_Post|null $form
	 *
	 * @return string
	 */
	function build_shortcode( $form ){
		$form = get_post( $form );
		
		return $form ? sprintf( '[contact-form-7 id="%d" title="%s"]', $form->ID, $form->post_title ) : '';
	}
	
	/**
	 * Возвращает список файлов, где встречается текущий шоткод.
	 *
	 * @since 0.4
	 *
	 * @return array|bool
	 */
	function all_shortcodes_in_files(){
		$forms = get_posts( [
			'numberposts' => - 1,
			'post_type'   => 'wpcf7_contact_form',
		] );
		
		if ( empty( $forms ) ) {
			return false;
		}
		
		$files = $this->list_files();
		
		if ( empty( $files ) ) {
			return false;
		}
		
		$list = [];
		/**
		 * @var WP_Post $form
		 */
		foreach ( $forms as $form ) {
			$form->files = $this->shortcode_in_files( $form, $files );
			$list[]      = $form;
		}
		
		return $list;
	}
	
	
	/**
	 * Возвращает список файлов, где встречается сигнатура шоткода.
	 *
	 * @since 0.4
	 *
	 * @param WP_Post $form
	 * @param array   $files
	 *
	 * @return array
	 */
	function shortcode_in_files( $form, $files ){
		$shortcode = $this->build_shortcode( $form );
		
		$files = array_filter( $files, function( $path ) use ( $shortcode ){
			$content_file = $this->get_file( $path );
			
			return strpos( $content_file, $shortcode ) !== false;
		} );
		
		return $files;
	}
	
	/**
	 * Возвращает список файлов для последующего поиска шоткодов в них.
	 *
	 * @since 0.4
	 *
	 * @return array|bool
	 */
	function list_files(){
		$files = list_files( get_stylesheet_directory() );
		if ( empty( $files ) ) {
			return false;
		}
		
		return array_filter( $files, function( $path ){
			return strpos( $path, '.php' ) !== false;
		} );
	}
	
	/**
	 * Создает подменю у плагина CF7.
	 *
	 * @since 0.4
	 */
	function menu(){
		add_submenu_page( 'wpcf7', 'Поиск форм', 'Поиск форм', 'wpcf7_read_contact_forms', 'wpcf7-sp', [
			$this,
			'render_page',
		] );
	}
	
	/**
	 * Выводит на экран контент страницы.
	 *
	 * @since 0.4
	 */
	function render_page(){
		include CF7SP_PATH . 'templates' . DIRECTORY_SEPARATOR . 'admin-page-template.php';
	}
	
	/**
	 * Возвращает контент файла.
	 *
	 * @since 0.4
	 *
	 * @param $path
	 *
	 * @return bool|string
	 */
	function get_file( $path ){
		
		if ( function_exists( 'realpath' ) ) {
			$path = realpath( $path );
		}
		
		if ( ! $path || ! @is_file( $path ) ) {
			return '';
		}
		
		return @file_get_contents( $path );
	}
}

/**
 * Активирует CF7 Search Posts.
 */
function wpcf7sp_init(){
	$wpcf7sp = new WPCF7SP();
	$wpcf7sp->init();
}

add_action( 'plugins_loaded', 'wpcf7sp_init' );