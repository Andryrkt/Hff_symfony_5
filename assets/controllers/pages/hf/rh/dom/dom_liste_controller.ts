import { Controller } from "@hotwired/stimulus";
import '@styles/pages/domListe.scss';

export default class extends Controller {
    connect() {
        console.log("📋 Dom Liste controller connected");
    }
}
