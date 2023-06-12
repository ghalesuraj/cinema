<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$cookie_sid     = isset( $_COOKIE['sid'] )      ? $_COOKIE['sid']       : '';
$cookie_rid     = isset( $_COOKIE['rid'] )      ? $_COOKIE['rid']       : '';
$showtime_id    = isset( $_GET['sid'] )         ? $_GET['sid']          : $cookie_sid;
$room_id        = isset( $_GET['rid'] )         ? $_GET['rid']          : $cookie_rid;
$movie_id       = MB_Movie()->get_id_by_showtime( $showtime_id );

$shortcode_map  = get_post_meta( $room_id, MB_PLUGIN_PREFIX_ROOM.'shortcode_img_map', true );
$room_seats     = get_post_meta( $room_id, MB_PLUGIN_PREFIX_ROOM.'seats', true );
$seat_booked    = MB_Booking()->get_seat_booked( $movie_id, $showtime_id, $room_id );

?>

<div class="mb-seat-map">
    <?php echo do_shortcode( $shortcode_map ); ?>
    <input type="hidden" name="data-seat" id="mb-seat" data-seat="<?php echo esc_attr( wp_json_encode( $room_seats ) ); ?>" />
    <input type="hidden" name="data-seat-booked" id="seat-booked" data-seat-booked="<?php echo esc_attr( wp_json_encode( $seat_booked ) ); ?>" />
</div>