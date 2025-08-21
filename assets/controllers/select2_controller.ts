import { Controller } from "@hotwired/stimulus";
import $ from "jquery";

export default class extends Controller {
  connect() {
    $(this.element).select2({
      theme: "bootstrap5",
      width: "100%",
      placeholder:
        (this.element as HTMLElement).dataset.placeholder || "Sélectionnez une option",
      allowClear: Boolean((this.element as HTMLElement).dataset.allowClear),
    });

    // Gestion de la destruction propre
    this.element.addEventListener("stimulus:disconnect", () => {
      $(this.element).select2("destroy");
    });
  }
}
