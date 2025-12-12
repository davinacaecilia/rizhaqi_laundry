document.addEventListener('DOMContentLoaded', function() {
    
    // --- CHART 1: Total Berat Cucian Masuk (Bulanan) ---
    // (Chart Kiri - Masih Dipakai)
    
    const museumData = { 
        labels: [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des' 
        ],
        datasets: [{
            label: 'Total Berat (Kg)', 
            data: [1500, 1650, 1400, 1800, 2000, 1950, 2100, 2300, 2200, 2500, 2600, 2750], 
            backgroundColor: [
                '#4ECDC4', '#45B7D1', '#96CEB4', '#FF6B6B', '#FFEAA7', '#DDA0DD',
                '#98D8C8', '#F8B500', '#4ECDC4', '#45B7D1', '#96CEB4', '#FF6B6B'
            ],
            borderWidth: 0,
            borderRadius: 8
        }]
    };

    const museumConfig = {
        type: 'bar',
        data: museumData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f0f0f0' },
                    ticks: { color: '#666' },
                    title: { display: true, text: 'Berat Cucian (Kg)' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#666', maxRotation: 45 },
                    title: { display: true, text: 'Bulan' }
                }
            }
        }
    };

    // Render Chart Kiri (Museum Chart)
    const museumCanvas = document.getElementById('museumChart');
    if (museumCanvas) {
        const museumCtx = museumCanvas.getContext('2d');
        new Chart(museumCtx, museumConfig);
    }


    // --- CHART 2: Distribusi Jenis Layanan (Doughnut Chart) ---
    // (Chart Kanan - SUDAH DIHAPUS DARI HTML, JADI JS-NYA DIKOMENTARI BIAR GAK ERROR)
    
    /* const mediumData = {
        labels: ['Cuci Setrika', 'Cuci Kering Saja', 'Setrika Saja', 'Bed Cover', 'Pakaian Bayi', 'Lainnya'],
        datasets: [{
            label: 'Persentase Layanan',
            data: [55, 15, 10, 15, 3, 2],
            backgroundColor: ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD'],
            borderColor: '#fff',
            borderWidth: 2
        }]
    };

    const mediumConfig = {
        type: 'doughnut',
        data: mediumData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { usePointStyle: true, padding: 20, color: '#666' } },
                tooltip: { 
                    callbacks: {
                        label: function(context) {
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

    // Render Chart Kanan (Medium Chart) - DIBATALKAN KARENA SUDAH JADI TABEL LOG
    const mediumCanvas = document.getElementById('mediumChart');
    if (mediumCanvas) {
        const mediumCtx = mediumCanvas.getContext('2d');
        new Chart(mediumCtx, mediumConfig);
    }
    */

});