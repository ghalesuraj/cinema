<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'MB_Checkout' ) ) {
    class MB_Checkout {
        protected static $_instance = null;

        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function __construct( $payment_gateways = null ) {
            add_action( 'init', array( $this, 'checkout_include' ) );

            add_action( 'init', array( $this, 'checkout_payment_gateways' ) );
        }

        public function checkout_include() {
            // Woo Payment
            require_once MB_PLUGIN_INC . 'class-mb-woo-payment.php';
        }

        public function checkout_payment_gateways() {
            $payment_gateways = class_exists( 'MB_Woo_Payment' ) ? new MB_Woo_Payment() : null;

            return $payment_gateways;
        }

        public function process_checkout( $data ) {
            if ( empty( $data ) ) $data = $_POST['data'];

            // Validate Booking
            $validate_booking = isset( $data['showtime_id'], $data['room_id'] ) ? MB_Booking()->validate_before_booking( $data ) : false;

            if ( ! $validate_booking ) {
                $result['mb_message']   = MB()->msg_session->get( 'mb_message' );
                $result['mb_reload']    = sprintf( esc_html__( 'Click here to reload the page or the page will automatically reload after %s seconds.', 'moviebooking' ), '<span class="time">10</span>' );

                // Remove Session
                MB()->msg_session->remove();

                echo json_encode( $result );
                wp_die();

                return false;
            }

            // Check Holding Ticket
            $holding_ticket = MB()->options->checkout->get('enable_holding_ticket', 'yes');

            if ( 'yes' === $holding_ticket ) {
                $check_holding_ticket = MB_Booking()->mb_check_holding_ticket( $data );

                if ( mb_array_exists( $check_holding_ticket ) ) {
                    $result['mb_message'] = $check_holding_ticket['mb_message'];
                    $result['mb_reload']  = $check_holding_ticket['mb_reload'];

                    echo json_encode( $result );
                    wp_die();

                    return false;
                }
            }

            // Create Booking
            $booking_id = MB_Booking()->add_booking( $data );

            if ( ! $booking_id ) return false;

            if ( 'yes' === $holding_ticket ) {
                MB_Booking()->mb_create_holding_ticket( $data, $booking_id );
            }

            // Setup a session for cart
            MB()->cart_session->remove();
            MB()->cart_session->set( 'booking_id', $booking_id );

            $payment = $this->checkout_payment_gateways();

            if ( $payment ) {
                $result = $payment->process();

                return $result;
            }

            return false;
        }
    }
}