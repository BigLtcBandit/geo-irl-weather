# Geo IRL Weather

A simple, embeddable overlay that displays the user's current location, weather, and time. It's designed to be used as a browser source in streaming software like OBS, but can be used in any web context.

## Features

*   **Geolocation:** Automatically detects the user's location.
*   **Weather Display:** Shows the current temperature and weather conditions using emoji icons.
*   **Time and Date:** Displays the current time and rotates to show the date every 15 seconds.
*   **Customizable Units:** Temperature can be displayed in Celsius (default) or Fahrenheit by adding `?temp=f` to the URL.
*   **Error Handling:** Displays informative error messages if location services are denied or other issues occur.
*   **Lightweight:** No external libraries (like Tailwind) are used in the final code, it's all vanilla HTML, CSS, and JavaScript.

## Setup

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/your-username/geo-irl-weather.git
    ```
2.  **Get a Google Maps API Key:**
    This project uses the Google Geocoding API to get the city and country name from the user's coordinates. You will need to get an API key from the [Google Cloud Console](https://console.cloud.google.com/).
    - Make sure to enable the "Geocoding API" for your project.
    - Secure your API key by restricting it to your domain.

3.  **Configure the proxy:**
    Open `proxy-geocode.php` and replace `'PUT-KEY-HERE'` with your actual Google Maps API key.
    ```php
    // IMPORTANT: Replace 'YOUR_GOOGLE_MAPS_API_KEY' with your actual API key.
    $googleApiKey = 'YOUR_GOOGLE_MAPS_API_KEY';
    ```
4.  **Host the files:**
    Upload the files to a web server that supports PHP. You can then access the overlay by navigating to `index.php` in your browser.

## Credits

This project is made possible by the following free APIs:

*   **[Open-Meteo](https://open-meteo.com/):** For weather data.
*   **[Google Maps Platform](https://developers.google.com/maps):** For reverse geocoding.

## License

This project is licensed under the MIT License.

**The MIT License (MIT)**

Copyright (c) 2023

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
