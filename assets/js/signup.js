document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('tuts-sign-up');
    const messages = document.getElementById('form-messages');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append('action', 'tuts_sign_up');

            console.log('Sending AJAX request to:', taskSignup.ajaxurl);
            console.log('Form data:', Object.fromEntries(formData));

            fetch(taskSignup.ajaxurl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    messages.classList.remove('error');
                    messages.classList.add('success');
                    messages.textContent = data.data.message;
                    form.reset();
                } else {
                    messages.classList.remove('success');
                    messages.classList.add('error');
                    messages.innerHTML = data.data.errors ? data.data.errors.join('<br>') : data.data.message;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messages.classList.remove('success');
                messages.classList.add('error');
                messages.textContent = 'An error occurred. Please try again.';
            });
        });
    }
});
