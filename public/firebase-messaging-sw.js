importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyD2-tVHXiR3i-2FeQkvmG74yA1uH0dFwh4",
  authDomain: "crm-jbl.firebaseapp.com",
  projectId: "crm-jbl",
  storageBucket: "crm-jbl.firebasestorage.app",
  messagingSenderId: "1059762873731",
  appId: "1:1059762873731:web:950b5f5dc79709b301c2b6",
  measurementId: "G-PZG4C1XH57"
});

const messaging = firebase.messaging();

// Handle background messages (panel is closed/minimized)
messaging.onBackgroundMessage(payload => {
  self.registration.showNotification(payload.notification.title, {
    body: payload.notification.body,
    icon: '/images/janata-bank/jb.png'
  });
});