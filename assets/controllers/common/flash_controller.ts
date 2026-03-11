import { Controller } from '@hotwired/stimulus';
import Swal from 'sweetalert2';

/**
 * Stimulus controller to display Symfony flash messages using SweetAlert2 toasts.
 */
export default class extends Controller {
    static values = {
        messages: Array,
    };

    declare readonly messagesValue: Array<{type: string, message: string}>;
    declare readonly hasMessagesValue: boolean;

    connect() {
        if (!this.hasMessagesValue || this.messagesValue.length === 0) {
            return;
        }

        // Fire toasts sequence
        this.messagesValue.forEach((flash, index) => {
            setTimeout(() => {
                let icon = flash.type;
                
                // Map common Symfony flash types to SweetAlert2 icon types
                if (icon === 'danger') {
                    icon = 'error';
                } else if (icon === 'notice') {
                    icon = 'info';
                }

                // If the icon is not one of the sweetalerts supported icons, default to info
                const validIcons = ['success', 'error', 'warning', 'info', 'question'];
                if (!validIcons.includes(icon)) {
                    icon = 'info';
                }

                Swal.fire({
                    icon: icon as any,
                    title: flash.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            }, index * 500); // 500ms delay between multiple toasts
        });
    }
}
