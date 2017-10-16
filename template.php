<?php
/**
 * Шаблон контента вкладки
 *
 * @var WPCF7SP $this
 */
?>
    <h2>Поиск формы в постах</h2>

    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Nunc tincidunt</a></li>
            <li><a href="#tabs-2">Proin dolor</a></li>
            <li><a href="#tabs-3">Aenean lacinia</a></li>
        </ul>
        <div id="tabs-1">
            <h2>Content heading 1</h2>
            <p>Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus.</p>
        </div>
        <div id="tabs-2">
            <h2>Content heading 2</h2>
            <p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus id
                nunc.</p>
        </div>
        <div id="tabs-3">
            <h2>Content heading 3</h2>
            <p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti.</p>
        </div>
    </div>

<?php
/**
 * @var WP_Post_Type $post_type
 */
foreach ( $this->allow_post_types() as $post_type ) {
	printf( '<p><a class="button-primary" href="#%s">%s</a></p>', $post_type->name, $post_type->label );
}
