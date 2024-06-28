<?php
/* display product size with stock status after product title */

function display_pa_size_after_title() {
    global $product;

    $attributes = $product->get_attributes();

    if ( isset( $attributes['pa_size'] ) ) {
        $pa_size = $attributes['pa_size'];

        if ( $pa_size->is_taxonomy() ) {
            // Get term names for the taxonomy-based attribute
            $terms = wc_get_product_terms( $product->get_id(), $pa_size->get_name(), array( 'fields' => 'all' ) );
        } else {
            // Get attribute value for the non-taxonomy attribute
            $terms = $pa_size->get_options();
        }

        if ( ! empty( $terms ) ) {
            echo '<p class="pa_size">';

            foreach ( $terms as $term ) {
                if ( $pa_size->is_taxonomy() ) {
                    $term_name = $term->name;
                    $term_slug = $term->slug;
                } else {
                    $term_name = $term;
                    $term_slug = sanitize_title( $term_name );
                }

                // Check stock quantity for each size
                $stock_status = 'stock-not-available';
                $variations = $product->get_available_variations();
                
                foreach ( $variations as $variation ) {
                    $attributes = $variation['attributes'];

                    if ( isset( $attributes['attribute_pa_size'] ) && $attributes['attribute_pa_size'] == $term_slug ) {
                        $variation_obj = new WC_Product_Variation( $variation['variation_id'] );
                        if ( $variation_obj->get_stock_quantity() > 0 ) {
                            $stock_status = 'stock-available';
                            break;
                        }
                    }
                }

                echo '<span class="' . esc_attr( $stock_status ) . '">' . esc_html( $term_name ) . '</span> ';
            }

            echo '</p>';
        }
    }
}
add_action( 'woocommerce_shop_loop_item_title', 'display_pa_size_after_title', 20 );
