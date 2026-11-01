@auth
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.13.0/firebase-app.js";
    import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.13.0/firebase-messaging.js";
        
    const app = initializeApp({
        apiKey: "AIzaSyD2-tVHXiR3i-2FeQkvmG74yA1uH0dFwh4",
        authDomain: "crm-jbl.firebaseapp.com",
        projectId: "crm-jbl",
        storageBucket: "crm-jbl.firebasestorage.app",
        messagingSenderId: "1059762873731",
        appId: "1:1059762873731:web:950b5f5dc79709b301c2b6",
        measurementId: "G-PZG4C1XH57"
    });
    
    const messaging  = getMessaging(app);
    const isDashboard = {{ request()->routeIs('dashboard') ? 'true' : 'false' }};

    Notification.requestPermission().then(async permission => {
        if (permission !== 'granted') return;

        const registration = await navigator.serviceWorker.register(
            '{{ asset("firebase-messaging-sw.js") }}'
        );

        if (isDashboard) {
            const token = await getToken(messaging, {
                vapidKey: 'BJRVLhSBOQ7LeSlPKjesJHaalhG1v2bF7LSiRXrc4Wy2Cb9cQ1NZ-A_2WWimPw6ehOle0EgYfnqtQ9l3snfjqoo',
                serviceWorkerRegistration: registration
            });

            if (token) {
                fetch('{{ route("update-web-fcm") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="_token"]').content
                    },
                    body: JSON.stringify({ token })
                });
            }
        }
    });

    onMessage(messaging, (payload) => {
        const title = payload.notification?.title ?? payload.data?.title ?? 'Notification';
        const body  = payload.notification?.body  ?? payload.data?.body  ?? '';
        if (Notification.permission === 'granted') {
            navigator.serviceWorker.ready.then(sw => {
                sw.showNotification(title, { body, icon: '{{ asset("images/janata-bank/jb.png") }}' });
            });
        }
    });
</script>
@endauth
