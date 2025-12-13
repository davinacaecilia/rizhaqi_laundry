 document.addEventListener("DOMContentLoaded", function() {
            // UI Toggle Search (Tidak wajib)
            const toggleIcon = document.getElementById('tableSearchIcon');
            const searchField = document.getElementById('laporanSearch');
            if(toggleIcon && searchField) {
                toggleIcon.addEventListener('click', function() {
                    searchField.classList.toggle('show');
                    if(searchField.classList.contains('show')) searchField.focus();
                });
            }

            // Tab Switching (PENTING untuk Navigasi manual tanpa reload)
            window.openTab = function(evt, tabName) {
                var i, tabcontent, tablinks;
                tabcontent = document.getElementsByClassName("tab-content");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                    tabcontent[i].classList.remove("active");
                }
                tablinks = document.getElementsByClassName("tab-btn");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].classList.remove("active");
                    // Reset style manual
                    tablinks[i].style.color = "#666"; 
                    tablinks[i].style.borderBottomColor = "transparent";
                }
                document.getElementById(tabName).style.display = "block";
                document.getElementById(tabName).classList.add("active");
                evt.currentTarget.classList.add("active");
                // Set style aktif
                evt.currentTarget.style.color = "var(--accent-blue)";
                evt.currentTarget.style.borderBottomColor = "var(--accent-blue)";
                
                // Update print header saat tab berubah
                updatePrintHeader(tabName);
            };
            
            // ===== FUNGSI UNTUK UPDATE HEADER PRINT =====
            function updatePrintHeader(tabName) {
                const titleEl = document.getElementById('printReportTitle');
                const periodEl = document.getElementById('printReportPeriod');
                
                if (tabName === 'tab-harian') {
                    titleEl.textContent = 'LAPORAN KEUANGAN HARIAN';
                    const dateInput = document.getElementById('laporanDate');
                    if (dateInput && dateInput.value) {
                        const date = new Date(dateInput.value);
                        const options = { day: 'numeric', month: 'long', year: 'numeric' };
                        const formatted = date.toLocaleDateString('id-ID', options);
                        periodEl.textContent = 'Tanggal: ' + formatted;
                    }
                } else if (tabName === 'tab-bulanan') {
                    titleEl.textContent = 'REKAPITULASI KEUANGAN BULANAN';
                    // Ambil dari h3 yang ada
                    const h3Text = document.querySelector('#tab-bulanan h3');
                    if (h3Text) {
                        const periode = h3Text.textContent.replace('Rekapitulasi Pendapatan: ', '').replace(/\s+/g, ' ').trim();
                        // Hilangkan icon
                        const cleanPeriode = periode.replace(/[^\w\s]/gi, '').trim();
                        periodEl.textContent = 'Bulan: ' + cleanPeriode;
                    }
                }
            }
            
            // ===== SET TANGGAL DAN WAKTU PRINT =====
            function setPrintDateTime() {
                const now = new Date();
                const dateTimeOptions = { 
                    day: 'numeric', 
                    month: 'long', 
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                const dateOnlyOptions = { 
                    day: 'numeric', 
                    month: 'long', 
                    year: 'numeric'
                };
                
                const dateTime = now.toLocaleDateString('id-ID', dateTimeOptions);
                const dateOnly = now.toLocaleDateString('id-ID', dateOnlyOptions);
                
                const dateTimeEl = document.getElementById('printDateTime');
                const dateLocationEl = document.getElementById('printDateLocation');
                
                if (dateTimeEl) dateTimeEl.textContent = dateTime;
                if (dateLocationEl) dateLocationEl.textContent = dateOnly;
            }
            
            // Set initial print header
            const activeTabEl = document.querySelector('.tab-content.active');
            if (activeTabEl) {
                updatePrintHeader(activeTabEl.id);
            }
            
            // Update print date sebelum print
            window.onbeforeprint = function() {
                setPrintDateTime();
                // Update header sekali lagi sebelum print
                const currentActive = document.querySelector('.tab-content.active');
                if (currentActive) {
                    updatePrintHeader(currentActive.id);
                }
            };
            
            // Set initial date
            setPrintDateTime();
            
            // ===== FUNGSI EXPORT KE EXCEL =====
            window.exportToExcel = function(type) {
                let workbook = XLSX.utils.book_new();
                
                if (type === 'harian') {
                    // Export Laporan Harian
                    const tanggal = document.getElementById('laporanDate').value;
                    const dateObj = new Date(tanggal);
                    const formattedDate = dateObj.toLocaleDateString('id-ID', { 
                        day: 'numeric', 
                        month: 'long', 
                        year: 'numeric' 
                    });
                    
                    // Sheet 1: Laporan Cucian Masuk
                    const table1 = document.getElementById('tablePemasukan');
                    const ws1Data = [];
                    
                    // Header Info
                    ws1Data.push(['RIZHAQI LAUNDRY']);
                    ws1Data.push(['Laporan Cucian Masuk']);
                    ws1Data.push(['Tanggal: ' + formattedDate]);
                    ws1Data.push([]); // Empty row
                    
                    // Table Header
                    const headers1 = [];
                    table1.querySelectorAll('thead th').forEach(th => {
                        headers1.push(th.textContent.trim());
                    });
                    ws1Data.push(headers1);
                    
                    // Table Body
                    table1.querySelectorAll('tbody tr').forEach(tr => {
                        const row = [];
                        tr.querySelectorAll('td').forEach(td => {
                            let text = td.textContent.trim();
                            // Remove "Rp" and format number
                            text = text.replace('Rp ', '').replace(/\./g, '');
                            row.push(text);
                        });
                        if (row.length > 0 && !tr.querySelector('.no-data-msg')) {
                            ws1Data.push(row);
                        }
                    });
                    
                    // Table Footer (Total)
                    const totalRow1 = [];
                    table1.querySelectorAll('tfoot tr td').forEach(td => {
                        let text = td.textContent.trim();
                        text = text.replace('Rp ', '').replace(/\./g, '');
                        totalRow1.push(text);
                    });
                    ws1Data.push(totalRow1);
                    
                    const ws1 = XLSX.utils.aoa_to_sheet(ws1Data);
                    XLSX.utils.book_append_sheet(workbook, ws1, "Cucian Masuk");
                    
                    // Sheet 2: Arus Kas
                    const table2 = document.getElementById('tableArusKas');
                    const ws2Data = [];
                    
                    // Header Info
                    ws2Data.push(['RIZHAQI LAUNDRY']);
                    ws2Data.push(['Arus Kas (Cashflow)']);
                    ws2Data.push(['Tanggal: ' + formattedDate]);
                    ws2Data.push([]); // Empty row
                    
                    // Table Header
                    const headers2 = [];
                    table2.querySelectorAll('thead th').forEach(th => {
                        headers2.push(th.textContent.trim());
                    });
                    ws2Data.push(headers2);
                    
                    // Table Body
                    table2.querySelectorAll('tbody tr').forEach(tr => {
                        const row = [];
                        tr.querySelectorAll('td').forEach(td => {
                            let text = td.textContent.trim();
                            text = text.replace('Rp ', '').replace(/\./g, '');
                            row.push(text);
                        });
                        if (row.length > 0 && !tr.querySelector('.no-data-msg')) {
                            ws2Data.push(row);
                        }
                    });
                    
                    // Table Footer (Total)
                    const totalRow2 = [];
                    table2.querySelectorAll('tfoot tr td').forEach(td => {
                        let text = td.textContent.trim();
                        text = text.replace('Rp ', '').replace(/\./g, '');
                        totalRow2.push(text);
                    });
                    ws2Data.push(totalRow2);
                    
                    const ws2 = XLSX.utils.aoa_to_sheet(ws2Data);
                    XLSX.utils.book_append_sheet(workbook, ws2, "Arus Kas");
                    
                    // Download
                    XLSX.writeFile(workbook, 'Laporan_Harian_' + tanggal + '.xlsx');
                    
                } else if (type === 'bulanan') {
                    // Export Laporan Bulanan
                    const bulan = document.getElementById('filterBulan');
                    const tahun = document.getElementById('filterTahun');
                    const bulanText = bulan.options[bulan.selectedIndex].text;
                    const tahunText = tahun.value;
                    
                    const table = document.getElementById('tabelBulanan');
                    const wsData = [];
                    
                    // Header Info
                    wsData.push(['RIZHAQI LAUNDRY']);
                    wsData.push(['Rekapitulasi Keuangan Bulanan']);
                    wsData.push(['Periode: ' + bulanText + ' ' + tahunText]);
                    wsData.push([]); // Empty row
                    
                    // Table Header
                    const headers = [];
                    table.querySelectorAll('thead th').forEach(th => {
                        headers.push(th.textContent.trim());
                    });
                    wsData.push(headers);
                    
                    // Table Body
                    table.querySelectorAll('tbody tr').forEach(tr => {
                        const row = [];
                        tr.querySelectorAll('td').forEach(td => {
                            let text = td.textContent.trim();
                            // Remove "Rp" and dots
                            text = text.replace('Rp ', '').replace(/\./g, '');
                            row.push(text);
                        });
                        if (row.length > 0 && !tr.querySelector('.no-data-msg')) {
                            wsData.push(row);
                        }
                    });
                    
                    // Table Footer (Grand Total)
                    const totalRow = [];
                    table.querySelectorAll('tfoot tr td').forEach(td => {
                        let text = td.textContent.trim();
                        text = text.replace('Rp ', '').replace(/\./g, '');
                        totalRow.push(text);
                    });
                    wsData.push(totalRow);
                    
                    const ws = XLSX.utils.aoa_to_sheet(wsData);
                    XLSX.utils.book_append_sheet(workbook, ws, "Rekap Bulanan");
                    
                    // Download
                    XLSX.writeFile(workbook, 'Laporan_Bulanan_' + bulanText + '_' + tahunText + '.xlsx');
                }
            };
        });