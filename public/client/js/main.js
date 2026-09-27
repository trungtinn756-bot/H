document.addEventListener("DOMContentLoaded", function () {
  // --- 1. KHAI BÁO BIẾN UI ---
  const toggleButton = document.getElementById("user-menu-toggle");
  const dropdownMenu = document.getElementById("user-menu-dropdown");
  const menuArrow = document.getElementById("user-menu-arrow");

  const notiBtn = document.getElementById("noti-btn");
  const notiDropdown = document.getElementById("noti-dropdown");
  const notiBadge = document.getElementById("noti-badge");
  const btnMarkAll = document.getElementById("page-mark-all");

  const menuToggle = document.getElementById("menu-toggle");
  const mainNav = document.getElementById("main-nav");
  const mobileSearchToggle = document.getElementById("mobile-search-toggle");
  const mobileSearchBar = document.getElementById("mobile-search-bar");

  const genreBtn = document.getElementById("genre-dropdown-btn");
  const genreBox = document.getElementById("genre-dropdown-box");
  const genreArrow = genreBtn ? genreBtn.querySelector(".id-arrow") : null;

  const catalogBtn = document.getElementById("catalog-dropdown-btn");
  const catalogBox = document.getElementById("catalog-dropdown-box");
  const catalogArrow = catalogBtn
    ? catalogBtn.querySelector(".catalog-arrow")
    : null;

  const backToTopBtn = document.getElementById("back-to-top");

  // --- 2. USER MENU DROPDOWN ---
  const toggleMenu = (isOpen) => {
    if (!dropdownMenu) return;
    const shouldOpen =
      isOpen !== undefined ? isOpen : dropdownMenu.classList.contains("hidden");
    if (shouldOpen) {
      dropdownMenu.classList.remove("hidden");
      if (toggleButton) toggleButton.setAttribute("aria-expanded", "true");
      if (menuArrow) menuArrow.classList.add("rotate-180");
    } else {
      dropdownMenu.classList.add("hidden");
      if (toggleButton) toggleButton.setAttribute("aria-expanded", "false");
      if (menuArrow) menuArrow.classList.remove("rotate-180");
    }
  };

  if (toggleButton && dropdownMenu) {
    toggleButton.addEventListener("click", (event) => {
      event.stopPropagation();
      toggleMenu();
      if (notiDropdown) notiDropdown.classList.add("hidden");
    });
  }

  // --- 3. HỆ THỐNG THÔNG BÁO AJAX ---
  if (notiBtn && notiDropdown) {
    const notiContainer = document.getElementById("noti-list-container");
    const btnLoadMore = document.getElementById("btn-load-more-noti");
    let currentOffset = 10;
    let globalUnreadCount = window.totalUnreadNotis || 0;

    function applyNotificationStyles() {
      if (notiBadge) {
        if (globalUnreadCount <= 0) {
          notiBadge.classList.add("hidden");
          notiBadge.innerText = "0";
        } else {
          notiBadge.classList.remove("hidden");
          notiBadge.innerText =
            globalUnreadCount > 999 ? "999+" : globalUnreadCount;
        }
      }
    }
    applyNotificationStyles();

    notiBtn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      toggleMenu(false);
      notiDropdown.classList.toggle("hidden");
    });

    notiDropdown.addEventListener("click", function (e) {
      const targetLink = e.target.closest("a[data-noti-id]");
      if (targetLink) {
        e.preventDefault();
        const notiId = targetLink.getAttribute("data-noti-id");
        const targetUrl = targetLink.getAttribute("href");
        const formData = new FormData();
        formData.append("id", notiId);

        fetch(`${window.projectRoot}/index.php?action=api-mark-single-read`, {
          method: "POST",
          body: formData,
        })
          .then((response) => {
            if (!response.ok) throw new Error("Mạng lỗi");
            return response.json();
          })
          .then(() => {
            window.location.href = targetUrl;
          })
          .catch((err) => {
            console.error("Lỗi cập nhật trạng thái đọc:", err);
            window.location.href = targetUrl;
          });
      }
    });

    if (btnLoadMore && notiContainer) {
      btnLoadMore.addEventListener("click", function (e) {
        e.stopPropagation();
        btnLoadMore.innerText = "Đang tải...";
        btnLoadMore.disabled = true;

        fetch(
          `${window.projectRoot}/index.php?action=api-get-notifications&offset=${currentOffset}`,
        )
          .then((response) => {
            if (!response.ok) throw new Error("Kết nối server thất bại");
            return response.json();
          })
          .then((data) => {
            if (!data || data.length === 0) {
              btnLoadMore.innerText = "Hết thông báo";
              btnLoadMore.style.opacity = "0.5";
              btnLoadMore.disabled = true;
              return;
            }

            data.forEach((noti) => {
              const isReadClass = noti.is_read == 1 ? "" : "unread";
              const iconClass =
                noti.type === "new_chapter" ? "chapter" : "system";
              const iconInner =
                noti.type === "new_chapter"
                  ? '<i class="fa-solid fa-bolt"></i>'
                  : '<i class="fa-solid fa-circle-info"></i>';

              const notiHtml = `
                                <a href="${window.projectRoot}/${noti.link || "#"}" data-noti-id="${noti.id}" class="noti-item ${isReadClass}">
                                    <div class="noti-item-flex">
                                        <div class="noti-icon ${iconClass}">${iconInner}</div>
                                        <div class="noti-text-box">
                                            <h4 class="noti-text-title">${noti.title}</h4>
                                            <p class="noti-text-desc">${noti.content}</p>
                                            <span class="noti-text-time">${noti.formatted_time}</span>
                                        </div>
                                    </div>
                                </a>`;
              notiContainer.insertAdjacentHTML("beforeend", notiHtml);
            });

            currentOffset += data.length;
            btnLoadMore.innerHTML =
              'Xem thêm <i class="fa-solid fa-angles-down text-[9px] ml-0.5"></i>';
            btnLoadMore.disabled = false;
            applyNotificationStyles();
          })
          .catch((err) => {
            console.error("Lỗi tải thêm thông báo:", err);
            btnLoadMore.innerHTML =
              'Thử lại <i class="fa-solid fa-rotate-right text-[9px] ml-0.5"></i>';
            btnLoadMore.disabled = false;
          });
      });
    }

    if (btnMarkAll) {
      btnMarkAll.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        globalUnreadCount = 0;
        if (notiBadge) notiBadge.classList.add("hidden");
        if (notiContainer) {
          notiContainer.innerHTML = `
                        <div style="padding: 2rem 0; text-align: center; color: #6b7280; user-select: none;">
                            <i class="fa-regular fa-bell-slashed" style="font-size: 1.25rem; display: block; color: #4b5563; margin-bottom: 0.5rem;"></i>
                            <p style="font-size: 0.75rem; font-style: italic;">Không có thông báo chưa đọc nào.</p>
                        </div>`;
        }
        fetch(
          `${window.projectRoot}/index.php?action=mark-all-notifications-read`,
          {
            method: "POST",
          },
        ).catch((err) => console.error("Lỗi đồng bộ:", err));
      });
    }
  }

  // --- 4. MOBILE NAVIGATION TOGGLE ---
  if (menuToggle && mainNav) {
    menuToggle.addEventListener("click", function (e) {
      e.stopPropagation();
      mainNav.classList.toggle("hidden-mobile");
      if (mobileSearchBar) mobileSearchBar.classList.add("hidden");
    });
  }

  if (mobileSearchToggle && mobileSearchBar) {
    mobileSearchToggle.addEventListener("click", function (e) {
      e.stopPropagation();
      mobileSearchBar.classList.toggle("hidden");
      if (!mobileSearchBar.classList.contains("hidden") && mainNav) {
        mainNav.classList.add("hidden-mobile");
      }
    });
  }

  // --- 5. DROPDOWN DANH MỤC TRÊN MOBILE ---
  if (genreBtn && genreBox) {
    genreBtn.addEventListener("click", function (e) {
      if (window.innerWidth < 768) {
        e.preventDefault();
        e.stopPropagation();
        genreBox.classList.toggle("hidden");
        if (genreArrow) genreArrow.classList.toggle("rotate-180");
      }
    });
  }

  if (catalogBtn && catalogBox) {
    catalogBtn.addEventListener("click", function (e) {
      if (window.innerWidth < 768) {
        e.preventDefault();
        e.stopPropagation();
        catalogBox.classList.toggle("hidden");
        if (catalogArrow) catalogArrow.classList.toggle("rotate-180");
      }
    });
  }

  // --- 6. CLOSE DROPDOWNS ON OUTSIDE CLICK / ESC ---
  document.addEventListener("click", (event) => {
    if (
      dropdownMenu &&
      !dropdownMenu.classList.contains("hidden") &&
      !toggleButton.contains(event.target)
    ) {
      toggleMenu(false);
    }
    if (
      notiDropdown &&
      !notiDropdown.contains(event.target) &&
      !notiBtn.contains(event.target)
    ) {
      notiDropdown.classList.add("hidden");
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      toggleMenu(false);
      if (notiDropdown) notiDropdown.classList.add("hidden");
    }
  });

  // --- 7. BACK TO TOP SCROLL ---
  if (backToTopBtn) {
    backToTopBtn.style.opacity = "0";
    backToTopBtn.style.pointerEvents = "none";
    window.addEventListener("scroll", function () {
      if (window.scrollY > 300) {
        backToTopBtn.style.opacity = "1";
        backToTopBtn.style.pointerEvents = "auto";
      } else {
        backToTopBtn.style.opacity = "0";
        backToTopBtn.style.pointerEvents = "none";
      }
    });
  }

  // --- 8. INITIALIZE LIVE SEARCH ---
  initLiveSearch(
    document.getElementById("search-desktop"),
    document.getElementById("search-results-desktop"),
  );
  initLiveSearch(
    document.getElementById("search-mobile"),
    document.getElementById("search-results-mobile"),
  );
});

