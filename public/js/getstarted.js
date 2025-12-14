document.addEventListener('DOMContentLoaded', () => {
    // Handle verification code input
    const inputs = document.querySelectorAll('.code-inputs input');
    const resendButton = document.querySelector('.resend-btn');

    inputs.forEach((input, index) => {
        // Auto-focus next input
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }
        });

        // Handle backspace
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    // Countdown timer
    let timeLeft = 75; // 1:15 in seconds
    const countdownDisplay = document.getElementById('countdown');

    const updateTimer = () => {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        countdownDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        if (timeLeft === 0) {
            resendButton.disabled = false;
            return;
        }

        timeLeft--;
        setTimeout(updateTimer, 1000);
    };

    updateTimer();

    // Handle resend button
    resendButton.addEventListener('click', () => {
        if (!resendButton.disabled) {
            // Reset timer
            timeLeft = 75;
            resendButton.disabled = true;
            updateTimer();

            // Clear inputs
            inputs.forEach(input => {
                input.value = '';
            });
            inputs[0].focus();

            // Show success message
            alert('New code sent!');
        }
    });
});









// Toggle Gender Active Buttons
document.querySelectorAll('.gender').forEach(button => {
    button.addEventListener('click', function () {
        // Remove 'active' class from all buttons
        document.querySelectorAll('.gender').forEach(btn => btn.classList.remove('active'));

        // Add 'active' class to clicked button
        this.classList.add('active');
    });
});




// Format phone number as user types
const phoneInput = document.querySelector('input[type="tel"]');
if (phoneInput) {
    phoneInput.addEventListener('input', (e) => {
        // Remove any non-digit characters
        let value = e.target.value.replace(/\D/g, '');
        console.log(value);

        // Format the number as "00 000 00 00"
        if (value.length > 0) {
            if (value.length <= 2) {
                value = value;
            } else if (value.length <= 5) {
                value = value.slice(0, 2) + ' ' + value.slice(2);
            } else if (value.length <= 7) {
                value = value.slice(0, 2) + ' ' + value.slice(2, 5) + ' ' + value.slice(5);
            } else {
                value = value.slice(0, 2) + ' ' + value.slice(2, 5) + ' ' +
                    value.slice(5, 7) + ' ' + value.slice(7, 9);
            }
        }

        e.target.value = value;
    });
}





// Handle verification code input and home page redirection
document.querySelectorAll('.code-inputs input').forEach(input => {
    input.addEventListener('input', () => {
        const code = Array.from(document.querySelectorAll('.code-inputs input'))
                          .map(input => input.value)
                          .join('');
        
        if (code === '1234') {
            const formData = gatherFormData();
            console.log(formData);
            // Redirect to home page
            // window.location.href = "/home";  // Change "/home" to your actual home page path
        }
    });
});


function gatherFormData() {
    const formData = {
        email: document.querySelector('#box1 input[type="email"]').value,
        fullName: document.querySelector('#box1 input[type="text"]').value,
        phoneNumber: document.querySelector('#box3 input[type="tel"]').value,
        country: document.querySelector('#box2 input[placeholder="Country"]').value,
        city: document.querySelector('#box2 input[placeholder="City Or village"]').value,
        gender: document.querySelector('.gender.active').value,
        age: document.querySelector('#box2 input[placeholder="Your Age"]').value,
        weight: document.querySelector('#box2 input[placeholder="Your Weight"]').value,
        pathologies: Array.from(document.querySelectorAll('#box2 input[placeholder="E.G. Diabetes"]'))
                          .map(input => input.value),
        allergies: Array.from(document.querySelectorAll('#box2 input[placeholder="Allergies"]'))
                         .map(input => input.value),
        chronicMedication: Array.from(document.querySelectorAll('#box2 input[placeholder="Chronic Medication"]'))
                                .map(input => input.value),
    };
    return formData;
}