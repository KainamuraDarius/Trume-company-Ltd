const toggle = document.querySelector(".menu-toggle");
const nav = document.querySelector(".nav");
toggle?.addEventListener("click", () => {
  const open = nav.classList.toggle("open");
  toggle.setAttribute("aria-expanded", open);
});
document
  .querySelectorAll(".nav a")
  .forEach((link) =>
    link.addEventListener("click", () => nav.classList.remove("open")),
  );
document.getElementById("year").textContent = new Date().getFullYear();

const formStatus = document.getElementById("form-status");
const formResult = new URLSearchParams(window.location.search);
if (formStatus && formResult.has("sent")) {
  formStatus.textContent = "Thank you. Your message has been sent to the Trume team.";
  formStatus.classList.add("success");
} else if (formStatus && formResult.has("error")) {
  formStatus.textContent = "Please complete all required fields and try again.";
  formStatus.classList.add("error");
}

const contactForm = document.getElementById("contact-form");
contactForm?.addEventListener("submit", (event) => {
  if (!contactForm.checkValidity()) return;

  event.preventDefault();
  const fields = new FormData(contactForm);
  const recipient = contactForm.dataset.gmailRecipient;
  const name = fields.get("full_name")?.toString().trim() || "";
  const email = fields.get("email")?.toString().trim() || "";
  const phone = fields.get("phone")?.toString().trim() || "Not provided";
  const subject = fields.get("subject")?.toString().trim() || "Website enquiry";
  const message = fields.get("message")?.toString().trim() || "";
  const body = `Name: ${name}\nEmail: ${email}\nPhone: ${phone}\n\nMessage:\n${message}`;
  const gmailUrl = new URL("https://mail.google.com/mail/");

  gmailUrl.search = new URLSearchParams({
    view: "cm",
    fs: "1",
    to: recipient,
    su: subject,
    body,
  }).toString();

  formStatus.textContent = "Opening Gmail so you can review and send your message.";
  formStatus.className = "form-status success";
  window.location.assign(gmailUrl.toString());
});

const typewriterHeading = document.querySelector(".typewriter-heading");
if (typewriterHeading) {
  const pieces = [...typewriterHeading.querySelectorAll("[data-type]")];
  const reducedMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
  ).matches;

  if (reducedMotion) {
    pieces.forEach((piece) => {
      piece.textContent = piece.dataset.type;
    });
  } else {
    let pieceIndex = 0;
    let characterIndex = 0;

    const writeNextCharacter = () => {
      if (pieceIndex >= pieces.length) return;

      const piece = pieces[pieceIndex];
      const text = piece.dataset.type;
      piece.textContent += text[characterIndex];
      characterIndex += 1;

      if (characterIndex === text.length) {
        pieceIndex += 1;
        characterIndex = 0;
      }

      if (pieceIndex < pieces.length) {
        window.setTimeout(writeNextCharacter, 42);
      }
    };

    window.setTimeout(writeNextCharacter, 300);
  }
}

document.querySelectorAll(".founder-more").forEach((button) => {
  button.addEventListener("click", () => {
    const card = button.closest(".founder-card");
    const expanded = card.classList.toggle("expanded");
    button.setAttribute("aria-expanded", expanded);
    button.firstChild.textContent = expanded ? "View less " : "View more ";
  });
});
