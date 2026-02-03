<?php
// Test script để kiểm tra xử lý schema
include "config.php";

echo "=== TEST SCHEMA PROCESSING ===\n\n";

// Test 1: Schema data với escape sequences
$testSchema1 = '{
    "schemavi": "{\r\n    \"@context\": \"https://schema.org\",\r\n    \"@graph\": [\r\n        {\r\n            \"@type\": \"CollectionPage\",\r\n            \"name\": \"Giày nam\",\r\n            \"description\": \"giày nam sản xuất những năm 80. 2022@ giày làm từ sợ thủy tinh\",\r\n            \"url\": \"http://localhost/dts-source/giay-nam\"\r\n        }\r\n    ]\r\n}"
}';

echo "Test 1: Schema với escape sequences\n";
$decoded = json_decode($testSchema1, true);
$processed = $func->processManualSchemaData($decoded['schemavi']);
echo "Processed:\n" . $processed . "\n\n";

// Test 2: Schema data trực tiếp
$testSchema2 = '{
    "@context": "https://schema.org",
    "@graph": [
        {
            "@type": "CollectionPage",
            "name": "Giày nam",
            "description": "giày nam sản xuất những năm 80",
            "url": "http://localhost/dts-source/giay-nam"
        }
    ]
}';

echo "Test 2: Schema JSON trực tiếp\n";
$processed2 = $func->processManualSchemaData($testSchema2);
echo "Processed:\n" . $processed2 . "\n\n";

// Test 3: Format schema for display
echo "Test 3: Format schema for display\n";
$formatted = $func->formatSchemaForDisplay($processed2);
echo "Formatted:\n" . $formatted . "\n\n";

echo "=== TEST COMPLETED ===\n";
?>
