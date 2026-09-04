document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('salesChart');
    
    if (canvas) {
        const ctx = canvas.getContext('2d');
        
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const data = JSON.parse(canvas.getAttribute('data-values'));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Gross Revenue (₱)',
                    data: data,
                    
                    /* --- BAR COLORS --- */
                    backgroundColor: '#8B0000',        // Dark Maroon Bar
                    borderColor: '#8B0000',            // Bar Border Color
                    borderWidth: 1.5,
                    borderRadius: 6,
                    hoverBackgroundColor: '#700000'   // Hover State Color
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: {
                            color: '#a1a1aa',          // X-Axis Text Color
                            font: { family: 'ui-sans-serif, system-ui', size: 12 }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)', // Vertical Gridlines
                            drawBorder: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#a1a1aa',          // Y-Axis Text Color
                            font: { family: 'ui-sans-serif, system-ui', size: 12 },
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.08)', // Horizontal Gridlines
                            drawBorder: false
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        /* --- TOOLTIP BOX COLORS --- */
                        backgroundColor: '#111214',    // Tooltip Card Background
                        titleColor: '#ffffff',          // Header Text
                        bodyColor: '#ef4444',           // Light Red Value Text
                        borderColor: '#27272a',        // Card Border
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