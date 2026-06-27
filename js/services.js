/**
 * Shield Point Capital — Services Page Contact Form
 */

(function () {
  "use strict";

  const form = document.getElementById("servicesContactForm");
  const formStatus = document.getElementById("servicesFormStatus");

  if (!form) return;

  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    formStatus.textContent = "";
    formStatus.className = "form-status";

    const formData = new FormData(form);
    const payload = {
      fullName: formData.get("fullName")?.toString().trim() || "",
      email: formData.get("email")?.toString().trim() || "",
      phone: formData.get("phone")?.toString().trim() || "",
      message: formData.get("message")?.toString().trim() || "",
      source: "Services Page",
    };

    if (!payload.fullName || !payload.email || !payload.message) {
      formStatus.textContent = "Please fill in all required fields.";
      formStatus.classList.add("error");
      return;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(payload.email)) {
      formStatus.textContent = "Please enter a valid email address.";
      formStatus.classList.add("error");
      return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
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
        formStatus.textContent = "Thank you! Your inquiry has been sent successfully.";
        formStatus.classList.add("success");
        form.reset();
      } else {
        formStatus.textContent = result.message || "Something went wrong. Please try again.";
        formStatus.classList.add("error");
      }
    } catch {
      formStatus.textContent = "Unable to send inquiry. Please check your connection and try again.";
      formStatus.classList.add("error");
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }
  });
})();
