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
