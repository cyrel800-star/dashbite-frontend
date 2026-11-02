import Alpine from 'alpinejs';
import '../css/customer.css';
import { mountPortal } from './portal';

window.Alpine = Alpine;

Alpine.data('themeToggle', () => ({
    theme: localStorage.getItem('theme') || 'light',

    init() {
        this.applyTheme();
        this.$watch('theme', () => this.applyTheme());
    },

    setTheme(theme) {
        this.theme = theme;
        localStorage.setItem('theme', theme);
    },

    applyTheme() {
        document.documentElement.classList.toggle('dark', this.theme === 'dark');
    },
}));

Alpine.data('restaurantOrder', (menu, deliveryFee) => ({
    menu,
    deliveryFee,
    cart: {},
    paymentMethod: 'cod',
    latitude: '',
    longitude: '',
    locationMessage: '',

    quantity(id) {
        return this.cart[id] || 0;
    },

    increment(id) {
        this.cart[id] = this.quantity(id) + 1;
    },

    decrement(id) {
        const quantity = this.quantity(id) - 1;
        if (quantity > 0) {
            this.cart[id] = quantity;
        } else {
            delete this.cart[id];
        }
    },

    get cartLines() {
        return this.menu
            .filter((item) => this.quantity(item.id) > 0)
            .map((item) => ({ ...item, quantity: this.quantity(item.id) }));
    },

    get subtotal() {
        return this.cartLines.reduce((sum, item) => sum + item.price * item.quantity, 0);
    },

    get total() {
        return this.subtotal + (this.cartLines.length ? this.deliveryFee : 0);
    },

    money(amount) {
        return `₱${Number(amount).toFixed(2)}`;
    },

    useCurrentLocation() {
        if (!navigator.geolocation) {
            this.locationMessage = 'Location services are unavailable in this browser.';
            return;
        }

        this.locationMessage = 'Getting your location…';
        navigator.geolocation.getCurrentPosition(
            ({ coords }) => {
                this.latitude = coords.latitude.toFixed(7);
                this.longitude = coords.longitude.toFixed(7);
                this.locationMessage = 'Location saved. Enter your delivery address above.';
            },
            () => {
                this.locationMessage = 'Unable to access your location. You can still enter your address manually.';
            },
            { enableHighAccuracy: true, timeout: 10000 },
        );
    },
}));

Alpine.start();

const portalHost = document.querySelector('#portal-app');
const customerHeader = document.querySelector('.customer-header');
const customerMain = document.querySelector('body > main');
let unmountPortal = () => {};

const showPortal = ({ token, user }) => {
    if (!portalHost || !token || !user) return;

    customerHeader?.setAttribute('hidden', '');
    customerMain?.setAttribute('hidden', '');
    portalHost.style.position = 'fixed';
    portalHost.style.inset = '0';
    portalHost.style.zIndex = '9999';
    portalHost.style.overflowY = 'auto';
    portalHost.style.overscrollBehaviorY = 'contain';
    portalHost.style.webkitOverflowScrolling = 'touch';
    portalHost.hidden = false;
    unmountPortal();
    unmountPortal = mountPortal(portalHost, {
        token,
        user,
        onLogout: () => {
            sessionStorage.removeItem('dashbite.auth.token');
            sessionStorage.removeItem('dashbite.auth.user');
            unmountPortal();
            window.location.assign('/restaurants');
        },
    });
};

window.addEventListener('dashbite:show-portal', (event) => showPortal(event.detail));

const savedToken = sessionStorage.getItem('dashbite.auth.token');
const savedUser = sessionStorage.getItem('dashbite.auth.user');

if (savedToken && savedUser) {
    try {
        const user = JSON.parse(savedUser);
        if (user?.name && user?.email && user?.role && user?.account_status) {
            showPortal({ token: savedToken, user });
        } else {
            sessionStorage.removeItem('dashbite.auth.token');
            sessionStorage.removeItem('dashbite.auth.user');
        }
    } catch {
        sessionStorage.removeItem('dashbite.auth.token');
        sessionStorage.removeItem('dashbite.auth.user');
    }
}
