<?php
/**
 * Шаблон страницы "Поиск форм".
 * Используется в административной части сайта.
 *
 * @var WPCF7SP $this
 */
?>
<div class="wrap">
    <h1><?php echo esc_html( strip_tags( get_admin_page_title() ) ) ?></h1>
	
	<?php
	
	$forms = $this->all_shortcodes_in_files();
	
	if ( $forms ):
		
		/**
		 * @var WP_Post $form
		 */
		foreach ( $forms as $form ):
			?>

            <h2><? echo esc_html( $form->post_title ); ?></h2>
			
			<?php
			if ( $form->files ) { ?>
                <ul>
					<?php foreach ( $form->files as $file ): ?>
                        <li><?php echo esc_html( $file ); ?></li>
					<?php endforeach; ?>
                </ul>
				<?php
			} else {
				echo '<p>Данный шоткод в файлах не найден.</p>';
			}
		endforeach;
	
	else:
		?>

        <p>Ни одного шоткода создано не было.</p>
		
		<?php
	endif;
	?>

</div>
