<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

global $post;

$room_id    = get_the_ID();
$room_url   = get_edit_post_link( $room_id );
$seats      = $this->get_mb_value( 'seats' );
$types      = MBC()->mb_get_taxonomies( 'room_type' );

?>

<div class="mb_room_detail">
    <div class="ova_row">
        <label>
            <strong><?php esc_html_e( 'Room ID:', 'moviebooking' ); ?></strong>
            <?php printf( _x( '%s', 'room link', 'moviebooking' ), '<a href="'.esc_url( $room_url ).'" target="_blank">#'.esc_html( $room_id ).'</a>' ); ?>
        </label>
        <br><br>
    </div>
    <div class="ova_row">
        <label>
            <strong><?php esc_html_e( 'Room Code*:', 'moviebooking' ); ?></strong>
            <input 
                type="text" 
                class="room_code" 
                value="<?php echo esc_attr( $this->get_mb_value( 'code' ) ); ?>" 
                placeholder="<?php esc_attr_e( 'room_1', 'moviebooking' ); ?>" 
                name="<?php echo esc_attr( $this->get_mb_name( 'code' ) ); ?>" 
                autocomplete="off" 
                autocorrect="off" 
                autocapitalize="none" 
                required />
        </label>
        <br><br>
    </div>
    <div class="ova_row">
        <label>
            <strong><?php esc_html_e( 'Type*:', 'moviebooking' ); ?></strong>
        </label>
        <select name="<?php echo esc_attr( $this->get_mb_name( 'type_id' ) ); ?>" class="room_type_id mb_select2" data-placeholder="<?php esc_html_e( 'Choose type', 'moviebooking' ); ?>" required>
            <?php if ( $types ): 
                $type_id    = $this->get_mb_value( 'type_id' );
                $selected   = '';

                foreach( $types as $type_item ):
                    if ( $type_id == $type_item->term_id ) {
                        $selected = ' selected';
                    } else {
                        $selected = '';
                    }
            ?>
                    <option value="<?php echo esc_attr( $type_item->term_id ); ?>"<?php echo esc_html( $selected ); ?>>
                        <?php echo esc_html( $type_item->name ); ?>
                    </option>
            <?php endforeach; endif; ?>
        </select>
        <br><br>
    </div>
    <div class="ova_row">
        <label>
            <strong><?php esc_html_e( 'Shortcode Image Map*:', 'moviebooking' ); ?></strong>
            <input 
                type="text" 
                class="room_shortcode_img_map" 
                value="<?php echo esc_attr( $this->get_mb_value( 'shortcode_img_map' ) ); ?>" 
                placeholder="<?php esc_attr_e( '[shortcode_seat_map]', 'moviebooking' ); ?>" 
                name="<?php echo esc_attr( $this->get_mb_name( 'shortcode_img_map' ) ); ?>" 
                autocomplete="off" 
                autocorrect="off" 
                autocapitalize="none" 
                required />
        </label>
        <br><br>
    </div>
    <div class="ova_row">
        <label>
            <strong><?php esc_html_e( 'Add Seat*', 'moviebooking' ); ?></strong>
            <div class="wrap_seat_map">
                <?php if ( ! empty( $seats ) && is_array( $seats ) ): ?>
                    <?php foreach( $seats as $k => $seat ): ?>
                        <div class="item-seat">
                            <div class="seat-id-field item-seat-field">
                                <label><?php esc_html_e( 'Seat*', 'moviebooking' ); ?></label>
                                <input 
                                    type="text" 
                                    class="seat-map-id" 
                                    value="<?php echo esc_attr( $seat['id'] ); ?>" 
                                    placeholder="<?php esc_attr_e( 'A1, A2, A3, ...', 'moviebooking' ); ?>" 
                                    name="<?php echo esc_attr( $this->get_mb_name( 'seats' ) ).'['.$k.']'.'[id]'; ?>" 
                                    autocomplete="off" 
                                    autocorrect="off" 
                                    autocapitalize="none" 
                                    required />
                            </div>
                            <div class="seat-price-field item-seat-field">
                                <label><?php printf( esc_html__( 'Price(%s)*', 'moviebooking' ), MBC()->mb_get_currency() ); ?></label>
                                <input 
                                    type="text" 
                                    class="seat-map-price" 
                                    value="<?php echo esc_attr( $seat['price'] ); ?>" 
                                    placeholder="<?php esc_attr_e( '10', 'moviebooking' ); ?>" 
                                    name="<?php echo esc_attr( $this->get_mb_name( 'seats' ) ).'['.$k.']'.'[price]'; ?>" 
                                    autocomplete="off" 
                                    autocorrect="off" 
                                    autocapitalize="none" 
                                    required />
                            </div>
                            <div class="seat-type-field item-seat-field">
                                <label><?php esc_html_e( 'Type', 'moviebooking' ); ?></label></label>
                                <input 
                                    type="text" 
                                    class="seat-map-type" 
                                    value="<?php echo esc_attr( $seat['type'] ); ?>" 
                                    placeholder="<?php esc_attr_e( 'Standard', 'moviebooking' ); ?>" 
                                    name="<?php echo esc_attr( $this->get_mb_name( 'seats' ) ).'['.$k.']'.'[type]'; ?>" 
                                    autocomplete="off" 
                                    autocorrect="off" 
                                    autocapitalize="none" />
                            </div>
                            <div class="item-seat-field seat-description-field">
                                <label><?php esc_html_e( 'Description', 'moviebooking' ); ?></label></label>
                                <input 
                                    type="text" 
                                    class="seat-map-description" 
                                    value="<?php echo esc_attr( $seat['description'] ); ?>" 
                                    placeholder="<?php esc_attr_e( 'Description of type seat', 'moviebooking' ); ?>" 
                                    name="<?php echo esc_attr( $this->get_mb_name( 'seats' ) ).'['.$k.']'.'[description]'; ?>" 
                                    autocomplete="off" 
                                    autocorrect="off" 
                                    autocapitalize="none" />
                            </div>
                            <div class="seat-color-field item-seat-field">
                                <label><?php esc_html_e( 'Color', 'moviebooking' ); ?></label></label>
                                <input 
                                    type="text" 
                                    class="seat-map-color mb-colorpicker" 
                                    value="<?php echo esc_attr( $seat['color'] ); ?>" 
                                    name="<?php echo esc_attr( $this->get_mb_name( 'seats' ) ).'['.$k.']'.'[color]'; ?>" 
                                    autocomplete="off" 
                                    autocorrect="off" 
                                    autocapitalize="none" />
                            </div>
                            <a href="javascript:void(0)" class="btn remove_seat_map">
                                <i class="dashicons-before dashicons-no-alt"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </label>
    </div>
    <div class="btn_add_seat_map">
        <button class="button add_seat_map">
            <?php esc_html_e( 'Add new seat', 'moviebooking' ); ?>
        </button>
        <div class="mb-loading">
            <i class="dashicons-before dashicons-update-alt"></i>
        </div>
    </div>
    <div class="ova_row">
        <label>
            <strong><?php esc_html_e( 'Description:', 'moviebooking' ); ?></strong>
        </label>
        <div class="content">
            <textarea name="<?php echo esc_attr( $this->get_mb_name( 'description' ) ); ?>" id="room_description" cols="100%" rows="5" spellcheck="false"><?php echo esc_html( $this->get_mb_value( 'description' ) ); ?></textarea>
            <span class="note">
                <?php esc_html_e( 'Description display at frontend and PDF Ticket.', 'moviebooking' ); ?>
            </span>
            <span class="note">
                <?php esc_html_e( 'Description limited 230 character in ticket.', 'moviebooking' ); ?>
            </span>
        </div>
        <br><br>
    </div>
    <div class="ova_row">
        <label>
            <strong><?php esc_html_e( 'Private Description:', 'moviebooking' ); ?></strong>
        </label>
        <div class="content">
            <textarea name="<?php echo esc_attr( $this->get_mb_name( 'private_description' ) ); ?>" id="room_private_description" cols="100%" rows="5" spellcheck="false"><?php echo esc_html( $this->get_mb_value( 'private_description' ) ); ?></textarea>
            <span class="note">
                <?php esc_html_e( 'Private Description in Ticket - Only see when bought ticket.', 'moviebooking' ); ?>
            </span>
            <span class="note">
                <?php esc_html_e( 'Description limited 230 character in ticket.', 'moviebooking' ); ?>
            </span>
        </div>
        <br><br>
    </div>
</div>