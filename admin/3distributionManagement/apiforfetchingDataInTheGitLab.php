<?php
    function fetchData($url)
    {
        // Use cURL to make API requests
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo "cURL Error: " . curl_error($ch);
            curl_close($ch);
            return null;
        }

        // Close cURL session
        curl_close($ch);

        // Decode the response
        return json_decode($response, true); // Return decoded JSON
    }

    // Fetch provinces in Region 1
    function fetchProvinces()
    {
        $url = "https://psgc.gitlab.io/api/regions/010000000/provinces/";
        return fetchData($url);
    }

    // Fetch municipalities in a province
    function fetchMunicipalities($provinceCode)
    {
        $url = "https://psgc.gitlab.io/api/provinces/$provinceCode/municipalities/";
        return fetchData($url);
    }

    // Fetch barangays in a municipality
    function fetchBarangays($municipalityCode)
    {
        $url = "https://psgc.gitlab.io/api/municipalities/$municipalityCode/barangays/";
        return fetchData($url);
    }

    // Example usage
    $provinces = fetchProvinces();
    if ($provinces) {
        foreach ($provinces as $province) {
            echo "Province: " . htmlspecialchars($province['name']) . "<br>";

            // Fetch municipalities in each province
            $municipalities = fetchMunicipalities($province['code']);
            if ($municipalities) {
                foreach ($municipalities as $municipality) {
                    echo "&emsp;Municipality: " . htmlspecialchars($municipality['name']) . "<br>";

                    // Fetch barangays in each municipality
                    $barangays = fetchBarangays($municipality['code']);
                    if ($barangays) {
                        foreach ($barangays as $barangay) {
                            echo "&emsp;&emsp;Barangay: " . htmlspecialchars($barangay['name']) . "<br>";
                        }
                    } else {
                        echo "&emsp;&emsp;No barangays found.<br>";
                    }
                }
            } else {
                echo "&emsp;No municipalities found.<br>";
            }
        }
    } else {
        echo "Error fetching provinces.<br>";
    }
?>