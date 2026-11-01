<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="_token"]').content;

        document.querySelectorAll('.notification-item').forEach(function(el) {
            el.addEventListener('click', function() {
                const id = this.dataset.id;
                const li = this.closest('li');

                fetch(`/notifications/${id}/mark-read`, {
                    method: 'POST'
                    , headers: {
                        'X-CSRF-TOKEN': csrfToken
                        , 'Content-Type': 'application/json'
                    }
                , }).then(() => {
                    li.remove();
                    adjustBadge(-1);
                    checkEmpty();
                });
            });
        });

        const markAllBtn = document.getElementById('mark-all-read');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function() {
                fetch('/notifications/mark-all-read', {
                    method: 'POST'
                    , headers: {
                        'X-CSRF-TOKEN': csrfToken
                        , 'Content-Type': 'application/json'
                    }
                , }).then(() => {
                    document.querySelectorAll('#notification-list li').forEach(el => el.remove());
                    adjustBadge(0, true);
                    markAllBtn.classList.add('d-none');
                    checkEmpty();
                });
            });
        }

        function adjustBadge(delta, reset = false) {
            const badge = document.getElementById('notification-badge');
            if (!badge) return;
            let current = parseInt(badge.textContent.trim()) || 0;
            const newCount = reset ? 0 : Math.max(0, current + delta);
            if (newCount > 0) {
                badge.textContent = newCount > 99 ? '99+' : newCount;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }

        function checkEmpty() {
            const list = document.getElementById('notification-list');
            if (list && list.querySelectorAll('li').length === 0) {
                const empty = document.createElement('li');
                empty.className = 'px-3 py-3 text-muted text-center';
                empty.textContent = 'No new notifications';
                list.appendChild(empty);
            }
        }
    });

</script>
