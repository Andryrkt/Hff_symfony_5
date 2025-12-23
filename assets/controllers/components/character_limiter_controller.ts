import { Controller } from "@hotwired/stimulus";


export default class extends Controller {
    static targets = ['input'];
    static values = {
        maxLength: Number,
    }

    declare inputTarget: HTMLInputElement;
    declare maxLengthValue: number;

    connect() {
        // console.log('CharacterLimiterController connected');
    }

    limiterCaracteres() {
        const input = this.inputTarget;

        if (input.value.length > this.maxLengthValue) {
            input.value = input.value.slice(0, this.maxLengthValue);
        }
    }
}
