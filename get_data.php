<?php
$year = $_GET['year'];
$month = $_GET['month'];

// List of common sari-sari store items
$itemNames = [
    "Coca Cola", "Sprite", "Royal", "Pepsi", "Mountain Dew",
    "Redhorse", "Emperador", "RC", "Magnolia", "Yakult", "San Miguel", "Delight", "Minute Maid",
    "Tanduay", "Gin", "C2", "Plus",
    "Zesto", "Chuckie", "7-Up", "Alfonso"
];

// Function to generate random item quantities using predefined item names
function generateRandomData($itemNames, $numItems = 3) {
    $data = [];
    $usedItems = []; // Track used items to prevent duplicates
    
    // Shuffle the array to randomize item selection
    $shuffledItems = $itemNames;
    shuffle($shuffledItems);
    
    // Take only unique items up to numItems
    foreach ($shuffledItems as $item) {
        if (count($data) >= $numItems) break;
        if (!in_array($item, $usedItems)) {
            $qty = rand(10, 100); // Generate a random quantity between 10 and 100
            $data[] = ['item_name' => $item, 'qty' => $qty];
            $usedItems[] = $item;
        }
    }
    return $data;
}

// Initialize an array with random data for all months
$all_data = [];
for ($i = 1; $i <= 12; $i++) {
    $all_data[$i] = [
        'month' => $i,
        'items' => generateRandomData($itemNames, 10) // Generate 10 unique items per month
    ];
}

// Format the final data for JSON output
$data = [];
foreach ($all_data as $month_data) {
    if ($month_data['month'] == $month) {
        foreach ($month_data['items'] as $item_data) {
            $data[] = [
                'month' => $month_data['month'],
                'item_name' => $item_data['item_name'],
                'qty' => $item_data['qty']
            ];
        }
        break; // Only process the requested month
    }
}

// Sort data by item name for consistent display
usort($data, function($a, $b) {
    return strcmp($a['item_name'], $b['item_name']);
});

echo json_encode($data);
?>
