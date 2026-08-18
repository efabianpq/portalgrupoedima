import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Formulario de contacto público: envía por fetch() a
 * ContactMessageController@store sin recargar la página. El backend ya
 * localiza los mensajes de validación y de error genérico según el idioma
 * activo (ver app/Http/Controllers/ContactMessageController.php); `errorMessage`
 * sólo se usa como respaldo si la respuesta no trae su propio `message`
 * (por ejemplo un error de red).
 */
Alpine.data('contactForm', (endpoint, errorMessage, initialMessage = '') => ({
    fields: { name: '', email: '', phone: '', message: initialMessage, website: '' },
    errors: {},
    submitting: false,
    success: false,
    successMessage: '',
    genericError: '',

    async submit() {
        this.submitting = true;
        this.errors = {};
        this.genericError = '';
        this.success = false;

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(this.fields),
            });

            const data = await response.json().catch(() => ({}));

            if (response.status === 422) {
                this.errors = Object.fromEntries(
                    Object.entries(data.errors || {}).map(([field, messages]) => [field, messages[0]]),
                );
                return;
            }

            if (!response.ok) {
                this.genericError = data.message || errorMessage;
                return;
            }

            this.success = true;
            this.successMessage = data.message;
            this.fields = { name: '', email: '', phone: '', message: '', website: '' };
        } catch (e) {
            this.genericError = errorMessage;
        } finally {
            this.submitting = false;
        }
    },
}));

Alpine.start();
