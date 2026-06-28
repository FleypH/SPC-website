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
  const RECIPIENT_EMAIL = "business@shieldpointcapital.co.zw";
  const WHATSAPP_NUMBER = "263776492182";

  if (contactForm) {
    function getContactValues() {
      return {
        fullName: contactForm.firstName?.value.trim() || "",
        email: contactForm.email?.value.trim() || "",
        phone: contactForm.phone?.value.trim() || "",
        message: contactForm.message?.value.trim() || "",
      };
    }

    function validateContactForm() {
      if (!formStatus) return null;
      formStatus.textContent = "";
      formStatus.className = "form-status";

      const values = getContactValues();

      if (!values.fullName || !values.email || !values.message) {
        formStatus.textContent = "Please fill in all required fields.";
        formStatus.classList.add("error");
        return null;
      }

      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(values.email)) {
        formStatus.textContent = "Please enter a valid email address.";
        formStatus.classList.add("error");
        return null;
      }

      return values;
    }

    function buildEmailSubject(values) {
      return "Shield Point Capital — Inquiry from " + values.fullName;
    }

    function buildInquiryText(values) {
      return [
        "Shield Point Capital Inquiry",
        "",
        "Full Name: " + values.fullName,
        "Email: " + values.email,
        "Phone: " + (values.phone || "Not provided"),
        "",
        "Message:",
        values.message,
      ].join("\n");
    }

    contactForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const values = validateContactForm();
      if (!values) return;

      const subject = encodeURIComponent(buildEmailSubject(values));
      const body = encodeURIComponent(buildInquiryText(values));
      window.location.href =
        "mailto:" + RECIPIENT_EMAIL + "?subject=" + subject + "&body=" + body;

      if (formStatus) {
        formStatus.textContent =
          "Your email app is opening with the subject and message ready. Tap Send to complete your inquiry.";
        formStatus.classList.add("success");
      }
    });

    const whatsappBtn = contactForm.querySelector('a[href*="wa.me"]');
    if (whatsappBtn) {
      whatsappBtn.addEventListener("click", function (e) {
        e.preventDefault();
        const values = validateContactForm();
        if (!values) return;

        const text = encodeURIComponent(buildInquiryText(values));
        window.open(
          "https://wa.me/" + WHATSAPP_NUMBER + "?text=" + text,
          "_blank"
        );

        if (formStatus) {
          formStatus.textContent =
            "WhatsApp is opening with your message ready. Tap Send to complete your inquiry.";
          formStatus.classList.add("success");
        }
      });
    }
  }
})();
