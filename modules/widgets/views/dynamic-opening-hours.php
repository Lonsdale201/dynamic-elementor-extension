<?php
/**
 * View: Dynamic Opening Hours
 *
 * @var array $settings
 * @var array $days
 * @var array $status
 */

$should_show_fallback        = $should_show_fallback ?? false;
$fallback_text               = $fallback_text ?? '';
$description_html            = $description_html ?? '';
$description_has_content     = $description_has_content ?? false;
$has_opening_hours_data      = $has_opening_hours_data ?? true;

if ( $should_show_fallback ) {
    echo '<div class="dynamic-opening-hours dynamic-opening-hours--fallback">';
    echo '<div class="dynamic-opening-hours__fallback">' . wp_kses_post( $fallback_text ) . '</div>';
    echo '</div>';
    return;
}

if ( ! $has_opening_hours_data ) {
    return;
}

$display_type = $settings['display_type'] ?? 'list';
$wrapper_classes = [
    'dynamic-opening-hours',
    'dynamic-opening-hours--' . $display_type,
];

if ( ! empty( $status['is_open'] ) ) {
    $wrapper_classes[] = 'dynamic-opening-hours--open';
} else {
    $wrapper_classes[] = 'dynamic-opening-hours--closed';
}

$wrapper_class_attr = esc_attr( implode( ' ', $wrapper_classes ) );
$title               = trim( (string) ( $settings['title_text'] ?? '' ) );
$status_text         = $status['text'] ?? '';
$is_open             = ! empty( $status['is_open'] );
$hide_status_badge   = ( 'yes' === ( $settings['hide_status_in_list'] ?? '' ) );

if ( 'status' === $display_type ) :
    ?>
    <div class="<?php echo $wrapper_class_attr; ?>">
        <?php if ( '' !== $title ) : ?>
            <div class="dynamic-opening-hours__title"><?php echo esc_html( $title ); ?></div>
        <?php endif; ?>

        <?php if ( $description_has_content ) : ?>
            <div class="dynamic-opening-hours__description"><?php echo wp_kses_post( $description_html ); ?></div>
        <?php endif; ?>

        <div class="dynamic-opening-hours__status dynamic-opening-hours__status--<?php echo $is_open ? 'open' : 'closed'; ?>">
            <?php echo esc_html( $status_text ); ?>
        </div>
    </div>
<?php
    return;
endif;
?>

<div class="<?php echo $wrapper_class_attr; ?>">
    <?php if ( '' !== $title ) : ?>
        <div class="dynamic-opening-hours__title"><?php echo esc_html( $title ); ?></div>
    <?php endif; ?>

    <?php if ( $description_has_content ) : ?>
        <div class="dynamic-opening-hours__description"><?php echo wp_kses_post( $description_html ); ?></div>
    <?php endif; ?>

    <?php if ( ! $hide_status_badge ) : ?>
        <div class="dynamic-opening-hours__status dynamic-opening-hours__status--<?php echo $is_open ? 'open' : 'closed'; ?>">
            <?php echo esc_html( $status_text ); ?>
        </div>
    <?php endif; ?>

    <div class="dynamic-opening-hours__list">
        <?php foreach ( $days as $day_key => $day ) :
            $is_today     = ! empty( $day['is_today'] );
            $is_closed    = ! empty( $day['is_closed'] );
            $row_classes  = [ 'dynamic-opening-hours__day' ];
            if ( $is_today ) {
                $row_classes[] = 'dynamic-opening-hours__day--today';
            }
            $row_class_attr = esc_attr( implode( ' ', $row_classes ) );
            $display_text   = $day['display'] ?? '';

            if ( '' === trim( $display_text ) && ! empty( $day['formatted'] ) ) {
                $display_text = $day['formatted'];
            }

            if ( '' === trim( $display_text ) && ! empty( $day['raw'] ) ) {
                $display_text = $day['raw'];
            }

            if ( '' === trim( $display_text ) ) {
                $display_text = $settings['closed_text'] ?? __( 'Closed', 'hw-ele-woo-dynamic' );
            }
            ?>
            <div class="<?php echo $row_class_attr; ?>">
                <span class="dynamic-opening-hours__day-label"><?php echo esc_html( $day['label'] ); ?></span>
                <span class="dynamic-opening-hours__day-time<?php echo $is_closed ? ' dynamic-opening-hours__day-time--closed' : ''; ?>">
                    <?php echo esc_html( $display_text ); ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
