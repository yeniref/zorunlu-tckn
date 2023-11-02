document.addEventListener("DOMContentLoaded", function() {
    var form = document.getElementById('tc-verification-form');
    var kayitSonuc = document.getElementById('kayit-sonuc');
    var ajaxurl = form.querySelector('input[name="ajaxurl"]').value;
    form.addEventListener("submit", function(event) {
        event.preventDefault();

        var formData = new FormData(form);

        // AJAX isteğini başlatın ve verileri sunucuya gönderin
        fetch(ajaxurl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-WP-Nonce': form.querySelector('input[name="harew-register-nonce"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            // Gelen cevabı işleyin
            if (data.success) {
                alert(data.message);
                form.reset();

            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Hata:', error);
        });
    });
});

