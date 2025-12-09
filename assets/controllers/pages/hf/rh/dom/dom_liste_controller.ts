import { Controller } from "@hotwired/stimulus";
import '@styles/pages/domListe.scss';
import { initAgenceServiceHandlers } from '@utils/AgenceServiceManager';

export default class extends Controller {
    connect() {
        console.log("📋 Dom Liste controller connected");
        initAgenceServiceHandlers();
    }
}
