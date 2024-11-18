const labels = document.querySelectorAll('.checkbox-label');

    labels.forEach(label => {
        // Add click event to each label
        label.addEventListener('click', () => {
            // Uncheck all checkboxes first
            labels.forEach(otherLabel => {
                const otherCheckbox = otherLabel.querySelector('input[type="checkbox"]');
                otherCheckbox.checked = false;
                otherLabel.classList.remove('checked');
            });

            // Then check the clicked one
            const checkbox = label.querySelector('input[type="checkbox"]');
            checkbox.checked = true;
            label.classList.add('checked');
        });
});