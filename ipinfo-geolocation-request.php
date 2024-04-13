<?php

function ipinfo_request() {

  // Get config parameters
  $options = get_option( 'ipinfo_geolocation_settings' );
  $anonymise = $options['ipinfo_geolocation_anonymise']; // Anonymise IP addresses
  $priority = $options['ipinfo_geolocation_priority']; // Priority
  $token = $options['ipinfo_geolocation_token'];
  $urlparts = wp_parse_url(home_url());
  $domain = $urlparts['host'];

  // First, get the IP address
  // https://stackoverflow.com/a/26261699
  $ip = isset($_SERVER['HTTP_CLIENT_IP'])
    ? $_SERVER['HTTP_CLIENT_IP'] 
    : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) 
      ? $_SERVER['HTTP_X_FORWARDED_FOR'] 
      : $_SERVER['REMOTE_ADDR']);  

  // Check for valid IP address and then anonymise if enabled
  // https://stackoverflow.com/a/67442940
  if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    // Remove last octet
    if ($anonymise == '1') {
      $replace_num = strrpos($ip, '.') - strlen($ip) + 1;
      $ip = substr_replace($ip, '0', $replace_num);  
    }
  } else if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    // Remove abbreviations from IPv6 address
    // https://stackoverflow.com/a/58577916
    $hex = bin2hex( inet_pton( $ip ) );
    if ( substr( $hex, 0, 24 ) == '00000000000000000000ffff' ) { // IPv4-mapped IPv6 addresses
      $ip = long2ip( hexdec( substr( $hex, -8 ) ) );
      // Remove last octet
      if ($anonymise == '1') {
        $replace_num = strrpos($ip, '.') - strlen($ip) + 1;
        $ip = substr_replace($ip, '0', $replace_num);
      }
    } else {
      $ip = implode( ':', str_split( $hex, 4 ) );
      // Remove last four hextets
      if ($anonymise == '1') {
        $ip = substr_replace( $ip, '0000:0000:0000:0000', -19 );
      }
    }
  } else {
    // If we don't have a real IP, set to localhost
    $ip = '127.0.0.1';
  }

  // Do we have a geoip cookie? If so, read cookie details
  if ($_COOKIE['geoip']) {
    $geocookie = stripslashes( $_COOKIE['geoip'] );
    $geocookie = json_decode( $geocookie, true );
    // Does it match the current IP address?
    if ($ip == $geocookie['ip']) {
      $geoip = $geocookie;
      $geoip_cookie = true;
    } else {
      $geoip_cookie = false;
    }
  } else {
    $geoip_cookie = false;
  }

  // If $geoip_cookie == false, send anonymised IP address to IPinfo.io to find geo details
  if ($geoip_cookie == false) {
    $response = wp_remote_get('https://ipinfo.io/' . $ip . '/json?token=' . $token);
    // Handle errors - documentation for rate limits: https://ipinfo.io/developers#rate-limits
    if ( is_wp_error( $response ) || ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
      switch ( wp_remote_retrieve_response_code( $response )) {
        case 400:
          error_log('Error from ipinfo - HTTP 400 Bad Request');
          break;
        case 404:
          error_log('Error from ipinfo - HTTP 404 Invalid URL');
          break;
        case 429:
          error_log('Error from ipinfo - HTTP 429 Quota Exceeded');
          break;
      }
      $geoip['country'] = 'XX';
    } else {
      $response_body = wp_remote_retrieve_body( $response );
      $geoip = json_decode( $response_body, true );
      // Handle bogon IPs etc
      if(!array_key_exists('country', $geoip)) {
        $geoip['country'] = 'XX';
      }
    }
    // Turn $geoip into a cookie
    setcookie('geoip', json_encode( $geoip ), strtotime('+1 year'), '/', $domain);
  }
  ?>

  <script>
    var geoip = <?php echo json_encode( $geoip ) ?>;
  </script>

  <?php
}

add_action('wp_head', 'ipinfo_request', $priority);

?>
