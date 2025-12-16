document.addEventListener('DOMContentLoaded', () => {
    const contentContainer = document.getElementById('doc-content');
    const titleContainer = document.getElementById('doc-title');
    const sidebarLinks = document.querySelectorAll('[data-doc-link]');

    const handleLinkClick = (e, url) => {
        e.preventDefault();

        // Visual feedback
        contentContainer.style.opacity = '0.5';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                contentContainer.innerHTML = data.content;
                titleContainer.textContent = data.title.replace(/[-_]/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

                // Update active state in sidebar
                sidebarLinks.forEach(l => l.classList.remove('active'));
                // Find sidebar link corresponding to this page
                const newActiveLink = document.querySelector(`nav#doc-sidebar a[href="${url}"]`) ||
                    document.querySelector(`nav#doc-sidebar a[href$="${url}"]`); // Fuzzy match
                if (newActiveLink) {
                    newActiveLink.classList.add('active');
                    // Scroll sidebar to active link if needed
                    newActiveLink.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }

                // Update URL without reload
                window.history.pushState({ path: url }, '', url);

                window.scrollTo(0, 0);
            })
            .catch(error => {
                console.error('Error:', error);
                contentContainer.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement de la documentation.</div>';
            })
            .finally(() => {
                contentContainer.style.opacity = '1';
            });
    };

    // Sidebar links
    sidebarLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            handleLinkClick(e, link.getAttribute('href'));
        });
    });

    // Content links delegation
    contentContainer.addEventListener('click', (e) => {
        const target = e.target as Element;
        const link = target.closest('a');
        if (link && link.getAttribute('href') && !link.getAttribute('href').startsWith('http') && !link.getAttribute('href').startsWith('#')) {
            handleLinkClick(e, link.getAttribute('href'));
        }
    });

    // Handle back details
    window.addEventListener('popstate', (event) => {
        if (event.state) {
            window.location.reload(); // Simple fallback for now
        }
    });
});
