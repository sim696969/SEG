document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('categoryChart');

    // Check if the chart canvas and data exist before trying to render the chart
    if (ctx && typeof categoryLabels !== 'undefined' && typeof categoryData !== 'undefined') {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: categoryLabels, // e.g., ['Academics', 'Facilities', 'Student Life']
                datasets: [{
                    label: '# of Feedback Submissions',
                    data: categoryData, // e.g., [12, 19, 3]
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            // Ensure only whole numbers are shown on the y-axis
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Hide the legend as it's self-explanatory
                    }
                }
            }
        });
    }
});
