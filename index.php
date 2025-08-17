<?php
// Include the analytics functions
require_once 'analytics.php';

// Check if the installation has already been tracked
if (!isset($_COOKIE['geo-weather-installed'])) {
    // If not, increment the install count
    increment_install_count();
    // Set a cookie to prevent tracking this user again. Expires in 10 years.
    setcookie('geo-weather-installed', 'true', time() + (10 * 365 * 24 * 60 * 60), "/");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geo Weather & Time Overlay</title>
    <!-- Preload the font to prevent render blocking -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap"></noscript>
    <style>
        /* Custom styles for the overlay, replacing Tailwind classes */
        body {
            /* Ensures the body is transparent if this HTML is embedded */
            background-color: transparent;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            overflow: hidden; /* Prevent scrollbars */
        }

        #weather-overlay {
            /* Fixed position to act as an overlay */
            position: fixed;
            top: 0;
            left: 0;
            width: 300px;
            height: 200px;
            background-color: transparent;
            color: #ffffff; /* White text */
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center content vertically */
            align-items: center; /* Center content horizontally */
            padding: 1rem;
            border-radius: 0.75rem; /* Replaces rounded-xl */
            box-sizing: border-box; /* Include padding in width/height */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7); /* Replaces text-shadow-md */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* Replaces shadow-lg */
            z-index: 50; /* Replaces z-50 */
        }

        /* Styling for individual elements */
        #location {
            font-size: 1.125rem; /* Replaces text-lg */
            font-weight: 700; /* Replaces font-bold */
            margin-bottom: 0.5rem; /* Replaces mb-2 */
            text-align: center;
        }

        #temperature-row {
            display: flex; /* Replaces flex */
            align-items: center; /* Replaces items-center */
            margin-bottom: 0.5rem; /* Replaces mb-2 */
        }

        #temperature {
            font-size: 2.25rem; /* Replaces text-4xl */
            font-weight: 800; /* Replaces font-extrabold */
            line-height: 1; /* Replaces leading-none */
            margin-right: 0.5rem; /* Replaces mr-2 */
        }

        #conditions-icon {
            font-size: 2.5rem; /* Larger size for the emoji icon */
            line-height: 1; /* Replaces leading-none */
        }

        #time {
            font-size: 1.5rem; /* Replaces text-2xl */
            font-weight: 500; /* Replaces font-medium */
        }

        #error-message {
            color: #ef4444; /* Replaces text-red-500 */
            font-size: 0.875rem; /* Replaces text-sm */
            margin-top: 0.5rem; /* Replaces mt-2 */
            text-align: center;
        }

        #loading-indicator {
            font-size: 1rem;
            margin-top: 1rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Utility class to hide elements, replacing Tailwind's 'hidden' */
        .hidden {
            display: none !important;
        }
    </style>
