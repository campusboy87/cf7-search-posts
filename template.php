<?php
/**
 * Шаблон контента вкладки
 *
 * @var WPCF7SP      $this
 * @var WP_Post_Type $post_type
 */

$post_types = $this->allow_post_types();

if ( $post_types ): ?>

    <h2>Поиск формы в постах</h2>

    <div id="post-type-tabs">

        <!-- Вкладки с типами постов -->
        <ul>
			<?php foreach ( $post_types as $post_type ): ?>
                <li>
                    <a data-post-type="<?php echo esc_attr( $post_type->name ); ?>"
                       href="#tabs-<?php echo esc_attr( $post_type->name ); ?>">
						<?php echo esc_attr( $post_type->label ); ?>
                    </a>
                </li>
			<?php endforeach; ?>
        </ul>

        <!-- Контент вкладок -->
		<?php foreach ( $post_types as $post_type ): ?>
            <div id="tabs-<?php echo esc_attr( $post_type->name ); ?>">
                <h2><?php echo esc_html( $post_type->label ); ?></h2>
                <p>Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus.</p>
            </div>
		<?php endforeach; ?>

    </div>

<?php endif;
