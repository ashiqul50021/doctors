{{--
    Show every <input type="date"> / <input type="datetime-local"> as DD/MM/YYYY.
    The browser's native date box follows the device locale (often MM/DD/YYYY), so we replace it
    with flatpickr. The original input keeps the Y-m-d value that the server expects; only the
    visible box shows d/m/Y. Inputs bound with wire:* (Livewire) are left native on purpose.
--}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script>
    (function () {
        function isLivewireBound(el) {
            return Array.from(el.attributes).some(a => a.name.startsWith('wire:'));
        }

        function initDmyPickers(root) {
            if (typeof flatpickr === 'undefined') return;
            (root || document).querySelectorAll('input[type="date"], input[type="datetime-local"]').forEach(function (el) {
                if (el._flatpickr || el.dataset.nativeDate !== undefined || isLivewireBound(el)) return;

                const withTime = el.type === 'datetime-local';
                const fp = flatpickr(el, {
                    dateFormat: withTime ? 'Y-m-d\\TH:i' : 'Y-m-d',   // value sent to the server (unchanged)
                    altInput: true,
                    altInputClass: (el.className + ' flatpickr-alt').trim(), // keep the page's own styling
                    altFormat: withTime ? 'd/m/Y h:i K' : 'd/m/Y',     // what the user sees
                    allowInput: true,
                    enableTime: withTime,
                    minDate: el.min || null,
                    maxDate: el.max || null,
                    disableMobile: true,
                    onChange: function () {
                        el.dispatchEvent(new Event('input', { bubbles: true }));
                    },
                });

                if (fp.altInput) {
                    fp.altInput.placeholder = withTime ? 'DD/MM/YYYY hh:mm' : 'DD/MM/YYYY';
                    if (el.id) fp.altInput.setAttribute('data-for', el.id);
                }
            });
        }

        window.initDmyPickers = initDmyPickers;
        document.addEventListener('DOMContentLoaded', function () { initDmyPickers(); });
        document.addEventListener('livewire:navigated', function () { initDmyPickers(); });
    })();
</script>
