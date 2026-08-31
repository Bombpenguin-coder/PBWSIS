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
                    backgroundColor: 'rgba(255, 140, 0, 0.85)', // Accent Orange
                    borderColor: '#ff8c00',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    hoverBackgroundColor: '#e07b00'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: {
                            color: '#a1a1aa', // Zinc-400 light text for dark mode
                            font: {
                                family: 'ui-sans-serif, system-ui',
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)', // Subtle grid lines
                            drawBorder: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#a1a1aa',
                            font: {
                                family: 'ui-sans-serif, system-ui',
                                size: 12
                            },
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.08)', // Dark-mode visible horizontal grid lines
                            drawBorder: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#111214',
                        titleColor: '#ffffff',
                        bodyColor: '#ff8c00',
                        borderColor: '#27272a',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₱' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});