import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["input"];

    declare inputTarget: HTMLInputElement;

    connect() {
        // console.log("NumberOnlyController connected");
    }

    filter() {
        const input = this.inputTarget;
        input.value = input.value.replace(/[^0-9]/g, '');
    }
}
