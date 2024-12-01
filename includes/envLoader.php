<?php

function loadEnv($file)
{
    if (!file_exists($file)) {
        throw new Exception("The .env file does not exist.");
    }

    // Read the .env file
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Remove comments and whitespace
        $line = trim($line);
        if (empty($line) || $line[0] === '#') {
            continue;
        }

        // Split the line into key and value
        list($key, $value) = explode('=', $line, 2);

        // Trim any extra spaces around key and value
        $key = trim($key);
        $value = trim($value);

        // Store the value in the $_ENV array
        $_ENV[$key] = $value;
    }
}

?>