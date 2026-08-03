document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('salesChart');
    
    // Only run this script if the sales chart exists on the page
    if (canvas) {
        const ctx = canvas.getContext('2d');
        
        // Parse the JSON data safely from the HTML attributes
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const data = JSON.parse(canvas.getAttribute('data-values'));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Gross Revenue (₱)',
                    data: data,
                    backgroundColor: 'rgba(127, 29, 29, 0.8)', // Tailwind red-900
                    borderColor: 'rgb(127, 29, 29)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value;
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
});