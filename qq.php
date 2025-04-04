// Function to calculate the mean of an array
function mean($array) {
    return array_sum($array) / count($array);
}

// Function to calculate the slope (m) and intercept (b) for linear regression
function linear_regression($x, $y) {
    $n = count($x);
    
    // Calculate the means of X and Y
    $x_mean = mean($x);
    $y_mean = mean($y);
    
    // Calculate the slope (m)
    $numerator = 0;
    $denominator = 0;
    for ($i = 0; $i < $n; $i++) {
        $numerator += ($x[$i] - $x_mean) * ($y[$i] - $y_mean);
        $denominator += ($x[$i] - $x_mean) ** 2;
    }
    
    $slope = $numerator / $denominator;
    
    // Calculate the intercept (b)
    $intercept = $y_mean - ($slope * $x_mean);
    
    return [$slope, $intercept];
}

// Function to predict the y value based on the slope and intercept
function predict($slope, $intercept, $x_value) {
    return $slope * $x_value + $intercept;
}

// Get the slope (m) and intercept (b)
list($slope, $intercept) = linear_regression($x, $y);

// Make predictions for a given value of x (for example, x = 11)
$x_value = 11;
$predicted_y = predict($slope, $intercept, $x_value);

echo "Slope (m): " . $slope . PHP_EOL;
echo "Intercept (b): " . $intercept . PHP_EOL;
echo "Predicted y value for x = $x_value: " . $predicted_y . PHP_EOL;

?>
Explanation:
mean($array): Computes the mean of an array.
linear_regression($x, $y): This function calculates the slope and intercept using the least squares method.
predict($slope, $intercept, $x_value): Predicts the value of y based on a given x value using the formula y = mx + b.
This script will output the slope, intercept, and the predicted value for a given x value. You can modify the $x and $y arrays to fit your own data.








