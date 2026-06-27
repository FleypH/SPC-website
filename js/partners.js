/**
 * Shield Point Capital — Partners Page Scripts
 */

(function () {
  "use strict";

  const partnershipForm = document.getElementById("partnershipForm");
  const formStatus = document.getElementById("partnershipFormStatus");

  if (!partnershipForm) return;

  partnershipForm.addEventListener("submit", async function (e) {
    e.preventDefault();
    formStatus.textContent = "";
    formStatus.className = "form-status";

    const formData = new FormData(partnershipForm);
    const services = formData.getAll("services").map(function (s) {
      return s.toString();
    });

    const payload = {
      orgName: formData.get("orgName")?.toString().trim() || "",
      orgType: formData.get("orgType")?.toString().trim() || "",
      businessSector: formData.get("businessSector")?.toString().trim() || "",
      country: formData.get("country")?.toString().trim() || "",
      website: formData.get("website")?.toString().trim() || "",
      contactName: formData.get("contactName")?.toString().trim() || "",
      jobTitle: formData.get("jobTitle")?.toString().trim() || "",
      contactEmail: formData.get("contactEmail")?.toString().trim() || "",
      contactPhone: formData.get("contactPhone")?.toString().trim() || "",
      partnershipInterest: formData.get("partnershipInterest")?.toString().trim() || "",
      services: services,
    };

    if (
      !payload.orgName ||
      !payload.orgType ||
      !payload.businessSector ||
      !payload.country ||
      !payload.contactName ||
      !payload.jobTitle ||
      !payload.contactEmail ||
      !payload.partnershipInterest
    ) {
      formStatus.textContent = "Please fill in all required fields.";
      formStatus.classList.add("error");
      return;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(payload.contactEmail)) {
      formStatus.textContent = "Please enter a valid email address.";
      formStatus.classList.add("error");
      return;
    }

    const submitBtn = partnershipForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = "Submitting…";

    try {
      const response = await fetch("api/partnership.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      const result = await response.json();

      if (response.ok && result.success) {
        formStatus.textContent = "Application submitted! Our team will contact you within 24 hours.";
        formStatus.classList.add("success");
        partnershipForm.reset();
      } else {
        formStatus.textContent = result.message || "Something went wrong. Please try again.";
        formStatus.classList.add("error");
      }
    } catch {
      formStatus.textContent = "Unable to submit application. Please check your connection and try again.";
      formStatus.classList.add("error");
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }
  });
})();
