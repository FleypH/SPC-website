/**
 * Shield Point Capital — Investors Page Scripts
 */

(function () {
  "use strict";

  const investorForm = document.getElementById("investorForm");
  const formStatus = document.getElementById("investorFormStatus");

  if (!investorForm) return;

  investorForm.addEventListener("submit", async function (e) {
    e.preventDefault();
    formStatus.textContent = "";
    formStatus.className = "form-status";

    const formData = new FormData(investorForm);
    const payload = {
      fullName: formData.get("fullName")?.toString().trim() || "",
      institution: formData.get("institution")?.toString().trim() || "",
      workEmail: formData.get("workEmail")?.toString().trim() || "",
      investmentRange: formData.get("investmentRange")?.toString().trim() || "",
      message: formData.get("message")?.toString().trim() || "",
    };

    if (!payload.fullName || !payload.institution || !payload.workEmail || !payload.investmentRange) {
      formStatus.textContent = "Please fill in all required fields.";
      formStatus.classList.add("error");
      return;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(payload.workEmail)) {
      formStatus.textContent = "Please enter a valid work email address.";
      formStatus.classList.add("error");
      return;
    }

    const submitBtn = investorForm.querySelector('button[type="submit"]');
    const originalHtml = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.textContent = "Sending…";

    try {
      const response = await fetch("api/investor-inquiry.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      const result = await response.json();

      if (response.ok && result.success) {
        formStatus.textContent = "Thank you! Your inquiry has been sent. We will be in touch shortly.";
        formStatus.classList.add("success");
        investorForm.reset();
      } else {
        formStatus.textContent = result.message || "Something went wrong. Please try again.";
        formStatus.classList.add("error");
      }
    } catch {
      formStatus.textContent = "Unable to send inquiry. Please check your connection and try again.";
      formStatus.classList.add("error");
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalHtml;
    }
  });
})();
