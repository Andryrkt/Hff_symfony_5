/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import { Application } from "stimulus";
import HelloController from "./controllers/hello_controller";
import ModalController from "./controllers/modal_controller";
import DomFirstFormController from "./controllers/dom_first_form_controller";
// imporation du bibliothèque bootstrap
import "bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";

// Import complet (icônes + CSS) font awesome
import "@fortawesome/fontawesome-free/css/all.min.css";
import "@fortawesome/fontawesome-free/js/all.min.js";

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";

// start the Stimulus application
import "./bootstrap";

//select 2
import "select2";
import "select2/dist/css/select2.css";
import "@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.css";

const application = Application.start();
application.register("hello", HelloController);
application.register("modal", ModalController); //modal controller stimulus
application.register("dom-first-form", DomFirstFormController);
