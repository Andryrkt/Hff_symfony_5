import { Controller } from "stimulus";
import { Modal } from "bootstrap";

export default class extends Controller {
  connect() {
    this.modal = new Modal(this.element);
  }

  open() {
    this.modal.show();
  }

  close() {
    this.modal.hide();
  }
}
