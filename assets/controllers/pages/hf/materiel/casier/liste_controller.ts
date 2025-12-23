import { Controller } from "@hotwired/stimulus";

export default class extends Controller {

    connect() {
        console.log("✅ Stimulus 'liste' controller connected.");
    }

    validate(event: MouseEvent) {
        console.log("▶️ 'validate' action triggered.");
        event.preventDefault();

        const currentButton = event.currentTarget as HTMLAnchorElement;
        const casierNom = currentButton.dataset.casierNom;
        const url = currentButton.dataset.validateUrl;

        window.Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: `Vous êtes sur le point de valider le casier "${casierNom}".`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, valider !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show a "processing" toast
                window.Swal.fire({
                    title: 'Validation en cours...',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    didOpen: () => {
                        window.Swal.showLoading();
                    }
                });

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success toast
                        window.Swal.fire({
                            icon: 'success',
                            title: data.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        // Replace the button with a disabled one
                        const newButton = document.createElement('button');
                        newButton.className = 'btn btn-sm btn-success fw-bold mb-3';
                        newButton.disabled = true;
                        newButton.textContent = 'Déjà Validé';
                        currentButton.parentNode.replaceChild(newButton, currentButton);
                    } else {
                        // Show error toast
                        window.Swal.fire({
                            icon: 'error',
                            title: data.message || 'Une erreur inattendue est survenue.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000, // Longer timer for errors
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Show network error toast
                    window.Swal.fire({
                        icon: 'error',
                        title: 'Impossible de contacter le serveur.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                });
            }
        });
    }
}
