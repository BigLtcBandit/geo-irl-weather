<?php

// File paths for storing counts
define('INSTALL_COUNT_FILE', 'installations.txt');
define('USAGE_COUNT_FILE', 'usage.txt');

/**
 * Gets the current count from a specified file.
 *
 * @param string $filename The file to read from.
 * @return int The current count, or 0 if the file doesn't exist.
 */
function get_count($filename) {
    if (!file_exists($filename)) {
        return 0;
    }
    return (int)file_get_contents($filename);
}

/**
 * Safely increments the count in a specified file.
 * Uses file locking to prevent race conditions.
 *
 * @param string $filename The file to update.
 */
function increment_count($filename) {
    $count = get_count($filename);
    $count++;

    // Use file locking to prevent race conditions
    $fp = fopen($filename, 'w');
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, $count);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}

/**
 * Increments the installation count.
 */
function increment_install_count() {
    increment_count(INSTALL_COUNT_FILE);
}

/**
 * Increments the usage count.
 */
function increment_usage_count() {
    increment_count(USAGE_COUNT_FILE);
}

/**
 * Gets the total installation count.
 *
 * @return int
 */
function get_install_count() {
    return get_count(INSTALL_COUNT_FILE);
}

/**
 * Gets the total usage count.
 *
 * @return int
 */
function get_usage_count() {
    return get_count(USAGE_COUNT_FILE);
}

?>