</head>
<body>
    <div id="weather-overlay">
        <div id="location">Loading Location...</div>
        <div id="temperature-row">
            <div id="temperature">--°C</div>
            <div id="conditions-icon"></div> <!-- Icon will be placed here -->
        </div>
        <div id="time">--:--:--</div>
        <div id="error-message" class="hidden"></div>
        <div id="loading-indicator"></div>
    </div>

    <script>
        // Get references to the DOM elements
        const locationElement = document.getElementById('location');
        const temperatureElement = document.getElementById('temperature');
        const conditionsIconElement = document.getElementById('conditions-icon');
        const timeElement = document.getElementById('time');
        const errorMessageElement = document.getElementById('error-message');
        const loadingIndicator = document.getElementById('loading-indicator');

        let latitude = null;
        let longitude = null;
        let tempUnit = 'celsius'; // Default to Celsius

        // Check URL parameter for temperature unit
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('temp') === 'f') {
            tempUnit = 'fahrenheit';
        }

        // Get update interval from URL, default to 30 minutes
        let updateInterval = 1800000; // Default to 30 minutes
        const updateParam = urlParams.get('update');
        if (updateParam) {
            const parsedInterval = parseInt(updateParam, 10);
            if (!isNaN(parsedInterval) && parsedInterval >= 15 && parsedInterval <= 30) {
                updateInterval = parsedInterval * 60 * 1000;
            }
        }

        // Variables for time/date rotation
        let displayMode = 0; // 0 for time, 1 for date
        let rotationCounter = 0; // Counts seconds for rotation

        // Function to display error messages
        function displayError(message) {
            errorMessageElement.textContent = `Error: ${message}`;
            errorMessageElement.classList.remove('hidden');
            loadingIndicator.classList.add('hidden');
        }

        // Function to update the current time or date
        function updateTime() {
            const now = new Date();

            // Increment counter and toggle display mode every 15 seconds
            rotationCounter++;
            if (rotationCounter >= 15) {
                displayMode = 1 - displayMode; // Toggle between 0 and 1
                rotationCounter = 0;
            }

            if (displayMode === 0) {
                // Display time with AM/PM
                const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
                timeElement.textContent = now.toLocaleTimeString('en-US', options);
            } else {
                // Display date
                const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
                timeElement.textContent = now.toLocaleDateString('en-US', options);
            }
        }

        // Function to fetch weather data from Open-Meteo
        async function getWeatherData(lat, lon) {
            const weatherApiUrl = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&temperature_unit=${tempUnit}&windspeed_unit=ms&precipitation_unit=mm&timezone=auto`;

            loadingIndicator.textContent = 'Fetching weather...';

            try {
                const response = await fetch(weatherApiUrl);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();

                if (data.current_weather) {
                    temperatureElement.textContent = `${Math.round(data.current_weather.temperature)}°${tempUnit === 'fahrenheit' ? 'F' : 'C'}`;
                    const weatherCode = data.current_weather.weathercode;
                    conditionsIconElement.textContent = getWeatherEmoji(weatherCode);
                } else {
                    conditionsIconElement.textContent = '';
                }

                errorMessageElement.classList.add('hidden');
                loadingIndicator.classList.add('hidden');

            } catch (error) {
                console.error('Failed to fetch weather data from Open-Meteo:', error);
                displayError('Could not fetch weather data.');
                temperatureElement.textContent = `--°${tempUnit === 'fahrenheit' ? 'F' : 'C'}`;
                conditionsIconElement.textContent = '';
                loadingIndicator.classList.add('hidden');
            }
        }

        // Helper function to map Open-Meteo weather codes to emoji icons
        function getWeatherEmoji(code) {
            switch (code) {
                case 0: return '☀️';
                case 1: return '🌤️';
                case 2: return '⛅';
                case 3: return '☁️';
                case 45: case 48: return '🌫️';
                case 51: case 53: case 55: return '🌧️';
                case 56: case 57: return '🧊🌧️';
                case 61: case 63: case 65: return '☔';
                case 66: case 67: return '🥶☔';
                case 71: case 73: case 75: return '🌨️';
                case 77: return '❄️';
                case 80: case 81: case 82: return '💦';
                case 85: case 86: return '🌨️';
                case 95: case 96: case 99: return '⛈️';
                default: return '❓';
            }
        }

        // Function to fetch geocoded location data from the Nominatim proxy
        async function getGeocodedLocation(lat, lon) {
            const geocodingProxyUrl = `proxy-geocode.php?lat=${lat}&lon=${lon}`;
            loadingIndicator.textContent = 'Getting location details...';
            try {
                const response = await fetch(geocodingProxyUrl);
                if (!response.ok) {
                    throw new Error(`Proxy error! status: ${response.status}`);
                }
                const data = await response.json();
                if (data.error) {
                    // Nominatim might return an error object with a message
                    throw new Error(data.error.message || data.error);
                }
                if (data.address) {
                    const city = data.address.city || data.address.town || data.address.village || 'Unknown City';
                    const country = data.address.country || 'Unknown Country';
                    locationElement.textContent = `${city}, ${country}`;
                } else {
                    locationElement.textContent = 'Location not found';
                }
                errorMessageElement.classList.add('hidden');
            } catch (error) {
                console.error('Failed to fetch geocoding data from proxy:', error);
                displayError(`Could not get location details. ${error.message}`);
                locationElement.textContent = 'Location N/A';
            } finally {
                loadingIndicator.classList.add('hidden');
            }
        }

        // Function to get user's geographical location
        function getLocation() {
            loadingIndicator.textContent = 'Getting location...';
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        latitude = position.coords.latitude;
                        longitude = position.coords.longitude;
                        console.log(`Location: Lat ${latitude}, Lon ${longitude}`);
                        loadingIndicator.textContent = 'Location found.';
                        Promise.all([
                            getGeocodedLocation(latitude, longitude),
                            getWeatherData(latitude, longitude)
                        ]);
                        updateTime();
                        setInterval(() => {
                            getWeatherData(latitude, longitude);
                        }, updateInterval);
                        setInterval(updateTime, 1000);
                    },
                    (error) => {
                        console.error('Geolocation error:', error);
                        let errorMessage = '';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = "Location access denied. Please enable it in your browser settings.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = "Location information is unavailable.";
                                break;
                            case error.TIMEOUT:
                                errorMessage = "The request to get user location timed out.";
                                break;
                            default:
                                errorMessage = "An unknown error occurred while getting location.";
                                break;
                        }
                        displayError(errorMessage);
                        locationElement.textContent = 'Location N/A';
                        loadingIndicator.classList.add('hidden');
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            } else {
                displayError('Geolocation is not supported by this browser.');
                locationElement.textContent = 'Location N/A';
                loadingIndicator.classList.add('hidden');
            }
        }

        // Initialize the overlay by getting the location
        window.onload = getLocation;
    </script>
</body>
</html>
