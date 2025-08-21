import { Controller } from "@hotwired/stimulus";
import { Modal } from "bootstrap";

export default class extends Controller {
  modal!: Modal;

  connect() {
    this.modal = new Modal(this.element as HTMLElement);
  }

  open() {
    this.modal.show();
  }

  close() {
    this.modal.hide();
  }
}