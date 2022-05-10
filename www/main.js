(function () {
    'use strict';

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation');

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener(
            'submit',
            function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            },
            false,
        );
    });
})();

// Modal form
const modal = document.getElementById('modal');
const openSend = document.getElementById('send');
const openDeposit = document.getElementById('deposit');
const cancelSend = document.getElementById('cancelSend');
const cancelDeposit = document.getElementById('cancelDeposit');
const formSend = document.getElementById('form-send');
const formDeposit = document.getElementById('form-deposit');

openSend.onclick = () => {
    modal.style.display = 'block';
    formSend.style.display = 'block';
    formDeposit.style.display = 'none';
};

cancelSend.onclick = () => {
    modal.style.display = 'none';
};

openDeposit.onclick = () => {
    modal.style.display = 'block';
    formDeposit.style.display = 'block';
    formSend.style.display = 'none';
};

cancelDeposit.onclick = () => {
    modal.style.display = 'none';
};
