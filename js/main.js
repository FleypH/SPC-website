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

  /* Cookie consent banner */
  const COOKIE_CONSENT_KEY = "shieldpoint_cookie_consent";

  function createCookieBanner() {
    const banner = document.createElement("div");
    banner.className = "cookie-consent";
    banner.id = "cookieConsent";
    banner.setAttribute("role", "dialog");
    banner.setAttribute("aria-live", "polite");
    banner.setAttribute("aria-label", "Cookie consent");
    banner.hidden = true;

    banner.innerHTML =
      '<div class="cookie-consent-panel">' +
      '<div class="cookie-consent-icon" aria-hidden="true">' +
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">' +
      '<path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/>' +
      '<path d="M8.5 8.5v.01M16 9v.01M9 16v.01M15.5 14.5v.01"/>' +
      "</svg></div>" +
      '<div class="cookie-consent-body">' +
      '<p class="cookie-consent-label">Cookie Notice</p>' +
      "<p>We do not use cookies to store passwords, track personal browsing behavior for advertising, or share your information with third parties.</p>" +
      "<p>By continuing to use this website, you agree to our use of essential cookies that help provide a better browsing experience.</p>" +
      "</div>" +
      '<div class="cookie-consent-actions">' +
      '<button type="button" class="btn cookie-consent-accept">Accept</button>' +
      '<button type="button" class="btn cookie-consent-reject">Reject</button>' +
      "</div></div>";

    banner
      .querySelector(".cookie-consent-accept")
      .addEventListener("click", function () {
        hideCookieConsent("accepted");
      });

    banner
      .querySelector(".cookie-consent-reject")
      .addEventListener("click", function () {
        hideCookieConsent("rejected");
      });

    document.body.appendChild(banner);
    return banner;
  }

  function showCookieConsent() {
    let banner = document.getElementById("cookieConsent");
    if (!banner) {
      banner = createCookieBanner();
    }

    banner.hidden = false;

    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        banner.classList.add("cookie-consent--visible");
      });
    });
  }

  function hideCookieConsent(choice) {
    const banner = document.getElementById("cookieConsent");
    if (!banner) return;

    if (choice) {
      try {
        localStorage.setItem(COOKIE_CONSENT_KEY, choice);
      } catch (err) {
        /* ignore storage errors */
      }
    }

    banner.classList.remove("cookie-consent--visible");
    banner.addEventListener(
      "transitionend",
      function () {
        if (!banner.classList.contains("cookie-consent--visible")) {
          banner.hidden = true;
        }
      },
      { once: true },
    );
  }

  document.addEventListener(
    "click",
    function (e) {
      const cookieLink = e.target.closest('a[href="#cookies"]');
      if (!cookieLink) return;
      e.preventDefault();
      showCookieConsent();
    },
    true,
  );

  try {
    if (!localStorage.getItem(COOKIE_CONSENT_KEY)) {
      showCookieConsent();
    }
  } catch (err) {
    showCookieConsent();
  }

  /* Smooth scroll offset for fixed header */
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener("click", function (e) {
      const targetId = this.getAttribute("href");
      if (
        !targetId ||
        targetId === "#" ||
        targetId === "#login" ||
        targetId === "#blog" ||
        targetId === "#cookies"
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

  /* Home page — reveal header CTAs on mobile after hero scroll */
  const homeHero = document.getElementById("home");
  if (document.body.classList.contains("page-home") && homeHero) {
    const headerHeight =
      getComputedStyle(document.documentElement)
        .getPropertyValue("--header-height")
        .trim() || "80px";

    const heroObserver = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          document.body.classList.toggle("header-past-hero", !entry.isIntersecting);
        });
      },
      {
        threshold: 0,
        rootMargin: "-" + headerHeight + " 0px 0px 0px",
      },
    );

    heroObserver.observe(homeHero);
  }
})();
