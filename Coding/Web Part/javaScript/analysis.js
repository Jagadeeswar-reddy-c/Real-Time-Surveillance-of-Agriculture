// Variable to store the previous chart instances
document.addEventListener("DOMContentLoaded", function() {
  // Your JavaScript code here


let previousChartNameValue;
let previousChartWeekly;

async function fetchData(deviceId, apiKey, fields, labels, fromDate, toDate) {
  const url = `https://api.thingspeak.com/channels/${deviceId}/feeds.json?api_key=${apiKey}&start=${fromDate}&end=${toDate}`;

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

// Function to create chart
function createChart(element, chartType, labels, datasets) {
    // Check if previous chart exists and destroy it
    if (element === targettedChart && previousChartNameValue) {
        previousChartNameValue.destroy();
    } else if (element === weeklyCanvas && previousChartWeekly) {
        previousChartWeekly.destroy();
    }

    // Create new chart
    const newChart = new Chart(element, {
        type: chartType,
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: "bottom" // Adjust as needed
                }
            }
        }
    });

    // Update previous chart reference based on the canvas
    if (element === targettedChart) {
        previousChartNameValue = newChart;
    } else if (element === weeklyCanvas) {
        previousChartWeekly = newChart;
    }
}
const timePeriodSelect = document.getElementById("time-period-select");
const nameValue = document.getElementById("name_value");
const targettedChart = document.getElementById("name_value_canvas");
const weeklyCanvas = document.getElementById("weekly");
// Event Listener to listen for changes in selection
timePeriodSelect.addEventListener("change", function() {
    const selectedValue = this.value;

    // Replace DEVICE_ID and API_KEY with your actual values
    const DEVICE_ID = 2443501;
    const API_KEY = 'HIH7KHEBSGBD4WVI';

    // Define the fields and labels based on the selected option
    let fields, labels, labelsWeekly;
    if (selectedValue === "humidity") {
        fields = ["field2"];
        labels = ["Humidity"];
        // labelsWeekly = ["Humidity Weekly"];
    } else if (selectedValue === "soil") {
        fields = ["field3"];
        labels = ["Soil Moisture"];
        // labelsWeekly = ["Soil Moisture Weekly"];
    } else if (selectedValue === "water") {
        fields = ["field4"];
        labels = ["Water Level"];
        // labelsWeekly = ["Water Level Weekly"];
    } else if (selectedValue === "minerals") {
        fields = ["field5"];
        labels = ["Minerals"];
        // labelsWeekly = ["Minerals Weekly"];
    }
    labelsWeekly = labels;

//deviceId, apiKey, fields, labels, fromDate, toDate
    const currentDate = new Date();
    const labelsSelected = Array.from({ length: 30 }, (_, i) => {
        const date = new Date(currentDate);
        date.setDate(date.getDate() - 6 + i);
        return date.toLocaleDateString(); // Adjust date format as needed
    });

    nameValue.textContent = selectedValue.charAt(0).toUpperCase() + selectedValue.slice(1);

    // Fetch data for the selected chart
    fetchData(DEVICE_ID, API_KEY, fields, labels,labelsSelected[0],labelsSelected[labelsSelected.length - 1])
        .then(datasets => {
            if (datasets) {
                const currentDate = new Date();
                const labels = Array.from({ length: 30 }, (_, i) => {
                    const date = new Date(currentDate);
                    date.setDate(date.getDate() - 6 + i);
                    return date.toLocaleDateString(); // Adjust date format as needed
                });

                // Update the corresponding chart with the new labels and datasets
                createChart(targettedChart, "line", labels, datasets);
            } else {
                console.error('No data fetched for the selected chart');
            }
        })
        .catch(error => {
            console.error('Error updating chart:', error.message);
        });

    // Fetch weekly data
    fetchData(DEVICE_ID, API_KEY, fields, labelsWeekly, labelsSelected[0], labelsSelected[7])
        .then(datasets => {
            if (datasets) {
                const currentDate = new Date();
                const labels = Array.from({ length: 7 }, (_, i) => {
                    const date = new Date(currentDate);
                    date.setDate(date.getDate() - 6 + i);
                    return date.toLocaleDateString(); // Adjust date format as needed
                });

                // Update the weekly chart with the new labels and datasets
                createChart(weeklyCanvas, "line", labels, datasets);
            } else {
                console.error('No data fetched for weekly chart');
            }
        })
        .catch(error => {
            console.error('Error fetching weekly data:', error.message);
        });
});
});