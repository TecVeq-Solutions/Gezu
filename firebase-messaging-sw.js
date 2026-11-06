// GEZUX Firebase Cloud Messaging Service Worker
importScripts('https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.8.0/firebase-messaging-compat.js');

// Initialize the Firebase app in the service worker
firebase.initializeApp({
  apiKey: "firebase-web-api-key",
  authDomain: "gezux-marketplace.firebaseapp.com",
  projectId: "gezux-marketplace",
  storageBucket: "gezux-marketplace.appspot.com",
  messagingSenderId: "1234567890",
  appId: "1:1234567890:web:abcdef123456"
});

const messaging = firebase.messaging();

// Background Push Notification Handler
messaging.onBackgroundMessage((payload) => {
  console.log('[GEZUX SW] Received background push message: ', payload);

  const title = payload.notification?.title || payload.data?.title || 'GEZUX Platform Alert';
  const body = payload.notification?.body || payload.data?.body || 'You have a new update on GEZUX.';
  const icon = payload.notification?.icon || '/images/gezux-favicon.jpg';
  const clickAction = payload.data?.url || payload.notification?.click_action || '/admin';

  const notificationOptions = {
    body: body,
    icon: icon,
    badge: '/images/gezux-favicon.jpg',
    data: {
      url: clickAction,
      time: new Date().toISOString()
    }
  };

  self.registration.showNotification(title, notificationOptions);
});

// Notification Click Handler
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const targetUrl = event.notification.data?.url || '/admin';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
      for (let client of windowClients) {
        if (client.url.includes(self.location.origin) && 'focus' in client) {
          client.navigate(targetUrl);
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});