// --- 9. LIVE SEARCH FUNCTION ---
function initLiveSearch(inputField, resultsContainer) {
  if (!inputField || !resultsContainer) return;
  let debounceTimeout;

  inputField.addEventListener("input", function () {
    const keyword = this.value.trim();
    clearTimeout(debounceTimeout);
    if (keyword.length < 2) {
      resultsContainer.innerHTML = "";
      resultsContainer.classList.add("hidden");
      return;
    }

    debounceTimeout = setTimeout(() => {
      fetch(
        `${window.projectRoot}/index.php?action=api-search-suggestions&q=${encodeURIComponent(keyword)}`,
      )
        .then((response) => response.json())
        .then((data) => {
          if (data.length === 0) {
            resultsContainer.innerHTML = `<div style="padding: 1rem; font-size: 0.75rem; color: #6b7280; font-style: italic; text-align: center; user-select: none;">Không tìm thấy truyện phù hợp...</div>`;
            resultsContainer.classList.remove("hidden");
            return;
          }
          let htmlContent = "";
          data.forEach((comic) => {
            const subTitleHtml = comic.other_title
              ? `<p class="search-subtitle">${comic.other_title}</p>`
              : "";
            htmlContent += `
                            <a href="${comic.link}" class="search-item">
                                <img src="${comic.thumbnail}" alt="${comic.title}" class="search-thumb" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=50&h=70&q=80';">
                                <div class="search-info">
                                    <h4 class="search-title">${comic.title}</h4>
                                    ${subTitleHtml}
                                </div>
                            </a>`;
          });
          resultsContainer.innerHTML = htmlContent;
          resultsContainer.classList.remove("hidden");
        })
        .catch((err) => console.error("Lỗi gợi ý tìm kiếm:", err));
    }, 300);
  });

  document.addEventListener("click", function (e) {
    if (
      !inputField.contains(e.target) &&
      !resultsContainer.contains(e.target)
    ) {
      resultsContainer.classList.add("hidden");
    }
  });
}
