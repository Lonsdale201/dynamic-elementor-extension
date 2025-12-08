<?php
/**
 * View: Dynamic Text Editor
 *
 * @var string $content Rendered HTML content (still sanitized with wp_kses_post inside the view)
 */

if ( empty( $content ) ) {
	return;
}

echo '<div class="dynamic-text-editor">';
echo wp_kses_post( $content );
echo '</div>';
