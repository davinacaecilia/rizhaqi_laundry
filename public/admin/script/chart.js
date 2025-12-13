document.addEventListener('DOMContentLoaded', function() {
    
    // --- CHART 1: Total Berat Cucian Masuk (Bulanan) ---
    
    // Cek apakah data dari Laravel ada? Kalau tidak ada, pakai array kosong (fallback)
    const dataReal = (typeof chartDataBerat !== 'undefined') ? chartDataBerat : [0,0,0,0,0,0,0,0,0,0,0,0];

    const museumData = { 
        labels: [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des' 
        ],
        datasets: [{
            label: 'Total Berat (Kg)', 
            // GUNAKAN DATA REAL DI SINI
            data: dataReal, 
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

    // Render Chart
    const museumCanvas = document.getElementById('museumChart');
    if (museumCanvas) {
        const museumCtx = museumCanvas.getContext('2d');
        new Chart(museumCtx, museumConfig);
    }
});