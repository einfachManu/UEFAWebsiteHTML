let currentStep = 1;
        
        function nextStep(step) {
            // Basic validation
            const inputs = document.querySelectorAll(`#step-${step} input:required, #step-${step} select:required`);
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value) {
                    isValid = false;
                    input.style.borderColor = 'var(--error)';
                    
                    // Add error message if it doesn't exist
                    let errorMessage = input.nextElementSibling;
                    if (!errorMessage || !errorMessage.classList.contains('error-message')) {
                        errorMessage = document.createElement('div');
                        errorMessage.classList.add('error-message');
                        errorMessage.textContent = 'Dieses Feld wird benötigt';
                        input.parentNode.insertBefore(errorMessage, input.nextSibling);
                    }
                } else {
                    input.style.borderColor = '';
                    
                    // Remove error message if it exists
                    const errorMessage = input.nextElementSibling;
                    if (errorMessage && errorMessage.classList.contains('error-message')) {
                        errorMessage.remove();
                    }
                }
            });
            
            // Special validation for ticket selection
            if (step === 3) {
                const cat1 = parseInt(document.getElementById('cat1').value) || 0;
                const cat2 = parseInt(document.getElementById('cat2').value) || 0;
                const cat3 = parseInt(document.getElementById('cat3').value) || 0;
                const fanFirst = parseInt(document.getElementById('fans-first').value) || 0;
                
                const totalTickets = cat1 + cat2 + cat3 + fanFirst;
                
                if (totalTickets === 0) {
                    isValid = false;
                    alert('Bitte wähle mindestens ein Ticket aus.');
                } else if (totalTickets > 2) {
                    isValid = false;
                    alert('Du kannst maximal 2 Tickets insgesamt auswählen.');
                }
            }
            
            if (!isValid) return;
            
            // If validation passes, proceed to next step
            document.getElementById(`step-${step}`).classList.add('hidden');
            document.getElementById(`step-${step+1}`).classList.remove('hidden');
            
            // Update progress bar
            document.querySelectorAll('.progress-step')[step-1].classList.remove('active');
            document.querySelectorAll('.progress-step')[step-1].classList.add('completed');
            document.querySelectorAll('.progress-step')[step].classList.add('active');
            
            currentStep = step + 1;
            
            // Update summary on final step
            if (currentStep === 6) {
                updateSummary();
            }
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        function prevStep(step) {
            document.getElementById(`step-${step}`).classList.add('hidden');
            document.getElementById(`step-${step-1}`).classList.remove('hidden');
            
            // Update progress bar
            document.querySelectorAll('.progress-step')[step-1].classList.remove('active');
            document.querySelectorAll('.progress-step')[step-2].classList.remove('completed');
            document.querySelectorAll('.progress-step')[step-2].classList.add('active');
            
            currentStep = step - 1;
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        function updateSummary() {
            // Get form values
            const firstName = document.getElementById('firstname').value;
            const lastName = document.getElementById('lastname').value;
            
            // Calculate ticket information
            const cat1 = parseInt(document.getElementById('cat1').value) || 0;
            const cat2 = parseInt(document.getElementById('cat2').value) || 0;
            const cat3 = parseInt(document.getElementById('cat3').value) || 0;
            const fanFirst = parseInt(document.getElementById('fans-first').value) || 0;
            
            let categoryText = '';
            let totalPrice = 0;
            let totalTickets = 0;
            
            if (cat1 > 0) {
                categoryText += `Kategorie 1 (${cat1}x), `;
                totalPrice += cat1 * 690;
                totalTickets += cat1;
            }
            
            if (cat2 > 0) {
                categoryText += `Kategorie 2 (${cat2}x), `;
                totalPrice += cat2 * 490;
                totalTickets += cat2;
            }
            
            if (cat3 > 0) {
                categoryText += `Kategorie 3 (${cat3}x), `;
                totalPrice += cat3 * 280;
                totalTickets += cat3;
            }
            
            if (fanFirst > 0) {
                categoryText += `Fans First (${fanFirst}x), `;
                totalPrice += fanFirst * 180;
                totalTickets += fanFirst;
            }
            
            // Remove trailing comma
            categoryText = categoryText.replace(/, $/, '');let currentStep = 1;
        }

function nextStep(step) {
  // Basic validation
  const inputs = document.querySelectorAll(`#step-${step} input:required, #step-${step} select:required`);
  let isValid = true;

  inputs.forEach(input => {
    if (!input.value) {
      isValid = false;
      input.style.borderColor = 'var(--error)';
      let errorMessage = input.nextElementSibling;
      if (!errorMessage || !errorMessage.classList.contains('error-message')) {
        errorMessage = document.createElement('div');
        errorMessage.classList.add('error-message');
        errorMessage.textContent = 'Dieses Feld wird benötigt';
        input.parentNode.insertBefore(errorMessage, input.nextSibling);
      }
    } else {
      input.style.borderColor = '';
      const errorMessage = input.nextElementSibling;
      if (errorMessage && errorMessage.classList.contains('error-message')) {
        errorMessage.remove();
      }
    }
  });

  // Special validation for ticket selection
  if (step === 3) {
    const cat1 = parseInt(document.getElementById('cat1').value) || 0;
    const cat2 = parseInt(document.getElementById('cat2').value) || 0;
    const cat3 = parseInt(document.getElementById('cat3').value) || 0;
    const fanFirst = parseInt(document.getElementById('fans-first').value) || 0;
    const totalTickets = cat1 + cat2 + cat3 + fanFirst;

    if (totalTickets === 0) {
      isValid = false;
      alert('Bitte wähle mindestens ein Ticket aus.');
    } else if (totalTickets > 2) {
      isValid = false;
      alert('Du kannst maximal 2 Tickets insgesamt auswählen.');
    }
  }

  if (!isValid) return;

  // Hide current, show next
  document.getElementById(`step-${step}`).classList.add('hidden');
  document.getElementById(`step-${step+1}`).classList.remove('hidden');

  // Update progress bar
  const steps = document.querySelectorAll('.progress-step');
  steps[step-1].classList.remove('active');
  steps[step-1].classList.add('completed');
  steps[step].classList.add('active');

  currentStep = step + 1;

  // Update summary when we arrive on step 6
  if (currentStep === 6) {
    updateSummary();
  }

  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep(step) {
  document.getElementById(`step-${step}`).classList.add('hidden');
  document.getElementById(`step-${step-1}`).classList.remove('hidden');

  // Update progress bar
  const steps = document.querySelectorAll('.progress-step');
  steps[step-1].classList.remove('active');
  steps[step-2].classList.remove('completed');
  steps[step-2].classList.add('active');

  currentStep = step - 1;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateSummary() {
  // 1. Name
  const firstName = document.getElementById('firstname').value;
  const lastName  = document.getElementById('lastname').value;
  document.getElementById('summary-name').textContent = `${firstName} ${lastName}`;

  // 2. Ticket-Auswahl ausrechnen
  const prices = { cat1: 690, cat2: 490, cat3: 280, 'fans-first': 180 };
  let totalPrice   = 0;
  let totalTickets = 0;
  let categoryText = [];

  ['cat1','cat2','cat3','fans-first'].forEach(id => {
    const count = parseInt(document.getElementById(id).value) || 0;
    if (count > 0) {
      const label = id === 'fans-first' ? 'Fans First' : `Kategorie ${id.slice(-1)}`;
      categoryText.push(`${label} (${count}x)`);
      totalPrice   += count * prices[id];
      totalTickets += count;
    }
  });

  // 3. Zusammenfassen
  document.getElementById('summary-category').textContent = categoryText.join(', ');
  document.getElementById('summary-count').textContent    = totalTickets;
  document.getElementById('summary-price').textContent    =
    `€${totalPrice.toLocaleString('de-DE', { minimumFractionDigits: 2 })}`;
}

function submitApplication() {
  // 1. (Optional) Finales Validieren der Checkboxes
  const finalChecks = document.querySelectorAll('#step-6 input[type="checkbox"]:required');
  for (const chk of finalChecks) {
    if (!chk.checked) {
      alert('Bitte bestätige die AGB und Datenschutzrichtlinie.');
      return;
    }
  }

  // 2. Erfolgsmeldung anzeigen
  document.getElementById('step-6').classList.add('hidden');
  document.getElementById('success-message').classList.remove('hidden');

  // 3. Fortschritts-Balken abschließen
  const steps = document.querySelectorAll('.progress-step');
  steps[5].classList.remove('active');
  steps[5].classList.add('completed');

  window.scrollTo({ top: 0, behavior: 'smooth' });
}