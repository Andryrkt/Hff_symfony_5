import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller usage:
 * <div data-controller="user-roles">
 *   <div data-user-roles-target="display">...</div>
 *   <form data-user-roles-target="form" hidden>...</form>
 *   <button data-action="user-roles#edit">Edit</button>
 *   <button data-action="user-roles#save" hidden>Save</button>
 *   <button data-action="user-roles#cancel" hidden>Cancel</button>
 * </div>
 */
export default class extends Controller {
    static targets = ['display', 'form', 'editButton', 'saveButton', 'cancelButton'];

    readonly displayTarget!: HTMLElement;
    readonly formTarget!: HTMLFormElement;
    readonly editButtonTarget!: HTMLButtonElement;
    readonly saveButtonTarget!: HTMLButtonElement;
    readonly cancelButtonTarget!: HTMLButtonElement;

    connect() {
        console.log('UserRoles controller connected');
    }

    edit() {
        this.displayTarget.hidden = true;
        this.formTarget.hidden = false;
        this.editButtonTarget.hidden = true;
        this.saveButtonTarget.hidden = false;
        this.cancelButtonTarget.hidden = false;
    }

    cancel() {
        this.displayTarget.hidden = false;
        this.formTarget.hidden = true;
        this.editButtonTarget.hidden = false;
        this.saveButtonTarget.hidden = true;
        this.cancelButtonTarget.hidden = true;
    }

    async save(event: Event) {
        event.preventDefault();

        const form = this.formTarget as HTMLFormElement;
        const formData = new FormData(form);
        const userId = (this.element as HTMLElement).dataset.userId;

        try {
            const response = await fetch(`/admin/user/${userId}/roles/update-ajax`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // Update the displayed roles
                this.displayTarget.textContent = data.roles.join(', ');
                this.cancel(); // Switch back to display mode
            } else {
                // Handle validation errors or other issues
                console.error('Error saving roles:', data.message);
                alert('Error saving roles: ' + data.message);
            }

        } catch (error) {
            console.error('Failed to save roles:', error);
            alert('Failed to save roles.');
        }
    }
}
