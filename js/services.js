/**
 * Shield Point Capital — Services Page Contact Form
 */

(function () {
  "use strict";

  const RECIPIENT_EMAIL = "info@fineramedia.co.zw";
  const WHATSAPP_NUMBER = "263780034146";

  const messageField = document.getElementById("message");

  function buildOrderMessage(card, btn) {
    const serviceTitle =
      document.querySelector(".service-pricing-title")?.textContent.trim() ||
      btn.dataset.service ||
      "Web Development";
    const plan =
      card.querySelector(".pricing-plan")?.textContent.trim() ||
      btn.dataset.plan ||
      "Selected Package";
    const amount = card.querySelector(".pricing-amount")?.textContent.trim() || "";
    const term = card.querySelector(".pricing-term")?.textContent.trim() || "";
    const features = Array.from(card.querySelectorAll(".pricing-features li"))
      .map(function (item) {
        return "• " + item.textContent.trim();
      })
      .join("\n");

    const lines = [
      "I would like to order the following package:",
      "",
      "Service: " + serviceTitle,
      "Plan: " + plan,
    ];

    if (amount) {
      lines.push("Price: " + amount + (term ? " " + term : ""));
    }

    if (features) {
      lines.push("", "Package includes:", features);
    }

    return lines.join("\n");
  }

  document.querySelectorAll(".pricing-order-btn[data-plan]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      const card = btn.closest(".pricing-card");
      if (messageField && card) {
        messageField.value = buildOrderMessage(card, btn);
      }

      requestAnimationFrame(function () {
        const firstName = document.getElementById("firstName");
        if (firstName) firstName.focus();
      });
    });
  });

  const form = document.getElementById("servicesContactForm");
  const formStatus = document.getElementById("servicesFormStatus");

  if (!form) return;

  function getFormValues() {
    return {
      fullName: form.firstName?.value.trim() || "",
      email: form.email?.value.trim() || "",
      phone: form.phone?.value.trim() || "",
      message: form.message?.value.trim() || "",
    };
  }

  function validateForm(values) {
    formStatus.textContent = "";
    formStatus.className = "form-status";

    if (!values.fullName || !values.email || !values.message) {
      formStatus.textContent = "Please fill in all required fields.";
      formStatus.classList.add("error");
      return false;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(values.email)) {
      formStatus.textContent = "Please enter a valid email address.";
      formStatus.classList.add("error");
      return false;
    }

    return true;
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

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const values = getFormValues();
    if (!validateForm(values)) return;

    const subject = encodeURIComponent(buildEmailSubject(values));
    const body = encodeURIComponent(buildInquiryText(values));
    window.location.href =
      "mailto:" + RECIPIENT_EMAIL + "?subject=" + subject + "&body=" + body;

    formStatus.textContent =
      "Your email app is opening with the subject and message ready. Tap Send to complete your inquiry.";
    formStatus.classList.add("success");
  });

  const whatsappBtn = form.querySelector('a[href*="wa.me"]');
  if (whatsappBtn) {
    whatsappBtn.addEventListener("click", function (e) {
      e.preventDefault();
      const values = getFormValues();
      if (!validateForm(values)) return;

      const text = encodeURIComponent(buildInquiryText(values));
      window.open(
        "https://wa.me/" + WHATSAPP_NUMBER + "?text=" + text,
        "_blank"
      );

      formStatus.textContent =
        "WhatsApp is opening with your message ready. Tap Send to complete your inquiry.";
      formStatus.classList.add("success");
    });
  }
})();
