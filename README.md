# wordpress-ipinfo-geolocation
Wordpress plugin to enable geolocation data from IPinfo.io

## Introduction
This plugin has a very simple goal: enable geolocation lookups from [IPinfo.io](https://ipinfo.io) for end users of Wordpress sites - then make that information available as a Javascript object where tag managers can find it. This plugin supports end user privacy by allowing the anonymisation of IP addresses, _before_ they are sent to IPinfo.io.

## Installation
1. Download all of the files from this repository and add them to a folder called `ipinfo-geolocation` within your Wordpress installations' `wp-content/plugins` directory.
2. Navigate to your Wordpress Admin console and enable the plugin.

## Configuration
The plugin has a very minimal configuration as follows:

### Enable IPinfo.io geolocation
When checked:
* Requests are made to the IPinfo.io geolocation service
* A cookie called 'geoip' is set with a 1 year expiry to store the geolocation details

If the end user IP address hasn't changed, the details stored in this cookie are used instead of requesting new geolocation details.

### Anonymise IP addresses
When checked:
* If the end user has an IPv4 IP address, the last octet is removed and replaced with a 0
* If the end user has an IPv6 IP address, the last four hextets are removed and replaced with 0s

This setting allows general location data to be obtained while respecting user privacy, and is recommended.

### IPinfo.io API token
The API token available after logging into the [IPinfo.io Dashboard](https://ipinfo.io/account).

This setting is required.

### Wordpress wp_head priority
This setting determines how early in the head the javascript object containing geolocation data appears - this is particularly important if you are relying on having access to geolocation data before other scripts run.

This setting is required. Recommended value: `-1000`.

## Javascript Object
The plugin generates a javascript object that appears in the `<head>` of the rendered HTML, and looks like this (formatting added to make this guide easier to read). Note the last octet of the IP address has been removed:

```html
<script>
  var geoip = {
    "ip": "8.8.8.0",
    "hostname": "dns.google",
    "anycast": true,
    "city": "Mountain View",
    "region": "California",
    "country": "US",
    "loc": "37.4056,-122.0775",
    "org": "AS15169 Google LLC",
    "postal": "94043",
    "timezone": "America/Los_Angeles"
  };
</script>
```

The two pieces of information you should be able to rely on is the IP address and country code. If the API response is invalid, or if the IP doesn't have a country code, the country code will be set to `XX`.

## Tag Managers
The main use case for this plugin is to enable geolocation data for use with Tag Managers (but could easily be used with other things as well). Some quick notes on using the output above with your Tag Manager of choice:

### Tealium iQ
Configure *JavaScript Variable* data layer variables with the Source set to the dotted notation for the output - e.g., `geoip.country` to add the country code to the data layer.

### Google Tag Manager
Configure *JavaScript Variable* variables with the Global Variable Name set to the dotted notation for the output - e.g., `geoip.country` to add the country code to the data layer.
