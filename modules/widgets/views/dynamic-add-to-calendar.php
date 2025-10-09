<?php
/**
 * View: Dynamic Add to Calendar
 *
 * @var array  $settings
 * @var string $calendar_url
 */

use Elementor\Icons_Manager;

if ( empty( $calendar_url ) ) {
    return;
}

$has_icon     = ! empty( $settings['button_icon']['value'] );
$icon_wrapper = '';

if ( $has_icon ) {
    ob_start();
    Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] );
    $icon_html    = ob_get_clean();
    $icon_wrapper = sprintf(
        '<span class="hwdw-icon hwdw-icon-%1$s">%2$s</span>',
        esc_attr( $settings['icon_position'] ),
        $icon_html
    );
}

$label = isset( $settings['button_text'] ) && $settings['button_text'] !== ''
    ? $settings['button_text']
    : __( 'Add to calendar', 'hw-ele-woo-dynamic' );

$label_html = sprintf( '<span class="hwdw-button-label">%s</span>', esc_html( $label ) );

$content = $label_html;

if ( $has_icon && 'left' === $settings['icon_position'] ) {
    $content = $icon_wrapper . $label_html;
} elseif ( $has_icon && 'right' === $settings['icon_position'] ) {
    $content = $label_html . $icon_wrapper;
}

$wrapper_classes       = 'dynamic-add-to-calendar-wrapper dynamic-bulk-addtocart-wrapper';
$button_wrapper_classes = 'dynamic-add-to-calendar-button dynamic-bulk-addtocart-button';
$anchor_classes         = 'dynamic-add-to-calendar dynamic-bulk-addtocart';
$content_classes        = 'dynamic-add-to-calendar-content dynamic-bulk-addtocart-content';

echo sprintf(
    '<div class="%1$s">
        <div class="%2$s">
            <a href="%3$s" class="%4$s" target="_blank" rel="noopener noreferrer">
                <span class="%5$s">%6$s</span>
            </a>
        </div>
    </div>',
    esc_attr( $wrapper_classes ),
    esc_attr( $button_wrapper_classes ),
    esc_url( $calendar_url ),
    esc_attr( $anchor_classes ),
    esc_attr( $content_classes ),
    $content
);
