// doughnut-chart.js

// Function to calculate the average of an array of numbers
function calculateAverage(arr) {
    const sum = arr.reduce((acc, curr) => acc + curr, 0);
    return sum / arr.length;
}

// Function to fetch data from the API
async function fetchData(deviceId, apiKey, fields, labels, fromDate, toDate) {
    const url = `https://api.thingspeak.com/channels/${deviceId}/feeds.json?api_key=${apiKey}&start=${fromDate.toISOString()}&end=${toDate.toISOString()}`;

    try {
        const response = await fetch(url);
        const data = await response.json();

        if (!data || !data.feeds) {
            throw new Error('Invalid response from ThingSpeak');
        }

        // Extracting data for each field
        const datasets = fields.map((field, index) => {
            const values = data.feeds.map(feed => parseFloat(feed[field]));
            return {
                label: labels[index],
                data: values
            };
        });

        return datasets;
    } catch (error) {
        console.error('Error fetching data:', error.message);
        return null;
    }
}

// Function to create the doughnut chart
function createDoughnutChart(canvas, labels, data) {
    new Chart(canvas, {
        type: "doughnut",
        data: {
            labels: labels,
            datasets: [{
                label: "Average",
                data: data,
                backgroundColor: [
                    "rgba(155, 128, 151, 1",
                    "rgba(254, 111, 162, 1",
                    "rgba(244, 164, 111, 1)",
                    "rgba(211, 1, 192, 1)",
                ],
                hoverBackgroundColor: "#ff90b8",
            }],
        },
        options: {
            responsive: true,
        },
    });
}

// Fetch data and create doughnut chart on page load
window.addEventListener('DOMContentLoaded', () => {
    const averagesCanvas = document.getElementById("averages");

    // Replace DEVICE_ID and API_KEY with your actual values
    const DEVICE_ID = 2443501;
    const API_KEY = 'HIH7KHEBSGBD4WVI';
    const fields = ["field2", "field3", "field4", "field5"];
    const labels = ["Humidity", "Soil Moisture", "Water Level", "Minerals"];

    const currentDate = new Date();
    const fromDate = new Date(currentDate);
    fromDate.setDate(fromDate.getDate() - 30); // Subtract 30 days from the current date

    // Ensure toDate is set to the current date
    const toDate = currentDate;

    // Fetch data for the doughnut chart
    fetchData(DEVICE_ID, API_KEY, fields, labels, fromDate, toDate)
        .then(datasets => {
            if (datasets) {
                const averages = datasets.map(dataset => calculateAverage(dataset.data));

                // Create doughnut chart for product revenue
                createDoughnutChart(averagesCanvas, labels, averages);
            } else {
                console.error('No data fetched for the doughnut chart');
            }
        })
        .catch(error => {
            console.error('Error fetching data for doughnut chart:', error.message);
        });
});
