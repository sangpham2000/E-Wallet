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

// Tab dashboard
const tabs = document.querySelectorAll('.tab-item');
const panes = document.querySelectorAll('.tab-pane');

const tabActive = document.querySelector('.tab-item.active');
const tabPaneActive = document.querySelector('.tab-pane.active');
const line = document.querySelector('.tabs .line');

requestIdleCallback(function () {
    line.style.left = tabActive.offsetLeft + 'px';
    line.style.width = tabActive.offsetWidth + 'px';
});

tabs.forEach((tab, index) => {
    const pane = panes[index];

    tab.onclick = function () {
        document.querySelector('.tab-item.active').classList.remove('active');
        document.querySelector('.tab-pane.active').classList.remove('active');

        line.style.left = this.offsetLeft + 'px';
        line.style.width = this.offsetWidth + 'px';

        this.classList.add('active');
        pane.classList.add('active');
    };
});

// Open dropdown menu
// const menuBtn = document.getElementById('header-menu');
// const menu = document.getElementById('dropdown-menu');
// menuBtn.addEventListener('click', () => {
//     menu.classList.toggle('hide');
// });

// window.addEventListener('mouseup', (event) => {
//     if (event.target != menu && event.target.parentNode != menu) {
//         menu.style.display = 'none';
//     }
// });

// Modal form
const modal = document.getElementById('modal');
const openSend = document.getElementById('send');
const cancelSend = document.getElementById('cancelSend');
const formSend = document.getElementById('form-send');

const openDeposit = document.getElementById('deposit');
const cancelDeposit = document.getElementById('cancelDeposit');
const formDeposit = document.getElementById('form-deposit');

const openBuyCard = document.getElementById('buycard');
const cancelBuyCard = document.getElementById('cancelBuyCard');
const formBuyCard = document.getElementById('form-buycard');

const openWithdraw = document.getElementById('withdraw');
const cancelWithdraw = document.getElementById('cancelWithdraw');
const formWithdraw = document.getElementById('form-withdraw');

openSend.onclick = () => {
    modal.style.display = 'block';
    formSend.style.display = 'block';
    formDeposit.style.display = 'none';
    formBuyCard.style.display = 'none';
    formWithdraw.style.display = 'none';
};

cancelSend.onclick = () => {
    modal.style.display = 'none';
};

openDeposit.onclick = () => {
    modal.style.display = 'block';
    formDeposit.style.display = 'block';
    formSend.style.display = 'none';
    formBuyCard.style.display = 'none';
    formWithdraw.style.display = 'none';
};

cancelDeposit.onclick = () => {
    modal.style.display = 'none';
};

openBuyCard.onclick = () => {
    modal.style.display = 'block';
    formBuyCard.style.display = 'block';
    formDeposit.style.display = 'none';
    formSend.style.display = 'none';
    formWithdraw.style.display = 'none';
};

cancelBuyCard.onclick = () => {
    modal.style.display = 'none';
};

openWithdraw.onclick = () => {
    modal.style.display = 'block';
    formWithdraw.style.display = 'block';
    formBuyCard.style.display = 'none';
    formDeposit.style.display = 'none';
    formSend.style.display = 'none';
};

cancelWithdraw.onclick = () => {
    modal.style.display = 'none';
};

const header = document.querySelector('.header');
window.addEventListener('scroll', () => {
    if (document.body.scrollTop > 80 || document.documentElement.scrollTop > 80) {
        header.classList.add('shrink');
    } else {
        header.classList.remove('shrink');
    }
});

// Toast function
function toast({ title = '', message = '', type = 'info', duration = 3000 }) {
    const main = document.getElementById('toast');
    if (main) {
        const toast = document.createElement('div');

        // Auto remove toast
        const autoRemoveId = setTimeout(function () {
            main.removeChild(toast);
        }, duration + 1000);

        // Remove toast when clicked
        toast.onclick = function (e) {
            if (e.target.closest('.toast__close')) {
                main.removeChild(toast);
                clearTimeout(autoRemoveId);
            }
        };

        const icons = {
            success: 'fa-solid fa-circle-check',
            info: 'fa-solid fa-circle-info',
            warning: 'fa-solid fa-triangle-exclamation',
            error: 'fa-solid fa-bug',
        };
        const icon = icons[type];
        const delay = (duration / 1000).toFixed(2);

        toast.classList.add('toast', `toast--${type}`);
        toast.style.animation = `slideInLeft ease .3s, fadeOut linear 1s ${delay}s forwards`;

        toast.innerHTML = `
                      <div class="toast__icon">
                          <i class="${icon}"></i>
                      </div>
                      <div class="toast__body">
                          <h3 class="toast__title">${title}</h3>
                          <p class="toast__msg">${message}</p>
                      </div>
                      <div class="toast__close">
                          <i class="fa-solid fa-xmark"></i>
                      </div>
                  `;
        main.appendChild(toast);
    }
}

// Solve cost transaction
const amountTransfer = document.getElementById('amount');
const transCost = document.getElementById('trans-cost');

amountTransfer.addEventListener('input', () => {
    transCost.value = Math.round((amountTransfer.value * 5) / 100);
});

// Solve cost witdraw
const amountWithdraw = document.getElementById('amount-withdraw');
const withdrawCost = document.getElementById('trans-cost-withdraw');

amountWithdraw.addEventListener('input', () => {
    withdrawCost.value = Math.round((amountWithdraw.value * 5) / 100);
});
