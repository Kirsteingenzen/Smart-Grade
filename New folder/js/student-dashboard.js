document.addEventListener("DOMContentLoaded", () => {
    // Toggle between editable and non-editable profile inputs
    const editBtn = document.getElementById('editBtn');
    const saveBtn = document.getElementById('saveBtn');
    const inputs = document.querySelectorAll('#profile input');

    

    if (editBtn && saveBtn && inputs.length > 0) {
        editBtn.addEventListener('click', () => {
            inputs.forEach(input => input.disabled = false);
            editBtn.disabled = true;
        });

        saveBtn.addEventListener('click', () => {
            inputs.forEach(input => input.disabled = true);
            editBtn.disabled = false;
        });
    }

    // Semester Section Navigation
    const navLinks = document.querySelectorAll('[data-section]');
    const contentSections = document.querySelectorAll('.content-section');

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const sectionId = link.getAttribute('data-section');

            // Skip logout link
            if (sectionId === 'logout') return;

            e.preventDefault();

            // Hide all sections
            contentSections.forEach(section => section.style.display = 'none');

            // Show the selected section
            const selectedSection = document.getElementById(sectionId);
            if (selectedSection) {
                selectedSection.style.display = 'block';
            }

            // Highlight active nav link
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        });
    });

    // Default: show Profile
    contentSections.forEach(section => section.style.display = 'none');
    const defaultSection = document.getElementById('profile');
    if (defaultSection) {
        defaultSection.style.display = 'block';
    }
});
