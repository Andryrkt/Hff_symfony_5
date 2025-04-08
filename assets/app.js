/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// imporation du bibliothèque bootstrap
import "bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";

// start the Stimulus application
import "./bootstrap";

import { Application } from "stimulus";
import HelloController from "./controllers/hello_controller";

const application = Application.start();
application.register("hello", HelloController);
