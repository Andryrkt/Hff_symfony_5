import { Controller } from '@hotwired/stimulus';
import Swal from 'sweetalert2';

export default class extends Controller {
    static values = {
        allowedTypes: Array,
        maxSize: Number, // en Mo
    };

    declare readonly allowedTypesValue: string[];
    declare readonly hasAllowedTypesValue: boolean;
    declare readonly maxSizeValue: number;
    declare readonly hasMaxSizeValue: boolean;

    validate(event: Event) {
        const input = event.target as HTMLInputElement;
        const files = input.files;

        if (!files || files.length === 0) {
            return;
        }

        const file = files[0];

        // 1. Validation du type de fichier
        if (this.hasAllowedTypesValue && this.allowedTypesValue.length > 0) {
            if (!this.allowedTypesValue.includes(file.type)) {
                this.showError(`Le type de fichier n'est pas autorisé. Types autorisés : ${this.allowedTypesValue.join(', ')}`);
                input.value = ''; // Réinitialise le champ
                return;
            }
        }

        // 2. Validation de la taille du fichier
        if (this.hasMaxSizeValue) {
            const maxSizeInBytes = this.maxSizeValue * 1024 * 1024;
            if (file.size > maxSizeInBytes) {
                this.showError(`Le fichier est trop volumineux (${(file.size / 1024 / 1024).toFixed(2)} Mo). La taille maximale est de ${this.maxSizeValue} Mo.`);
                input.value = ''; // Réinitialise le champ
                return;
            }
        }
    }

    private showError(message: string) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur de fichier',
            text: message,
            confirmButtonColor: '#fbbb01',
        });
    }
}
