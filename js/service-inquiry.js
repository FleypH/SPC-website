/**
 * Shield Point Capital — Service Inquiry Form (Email / WhatsApp)
 */

(function () {
  "use strict";

  const RECIPIENT_EMAIL = "info@fineramedia.co.zw";
  const WHATSAPP_NUMBER = "263780034146";

  const form = document.getElementById("serviceInquiryForm");
  const formStatus = document.getElementById("serviceInquiryStatus");
  const sendEmailBtn = document.getElementById("sendEmailBtn");
  const sendWhatsAppBtn = document.getElementById("sendWhatsAppBtn");

  if (!form || !sendEmailBtn || !sendWhatsAppBtn) return;

  const serviceTitle = form.dataset.serviceTitle || "Service Inquiry";
  const serviceDescription = form.dataset.serviceDescription || "";

  function setStatus(message, type) {
    if (!formStatus) return;
    formStatus.textContent = message;
    formStatus.className = "form-status" + (type ? " " + type : "");
  }

  function getFormValues() {
    return {
      fullName: form.fullName.value.trim(),
      email: form.email.value.trim(),
      phone: form.phone.value.trim(),
    };
  }

  function validateForm() {
    const values = getFormValues();
    setStatus("");

    if (!values.fullName || !values.email) {
      setStatus("Please fill in your full name and email address.", "error");
      return null;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(values.email)) {
      setStatus("Please enter a valid email address.", "error");
      return null;
    }

    return values;
  }

  function buildEmailSubject(values) {
    return (
      "Shield Point Capital — " + serviceTitle + " Inquiry from " + values.fullName
    );
  }

  function buildInquiryText(values) {
    return [
      "Shield Point Capital Service Inquiry",
      "",
      "Service: " + serviceTitle,
      "",
      "Service Details:",
      serviceDescription,
      "",
      "Full Name: " + values.fullName,
      "Email: " + values.email,
      "Phone: " + (values.phone || "Not provided"),
    ].join("\n");
  }

  sendEmailBtn.addEventListener("click", function () {
    const values = validateForm();
    if (!values) return;

    const subject = encodeURIComponent(buildEmailSubject(values));
    const body = encodeURIComponent(buildInquiryText(values));
    window.location.href =
      "mailto:" + RECIPIENT_EMAIL + "?subject=" + subject + "&body=" + body;

    setStatus(
      "Your email app is opening with the subject and message ready. Tap Send to complete your inquiry.",
      "success"
    );
  });

  sendWhatsAppBtn.addEventListener("click", function () {
    const values = validateForm();
    if (!values) return;

    const message = encodeURIComponent(buildInquiryText(values));
    window.open(
      "https://wa.me/" + WHATSAPP_NUMBER + "?text=" + message,
      "_blank"
    );

    setStatus(
      "WhatsApp is opening with your message ready. Tap Send to complete your inquiry.",
      "success"
    );
  });
})();
