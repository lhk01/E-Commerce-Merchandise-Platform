function handleCheckboxClick(clickedCheckbox) {
  // Get all checkboxes with the name "payment-method"
  const checkboxes = document.getElementsByName("payment-method");

  // Loop through all checkboxes and uncheck those that are not the clicked one
  checkboxes.forEach((checkbox) => {
      if (checkbox !== clickedCheckbox) {
          checkbox.checked = false;
      }
});
        

    }