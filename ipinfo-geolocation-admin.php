<?php
add_action( 'admin_menu', 'ipinfo_geolocation_add_admin_menu' );
add_action( 'admin_init', 'ipinfo_geolocation_settings_init' );

function ipinfo_geolocation_add_admin_menu() {

  add_options_page(
    'IPinfo.io Geolocation Settings', // Page title
    'IPinfo.io Geolocation', // Menu title
    'manage_options', // Capability
    'ipinfo_geolocation_settings', // Menu slug
    'ipinfo_geolocation_options_page' // Callback
  );

}

function ipinfo_geolocation_settings_init() { 

	register_setting( 'pluginPage', 'ipinfo_geolocation_settings' );

	add_settings_section(
    'ipinfo_geolocation_pluginPage_section', 
		__( 'IPinfo.io Geolocation Settings', 'ipinfo_geolocation' ), 
		'ipinfo_geolocation_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'ipinfo_geolocation_enable', 
		__( 'Enable IPinfo.io geolocation', 'ipinfo_geolocation' ), 
		'ipinfo_geolocation_enable_render', 
		'pluginPage', 
		'ipinfo_geolocation_pluginPage_section' 
	);

	add_settings_field( 
		'ipinfo_geolocation_anonymise', 
		__( 'Anonymise IP addresses', 'ipinfo_geolocation' ), 
		'ipinfo_geolocation_anonymise_render', 
		'pluginPage', 
		'ipinfo_geolocation_pluginPage_section' 
	);

	add_settings_field( 
		'ipinfo_geolocation_token', 
		__( 'IPinfo.io API token', 'ipinfo_geolocation' ), 
		'ipinfo_geolocation_token_render', 
		'pluginPage', 
		'ipinfo_geolocation_pluginPage_section' 
	);

	add_settings_field( 
		'ipinfo_geolocation_priority', 
		__( 'Wordpress wp_head priority', 'ipinfo_geolocation' ), 
		'ipinfo_geolocation_priority_render', 
		'pluginPage', 
		'ipinfo_geolocation_pluginPage_section' 
	);

}

function ipinfo_geolocation_enable_render() { 

	$options = get_option( 'ipinfo_geolocation_settings' );
	?>
	<input type='checkbox' name='ipinfo_geolocation_settings[ipinfo_geolocation_enable]' <?php checked( $options['ipinfo_geolocation_enable'], 1 ); ?> value='1'>
	<?php

}

function ipinfo_geolocation_anonymise_render() { 

	$options = get_option( 'ipinfo_geolocation_settings' );
	?>
	<input type='checkbox' name='ipinfo_geolocation_settings[ipinfo_geolocation_anonymise]' <?php checked( $options['ipinfo_geolocation_anonymise'], 1 ); ?> value='1'>
	<?php

}

function ipinfo_geolocation_token_render() { 

	$options = get_option( 'ipinfo_geolocation_settings' );
	?>
	<input type='text' name='ipinfo_geolocation_settings[ipinfo_geolocation_token]' value='<?php echo $options['ipinfo_geolocation_token']; ?>'>
	<?php

}

function ipinfo_geolocation_priority_render() { 

	$options = get_option( 'ipinfo_geolocation_settings' );
	?>
	<input type='text' name='ipinfo_geolocation_settings[ipinfo_geolocation_priority]' value='<?php echo $options['ipinfo_geolocation_priority']; ?>'>
	<?php

}

function ipinfo_geolocation_settings_section_callback() { 

	echo __( 'Settings for enabling and accessing the IPinfo.io API', 'ipinfo_geolocation' );

}

function ipinfo_geolocation_options_page() {

  ?>
  <form action='options.php' method='post'>
    <?php
    settings_fields( 'pluginPage' );
    do_settings_sections( 'pluginPage' );
    submit_button();
    ?>
  </form>
  <?php

}
