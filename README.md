# Geo IRL Weather

A simple, embeddable overlay that displays the user's current location, weather, and time. It's designed to be used as a browser source in IRL PRO for Android.

## Features

*   **Geolocation:** Automatically detects the user's location.

## Hosted Version

A hosted version of this project is available at [https://kickis.fun/](https://kickis.fun/).
*   **Weather Display:** Shows the current temperature and weather conditions using emoji icons.
*   **Time and Date:** Displays the current time and rotates to show the date every 15 seconds.
*   **Error Handling:** Displays informative error messages if location services are denied or other issues occur.
*   **Lightweight:** No external libraries are used; it's all vanilla HTML, CSS, and JavaScript.

### Customization

The overlay can be customized by adding the following URL parameters:

*   **Temperature Units:**
    *   `?temp=c` for Celsius (default)
    *   `?temp=f` for Fahrenheit
*   **Update Interval:**
    *   `?update=[minutes]` to set the weather update frequency.
    *   The value can be any integer from **15** to **30** minutes.
    *   If not specified, the default is 30 minutes.
    *   Example: `?update=20` will update the weather every 20 minutes.

## Setup

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/BigLtcBandit/geo-irl-weather.git
    ```
2.  **Host the files:**
    Upload the files to a web server that supports PHP. You can then access the overlay by navigating to `index.php` in your browser.

    This project uses the [Nominatim API](https://nominatim.org/release-docs/latest/api/Reverse/) for reverse geocoding, which does not require an API key for basic usage. Please be mindful of their [Usage Policy](https://operations.osmfoundation.org/policies/nominatim/).

## Credits

This project is made possible by the following free APIs:

*   **[Open-Meteo](https://open-meteo.com/):** For weather data.
*   **[Nominatim (OpenStreetMap)](https://nominatim.org/):** For reverse geocoding.

