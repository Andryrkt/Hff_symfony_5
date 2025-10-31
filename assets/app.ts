/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// ⚠️ CORRECTION : Importez depuis '@hotwired/stimulus' au lieu de 'stimulus'
import { Application } from "@hotwired/stimulus";
import HelloController from "./controllers/hello_controller";
import ModalController from "./controllers/modal_controller";
import NavigationController from "./controllers/navigation_controller";
import UserRolesController from "./controllers/user_roles_controller";
import ClickableController from "./controllers/inline_edit_controller"; // ⭐ AJOUT

// imporation du bibliothèque bootstrap
import "bootstrap";

// Import complet (icônes + CSS) font awesome
import "@fortawesome/fontawesome-free/css/all.min.css";
import "@fortawesome/fontawesome-free/js/all.min.js";

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.scss";

// start the Stimulus application
import "./bootstrap";

//select 2
import "select2";
import "select2/dist/css/select2.css";
import "select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css";

// Import des utilitaires de navigation
import { ChronometerManager } from "./js/utils/chronometer";
import { SessionManager } from "./js/utils/session";
import { ToastManager } from "./js/utils/toast";

const application = Application.start();
application.register("hello", HelloController);
application.register("modal", ModalController);
application.register("navigation", NavigationController);
application.register("user-roles", UserRolesController);
application.register("clickable", ClickableController); // ⭐ AJOUT

// Initialisation des gestionnaires de navigation
document.addEventListener('DOMContentLoaded', function () {
    // Initialiser le chronomètre de session
    const chronometer = new ChronometerManager();
    chronometer.init();

    // Initialiser la gestion de session
    const sessionManager = new SessionManager();
    sessionManager.init();

    // Initialiser les notifications toast
    const toastManager = new ToastManager();
    toastManager.init();
});

import './styles/home.css';