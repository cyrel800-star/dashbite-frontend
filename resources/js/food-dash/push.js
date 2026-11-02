// ============================================
// PUSH NOTIFICATIONS SUBSCRIPTION
// ============================================

/**
 * Convert VAPID public key from base64 to Uint8Array.
 */
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
}

/**
 * Check kung supported ang push notifications.
 */
export function isPushSupported() {
    return 'serviceWorker' in navigator &&
           'PushManager' in window &&
           'Notification' in window;
}

/**
 * Kunin ang current permission status.
 */
export function getPermissionStatus() {
    if (!isPushSupported()) {
        return 'unsupported';
    }
    return Notification.permission;
}

/**
 * Mag-subscribe sa push notifications.
 */
export async function subscribeToPush() {
    if (!isPushSupported()) {
        throw new Error('Push notifications are not supported on this browser.');
    }

    // 1. Request permission
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        throw new Error('Permission denied.');
    }

    // 2. Register service worker
    const registration = await navigator.serviceWorker.ready;

    // 3. Get VAPID public key from server
    const vapidResponse = await fetch('/push/vapid-public-key', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    });
    const { publicKey } = await vapidResponse.json();

    if (!publicKey) {
        throw new Error('VAPID public key not configured on server.');
    }

    // 4. Subscribe to push
    const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(publicKey),
    });

    // 5. Send subscription to server
    await sendSubscriptionToServer(subscription);

    return subscription;
}

/**
 * I-save ang subscription sa server.
 */
async function sendSubscriptionToServer(subscription) {
    const response = await fetch('/push/subscribe', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(subscription.toJSON()),
    });

    if (!response.ok) {
        throw new Error('Failed to save subscription on server.');
    }

    return response.json();
}

/**
 * I-unsubscribe sa push notifications.
 */
export async function unsubscribeFromPush() {
    if (!isPushSupported()) return;

    const registration = await navigator.serviceWorker.ready;
    const subscription = await registration.pushManager.getSubscription();

    if (!subscription) {
        return;
    }

    const endpoint = subscription.endpoint;

    // 1. Unsubscribe from browser
    await subscription.unsubscribe();

    // 2. Remove from server
    await fetch('/push/unsubscribe', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ endpoint }),
    });
}

/**
 * Check kung naka-subscribe ang user.
 */
export async function isSubscribed() {
    if (!isPushSupported()) return false;

    const registration = await navigator.serviceWorker.ready;
    const subscription = await registration.pushManager.getSubscription();

    return subscription !== null;
}