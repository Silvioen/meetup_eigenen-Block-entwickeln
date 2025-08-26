<?php
/**
 * Plugin Name: Meetup Info
 * Description: Zeigt Datum, Speaker, Anmeldung und Aufzeichnung eines Meetups an.
 * Author:      Silvio Endruhn
 * Author URI:  https://endruhn.de/
 * License:     GNUGPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0
 * Version:     1.0
 **/


// 1) Block registrieren (Pfad anpassen: child/theme oder plugin)
function se_register_meetup_block() {
    $block_dir = get_stylesheet_directory() . '/blocks/meetup-block'; // falls Child Theme
    if ( function_exists( 'register_block_type_from_metadata' ) ) {
        register_block_type_from_metadata( $block_dir, [
            'render_callback' => 'se_render_meetup_block',
        ] );
    } else {
        // Fallback für ältere WP-Versionen:
        register_block_type( $block_dir, [
            'render_callback' => 'se_render_meetup_block',
        ] );
    }
}
add_action( 'init', 'se_register_meetup_block' );

// 2) Serverseitige Ausgabe (sichere Ausgabe, esc / kses)
function se_render_meetup_block( $attributes ) {
    $date = isset( $attributes['date'] ) ? esc_html( $attributes['date'] ) : '';
    $speaker = isset( $attributes['speaker'] ) ? esc_html( $attributes['speaker'] ) : '';
    $registration = isset( $attributes['registrationUrl'] ) ? esc_url( $attributes['registrationUrl'] ) : '';
    $recording = isset( $attributes['recordingUrl'] ) ? esc_url( $attributes['recordingUrl'] ) : '';
    $description = isset( $attributes['description'] ) ? wp_kses_post( $attributes['description'] ) : '';

    ob_start();
    ?>
    <div class="meetup-block">
        <?php if ( $speaker ) : ?>
            <h3 class="meetup-speaker"><?php echo $speaker; ?></h3>
        <?php endif; ?>

        <?php if ( $date ) : ?>
            <div class="meetup-date"><?php echo $date; ?></div>
        <?php endif; ?>

        <?php if ( $description ) : ?>
            <div class="meetup-desc"><?php echo $description; ?></div>
        <?php endif; ?>

        <div class="meetup-links">
            <?php if ( $registration ) : ?>
                <a class="meetup-register" href="<?php echo $registration; ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Anmelden', 'se-meetup' ); ?></a>
            <?php endif; ?>
            <?php if ( $recording ) : ?>
                <a class="meetup-recording" href="<?php echo $recording; ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Aufzeichnung', 'se-meetup' ); ?></a>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// 3) Block nur im Post Type 'meetup' verfügbar machen (Inserter / Inserter-Liste)
//    Anpassbar: $block_name und $allowed_post_types
add_filter( 'allowed_block_types_all', 'se_allow_meetup_block_only', 10, 2 );
function se_allow_meetup_block_only( $allowed_block_types, $editor_context ) {
    $allowed_post_types = array( 'meetup' ); // <-- POST-TYPE SLUG anpassen
    $block_name = 'se/meetup';

    $current_post_type = $editor_context->post?->post_type ?? null;

    // Wenn kein Kontext (z. B. Site Editor), belassen wie es ist
    if ( ! $current_post_type ) {
        return $allowed_block_types;
    }

    // Wenn wir uns NICHT im erlaubten Post Type befinden: entferne unseren Block
    if ( ! in_array( $current_post_type, $allowed_post_types, true ) ) {
        // Wenn true = alle erlaubt, hole alle registrierten Blocks
        if ( $allowed_block_types === true ) {
            $all = WP_Block_Type_Registry::get_instance()->get_all_registered();
            $allowed = array_keys( $all );
        } else {
            $allowed = (array) $allowed_block_types;
        }

        return array_values( array_diff( $allowed, array( $block_name ) ) );
    }

    // Im erlaubten Post Type: belasse die Standardauswahl (oder ggf. whiteliste)
    return $allowed_block_types;
}