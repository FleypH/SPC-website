/**
 * Shield Point Capital — Partners Page Scripts
 */

(function () {
  "use strict";

  const RECIPIENT_EMAIL = "partners@shieldpointcapital.co.zw";
  const WHATSAPP_NUMBER = "263776492182";

  const partnershipForm = document.getElementById("partnershipForm");
  const formStatus = document.getElementById("partnershipFormStatus");
  const contactForm = document.getElementById("partnerContactForm");
  const contactFormStatus = document.getElementById("partnerContactFormStatus");

  function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  if (partnershipForm && formStatus) {
    function getPartnershipValues() {
      const services = Array.from(
        partnershipForm.querySelectorAll('input[name="services"]:checked')
      ).map(function (input) {
        return input.value;
      });

      return {
        orgName: partnershipForm.orgName?.value.trim() || "",
        orgType: partnershipForm.orgType?.value.trim() || "",
        businessSector: partnershipForm.businessSector?.value.trim() || "",
        country: partnershipForm.country?.value.trim() || "",
        website: partnershipForm.website?.value.trim() || "",
        contactName: partnershipForm.contactName?.value.trim() || "",
        jobTitle: partnershipForm.jobTitle?.value.trim() || "",
        contactEmail: partnershipForm.contactEmail?.value.trim() || "",
        contactPhone: partnershipForm.contactPhone?.value.trim() || "",
        partnershipInterest:
          partnershipForm.partnershipInterest?.value.trim() || "",
        services: services,
      };
    }

    function validatePartnershipForm(values) {
      formStatus.textContent = "";
      formStatus.className = "form-status";

      if (
        !values.orgName ||
        !values.orgType ||
        !values.businessSector ||
        !values.country ||
        !values.contactName ||
        !values.jobTitle ||
        !values.contactEmail ||
        !values.partnershipInterest
      ) {
        formStatus.textContent = "Please fill in all required fields.";
        formStatus.classList.add("error");
        return false;
      }

      if (!validateEmail(values.contactEmail)) {
        formStatus.textContent = "Please enter a valid email address.";
        formStatus.classList.add("error");
        return false;
      }

      return true;
    }

    partnershipForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const values = getPartnershipValues();
      if (!validatePartnershipForm(values)) return;

      const servicesList =
        values.services.length > 0 ? values.services.join(", ") : "None selected";

      const subject = encodeURIComponent(
        "Shield Point Capital — Partnership Application from " + values.orgName
      );
      const body = encodeURIComponent(
        [
          "Shield Point Capital — Partnership Application",
          "",
          "Organization Details",
          "Organization: " + values.orgName,
          "Type: " + values.orgType,
          "Business Sector: " + values.businessSector,
          "Country: " + values.country,
          "Website: " + (values.website || "Not provided"),
          "",
          "Primary Contact",
          "Name: " + values.contactName,
          "Job Title: " + values.jobTitle,
          "Email: " + values.contactEmail,
          "Phone: " + (values.contactPhone || "Not provided"),
          "",
          "Partnership Details",
          values.partnershipInterest,
          "",
          "Services of Interest: " + servicesList,
        ].join("\n")
      );

      window.location.href =
        "mailto:" + RECIPIENT_EMAIL + "?subject=" + subject + "&body=" + body;

      formStatus.textContent =
        "Your email app is opening with the subject and message ready. Tap Send to complete your application.";
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
        "Shield Point Capital — Partner Contact Inquiry",
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

    const whatsappBtn = contactForm.querySelector(".partner-contact-whatsapp");
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
