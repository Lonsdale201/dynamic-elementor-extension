<?php
/**
 * @var array $settings
 * @var array $pairs
 */

if ( empty( $pairs ) ) {
    echo '<p>' . esc_html__( 'No products.', 'hw-ele-woo-dynamic' ) . '</p>';
    return;
}

$nonce = wp_create_nonce( 'hw_add_multiple_to_cart' );
$url   = add_query_arg(
    [
        'add-to-cart' => implode( ',', array_map( 'intval', $pairs ) ),
        'redirect'    => $settings['redirect'],
        '_hw_nonce'   => $nonce,
    ],
    wc_get_cart_url()
);

$has_icon = ! empty( $settings['icon']['value'] );
if ( $has_icon ) {
    ob_start();
    \Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
    $icon_html    = ob_get_clean();
    $icon_wrapper = sprintf(
        '<span class="hwdw-icon hwdw-icon-%1$s">%2$s</span>',
        esc_attr( $settings['icon_position'] ),
        $icon_html
    );
} else {
    $icon_wrapper = '';
}

// Button label
$label_html = sprintf(
    '<span class="hwdw-button-label">%s</span>',
    esc_html( $settings['button_label'] )
);

if ( $has_icon && 'left' === $settings['icon_position'] ) {
    $content = $icon_wrapper . $label_html;
} elseif ( $has_icon && 'right' === $settings['icon_position'] ) {
    $content = $label_html . $icon_wrapper;
} else {
    $content = $label_html;
}

// Final markup
printf(
    '<div class="dynamic-bulk-addtocart-wrapper">
        <div class="dynamic-bulk-addtocart-button">
            <a href="%1$s" class="dynamic-bulk-addtocart">
                <span class="dynamic-bulk-addtocart-content">%2$s</span>
            </a>
        </div>
    </div>',
    esc_url( $url ),
    $content
);
