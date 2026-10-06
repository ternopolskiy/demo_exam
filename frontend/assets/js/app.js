document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[name="date"]').forEach(function (input) {
        input.setAttribute('inputmode', 'numeric');
        input.setAttribute('maxlength', '10');
        input.addEventListener('input', function () {
            var digits = input.value.replace(/\D/g, '').slice(0, 8);
            var out = digits.slice(0, 2);
            if (digits.length > 2) {
                out += '.' + digits.slice(2, 4);
            }
            if (digits.length > 4) {
                out += '.' + digits.slice(4, 8);
            }
            input.value = out;
        });
    });

    document.querySelectorAll('input[name="time"]').forEach(function (input) {
        input.setAttribute('inputmode', 'numeric');
        input.setAttribute('maxlength', '5');
        input.addEventListener('input', function () {
            var digits = input.value.replace(/\D/g, '').slice(0, 4);
            var out = digits.slice(0, 2);
            if (digits.length > 2) {
                out += ':' + digits.slice(2, 4);
            }
            input.value = out;
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#toasts .toast').forEach(function (toast) {
        requestAnimationFrame(function () {
            toast.classList.add('show');
        });
        setTimeout(function () {
            toast.classList.remove('show');
            setTimeout(function () {
                toast.remove();
            }, 300);
        }, 4000);
    });
});

document.addEventListener('DOMContentLoaded', function () {
    var slider = document.getElementById('slider');
    if (!slider) {
        return;
    }

    var slides = Array.prototype.slice.call(slider.querySelectorAll('.slide'));
    var dotsBox = slider.querySelector('.slider-dots');
    var index = 0;
    var timer = null;

    slides.forEach(function (_, i) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'slider-dot';
        dot.setAttribute('aria-label', 'Изображение ' + (i + 1));
        dot.addEventListener('click', function () {
            go(i);
            restart();
        });
        dotsBox.appendChild(dot);
    });

    var dots = Array.prototype.slice.call(dotsBox.children);

    function go(i) {
        index = (i + slides.length) % slides.length;
        slides.forEach(function (slide, n) {
            slide.classList.toggle('active', n === index);
        });
        dots.forEach(function (dot, n) {
            dot.classList.toggle('active', n === index);
        });
    }

    function start() {
        stop();
        timer = setInterval(function () {
            go(index + 1);
        }, 3000);
    }

    function stop() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    function restart() {
        stop();
        start();
    }

    slider.querySelector('.slider-prev').addEventListener('click', function () {
        go(index - 1);
        restart();
    });
    slider.querySelector('.slider-next').addEventListener('click', function () {
        go(index + 1);
        restart();
    });
    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', start);

    go(0);
    start();
});
