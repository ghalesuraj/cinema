<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'MB_Booking' ) ) {
    class MB_Booking {
        /**
         * instance
         * @var null
         */
        protected static $_instance = null;

        protected $_prefix = MB_PLUGIN_PREFIX_BOOKING;

        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        public function add_booking( $data ) {
            if ( ! empty( $data ) && is_array( $data ) ) {
                $showtime_id    = isset( $data['showtime_id'] ) ? sanitize_text_field( $data['showtime_id'] ) : '';
                $city_id        = get_post_meta( $showtime_id, MB_PLUGIN_PREFIX_SHOWTIME.'city_id', true );
                $venue_id       = get_post_meta( $showtime_id, MB_PLUGIN_PREFIX_SHOWTIME.'venue_id', true );
                $room_id        = isset( $data['room_id'] ) ? sanitize_text_field( $data['room_id'] ) : '';
                $movie_id       = MB_Movie()->get_id_by_showtime( $showtime_id );

                if ( ! $showtime_id || ! $room_id || ! $movie_id ) {
                    return false;
                }

                $movie_title    = get_the_title( $movie_id );
                $movie_author   = get_post_field( 'post_author', $movie_id );

                // Cart
                $cart = isset( $data['cart'] ) ? (array)$data['cart'] : array();
                $cart = MB_Cart()->sanitize_cart_map( $cart );

                // Date
                $date = get_post_meta( $showtime_id, MB_PLUGIN_PREFIX_SHOWTIME.'date', true );
                
                // Seat
                $seat_book = array();

                if ( ! empty( $cart ) && is_array( $cart ) ) {
                    foreach( $cart as $item ) {
                        $seat_book[] = $item['id'];
                    }
                }

                // Quantity
                $qty = isset( $data['qty'] ) ? absint( $data['qty'] ) : 0;

                // Sub Total
                $subtotal = isset( $data['subtotal'] ) ? floatval( $data['subtotal'] ) : 0;

                // Discount
                $discount = isset( $data['discount'] ) ? floatval( $data['discount'] ) : 0;
                $discount_code = isset( $data['discount_code'] ) ? sanitize_text_field( $data['discount_code'] ) : '';

                // Tax
                $tax = isset( $data['tax'] ) ? floatval( $data['tax'] ) : 0;

                // Ticket Fee
                $ticket_fee = isset( $data['ticket_fee'] ) ? floatval( $data['ticket_fee'] ) : 0;

                // Total
                $total = isset( $data['total'] ) ? floatval( $data['total'] ) : 0;

                // Booking data
                $post_data['post_type']     = 'mb_booking';
                $post_data['post_title']    = $movie_title;
                $post_data['post_status']   = 'publish';
                $post_data['post_name']     = $movie_title;
                $post_data['post_author']   = $movie_author;

                $meta_input = array(
                    $this->_prefix.'customer'           => '',
                    $this->_prefix.'customer_name'      => '',
                    $this->_prefix.'customer_email'     => '',
                    $this->_prefix.'customer_phone'     => '',
                    $this->_prefix.'customer_address'   => '',
                    $this->_prefix.'showtime_id'        => $showtime_id,
                    $this->_prefix.'city_id'            => $city_id,
                    $this->_prefix.'venue_id'           => $venue_id,
                    $this->_prefix.'room_id'            => $room_id,
                    $this->_prefix.'movie_id'           => $movie_id,
                    $this->_prefix.'cart'               => $cart,
                    $this->_prefix.'seat'               => $seat_book,
                    $this->_prefix.'date'               => $date,
                    $this->_prefix.'qty'                => $qty,
                    $this->_prefix.'subtotal'           => $subtotal,
                    $this->_prefix.'discount'           => $discount,
                    $this->_prefix.'discount_code'      => $discount_code,
                    $this->_prefix.'tax'                => $tax,
                    $this->_prefix.'ticket_fee'         => $ticket_fee,
                    $this->_prefix.'total'              => $total,
                    $this->_prefix.'status'             => 'Pending',
                );

                // Check Holding Ticket
                $holding_ticket = MB()->options->checkout->get('enable_holding_ticket', 'yes');

                if ( 'yes' === $holding_ticket ) {
                    $meta_input[$this->_prefix.'time_countdown_checkout']   = current_time( 'timestamp' );
                    $meta_input[$this->_prefix.'status_holding_ticket']     = 'Pending';
                }

                $post_data['meta_input'] = apply_filters( 'mb_ft_booking_metabox_input', $meta_input );

                if ( $booking_id = wp_insert_post( $post_data, true ) ) {
                    
                    $arr_post = array(
                        'ID'            => $booking_id,
                        'post_title'    => '#' . $booking_id . ' - ' . $movie_title,
                    );

                    wp_update_post( $arr_post );

                    return $booking_id;
                }
            }

            return false;
        }

        public function mb_create_holding_ticket( $data = array(), $booking_id = null ) {
            $showtime_id    = isset( $data['showtime_id'] ) ? sanitize_text_field( $data['showtime_id'] ) : '';
            $room_id        = isset( $data['room_id'] ) ? sanitize_text_field( $data['room_id'] ) : '';
            $movie_id       = MB_Movie()->get_id_by_showtime( $showtime_id );
            $cart           = isset( $data['cart'] ) ? mb_sanitize_cart( $data['cart'] ) : array();

            // Movie Author
            $movie_obj          = get_post( $movie_id );
            $movie_author_id    = $movie_obj->post_author;

            if ( mb_array_exists( $cart ) ) {
                foreach( $cart as $items ) {
                    if ( isset( $items['id'] ) && $items['id'] ) {
                        $code = $movie_id . '_' . $showtime_id . '_' . $room_id . '_' . trim( $items['id'] );

                        $data = array(
                            'post_type'     => 'holding_ticket',
                            'post_title'    => $code,
                            'post_status'   => 'publish',
                            'post_name'     => $code,
                            'post_author'   => $movie_author_id,
                        );

                        $meta_input = array(
                            $this->_prefix.'movie_id'       => $movie_id,
                            $this->_prefix.'showtime_id'    => $showtime_id,
                            $this->_prefix.'room_id'        => $room_id,
                            $this->_prefix.'booking_id'     => $booking_id,
                            $this->_prefix.'seat'           => trim( $items['id'] ),
                            $this->_prefix.'cart'           => $cart,
                            $this->_prefix.'current_time'   => current_time( 'timestamp' ),
                            $this->_prefix.'code_checkout'  => $code,
                        );

                        $data['meta_input'] = apply_filters( 'mb_ft_create_holding_ticket_metabox_input', $meta_input );
                        $holding_ticket_id  = wp_insert_post( $data, true );
                    }
                }
            }
        }

        public function mb_check_holding_ticket( $data = array() ) {
            $result         = array();
            $showtime_id    = isset( $data['showtime_id'] ) ? sanitize_text_field( $data['showtime_id'] ) : '';
            $room_id        = isset( $data['room_id'] ) ? sanitize_text_field( $data['room_id'] ) : '';
            $movie_id       = MB_Movie()->get_id_by_showtime( $showtime_id );
            $cart           = isset( $data['cart'] ) ? mb_sanitize_cart( $data['cart'] ) : array();

            $time_countdown_checkout = absint( MB()->options->checkout->get('max_time_complete_payment', 600) );

            if ( mb_array_exists( $cart ) ) {
                $code_arr = array();

                foreach( $cart as $items ) {
                    if ( isset( $items['id'] ) && $items['id'] ) {
                        $code = $movie_id . '_' . $showtime_id . '_' . $room_id . '_' . trim( $items['id'] );
                        array_push( $code_arr, $code );
                    }
                }

                if ( mb_array_exists( $code_arr ) ) {
                    $seats_regexp = implode( '|', $code_arr );

                    $agrs = [
                        'post_type'         => 'holding_ticket',
                        'post_status'       => 'publish',
                        'posts_per_page'    => -1,
                        'fields'            => 'ids',
                        'meta_query'        => array(
                            array(
                                'key'       => $this->_prefix.'code_checkout',
                                'value'     => $seats_regexp,
                                'compare'   => 'REGEXP' 
                            ),
                        ),
                    ];

                    $holding_tickets = get_posts( $agrs );

                    if ( mb_array_exists( $holding_tickets ) ) {
                        foreach( $holding_tickets as $ht_id ) {
                            $seat           = get_post_meta( $ht_id, $this->_prefix.'seat', true );
                            $time_checkout  = get_post_meta( $ht_id, $this->_prefix.'current_time', true );
                            $past_time      = absint( current_time( 'timestamp' ) ) - absint( $time_checkout );
                            
                            if ( $past_time < $time_countdown_checkout ) {
                                $result['mb_message']   = sprintf( esc_html__( 'Seat %s has been selected(pending payment)!', 'moviebooking' ), $seat );
                                $result['mb_reload']    = sprintf( esc_html__( 'Click here to reload the page or the page will automatically reload after %s seconds.', 'moviebooking' ), '<span class="time">10</span>' );

                                break;
                            }
                        }
                    }
                }
            }

            return $result;
        }

        public function success( $booking_id, $order_id = null ) {
            $prefix = MB_PLUGIN_PREFIX_BOOKING;

            // Update Status in booking
            if ( apply_filters( 'mb_ft_new_order_update_status_completed', true ) ) {
                update_post_meta( $booking_id, $prefix.'status', 'Completed', 'Pending' );
            }

            // Update Discount
            $update_coupon = MB_Movie()->update_coupon( $booking_id );

            // Restrict when Reload page while ajax are processing checkout
            if ( ! isset( $_SESSION['booking_id_current'] ) || $_SESSION['booking_id_current'] != $booking_id ) {
                // Add Ticket
                $ticket_ids = MB_Ticket()->add_ticket( $booking_id );

                // Update Record ticket ids to Booking 
                update_post_meta( $booking_id, $prefix.'ticket_ids', $ticket_ids );

                // Update ticket
                if ( ! empty( $ticket_ids ) && is_array( $ticket_ids ) ) {
                    $data_update = array();

                    $customer_id        = get_post_meta( $booking_id, $prefix . 'customer', true );
                    $customer_name      = get_post_meta( $booking_id, $prefix . 'customer_name', true );
                    $customer_email     = get_post_meta( $booking_id, $prefix . 'customer_email', true );
                    $customer_phone     = get_post_meta( $booking_id, $prefix . 'customer_phone', true );
                    $customer_address   = get_post_meta( $booking_id, $prefix . 'customer_address', true );

                    $data_update['customer_id']         = $customer_id;
                    $data_update['customer_name']       = $customer_name;
                    $data_update['customer_email']      = $customer_email;
                    $data_update['customer_phone']      = $customer_phone;
                    $data_update['customer_address']    = $customer_address;

                    foreach( $ticket_ids as $ticket_id ) {
                        $update = MB_Ticket()->update_ticket( $ticket_id, $data_update );
                    }
                }

                $enable_send_booking_email = MB()->options->mail->get( 'enable_send_booking_email', 'yes' );

                if ( $enable_send_booking_email === 'yes' && apply_filters( 'mb_ft_new_order_use_system_mail', true ) ) {
                    MB_Email()->mb_sendmail_by_booking_id( $booking_id );
                }

                $_SESSION['booking_id_current'] = $booking_id;

                MB()->cart_session->remove();
            }

            return true;
        }

        public function get_seat_booked( $movie_id = null, $showtime_id = null, $room_id = null ) {
            if ( ! $movie_id || ! $showtime_id || ! $room_id ) return array();

            $movie_ids = MB_Movie()->get_ids_multi_lang( $movie_id );

            $args = array(
                'post_type'     => 'mb_booking',
                'post_status'   => 'publish',
                'meta_query'    => array(
                    'relation'  => 'AND',
                    array(
                        'key'       => $this->_prefix . 'movie_id',
                        'value'     => $movie_ids,
                        'compare'   => 'IN',
                    ),
                    array(
                        'key'       => $this->_prefix . 'showtime_id',
                        'value'     => $showtime_id,
                    ),
                    array(
                        'key'       => $this->_prefix . 'room_id',
                        'value'     => $room_id,
                    ),
                    array(
                        'key'       => $this->_prefix . 'status',
                        'value'     => 'Completed',
                    ),
                ),
                'posts_per_page'    => -1, 
                'numberposts'       => -1,
                'nopaging'          => true,
            );

            $bookings       = get_posts( $args );
            $seat_booked    = array();

            if ( ! empty( $bookings ) && is_array( $bookings ) ) {
                foreach( $bookings as $booking ) {
                    $seats = get_post_meta( $booking->ID, $this->_prefix . 'seat', true );

                    if ( ! empty( $seats ) && is_array( $seats ) ) {
                        foreach( $seats as $seat ) {
                            $seat_booked[] = $seat;
                        }
                    }
                }
            }

            return $seat_booked;
        }

        public function validate_before_booking( $data ) {
            $showtime_id    = isset( $data['showtime_id'] ) ? sanitize_text_field( $data['showtime_id'] ) : '';
            $room_id        = isset( $data['room_id'] ) ? sanitize_text_field( $data['room_id'] ) : '';
            $movie_id       = MB_Movie()->get_id_by_showtime( $showtime_id );
            $cart           = isset( $data['cart'] ) ? mb_sanitize_cart( $data['cart'] ) : array();
            $discount_code  = isset( $data['discountCode'] ) ? sanitize_text_field( $data['discountCode'] ) : '';

            // Check seat and discount
            $validate = MB_Room()->check_seat_exist( $movie_id, $showtime_id, $room_id, $cart, $discount_code );

            return $validate;
        }

        public function mb_get_total_booking( $movie_id = null, $status = '', $from = false, $to = false, $city_id = null, $venue_id = null, $room_id = null ) {
            $args = array(
                'post_type'      => 'mb_booking',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'fields'         => 'ids',
                'meta_query'     => array(
                    'relation'   => 'AND',
                ),
            );

            // Movie
            if ( $movie_id ) {
                array_push( $args['meta_query'] , array(
                    'key'     => $this->_prefix.'movie_id',
                    'value'   => $movie_id,
                    'compare' => '=',
                ));
            }

            // Status
            if ( $status ) {
                array_push( $args['meta_query'] , array(
                    'key'     => $this->_prefix.'status',
                    'value'   => $status,
                    'compare' => '=',
                ));
            }

            if ( $from && $to ) {
                array_push( $args['meta_query'], array(
                    'key'     => $this->_prefix.'date',
                    'value'   => array( $from, $to ),
                    'type'    => 'numeric',
                    'compare' => 'BETWEEN',
                ));
            } elseif ( $from && ! $to ) {
                array_push( $args['meta_query'], array(
                    'key'     => $this->_prefix.'date',
                    'value'   => $from,
                    'type'    => 'numeric',
                    'compare' => '>=',
                ));
            } elseif ( ! $from && $to ) {
                array_push( $args['meta_query'], array(
                    'key'     => $this->_prefix.'date',
                    'value'   => $to,
                    'type'    => 'numeric',
                    'compare' => '<=',
                ));
            } else {
                // nothing
            }

            // City
            if ( $city_id ) {
                array_push( $args['meta_query'] , array(
                    'key'     => $this->_prefix.'city_id',
                    'value'   => $city_id,
                    'compare' => '=',
                ));
            }

            // Venue
            if ( $venue_id ) {
                array_push( $args['meta_query'] , array(
                    'key'     => $this->_prefix.'venue_id',
                    'value'   => $venue_id,
                    'compare' => '=',
                ));
            }

            // Room
            if ( $room_id ) {
                array_push( $args['meta_query'] , array(
                    'key'     => $this->_prefix.'room_id',
                    'value'   => $room_id,
                    'compare' => '=',
                ));
            }

            $booking_query  = apply_filters( 'mb_ft_get_total_booking_query', $args );
            $bookings       = get_posts( $booking_query );

            $result = array(
                'subtotal'      => 0,
                'discount'      => 0,
                'tax'           => 0,
                'ticket_fee'    => 0,
                'total'         => 0,
            );

            if ( ! empty( $bookings ) && is_array( $bookings ) ) {
                foreach ( $bookings as $booking_id ) {
                    $result['subtotal']     += floatval( get_post_meta( $booking_id, $this->_prefix.'subtotal', true ) );
                    $result['discount']     += floatval( get_post_meta( $booking_id, $this->_prefix.'discount', true ) );
                    $result['tax']          += floatval( get_post_meta( $booking_id, $this->_prefix.'tax', true ) );
                    $result['ticket_fee']   += floatval( get_post_meta( $booking_id, $this->_prefix.'ticket_fee', true ) ) * floatval( get_post_meta( $booking_id, $this->_prefix.'qty', true ) );
                    $result['total']        += floatval( get_post_meta( $booking_id, $this->_prefix.'total', true ) );
                }
            }

            return $result;
        }
    }

    return new MB_Booking();
}