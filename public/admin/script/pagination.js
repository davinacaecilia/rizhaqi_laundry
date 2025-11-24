const totalPages = 3;
  let currentPage = 1;

  function renderPagination() {
    const container = document.getElementById("pagination");
    container.innerHTML = "";

    // Tombol First
    if (currentPage > 1) {
      const firstBtn = createButton("« First", () => changePage(1));
      container.appendChild(firstBtn);
    }

    // Tombol Previous
    if (currentPage > 1) {
      const prevBtn = createButton("Previous", () => changePage(currentPage - 1));
      container.appendChild(prevBtn);
    }

    // Nomor halaman
    for (let i = 1; i <= totalPages; i++) {
      const pageBtn = createButton(i, () => changePage(i));
      if (i === currentPage) pageBtn.classList.add("active");
      container.appendChild(pageBtn);
    }

    // Tombol Next
    if (currentPage < totalPages) {
      const nextBtn = createButton("Next", () => changePage(currentPage + 1));
      container.appendChild(nextBtn);
    }

    // Tombol Last
    if (currentPage < totalPages) {
      const lastBtn = createButton("Last »", () => changePage(totalPages));
      container.appendChild(lastBtn);
    }
  }

  function createButton(text, onClick) {
    const btn = document.createElement("button");
    btn.textContent = text;
    btn.onclick = onClick;
    return btn;
  }

  function changePage(page) {
    currentPage = page;
    renderPagination();
    // Lo bisa panggil fungsi loadPageData(page) di sini kalau mau load data sesuai halaman
  }

  // Inisialisasi
  renderPagination();