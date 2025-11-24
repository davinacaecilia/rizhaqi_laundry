// Pastikan kode ini di dalam event listener DOMContentLoaded (Jika ada)

    // Data for chart 1: Total Berat Cucian Masuk (Bulanan)
    const museumData = { // Kita tetap pakai nama 'museumData' di JS agar tidak merusak kode HTML
        labels: [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des' // Label Bulan
        ],
        datasets: [{
            label: 'Total Berat (Kg)', // Label untuk sumbu Y
            data: [1500, 1650, 1400, 1800, 2000, 1950, 2100, 2300, 2200, 2500, 2600, 2750], // Data dummy volume Kg per bulan
            backgroundColor: [
                '#4ECDC4',
                '#45B7D1',
                '#96CEB4',
                '#FF6B6B',
                '#FFEAA7',
                '#DDA0DD',
                '#98D8C8',
                '#F8B500',
                '#4ECDC4',
                '#45B7D1',
                '#96CEB4',
                '#FF6B6B'
            ],
            borderWidth: 0,
            borderRadius: 8
        }]
    };

    // Data for chart 2: Distribusi Jenis Layanan (Doughnut Chart)
    const mediumData = { // Kita tetap pakai nama 'mediumData'
        labels: [
            'Cuci Setrika', 
            'Cuci Kering Saja',    
            'Setrika Saja',   
            'Bed Cover/Selimut',
            'Pakaian Bayi', 
            'Lainnya'    
        ],
        datasets: [{
            label: 'Persentase Layanan', // Label untuk legend
            data: [55, 15, 10, 15, 3, 2], // Data dummy persentase total order
            backgroundColor: [
                '#FF6B6B', // Cuci Setrika (Paling Besar)
                '#4ECDC4', // Cuci Kering Saja
                '#45B7D1', // Setrika Saja
                '#96CEB4', // Bed Cover
                '#FFEAA7', // Pakaian Bayi
                '#DDA0DD'  // Lainnya
            ],
            borderColor: '#fff',
            borderWidth: 2
        }]
    };

    // Chart 1 configuration (Bar Chart) - Total Berat Cucian
    const museumConfig = {
        type: 'bar',
        data: museumData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f0f0f0'
                    },
                    ticks: {
                        color: '#666'
                    },
                    title: { // Judul Sumbu Y
                        display: true,
                        text: 'Berat Cucian (Kg)' 
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#666',
                        maxRotation: 45
                    },
                    title: { // Judul Sumbu X
                        display: true,
                        text: 'Bulan' 
                    }
                }
            }
        }
    };

    // Chart 2 configuration (Doughnut Chart) - Distribusi Layanan
    const mediumConfig = {
        type: 'doughnut',
        data: mediumData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        color: '#666'
                    }
                },
                tooltip: { 
                    callbacks: {
                        label: function(context) {
                            // Menampilkan Label + Persentase
                            const label = context.label || '';
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const currentValue = context.parsed;
                            const percentage = Math.round((currentValue/total) * 100);
                            return `${label}: ${currentValue} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '60%'
        }
    };

    // Render charts
    const museumCtx = document.getElementById('museumChart').getContext('2d');
    const mediumCtx = document.getElementById('mediumChart').getContext('2d');

    new Chart(museumCtx, museumConfig);
    new Chart(mediumCtx, mediumConfig);