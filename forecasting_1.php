<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Data Graph</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4" style="background-color: #595959; color: white; padding: 10px; margin-top: -40px;"><a href="index.php?page=home" class="btn btn-primary" style="float: left; margin-top: 5px;">Dashboard</a>Demand Forecasting</h1>
        <form id="dateForm" class="form-inline justify-content-center">
            <div class="form-group mb-2">
                <label for="month" class="mr-2">Month:</label>
                <select id="month" name="month" class="form-control mr-3" onchange="generateGraph()">
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>
            <div class="form-group mb-2">
                <label for="year" class="mr-2">Year:</label>
                <input type="number" id="year" name="year" class="form-control mr-3" min="2000" max="2100" value="2023" onchange="generateGraph()">
            </div>
        </form>
        <div class="mt-4">
            <canvas id="salesChart"></canvas>
        </div>
        <button type="button" class="btn btn-warning mt-3" onclick="openInterpretationModal()" style="float: right; color: white">View Interpretation</button><br><br><br><br><br><br>
    </div>
    <!-- Modal -->
<div class="modal fade" id="interpretationModal" tabindex="-1" role="dialog" aria-labelledby="interpretationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="interpretationModalLabel">Interpretation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="interpretationContent">
                <!-- Interpretation content will be displayed here -->
            </div>
            <!-- Add a print button inside the modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeInterpretationModal()" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printModalContent()">Print</button>
            </div>
        </div>
    </div>
</div>

    <script>
        function closeInterpretationModal() {
            $('#interpretationModal').modal('hide');
        }
        
        function printModalContent() {
            const headerTitle = '<h1 style="text-align: center; background-color: gray; color: white; padding: 10px;">Demand Forecasting Analysis</h1>';
            const subhead = '<p style="font-weight: bold;">Interpretation</p>';
            const printContents = headerTitle + subhead + document.getElementById('interpretationContent').innerHTML;
            const originalContents = document.body.innerHTML;

            // Replace the body content with the modal content
            document.body.innerHTML = printContents;

            // Trigger the print dialog
            window.print();

            // Restore original body content
            document.body.innerHTML = originalContents;
        }
    </script>

    <script>
        let chart;

        function openInterpretationModal() {
        const month = document.getElementById('month').value;
        const year = document.getElementById('year').value;

        // Fetch the data for the current month and year
        fetch(`get_data.php?month=${month}&year=${year}`)
            .then(response => response.json())
            .then(data => {
                // Generate interpretation and table HTML based on the current data
                const interpretation = generateInterpretation(data);
                const tableHTML = generateTableHTML(data);

                // Update modal content with interpretation and table
                document.getElementById('interpretationContent').innerHTML = `${interpretation}<br>${tableHTML}`;

                // Show the modal
                $('#interpretationModal').modal('show');
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    function generateInterpretation(data) {
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        const monthNumber = parseInt(document.getElementById('month').value) - 1; // Subtract 1 because array indexing starts from 0
        const monthName = monthNames[monthNumber];
        const year2 = document.getElementById('year').value;
        // Assuming data is an array of objects with 'name' and 'qty' properties

        // Calculate total quantity sold
        const totalQuantity = data.reduce((acc, curr) => acc + parseInt(curr.qty), 0);

        // Find the product with the maximum quantity sold
        let maxSoldProduct = { name: '', qty: 0 };
        data.forEach(product => {
            if (parseInt(product.qty) > maxSoldProduct.qty) {
                maxSoldProduct = product;
            }
        });

        // Generate interpretation based on the calculated values
        const interpretation = `&nbsp &nbsp &nbsp Based on the historical sales data. As of ${monthName}, ${year2}. The overall product quantity sold for the whole month is ${totalQuantity} qty. While the "${maxSoldProduct.name}" was the best-seller product with ${maxSoldProduct.qty} qty sold. The table below shows the detailed analysis of product sales`;

        return interpretation;
    }


    function generateTableHTML(data) {
        let tableHTML = `<br><table class="table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity Sold</th>
                                </tr>
                            </thead>
                            <tbody>`;

        data.forEach(product => {
            tableHTML += `<tr>
                            <td>${product.name}</td>
                            <td>${product.qty}</td>
                        </tr>`;
        });

        tableHTML += `</tbody>
                    </table>`;

        return tableHTML;
    }

    async function generateGraph() {
        const month = document.getElementById('month').value;
        const year = document.getElementById('year').value;

        const response = await fetch(`get_data.php?month=${month}&year=${year}`);
        const data = await response.json();

        const labels = data.map(item => item.name);
        const values = data.map(item => item.qty);

        const ctx = document.getElementById('salesChart').getContext('2d');

        if (chart) {
            chart.destroy();
        }

        const colors = generateRandomColors(data.length);

        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Sold',
                    data: values,
                    backgroundColor: colors,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Automatically generate interpretation
        const interpretation = generateInterpretation(data);
        document.getElementById('interpretationContent').innerText = interpretation;
    }


        function generateRandomColors(numColors) {
            const colors = [];
            for (let i = 0; i < numColors; i++) {
                const color = `rgba(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, 0.6)`;
                colors.push(color);
            }
            return colors;
        }

        window.onload = generateGraph;


</script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
