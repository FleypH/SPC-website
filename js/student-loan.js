/**
 * Shield Point Capital — Student Loan Application Form
 */

(function () {
  "use strict";

  const RECIPIENT_EMAIL = "business@shieldpointcapital.co.zw";
  const WHATSAPP_NUMBER = "263776492182";
  const MAX_FILE_SIZE = 5 * 1024 * 1024;

  const form = document.getElementById("studentLoanForm");
  const formStatus = document.getElementById("loanFormStatus");
  const sendEmailBtn = document.getElementById("sendEmailBtn");
  const sendWhatsAppBtn = document.getElementById("sendWhatsAppBtn");

  if (!form || !sendEmailBtn || !sendWhatsAppBtn) return;

  function setStatus(message, type) {
    formStatus.textContent = message;
    formStatus.className = "form-status" + (type ? " " + type : "");
  }

  function getFormValues() {
    return {
      firstName: form.firstName.value.trim(),
      lastName: form.lastName.value.trim(),
      email: form.email.value.trim(),
      phone: form.phone.value.trim(),
      idNumber: form.idNumber.value.trim(),
      loanAmount: form.loanAmount.value.trim(),
      idPhoto: form.idPhoto.files[0] || null,
      facePhoto: form.facePhoto.files[0] || null,
    };
  }

  function validateForm(requireFiles) {
    const values = getFormValues();

    setStatus("");

    if (
      !values.firstName ||
      !values.lastName ||
      !values.email ||
      !values.idNumber ||
      !values.loanAmount
    ) {
      setStatus("Please fill in all required fields.", "error");
      return null;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(values.email)) {
      setStatus("Please enter a valid email address.", "error");
      return null;
    }

    if (Number(values.loanAmount) <= 0) {
      setStatus("Please enter a valid loan amount.", "error");
      return null;
    }

    if (requireFiles) {
      if (!values.idPhoto || !values.facePhoto) {
        setStatus("Please upload both your ID and face photos.", "error");
        return null;
      }

      if (
        values.idPhoto.size > MAX_FILE_SIZE ||
        values.facePhoto.size > MAX_FILE_SIZE
      ) {
        setStatus("Each file must be 5MB or smaller.", "error");
        return null;
      }
    }

    return values;
  }

  function buildEmailSubject(values) {
    return (
      "Student Loan Application — " + values.firstName + " " + values.lastName
    );
  }

  function buildApplicationText(values) {
    const lines = [
      "Student Loan Application",
      "",
      "First Name: " + values.firstName,
      "Last Name: " + values.lastName,
      "Email: " + values.email,
      "Phone: " + (values.phone || "Not provided"),
      "National ID / Passport: " + values.idNumber,
      "Loan Amount: $" + values.loanAmount,
    ];

    if (values.idPhoto && values.facePhoto) {
      lines.push(
        "",
        "Attachments to include:",
        "• ID / Passport photo: " + values.idPhoto.name,
        "• Face photo: " + values.facePhoto.name,
        "",
        "Please attach the files listed above before sending."
      );
    }

    return lines.join("\n");
  }

  sendEmailBtn.addEventListener("click", function () {
    const values = validateForm(true);
    if (!values) return;

    const subject = encodeURIComponent(buildEmailSubject(values));
    const body = encodeURIComponent(buildApplicationText(values));
    window.location.href =
      "mailto:" + RECIPIENT_EMAIL + "?subject=" + subject + "&body=" + body;

    setStatus(
      "Your email app is opening with the subject and message ready. Attach your ID and face photos, then tap Send.",
      "success"
    );
  });

  sendWhatsAppBtn.addEventListener("click", function () {
    const values = validateForm(true);
    if (!values) return;

    const message = encodeURIComponent(buildApplicationText(values));
    window.open(
      "https://wa.me/" + WHATSAPP_NUMBER + "?text=" + message,
      "_blank"
    );

    setStatus(
      "WhatsApp is opening with your message ready. Attach your ID and face photos, then tap Send.",
      "success"
    );
  });
})();
