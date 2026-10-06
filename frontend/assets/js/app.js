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
