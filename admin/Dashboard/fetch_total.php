<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_darfo1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetching the data
$queries = [
    "interventions" => "SELECT COUNT(DISTINCT intervention_id) AS total_interventions FROM tbl_distribution",
    "beneficiaries" => "SELECT COUNT(DISTINCT beneficiary_id) AS total_beneficiaries FROM tbl_distribution",
    "individual_percentage" => "SELECT (SUM(quantity) / (SELECT SUM(quantity) FROM tbl_intervention_inventory) * 100) AS individual_percentage FROM tbl_distribution WHERE type_of_distribution = 'Individual'",
    "group_total" => "SELECT SUM(quantity) AS total_group FROM tbl_distribution WHERE type_of_distribution = 'Group'"
];

$results = [];

foreach ($queries as $key => $query) {
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $results[$key] = $row[array_keys($row)[0]] ?? 0;
}

// Close connection
$conn->close();

// Dynamic card data
$cardData = [
    ["title" => "Intervention Distributed", "value" => $results["interventions"], "icon" => "fa-calendar", "color" => "primary"],
    ["title" => "Number of Beneficiary", "value" => $results["beneficiaries"], "icon" => "fa-dollar-sign", "color" => "success"],
    ["title" => "Product Distributed (Individual)", "value" => round($results["individual_percentage"], 2) . "%", "icon" => "fa-clipboard-list", "color" => "info"],
    ["title" => "Product Distributed (Group)", "value" => number_format($results["group_total"]), "icon" => "fa-box", "color" => "warning"]
];
?>