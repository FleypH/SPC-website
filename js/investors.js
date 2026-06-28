/**
 * Shield Point Capital — Investors Page Scripts
 */

(function () {
  "use strict";

  const RECIPIENT_EMAIL = "investors@shieldpointcapital.co.zw";
  const WHATSAPP_NUMBER = "263776492182";

  const investorForm = document.getElementById("investorForm");
  const formStatus = document.getElementById("investorFormStatus");
  const contactForm = document.getElementById("investorContactForm");
  const contactFormStatus = document.getElementById("investorContactFormStatus");

  function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  if (investorForm && formStatus) {
    function getInvestorFormValues() {
      return {
        fullName: investorForm.fullName?.value.trim() || "",
        institution: investorForm.institution?.value.trim() || "",
        workEmail: investorForm.workEmail?.value.trim() || "",
        investmentRange: investorForm.investmentRange?.value.trim() || "",
        message: investorForm.message?.value.trim() || "",
      };
    }

    function validateInvestorForm(values) {
      formStatus.textContent = "";
      formStatus.className = "form-status";

      if (
        !values.fullName ||
        !values.institution ||
        !values.workEmail ||
        !values.investmentRange
      ) {
        formStatus.textContent = "Please fill in all required fields.";
        formStatus.classList.add("error");
        return false;
      }

      if (!validateEmail(values.workEmail)) {
        formStatus.textContent = "Please enter a valid work email address.";
        formStatus.classList.add("error");
        return false;
      }

      return true;
    }

    investorForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const values = getInvestorFormValues();
      if (!validateInvestorForm(values)) return;

      const subject = encodeURIComponent(
        "Shield Point Capital — Investor Inquiry from " + values.fullName
      );
      const body = encodeURIComponent(
        [
          "Shield Point Capital — Investor Inquiry",
          "",
          "Full Name: " + values.fullName,
          "Institution: " + values.institution,
          "Work Email: " + values.workEmail,
          "Investment Range: " + values.investmentRange,
          "",
          "Message:",
          values.message || "Not provided",
        ].join("\n")
      );

      window.location.href =
        "mailto:" + RECIPIENT_EMAIL + "?subject=" + subject + "&body=" + body;

      formStatus.textContent =
        "Your email app is opening with the subject and message ready. Tap Send to complete your inquiry.";
      formStatus.classList.add("success");
    });
  }

  if (contactForm && contactFormStatus) {
    function getContactValues() {
      return {
        fullName: contactForm.fullName?.value.trim() || "",
        email: contactForm.email?.value.trim() || "",
        phone: contactForm.phone?.value.trim() || "",
        message: contactForm.message?.value.trim() || "",
      };
    }

    function validateContactForm(values) {
      contactFormStatus.textContent = "";
      contactFormStatus.className = "form-status";

      if (!values.fullName || !values.email || !values.message) {
        contactFormStatus.textContent = "Please fill in all required fields.";
        contactFormStatus.classList.add("error");
        return false;
      }

      if (!validateEmail(values.email)) {
        contactFormStatus.textContent = "Please enter a valid email address.";
        contactFormStatus.classList.add("error");
        return false;
      }

      return true;
    }

    function buildContactText(values) {
      return [
        "Shield Point Capital — Investor Contact Inquiry",
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
      const values = getContactValues();
      if (!validateContactForm(values)) return;

      const subject = encodeURIComponent(
        "Shield Point Capital — Inquiry from " + values.fullName
      );
      const body = encodeURIComponent(buildContactText(values));

      window.location.href =
        "mailto:" + RECIPIENT_EMAIL + "?subject=" + subject + "&body=" + body;

      contactFormStatus.textContent =
        "Your email app is opening with the subject and message ready. Tap Send to complete your inquiry.";
      contactFormStatus.classList.add("success");
    });

    const whatsappBtn = contactForm.querySelector(".investor-contact-whatsapp");
    if (whatsappBtn) {
      whatsappBtn.addEventListener("click", function (e) {
        e.preventDefault();
        const values = getContactValues();
        if (!validateContactForm(values)) return;

        const text = encodeURIComponent(buildContactText(values));
        window.open(
          "https://wa.me/" + WHATSAPP_NUMBER + "?text=" + text,
          "_blank"
        );

        contactFormStatus.textContent =
          "WhatsApp is opening with your message ready. Tap Send to complete your inquiry.";
        contactFormStatus.classList.add("success");
      });
    }
  }
})();
