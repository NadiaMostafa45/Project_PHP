<script>
    (function() {
        if (window.__navLockInitialized) {
            return;
        }
        window.__navLockInitialized = true;

        const lockNavigation = () => history.pushState(null, '', location.href);
        lockNavigation();
        window.addEventListener('popstate', lockNavigation);

        window.addEventListener('keydown', function(e) {
            const tagName = (e.target && e.target.tagName) ? e.target.tagName.toLowerCase() : '';
            const isTextInput = tagName === 'input' || tagName === 'textarea';

            if (e.altKey && (e.key === 'ArrowLeft' || e.key === 'ArrowRight')) {
                e.preventDefault();
            }

            if (!isTextInput && e.key === 'Backspace') {
                e.preventDefault();
            }
        });
    })();
</script>