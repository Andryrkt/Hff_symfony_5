// import du logo
import logoPath from "../../images/logoHFF.jpg";

// Stocker le logo dans window seulement si nécessaire
if (typeof window !== 'undefined') {
  window.logoPath = logoPath;
}

/* Fonctions */
function togglePasswordVisibility() {
  try {
    const passwordInput = document.getElementById("password");
    const icon = document.getElementById("toggleIcon");
    
    if (!passwordInput || !icon) {
      console.error("Éléments introuvables pour la gestion du mot de passe");
      return;
    }

    const isVisible = passwordInput.type === "text";
    passwordInput.type = isVisible ? "password" : "text";
    icon.classList.toggle("fa-eye");
    icon.classList.toggle("fa-eye-slash");
    
    const toggleButton = document.getElementById("togglePassword");
    if (toggleButton) {
      toggleButton.setAttribute("aria-pressed", String(!isVisible));
    }
  } catch (error) {
    console.error("Erreur lors du toggle du mot de passe:", error);
  }
}

function handleLoadAnimation() {
  document.body.classList.add("loaded");
}

/* Initialisation */
document.addEventListener("DOMContentLoaded", () => {
  // Gestion du mot de passe
  const togglePasswordButton = document.getElementById("togglePassword");
  if (togglePasswordButton) {
    togglePasswordButton.addEventListener("click", togglePasswordVisibility);
  }

  // Animation au chargement
  window.addEventListener("load", handleLoadAnimation);
  
  // Nettoyage du localStorage
  try {
    localStorage.clear();
    console.log("localStorage nettoyé");
  } catch (error) {
    console.error("Erreur lors du nettoyage du localStorage:", error);
  }
});