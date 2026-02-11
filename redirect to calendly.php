add_action( 'woocommerce_thankyou', 'traverze_redirect_to_calendly_after_payment', 20 );

function traverze_redirect_to_calendly_after_payment( $order_id ) {

    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    $target_product_id = 18962;

    // Prevent repeat redirect per order
    if ( $order->get_meta( '_calendly_redirect_done' ) ) {
        return;
    }

    foreach ( $order->get_items() as $item ) {
        if ( (int) $item->get_product_id() === $target_product_id ) {

            // Mark redirect as done immediately
            $order->update_meta_data( '_calendly_redirect_done', 'yes' );
            $order->save();

            ?>
            <div style="max-width:500px;margin:40px auto;padding:20px;border:2px solid #4CAF50;border-radius:10px;text-align:center;font-size:18px;color:#4CAF50;">
                Payment successful. Redirecting you to book your consultation…
            </div>

            <script>
                setTimeout(function () {
                    window.location.href = "https://calendly.com/info-61098/30min";
                }, 1500);
            </script>
            <?php
            break;
        }
    }
}
