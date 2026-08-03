document.addEventListener("DOMContentLoaded", () => {
  // Sticky header shadow
  const header = document.querySelector(".header-middle");
  if (header) {
    const onScroll = () => {
      header.style.boxShadow = window.scrollY > 40 ? "0 4px 16px rgba(0,0,0,.08)" : "none";
    };
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  // Qty buttons
  document.querySelectorAll("[data-qty]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const input = btn.parentElement.querySelector("input");
      if (!input) return;
      let val = parseInt(input.value, 10) || 1;
      val = btn.dataset.qty === "plus" ? val + 1 : Math.max(1, val - 1);
      input.value = val;
    });
  });

  // Newsletter prevent default
  document.querySelectorAll(".newsletter-form").forEach((form) => {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const email = form.querySelector('input[type="email"]');
      if (email && email.value) {
        alert("Thanks for subscribing!");
        email.value = "";
      }
    });
  });
});
