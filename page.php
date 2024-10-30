<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( __( 'You do not have sufficient permissions to access this page.', 'callback-widget-pulsar' ) );
}
?>

<div class="wrap">
    <h2>Callback widget Pulsar</h2>

    <form method="post" action="options.php">
		<?php
		settings_fields( 'callback_widget_pulsar' );
		do_settings_sections( 'callback-widget-pulsar' );
		?>
		<?php submit_button(); ?>
    </form>
</div>