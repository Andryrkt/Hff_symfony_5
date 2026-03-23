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

                const isCritical = icon === 'error' || icon === 'warning';

                const config: any = {
                    icon: icon,
                    title: flash.message,
                    confirmButtonColor: '#ffc107',
                };

                if (isCritical) {
                    // Critical messages: show as modal, require user action
                    config.toast = false;
                    config.position = 'center';
                    config.showConfirmButton = true;
                    config.confirmButtonText = 'OK';
                    config.timer = undefined;
                    config.timerProgressBar = false;
                } else {
                    // Non-critical: show as ephemeral toast
                    config.toast = true;
                    config.position = 'top-end';
                    config.showConfirmButton = false;
                    config.timer = 5000;
                    config.timerProgressBar = true;
                    config.didOpen = (toast: HTMLElement) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    };
                }

                Swal.fire(config);
            }, index * 500); // 500ms delay between multiple toasts
        });

    }
}
