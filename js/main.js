/**
 * Shield Point Capital — Main Page Scripts
 */

(function () {
  "use strict";

  const navToggle = document.getElementById("navToggle");
  const mainNav = document.getElementById("mainNav");
  const contactForm = document.getElementById("contactForm");
  const formStatus = document.getElementById("formStatus");

  /* Mobile navigation */
  if (navToggle && mainNav) {
    navToggle.addEventListener("click", function () {
      navToggle.classList.toggle("active");
      mainNav.classList.toggle("open");
    });

    mainNav.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        navToggle.classList.remove("active");
        mainNav.classList.remove("open");
      });
    });
  }

  /* Services dropdown */
  document.querySelectorAll(".nav-dropdown").forEach(function (dropdown) {
    const toggle = dropdown.querySelector(".nav-dropdown-toggle");
    if (!toggle) return;

    toggle.addEventListener("click", function (e) {
      e.stopPropagation();
      const isOpen = dropdown.classList.toggle("open");
      toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");

      document.querySelectorAll(".nav-dropdown.open").forEach(function (other) {
        if (other === dropdown) return;
        other.classList.remove("open");
        const otherToggle = other.querySelector(".nav-dropdown-toggle");
        if (otherToggle) {
          otherToggle.setAttribute("aria-expanded", "false");
        }
      });
    });

    dropdown.querySelectorAll(".nav-dropdown-menu a").forEach(function (link) {
      link.addEventListener("click", function () {
        dropdown.classList.remove("open");
        toggle.setAttribute("aria-expanded", "false");
      });
    });
  });

  document.addEventListener("click", function () {
    document.querySelectorAll(".nav-dropdown.open").forEach(function (dropdown) {
      dropdown.classList.remove("open");
      const toggle = dropdown.querySelector(".nav-dropdown-toggle");
      if (toggle) {
        toggle.setAttribute("aria-expanded", "false");
      }
    });
  });

  /* Smooth scroll offset for fixed header */
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener("click", function (e) {
      const targetId = this.getAttribute("href");
      if (
        !targetId ||
        targetId === "#" ||
        targetId === "#login" ||
        targetId === "#blog"
      ) {
        if (targetId === "#login" || targetId === "#blog") {
          e.preventDefault();
        }
        return;
      }

      const target = document.querySelector(targetId);
      if (!target) return;

      e.preventDefault();
      const headerHeight =
        parseInt(
          getComputedStyle(document.documentElement).getPropertyValue(
            "--header-height",
          ),
          10,
        ) || 80;
      const top =
        target.getBoundingClientRect().top + window.scrollY - headerHeight;
      window.scrollTo({ top: top, behavior: "smooth" });
    });
  });

  /* Contact form */
  if (contactForm) {
    contactForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      formStatus.textContent = "";
      formStatus.className = "form-status";

      const formData = new FormData(contactForm);
      const payload = {
        firstName: formData.get("firstName")?.toString().trim() || "",
        lastName: formData.get("lastName")?.toString().trim() || "",
        email: formData.get("email")?.toString().trim() || "",
        phone: formData.get("phone")?.toString().trim() || "",
        message: formData.get("message")?.toString().trim() || "",
      };

      if (
        !payload.firstName ||
        !payload.lastName ||
        !payload.email ||
        !payload.message
      ) {
        formStatus.textContent = "Please fill in all required fields.";
        formStatus.classList.add("error");
        return;
      }

      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(payload.email)) {
        formStatus.textContent = "Please enter a valid email address.";
        formStatus.classList.add("error");
        return;
      }

      const submitBtn = contactForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = "Sending…";

      try {
        const response = await fetch("api/contact.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload),
        });

        const result = await response.json();

        if (response.ok && result.success) {
          formStatus.textContent =
            "Thank you! Your message has been sent successfully.";
          formStatus.classList.add("success");
          contactForm.reset();
        } else {
          formStatus.textContent =
            result.message || "Something went wrong. Please try again.";
          formStatus.classList.add("error");
        }
      } catch {
        formStatus.textContent =
          "Unable to send message. Please check your connection and try again.";
        formStatus.classList.add("error");
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      }
    });
  }
})();
