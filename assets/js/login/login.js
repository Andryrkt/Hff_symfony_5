// import du logo
import logoPath from "../../images/logoHFF.jpg";

window.logoPath = logoPath;

/* pour l'affichage de moty de passe*/
document
  .getElementById("togglePassword")
  .addEventListener("click", function () {
    const passwordInput = document.getElementById("password");
    const icon = document.getElementById("toggleIcon");
    const isVisible = passwordInput.type === "text";

    passwordInput.type = isVisible ? "password" : "text";
    icon.classList.toggle("fa-eye");
    icon.classList.toggle("fa-eye-slash");
    this.setAttribute("aria-pressed", String(!isVisible));
  });

/*pour une petite annimation pour l'entrer */
window.addEventListener("load", () => {
  document.body.classList.add("loaded");
});

localStorage.clear(); // Vider le localStorage
