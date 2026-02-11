<?php
add_action( 'woocommerce_thankyou', 'traverze_redirect_to_calendly_after_payment', 20 );

function traverze_redirect_to_calendly_after_payment( $order_id ) {
	if ( ! $order_id ) {
		return;
	}

	if ( is_admin() ) {
		return;
	}

	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}

	$target_product_id = 18962;
	$calendly_url      = 'https://calendly.com/info-61098/30min';

	// Only redirect for successful order states.
	$allowed_statuses = array( 'processing', 'completed' );
	if ( ! in_array( $order->get_status(), $allowed_statuses, true ) ) {
		return;
	}

	// Explicitly block unpaid/failed/cancelled-style outcomes.
	if ( ! $order->is_paid() ) {
		return;
	}

	// Prevent repeat redirect per order.
	if ( 'yes' === $order->get_meta( '_calendly_redirect_done', true ) ) {
		return;
	}

	$has_target_product = false;
	foreach ( $order->get_items() as $item ) {
		if ( (int) $item->get_product_id() === $target_product_id ) {
			$has_target_product = true;
			break;
		}
	}

	if ( ! $has_target_product ) {
		return;
	}

	// Use server-side redirect when possible; fallback to client-side redirect below.
	if ( ! headers_sent() ) {
		$order->update_meta_data( '_calendly_redirect_done', 'yes' );
		$order->save();

		wp_safe_redirect( $calendly_url );
		exit;
	}

	$order->update_meta_data( '_calendly_redirect_done', 'yes' );
	$order->save();

	?>
	<div style="max-width:500px;margin:40px auto;padding:20px;border:2px solid #4CAF50;border-radius:10px;text-align:center;font-size:18px;color:#4CAF50;">
		Payment successful. Redirecting you to book your consultation…
	</div>

	<script>
		setTimeout(function () {
			window.location.href = <?php echo wp_json_encode( esc_url_raw( $calendly_url ) ); ?>;
		}, 1500);
	</script>

	<noscript>
		<meta http-equiv="refresh" content="1;url=<?php echo esc_url( $calendly_url ); ?>">
	</noscript>
	<?php
}
