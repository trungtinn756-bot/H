// --- 1. HÀM AJAX ĐỌC THÔNG BÁO DÀNH RIÊNG CHO ADMIN ---
function markAsRead(notiId, element, event) {
  if (event) event.preventDefault(); // Ngăn trình duyệt chuyển hướng ngay lập tức

  const targetUrl = element.getAttribute("href");
  const formData = new FormData();
  formData.append("id", notiId);

  fetch(`${window.projectRoot}/index.php?action=api-mark-single-read`, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) throw new Error("Lỗi mạng");
      return response.json();
    })
    .then(() => {
      window.location.href = targetUrl;
    })
    .catch((error) => {
      console.error("Lỗi đồng bộ thông báo Admin:", error);
      window.location.href = targetUrl;
    });
}

document.addEventListener("DOMContentLoaded", function () {
  // --- 2. XỬ LÝ SIDEBAR TRÊN MOBILE ---
  const sidebarToggle = document.getElementById("sidebar-toggle");
  const sidebarMenu = document.getElementById("sidebar-menu");
  const sidebarBackdrop = document.getElementById("sidebar-backdrop");

  const toggleSidebar = (isOpen) => {
    if (!sidebarMenu || !sidebarBackdrop) return;
    const shouldOpen =
      isOpen !== undefined
        ? isOpen
        : sidebarMenu.classList.contains("sidebar-closed");

    if (shouldOpen) {
      sidebarMenu.classList.remove("sidebar-closed");
      sidebarBackdrop.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    } else {
      sidebarMenu.classList.add("sidebar-closed");
      sidebarBackdrop.classList.add("hidden");
      document.body.style.overflow = "";
    }
  };

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", (event) => {
      event.stopPropagation();
      toggleSidebar();
    });
  }

  if (sidebarBackdrop) {
    sidebarBackdrop.addEventListener("click", () => {
      toggleSidebar(false);
    });
  }

  // --- 3. XỬ LÝ DROPDOWN USER MENU ---
  const userToggle = document.getElementById("user-menu-toggle");
  const userDropdown = document.getElementById("user-menu-dropdown");
  const userArrow = document.getElementById("user-menu-arrow");

  const toggleUserMenu = (isOpen) => {
    if (!userDropdown) return;
    const shouldOpen =
      isOpen !== undefined ? isOpen : userDropdown.classList.contains("hidden");
    if (shouldOpen) {
      userDropdown.classList.remove("hidden");
      if (userToggle) userToggle.setAttribute("aria-expanded", "true");
      if (userArrow) userArrow.style.transform = "rotate(180deg)";
    } else {
      userDropdown.classList.add("hidden");
      if (userToggle) userToggle.setAttribute("aria-expanded", "false");
      if (userArrow) userArrow.style.transform = "rotate(0deg)";
    }
  };

  if (userToggle && userDropdown) {
    userToggle.addEventListener("click", (event) => {
      event.stopPropagation();
      if (notiDropdown) notiDropdown.classList.add("hidden");
      toggleUserMenu();
    });
  }

  // --- 4. XỬ LÝ DROPDOWN THÔNG BÁO ADMIN ---
  const notiToggle = document.getElementById("noti-menu-toggle");
  const notiDropdown = document.getElementById("noti-menu-dropdown");

  const toggleNotiMenu = (isOpen) => {
    if (!notiDropdown) return;
    const shouldOpen =
      isOpen !== undefined ? isOpen : notiDropdown.classList.contains("hidden");
    if (shouldOpen) {
      notiDropdown.classList.remove("hidden");
      toggleUserMenu(false);
    } else {
      notiDropdown.classList.add("hidden");
    }
  };

  if (notiToggle && notiDropdown) {
    notiToggle.addEventListener("click", (event) => {
      event.stopPropagation();
      toggleNotiMenu();
    });
  }

  // --- 5. ĐÓNG MENU KHI CLICK NGOÀI HOẶC BẤM ESC ---
  document.addEventListener("click", (event) => {
    if (
      userDropdown &&
      !userDropdown.classList.contains("hidden") &&
      !userToggle.contains(event.target)
    ) {
      toggleUserMenu(false);
    }
    if (
      notiDropdown &&
      !notiDropdown.classList.contains("hidden") &&
      !notiToggle.contains(event.target) &&
      !notiDropdown.contains(event.target)
    ) {
      toggleNotiMenu(false);
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      toggleSidebar(false);
      toggleUserMenu(false);
      toggleNotiMenu(false);
    }
  });

  // --- 6. HỖ TRỢ CUỘN TRANG BẰNG PHÍM SPACE ---
  const mainContent = document.querySelector("main");
  if (mainContent) {
    window.addEventListener("keydown", function (event) {
      if (event.key === " " || event.keyCode === 32) {
        const activeEl = document.activeElement;
        if (
          activeEl &&
          (activeEl.tagName === "INPUT" ||
            activeEl.tagName === "TEXTAREA" ||
            activeEl.isContentEditable)
        ) {
          return;
        }
        event.preventDefault();
        const scrollAmount = mainContent.clientHeight * 0.8;

        if (event.shiftKey) {
          mainContent.scrollBy({
            top: -scrollAmount,
            behavior: "smooth",
          });
        } else {
          mainContent.scrollBy({
            top: scrollAmount,
            behavior: "smooth",
          });
        }
      }
    });
  }
});
