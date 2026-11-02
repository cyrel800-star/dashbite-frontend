import { renderRestaurantProfile } from './restaurant-profile';

const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
}[character]));

const currency = (value) => `₱${Number(value || 0).toFixed(2)}`;

async function request(path, token, options = {}) {
    const headers = { Accept: 'application/json' };

    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    if (options.body !== undefined) {
        headers['Content-Type'] = 'application/json';
    }

    const response = await fetch(path, {
        method: options.method || 'GET',
        headers,
        body: options.body === undefined ? undefined : JSON.stringify(options.body),
    });
    const text = await response.text();
    let payload = null;

    if (text) {
        try {
            payload = JSON.parse(text);
        } catch {
            payload = null;
        }
    }

    if (!response.ok) {
        const validationMessage = payload?.errors
            ? Object.values(payload.errors).flat().find((error) => typeof error === 'string')
            : null;
        throw new Error(validationMessage || payload?.message || `Request failed (${response.status}).`);
    }

    return payload;
}

const styles = `
    :host { display: block; min-height: 100vh; color: #242424; font: 15px/1.5 Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif; }
    * { box-sizing: border-box; }
    button, input, textarea { font: inherit; }
    button { cursor: pointer; }
    .fd-portal { min-height: 100vh; background: #f7f7f5; }
    .fd-topbar { height: 72px; display: flex; align-items: center; justify-content: space-between; padding: 0 34px; background: #fff; border-bottom: 1px solid #eae9e6; }
    .fd-brand { display: inline-flex; align-items: center; gap: 11px; color: #242424; font-size: 19px; font-weight: 750; letter-spacing: -.5px; text-decoration: none; }
    .fd-brand img { width: 36px; height: 36px; }
    .fd-brand-accent { color: #ff6b35; }
    .fd-top-user { display: flex; align-items: center; gap: 14px; }
    .fd-user-copy { text-align: right; line-height: 1.3; }
    .fd-user-name { font-size: 13px; font-weight: 650; }
    .fd-user-role { margin-top: 3px; color: #898781; font-size: 11px; text-transform: capitalize; }
    .fd-avatar { display: grid; width: 38px; height: 38px; place-items: center; border: 0; border-radius: 50%; background: #fff0e9; color: #e85c2b; font-weight: 700; }
    .fd-logout { border: 1px solid #e7e5e1; border-radius: 9px; background: #fff; padding: 8px 13px; color: #5b5954; font-size: 12px; font-weight: 600; }
    .fd-logout:hover { border-color: #ff6b35; color: #e85c2b; }
    .fd-frame { display: grid; min-height: calc(100vh - 72px); grid-template-columns: 248px minmax(0, 1fr); }
    .fd-sidebar { display: flex; flex-direction: column; gap: 7px; padding: 28px 17px; border-right: 1px solid #eae9e6; background: #fff; }
    .fd-side-label { margin: 0 10px 11px; color: #a09d96; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
    .fd-nav { display: flex; align-items: center; gap: 12px; width: 100%; border: 0; border-radius: 10px; background: transparent; padding: 11px 12px; color: #64615c; text-align: left; font-size: 13px; font-weight: 550; }
    .fd-nav:hover { background: #faf6f3; color: #dc5727; }
    .fd-nav.is-active { background: #fff0e9; color: #df5b2b; font-weight: 700; }
    .fd-nav svg { width: 18px; height: 18px; flex: 0 0 auto; }
    .fd-nav-soon { margin-left: auto; color: #aaa69f; font-size: 9px; font-weight: 600; }
    .fd-sidebar-note { margin: auto 5px 0; border: 1px solid #f1e4dc; border-radius: 11px; background: #fffaf7; padding: 13px; color: #756d65; font-size: 11px; }
    .fd-sidebar-note strong { display: block; margin-bottom: 4px; color: #4a423d; font-size: 12px; }
    .fd-main { min-width: 0; padding: 38px clamp(20px, 4vw, 58px) 55px; }
    .fd-content { width: 100%; max-width: 1060px; margin: 0 auto; }
    .fd-kicker { color: #e46638; font-size: 10px; font-weight: 750; letter-spacing: 1.4px; text-transform: uppercase; }
    .fd-heading-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; margin-bottom: 24px; }
    .fd-title { margin: 5px 0 5px; color: #242424; font-size: clamp(24px, 3vw, 32px); font-weight: 740; letter-spacing: -.9px; line-height: 1.2; }
    .fd-subtitle { margin: 0; color: #85817b; font-size: 13px; }
    .fd-card { border: 1px solid #eae8e4; border-radius: 14px; background: #fff; box-shadow: 0 3px 12px rgba(40, 35, 29, .025); }
    .fd-card-pad { padding: 21px; }
    .fd-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 18px; }
    .fd-search { width: min(100%, 350px); min-height: 42px; border: 1px solid #e7e5e1; border-radius: 9px; outline: 0; background: #fff; padding: 9px 13px; color: #282724; }
    .fd-search:focus, .fd-input:focus, .fd-textarea:focus { border-color: #ff986f; box-shadow: 0 0 0 3px rgba(255, 107, 53, .12); outline: 0; }
    .fd-count { color: #88847d; font-size: 12px; white-space: nowrap; }
    .fd-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(245px, 1fr)); gap: 15px; }
    .fd-restaurant-card { overflow: hidden; }
    .fd-restaurant-art { display: grid; height: 113px; place-items: center; background: radial-gradient(circle at 75% 15%, #ffe1d2, transparent 40%), linear-gradient(135deg, #fff5ec, #f8eee5); color: #ee7950; }
    .fd-restaurant-art svg { width: 43px; height: 43px; }
    .fd-restaurant-info { padding: 16px; }
    .fd-card-title { margin: 0; color: #292825; font-size: 15px; font-weight: 700; }
    .fd-muted { margin: 6px 0 0; color: #89857f; font-size: 12px; }
    .fd-card-bottom { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-top: 15px; }
    .fd-status { display: inline-flex; align-items: center; gap: 6px; color: #46835a; font-size: 11px; font-weight: 650; }
    .fd-status::before { width: 7px; height: 7px; border-radius: 50%; background: currentColor; content: ""; }
    .fd-status.is-closed { color: #a26b45; }
    .fd-button { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 39px; border: 1px solid transparent; border-radius: 9px; background: #ff6b35; padding: 9px 14px; color: #fff; font-size: 12px; font-weight: 700; }
    .fd-button:hover { background: #ed5c29; }
    .fd-button:disabled { cursor: not-allowed; opacity: .48; }
    .fd-button-secondary { border-color: #e6e3df; background: #fff; color: #514e49; }
    .fd-button-secondary:hover { border-color: #f6a17f; background: #fffaf7; color: #d85122; }
    .fd-button-danger { border-color: #f2d8d2; background: #fff; color: #b44937; }
    .fd-button-danger:hover { background: #fff4f1; }
    .fd-button-small { min-height: 32px; padding: 6px 10px; font-size: 11px; }
    .fd-notice { margin: 0 0 18px; border: 1px solid #f0dfc7; border-radius: 10px; background: #fffaf1; padding: 12px 14px; color: #775c36; font-size: 12px; }
    .fd-notice.is-error { border-color: #f1d0cc; background: #fff6f5; color: #a33f35; }
    .fd-notice.is-success { border-color: #d5e9d8; background: #f4fbf4; color: #3e7048; }
    .fd-empty { padding: 44px 20px; color: #88847d; text-align: center; font-size: 13px; }
    .fd-back { margin-bottom: 17px; border: 0; background: transparent; padding: 0; color: #d95c30; font-size: 12px; font-weight: 650; }
    .fd-detail-layout { display: grid; grid-template-columns: minmax(0, 1fr) 295px; align-items: start; gap: 18px; }
    .fd-section-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; border-bottom: 1px solid #efede9; padding: 17px 20px; }
    .fd-section-head h2 { margin: 0; color: #33312e; font-size: 14px; font-weight: 700; }
    .fd-menu-row { display: flex; align-items: center; justify-content: space-between; gap: 15px; border-bottom: 1px solid #f0efec; padding: 17px 20px; }
    .fd-menu-row:last-child { border-bottom: 0; }
    .fd-menu-copy { min-width: 0; }
    .fd-menu-meta { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; margin-top: 5px; }
    .fd-menu-name { margin: 0; color: #35332f; font-size: 13px; font-weight: 680; }
    .fd-price { margin-top: 5px; color: #e26537; font-size: 12px; font-weight: 700; }
    .fd-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .fd-form-field { display: flex; flex-direction: column; gap: 5px; }
    .fd-form-field.is-wide { grid-column: 1 / -1; }
    .fd-label { color: #56534e; font-size: 11px; font-weight: 650; }
    .fd-input, .fd-textarea { width: 100%; min-height: 39px; border: 1px solid #e5e3de; border-radius: 8px; background: #fff; padding: 9px 11px; color: #262522; font-size: 12px; }
    .fd-textarea { min-height: 72px; resize: vertical; }
    .fd-form-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 12px; }
    .fd-inline-actions { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; }
    .fd-tag { display: inline-flex; border-radius: 999px; background: #f1f0ed; padding: 4px 8px; color: #77736c; font-size: 10px; font-weight: 650; }
    .fd-tag.is-available { background: #eaf6ed; color: #458355; }
    .fd-tag.is-unavailable { background: #f7eeee; color: #a65c4d; }
    .fd-stat-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
    .fd-stat { padding: 17px 19px; }
    .fd-stat-label { color: #89857f; font-size: 11px; }
    .fd-stat-value { margin-top: 5px; color: #292825; font-size: 22px; font-weight: 730; letter-spacing: -.5px; }
    .fd-callout { display: flex; align-items: flex-start; gap: 13px; border: 1px solid #f0dfc7; border-radius: 12px; background: #fffaf1; padding: 16px; color: #775c36; font-size: 12px; }
    .fd-callout strong { display: block; margin-bottom: 4px; color: #5e4d32; font-size: 13px; }
    .fd-callout-icon { display: grid; width: 29px; height: 29px; flex: 0 0 auto; place-items: center; border-radius: 50%; background: #ffefd7; font-weight: 800; }
    .fd-callout.is-info { border-color: #dae5ef; background: #f6f9fc; color: #526b81; }
    .fd-callout.is-info strong { color: #40596e; }
    .fd-toggle-row { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 18px 20px; }
    .fd-toggle { position: relative; width: 46px; height: 26px; flex: 0 0 auto; border: 0; border-radius: 30px; background: #d5d3cf; transition: background .15s ease; }
    .fd-toggle::after { position: absolute; top: 3px; left: 3px; width: 20px; height: 20px; border-radius: 50%; background: white; box-shadow: 0 1px 3px #0002; content: ""; transition: transform .15s ease; }
    .fd-toggle.is-on { background: #49a465; }
    .fd-toggle.is-on::after { transform: translateX(20px); }
    .fd-loading { padding: 36px; color: #89857f; text-align: center; font-size: 12px; }
    .fd-modal-backdrop { position: fixed; inset: 0; z-index: 20; display: flex; align-items: center; justify-content: center; overflow-y: auto; background: rgba(25, 24, 22, .58); padding: 20px; }
    .fd-modal-card { width: min(100%, 520px); margin: auto; }
    @media (max-width: 800px) { .fd-frame { grid-template-columns: 1fr; } .fd-sidebar { flex-direction: row; overflow-x: auto; border-right: 0; border-bottom: 1px solid #eae9e6; padding: 10px 14px; } .fd-side-label, .fd-sidebar-note { display: none; } .fd-nav { width: auto; flex: 0 0 auto; padding: 9px 11px; white-space: nowrap; } .fd-nav-soon { display: none; } .fd-main { padding-top: 26px; } }
    @media (max-width: 600px) { .fd-topbar { height: 62px; padding: 0 12px; } .fd-top-user { gap: 8px; } .fd-logout { padding: 7px 10px; } .fd-brand { gap: 7px; font-size: 16px; } .fd-brand img { width: 30px; height: 30px; } .fd-user-copy { display: none; } .fd-frame { min-height: calc(100vh - 62px); } .fd-sidebar { padding: 8px 10px; } .fd-nav { gap: 8px; padding: 8px 10px; font-size: 12px; } .fd-nav svg { width: 16px; height: 16px; } .fd-main { padding: 22px 14px 36px; } .fd-heading-row { flex-direction: column; gap: 8px; margin-bottom: 18px; } .fd-title { font-size: 25px; } .fd-toolbar { align-items: stretch; flex-direction: column; gap: 8px; } .fd-search { width: 100%; } .fd-detail-layout { grid-template-columns: 1fr; gap: 12px; } .fd-stat-grid { grid-template-columns: 1fr; gap: 9px; } .fd-stat { padding: 13px 15px; } .fd-stat-value { font-size: 19px; } .fd-menu-row { align-items: flex-start; gap: 9px; padding: 14px 13px; } .fd-menu-row > .fd-button { flex: 0 0 auto; align-self: center; } .fd-section-head { padding: 14px 13px; } .fd-card-pad { padding: 15px; } .fd-inline-actions { gap: 5px; } .fd-inline-actions .fd-button-small { padding: 6px 8px; } .fd-grid { grid-template-columns: 1fr; } }
`;

const icons = {
    restaurants: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01M16 13v.01M16 16v.01M16 19v.01"/></svg>',
    basket: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="m3 9 2 11h14l2-11H3Zm0 0h18M8 9l4-6m4 6-4-6M9 13v3m6-3v3"/></svg>',
    menu: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>',
    orders: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h10l3 3v15H4V3h3Zm0 0v5h10V3M8 13h8m-8 4h8"/></svg>',
    rider: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16V6h11v10H3Zm11-6h4l3 3v3h-7v-6ZM7 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm11 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/></svg>',
};

function navigationFor(role) {
    if (role === 'customer') {
        return [
            { id: 'restaurants', label: 'Explore restaurants', icon: icons.restaurants },
            { id: 'basket', label: 'Your basket', icon: icons.basket },
            { id: 'orders', label: 'Orders', icon: icons.orders, unavailable: true },
        ];
    }

    if (role === 'restaurant') {
        return [
            { id: 'menu', label: 'Menu management', icon: icons.menu },
            { id: 'preview', label: 'Menu preview', icon: icons.restaurants },
            { id: 'orders', label: 'Incoming orders', icon: icons.orders },
            { id: 'reviews', label: 'Reviews', icon: icons.orders },
        ];
    }

    if (role === 'rider') {
        return [
        { id: 'availability', label: 'Availability', icon: icons.rider },
        { id: 'deliveries', label: 'Deliveries', icon: icons.orders, unavailable: true },
        ];
    }

    return [{ id: 'admin', label: 'Admin panel', icon: icons.orders, unavailable: true }];
}

export function mountPortal(host, { token, user, onLogout }) {
    const shadow = host.shadowRoot || host.attachShadow({ mode: 'open' });
    const originalTitle = document.title;
    const state = {
        page: user.role === 'customer'
            ? 'restaurants'
            : user.role === 'restaurant'
                ? 'menu'
                : user.role === 'rider'
                    ? 'availability'
                    : 'admin',
        notice: '',
        noticeType: '',
        busy: false,
        query: '',
        restaurants: [],
        selectedRestaurant: null,
        menu: [],
        restaurantProfile: null,
        orders: [],
        reviews: [],
        basket: {},
        basketItems: {},
        editingItem: null,
        showAddItem: false,
        availabilityRequest: null,
    };

    const nav = navigationFor(user.role);
    const roleLabel = user.role === 'restaurant' ? 'Restaurant' : user.role;
    const initials = String(user.name || user.email || '?')
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part.charAt(0))
        .join('')
        .toUpperCase();
    document.title = `${roleLabel} panel | DashBite`;

    shadow.innerHTML = `
        <link rel="stylesheet" href="/food-dash/app.css">
        <style>${styles}</style>
        <div class="fd-portal">
            <header class="fd-topbar">
                <a class="fd-brand" href="/" aria-label="DashBite home">
                    <img src="/images/dashbite-mark.svg" alt="">
                    <span>Dash<span class="fd-brand-accent">Bite</span></span>
                </a>
                <div class="fd-top-user">
                    <div class="fd-user-copy">
                        <div class="fd-user-name">${escapeHtml(user.name)}</div>
                        <div class="fd-user-role">${escapeHtml(roleLabel)} panel</div>
                    </div>
                    ${user.role === 'restaurant'
                ? `<button class="fd-avatar" type="button" data-action="open-profile" aria-label="Restaurant profile settings" title="Restaurant profile settings">${escapeHtml(initials)}</button>`
                : `<div class="fd-avatar" aria-hidden="true">${escapeHtml(initials)}</div>`}
                    <button class="fd-logout" type="button" data-action="logout">Sign out</button>
                </div>
            </header>
            <div class="fd-frame">
                <nav class="fd-sidebar" aria-label="${escapeHtml(roleLabel)} navigation">
                    <p class="fd-side-label">Workspace</p>
                    ${nav.map((item) => `
                        <button class="fd-nav${state.page === item.id ? ' is-active' : ''}" type="button" data-page="${item.id}" aria-current="${state.page === item.id ? 'page' : 'false'}">
                            ${item.icon}<span>${item.label}</span>${item.unavailable ? '<span class="fd-nav-soon">API NEEDED</span>' : ''}
                        </button>
                    `).join('')}
                    <div class="fd-sidebar-note">
                        <strong>${escapeHtml(roleLabel)} workspace</strong>
                        Features shown here are limited to the APIs currently available in this system.
                    </div>
                </nav>
                <main class="fd-main">
                    <div class="fd-content">
                        <div id="portal-notice" aria-live="polite"></div>
                        <div id="portal-content"><div class="fd-loading">Loading your workspace…</div></div>
                    </div>
                </main>
            </div>
        </div>`;

    const content = shadow.querySelector('#portal-content');
    const notice = shadow.querySelector('#portal-notice');

    function setNotice(message = '', type = '') {
        state.notice = message;
        state.noticeType = type;
        notice.innerHTML = message
            ? `<div class="fd-notice${type ? ` is-${type}` : ''}" role="${type === 'error' ? 'alert' : 'status'}">${escapeHtml(message)}</div>`
            : '';
    }

    function heading(kicker, title, subtitle, action = '') {
        return `<div class="fd-heading-row"><div><div class="fd-kicker">${kicker}</div><h1 class="fd-title">${title}</h1><p class="fd-subtitle">${subtitle}</p></div>${action}</div>`;
    }

    function restaurantCards() {
        const query = state.query.trim().toLowerCase();
        const matches = state.restaurants.filter((restaurant) => (
            !query || `${restaurant.name} ${restaurant.address}`.toLowerCase().includes(query)
        ));

        if (!matches.length) {
            return state.restaurants.length
                ? `<div class="fd-card fd-empty" role="status"><h2 class="fd-card-title">No restaurants found</h2><p class="fd-muted">Try another restaurant name or location.</p><button class="fd-button fd-button-secondary fd-button-small" style="margin-top:14px" type="button" data-action="clear-search">Clear search</button></div>`
                : `<div class="fd-card fd-empty" role="status"><h2 class="fd-card-title">No restaurants available right now</h2><p class="fd-muted">There are no open restaurants to browse. Please check back soon.</p><button class="fd-button fd-button-secondary fd-button-small" style="margin-top:14px" type="button" data-action="retry">Refresh restaurants</button></div>`;
        }

        return `<div class="fd-grid" aria-label="Available restaurants">${matches.map((restaurant) => `
            <article class="fd-card fd-restaurant-card">
                <div class="fd-restaurant-art" aria-hidden="true">
                    <svg fill="none" viewBox="0 0 48 48" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20h30l-3 20H12L9 20Zm7 0v-5a8 8 0 0 1 16 0v5M18 29h12"/></svg>
                </div>
                <div class="fd-restaurant-info">
                    <h2 class="fd-card-title">${escapeHtml(restaurant.name)}</h2>
                    <p class="fd-muted">${escapeHtml(restaurant.address || 'Address not provided')}</p>
                    <div class="fd-card-bottom">
                        <span class="fd-status${restaurant.is_open ? '' : ' is-closed'}">${restaurant.is_open ? 'Open now' : 'Closed'}</span>
                        <button class="fd-button fd-button-small" type="button" data-action="open-restaurant" data-id="${Number(restaurant.id)}" ${restaurant.is_open ? '' : 'disabled'}>View menu</button>
                    </div>
                </div>
            </article>
        `).join('')}</div>`;
    }

    function customerRestaurants() {
        if (state.selectedRestaurant) {
            const restaurant = state.selectedRestaurant;
            return `
                <button class="fd-back" type="button" data-action="back-restaurants">← All restaurants</button>
                ${heading('Restaurant menu', escapeHtml(restaurant.name), escapeHtml(restaurant.address || 'Menu and item availability'))}
                <div class="fd-detail-layout">
                    <section class="fd-card">
                        <div class="fd-section-head"><h2>Menu</h2><span class="fd-count">${state.menu.length} available ${state.menu.length === 1 ? 'item' : 'items'}</span></div>
                        ${state.menu.length ? state.menu.map((item) => `
                            <div class="fd-menu-row">
                                <div class="fd-menu-copy"><h3 class="fd-menu-name">${escapeHtml(item.name)}</h3><p class="fd-muted">${escapeHtml(item.description || 'Made fresh to order')}</p><div class="fd-menu-meta"><span class="fd-price">${currency(item.price)}</span><span class="fd-tag ${item.is_available ? 'is-available' : 'is-unavailable'}">${item.is_available ? 'Available' : 'Unavailable'}</span></div></div>
                                <button class="fd-button fd-button-secondary fd-button-small" type="button" data-action="add-basket" data-id="${Number(item.id)}" ${item.is_available ? '' : 'disabled'}>${item.is_available ? 'Add' : 'Unavailable'}</button>
                            </div>
                        `).join('') : '<div class="fd-empty" role="status"><h3 class="fd-card-title">No available items</h3><p class="fd-muted">This restaurant has no menu items available right now.</p></div>'}
                    </section>
                    <aside class="fd-card fd-card-pad">
                        <div class="fd-kicker">Your basket</div>
                        <h2 class="fd-card-title" style="margin-top:5px">Ready when you are</h2>
                        ${basketSummary()}
                        <div class="fd-notice" style="margin:15px 0 0">Ordering is not available yet: this system has no customer order or checkout API.</div>
                        <button class="fd-button" style="width:100%" type="button" disabled title="Customer order API is not available">Checkout unavailable</button>
                    </aside>
                </div>`;
        }

        return `
            ${heading('Customer workspace', 'Find your next favorite', 'Browse restaurants currently open for ordering.')}
            <div class="fd-toolbar">
                <input class="fd-search" type="search" data-search placeholder="Search restaurants or locations…" value="${escapeHtml(state.query)}" aria-label="Search restaurants">
                <span class="fd-count" aria-live="polite">${state.restaurants.filter((restaurant) => {
                    const query = state.query.trim().toLowerCase();
                    return !query || `${restaurant.name} ${restaurant.address}`.toLowerCase().includes(query);
                }).length} of ${state.restaurants.length} restaurants</span>
            </div>
            ${restaurantCards()}`;
    }

    function basketLines() {
        return Object.entries(state.basket)
            .filter(([, quantity]) => quantity > 0)
            .map(([id, quantity]) => {
                const item = state.basketItems[id];
                return item ? { ...item, quantity } : null;
            })
            .filter(Boolean);
    }

    function basketSummary() {
        const lines = basketLines();

        if (!lines.length) {
            return '<p class="fd-muted" style="margin-top:14px">Your basket is empty. Add items from a restaurant menu.</p>';
        }

        return `
            <div style="margin-top:15px">
                ${lines.map((item) => `
                    <div class="fd-menu-row" style="padding:12px 0">
                        <div><h3 class="fd-menu-name">${escapeHtml(item.name)}</h3><p class="fd-muted">${item.quantity} × ${currency(item.price)}${item.restaurant_name ? ` · ${escapeHtml(item.restaurant_name)}` : ''}</p></div>
                        <div class="fd-inline-actions">
                            <button class="fd-button fd-button-secondary fd-button-small" type="button" data-action="basket-dec" data-id="${Number(item.id)}" aria-label="Remove one ${escapeHtml(item.name)}">−</button>
                            <span class="fd-count">${item.quantity}</span>
                            <button class="fd-button fd-button-secondary fd-button-small" type="button" data-action="basket-inc" data-id="${Number(item.id)}" aria-label="Add one ${escapeHtml(item.name)}">+</button>
                            <button class="fd-button fd-button-danger fd-button-small" type="button" data-action="basket-remove" data-id="${Number(item.id)}" aria-label="Remove ${escapeHtml(item.name)} from basket">Remove</button>
                        </div>
                    </div>
                `).join('')}
                <div class="fd-card-bottom"><strong>Subtotal</strong><strong>${currency(lines.reduce((sum, item) => sum + Number(item.price) * item.quantity, 0))}</strong></div>
            </div>`;
    }

    function customerBasket() {
        const lines = basketLines();
        const itemCount = lines.reduce((sum, item) => sum + item.quantity, 0);

        return `
            ${heading('Customer workspace', 'Your basket', 'Review the items you have added while browsing.')}
            <div class="fd-detail-layout">
                <section class="fd-card">
                    <div class="fd-section-head"><h2>Selected items</h2><span class="fd-count">${itemCount} ${itemCount === 1 ? 'item' : 'items'}</span></div>
                    ${lines.length
                        ? basketSummary()
                        : '<div class="fd-empty" role="status"><h3 class="fd-card-title">Your basket is empty</h3><p class="fd-muted">Browse a restaurant menu to add items to your basket.</p><button class="fd-button fd-button-secondary fd-button-small" style="margin-top:14px" type="button" data-action="go-restaurants">Browse restaurants</button></div>'}
                </section>
                <aside class="fd-card fd-card-pad">
                    <div class="fd-kicker">Next step</div>
                    <h2 class="fd-card-title" style="margin-top:5px">Checkout is not connected</h2>
                    <p class="fd-muted">Your basket is a local preview only. No order will be placed or saved.</p>
                    <div class="fd-notice" style="margin:15px 0 0">The available API does not include customer ordering or checkout.</div>
                    <button class="fd-button" style="width:100%" type="button" disabled>Checkout unavailable</button>
                </aside>
            </div>`;
    }

    function menuItemCard(item) {
        const available = Boolean(item.is_available);

        if (state.editingItem === item.id) {
            return `
                <form class="fd-menu-row" data-form="edit-menu-item" data-id="${Number(item.id)}">
                    <div class="fd-form-grid" style="width:100%">
                        <label class="fd-form-field"><span class="fd-label">Item name</span><input class="fd-input" name="name" maxlength="255" value="${escapeHtml(item.name)}" required></label>
                        <label class="fd-form-field"><span class="fd-label">Price (₱)</span><input class="fd-input" name="price" type="number" step="0.01" min="0.01" value="${Number(item.price)}" required></label>
                        <label class="fd-form-field is-wide"><span class="fd-label">Description</span><textarea class="fd-textarea" name="description">${escapeHtml(item.description || '')}</textarea></label>
                        <div class="fd-form-actions is-wide"><button class="fd-button fd-button-secondary fd-button-small" type="button" data-action="cancel-edit">Cancel</button><button class="fd-button fd-button-small" type="submit">Save changes</button></div>
                    </div>
                </form>`;
        }

        return `
            <div class="fd-menu-row">
                <div><h3 class="fd-menu-name">${escapeHtml(item.name)}</h3><p class="fd-muted">${escapeHtml(item.description || 'No description')}</p><div class="fd-inline-actions" style="margin-top:8px"><span class="fd-price">${currency(item.price)}</span><span class="fd-tag ${available ? 'is-available' : 'is-unavailable'}">${available ? 'Available' : 'Unavailable'}</span></div></div>
                <div class="fd-inline-actions">
                    <button class="fd-button fd-button-secondary fd-button-small" type="button" data-action="edit-item" data-id="${Number(item.id)}">Edit</button>
                    <button class="fd-button fd-button-secondary fd-button-small" type="button" data-action="toggle-item" data-id="${Number(item.id)}">${available ? 'Mark unavailable' : 'Mark available'}</button>
                    <button class="fd-button fd-button-danger fd-button-small" type="button" data-action="delete-item" data-id="${Number(item.id)}">Delete</button>
                </div>
            </div>`;
    }

    function restaurantMenu() {
        return `
            ${heading('Restaurant workspace', 'Manage your menu', 'Changes are saved to this restaurant’s menu through the existing API.')}
            <div class="fd-stat-grid">
                <div class="fd-card fd-stat"><div class="fd-stat-label">Total menu items</div><div class="fd-stat-value">${state.menu.length}</div></div>
                <div class="fd-card fd-stat"><div class="fd-stat-label">Available now</div><div class="fd-stat-value">${state.menu.filter((item) => item.is_available).length}</div></div>
                <div class="fd-card fd-stat"><div class="fd-stat-label">Unavailable</div><div class="fd-stat-value">${state.menu.filter((item) => !item.is_available).length}</div></div>
            </div>
            <section class="fd-card">
                <div class="fd-section-head">
                    <h2>Your menu items</h2>
                    <button class="fd-button fd-button-small" style="width:38px;height:38px;padding:0" type="button" data-action="new-item" aria-label="Add item" title="Add item">
                        <svg aria-hidden="true" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                        </svg>
                    </button>
                </div>
                ${state.showAddItem ? `
                    <div class="fd-modal-backdrop" data-action="cancel-new-item">
                        <section class="fd-card fd-modal-card" role="dialog" aria-modal="true" aria-labelledby="add-menu-title">
                            <div class="fd-section-head">
                                <div><div class="fd-kicker">Add to menu</div><h2 id="add-menu-title" class="fd-card-title" style="margin-top:5px">New item</h2></div>
                                <button class="fd-button fd-button-secondary fd-button-small" type="button" data-action="cancel-new-item" aria-label="Close add item form">✕</button>
                            </div>
                            <form class="fd-card-pad" data-form="add-menu-item">
                                <div class="fd-form-grid">
                                    <label class="fd-form-field is-wide"><span class="fd-label">Item name</span><input class="fd-input" name="name" maxlength="255" placeholder="e.g. House burger" required></label>
                                    <label class="fd-form-field is-wide"><span class="fd-label">Description</span><textarea class="fd-textarea" name="description" placeholder="Describe this item"></textarea></label>
                                    <label class="fd-form-field is-wide"><span class="fd-label">Price (₱)</span><input class="fd-input" name="price" type="number" step="0.01" min="0.01" placeholder="0.00" required></label>
                                </div>
                                <div class="fd-form-actions">
                                    <button class="fd-button fd-button-secondary fd-button-small" type="button" data-action="cancel-new-item">Cancel</button>
                                    <button class="fd-button fd-button-small" type="submit">Add menu item</button>
                                </div>
                            </form>
                        </section>
                    </div>
                ` : ''}
                ${state.menu.length ? state.menu.map(menuItemCard).join('') : '<div class="fd-empty">No menu items yet. Use the plus button to add your first item.</div>'}
            </section>`;
    }

    function restaurantPreview() {
        const restaurant = state.restaurantProfile;
        if (!restaurant) {
            return '<div class="fd-card fd-empty">Restaurant profile is not available.</div>';
        }

        const availableItems = state.menu.filter((item) => item.is_available);

        return `
            ${heading('Customer view', escapeHtml(restaurant.name), 'A read-only preview of your public restaurant listing and menu.')}
            <section class="fd-card" style="overflow:hidden">
                ${restaurant.cover_image_url ? `<img src="${escapeHtml(restaurant.cover_image_url)}" alt="" style="display:block;width:100%;height:180px;object-fit:cover">` : '<div class="fd-restaurant-art" style="height:180px">DashBite</div>'}
                <div class="fd-card-pad">
                    <div style="display:flex;align-items:center;gap:12px">
                        ${restaurant.profile_image_url ? `<img src="${escapeHtml(restaurant.profile_image_url)}" alt="" style="width:52px;height:52px;border-radius:12px;object-fit:cover">` : ''}
                        <div>
                            <h2 class="fd-card-title">${escapeHtml(restaurant.name)}</h2>
                            <p class="fd-muted">${escapeHtml(restaurant.cuisine || 'Cuisine not set')} · ${Number(restaurant.prep_time_minutes || 20)} min</p>
                        </div>
                    </div>
                    <p class="fd-muted" style="margin-top:12px">${escapeHtml(restaurant.address || '')}</p>
                    <div style="margin-top:18px;border-top:1px solid #efede9;padding-top:15px">
                        <h3 class="fd-card-title">Available menu</h3>
                        ${availableItems.length
                ? availableItems.map((item) => `<div class="fd-menu-row" style="padding-left:0;padding-right:0"><div><strong class="fd-menu-name">${escapeHtml(item.name)}</strong><p class="fd-muted">${escapeHtml(item.description || '')}</p></div><strong class="fd-price">${currency(item.price)}</strong></div>`).join('')
                : '<div class="fd-empty">No available menu items.</div>'}
                    </div>
                </div>
            </section>`;
    }

    function restaurantOrders() {
        return `
            ${heading('Restaurant workspace', 'Incoming orders', 'Review customer orders and advance them through preparation.')}
            <section class="fd-card">
                <div class="fd-section-head"><h2>Recent orders</h2><button class="fd-button fd-button-secondary fd-button-small" type="button" data-action="refresh-orders">Refresh</button></div>
                ${state.orders.length ? state.orders.map((order) => {
                const status = typeof order.status === 'string' ? order.status : order.status?.value;
                const next = ({ pending: 'accepted', accepted: 'preparing', preparing: 'ready', ready: 'completed' })[status];
                return `<article class="fd-card-pad" style="border-bottom:1px solid #efede9">
                    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap">
                        <div><h3 class="fd-card-title">Order #${Number(order.id)} · ${escapeHtml(order.customer?.name || 'Customer')}</h3><p class="fd-muted">${escapeHtml(new Date(order.created_at).toLocaleString())} · ${escapeHtml(order.delivery_address)}</p></div>
                        <span class="fd-tag ${status === 'completed' ? 'is-available' : ''}">${escapeHtml(status)}</span>
                    </div>
                    <p class="fd-muted" style="margin-top:10px">${(order.items || []).map((item) => `${Number(item.quantity)} × ${escapeHtml(item.item_name)}`).join(' · ')}</p>
                    <div class="fd-card-bottom"><span class="fd-muted">${escapeHtml(String(order.payment_method).toUpperCase())} · ${currency(order.total)}</span>
                        <div class="fd-inline-actions">
                            ${next ? `<button class="fd-button fd-button-small" type="button" data-action="update-order-status" data-id="${Number(order.id)}" data-status="${next}">Mark ${next}</button>` : ''}
                            ${['pending', 'accepted'].includes(status) ? `<button class="fd-button fd-button-danger fd-button-small" type="button" data-action="update-order-status" data-id="${Number(order.id)}" data-status="cancelled">Cancel</button>` : ''}
                        </div>
                    </div>
                </article>`;
            }).join('') : '<div class="fd-empty">No orders yet. Customer orders will appear here.</div>'}
            </section>`;
    }

    function restaurantReviews() {
        const average = state.reviews.length
            ? state.reviews.reduce((sum, review) => sum + Number(review.rating), 0) / state.reviews.length
            : 0;

        return `
            ${heading('Restaurant workspace', 'Customer reviews', 'Feedback from customers who completed an order.')}
            <div class="fd-stat-grid"><div class="fd-card fd-stat"><div class="fd-stat-label">Reviews shown</div><div class="fd-stat-value">${state.reviews.length}</div></div><div class="fd-card fd-stat"><div class="fd-stat-label">Average rating</div><div class="fd-stat-value">${state.reviews.length ? `${average.toFixed(1)} / 5` : '—'}</div></div></div>
            <section class="fd-card">
                ${state.reviews.length ? state.reviews.map((review) => `<article class="fd-card-pad" style="border-bottom:1px solid #efede9"><div style="display:flex;justify-content:space-between;gap:12px"><strong class="fd-menu-name">${escapeHtml(review.customer?.name || 'Customer')}</strong><span class="fd-price">${Number(review.rating)} / 5</span></div>${review.comment ? `<p class="fd-muted" style="margin-top:8px">${escapeHtml(review.comment)}</p>` : ''}<time class="fd-muted" datetime="${escapeHtml(review.created_at)}">${escapeHtml(new Date(review.created_at).toLocaleDateString())}</time></article>`).join('') : '<div class="fd-empty">No customer reviews yet.</div>'}
            </section>`;
    }

    function riderAvailability() {
        const statusText = state.availabilityRequest === null
            ? 'Not reported'
            : state.availabilityRequest ? 'Online (this session)' : 'Offline (this session)';
        const isOnline = state.availabilityRequest === true;
        const availabilityLabel = state.availabilityRequest === null
            ? 'Not reported'
            : isOnline ? 'Online' : 'Offline';

        return `
            ${heading('Rider workspace', 'Availability', 'Send an availability signal using the rider API.')}
            <div class="fd-stat-grid">
                <div class="fd-card fd-stat"><div class="fd-stat-label">Current status</div><div class="fd-stat-value" style="font-size:18px">${statusText}</div></div>
                <div class="fd-card fd-stat"><div class="fd-stat-label">Active deliveries</div><div class="fd-stat-value">—</div></div>
                <div class="fd-card fd-stat"><div class="fd-stat-label">Delivery history</div><div class="fd-stat-value">—</div></div>
            </div>
            <section class="fd-card">
                <div class="fd-section-head"><h2>Delivery availability</h2><span class="fd-tag ${isOnline ? 'is-available' : ''}">${availabilityLabel}</span></div>
                <div class="fd-toggle-row">
                    <div><h3 class="fd-menu-name">Set yourself ${isOnline ? 'offline' : 'online'}</h3><p class="fd-muted">You can send an availability update to the server.</p></div>
                    <button class="fd-toggle${isOnline ? ' is-on' : ''}" type="button" role="switch" aria-checked="${isOnline}" aria-label="Toggle rider availability" data-action="toggle-availability" ${state.busy ? 'disabled' : ''}></button>
                </div>
            </section>
            <div class="fd-callout" style="margin-top:17px"><span class="fd-callout-icon">!</span><div><strong>Availability is not persisted yet</strong>The current rider endpoint only acknowledges the request; it does not save a status. This display is temporary for this browser session. Delivery offers, history, and profile updates need additional APIs.</div></div>`;
    }

    function unsupportedPage() {
        const titles = {
            orders: user.role === 'customer' ? ['Orders', 'Customer order history and checkout APIs are not available in this system yet.'] : ['Incoming orders', 'There is no restaurant order-list or order-update API available yet.'],
            deliveries: ['Deliveries', 'Rider delivery offers, accepting jobs, and delivery history APIs are not available yet.'],
        };
        const [title, description] = titles[state.page] || ['Not available', 'This panel is not supported by the current API.'];

        return `
            ${heading(`${escapeHtml(roleLabel)} workspace`, title, description)}
            <div class="fd-card fd-card-pad">
                <div class="fd-callout is-info"><span class="fd-callout-icon">i</span><div><strong>This area is intentionally not simulated</strong>To avoid showing false success or fake records, this screen is available as a clear status only until the system provides the required API endpoints.</div></div>
            </div>`;
    }

    function render() {
        if (user.role === 'admin') {
            content.innerHTML = `
                ${heading('Admin workspace', 'Admin panel unavailable', 'This UI is limited to the currently supported customer, restaurant, and rider APIs.')}
                <div class="fd-card fd-card-pad"><div class="fd-callout is-info"><span class="fd-callout-icon">i</span><div><strong>No admin dashboard API is connected</strong>The existing admin endpoint only acknowledges account approval and does not provide dashboard data.</div></div></div>`;
            return;
        }

        if (user.account_status !== 'approved') {
            content.innerHTML = `
                ${heading(`${escapeHtml(roleLabel)} workspace`, 'Account access pending', 'Some panel features require an approved account.')}
                <div class="fd-card fd-card-pad"><div class="fd-callout"><span class="fd-callout-icon">!</span><div><strong>Account status: ${escapeHtml(user.account_status)}</strong>Your account has not been approved for protected panel features. Contact an administrator.</div></div></div>`;
            return;
        }

        if (user.role === 'customer' && state.page === 'restaurants') {
            content.innerHTML = customerRestaurants();
        } else if (user.role === 'customer' && state.page === 'basket') {
            content.innerHTML = customerBasket();
        } else if (user.role === 'restaurant' && state.page === 'menu') {
            content.innerHTML = restaurantMenu();
        } else if (user.role === 'restaurant' && state.page === 'preview') {
            content.innerHTML = restaurantPreview();
        } else if (user.role === 'restaurant' && state.page === 'orders') {
            content.innerHTML = restaurantOrders();
        } else if (user.role === 'restaurant' && state.page === 'profile') {
            content.innerHTML = renderRestaurantProfile(state.restaurantProfile, escapeHtml, heading);
        } else if (user.role === 'restaurant' && state.page === 'reviews') {
            content.innerHTML = restaurantReviews();
        } else if (user.role === 'rider' && state.page === 'availability') {
            content.innerHTML = riderAvailability();
        } else {
            content.innerHTML = unsupportedPage();
        }
    }

    async function loadRestaurants() {
        content.innerHTML = '<div class="fd-loading">Loading restaurants…</div>';

        try {
            const payload = await request('/api/catalog/restaurants', token);
            state.restaurants = Array.isArray(payload?.data) ? payload.data : [];
            render();
        } catch (error) {
            content.innerHTML = `<div class="fd-card fd-empty">${escapeHtml(error.message)}<p><button class="fd-button fd-button-secondary fd-button-small" type="button" data-action="retry">Try again</button></p></div>`;
        }
    }

    async function loadRestaurantMenu(id) {
        state.busy = true;
        setNotice();
        state.selectedRestaurant = state.restaurants.find((restaurant) => restaurant.id === Number(id)) || null;
        content.innerHTML = '<div class="fd-loading">Loading restaurant menu…</div>';

        try {
            const [restaurantPayload, menuPayload] = await Promise.all([
                request(`/api/catalog/restaurants/${id}`, token),
                request(`/api/catalog/restaurants/${id}/menu`, token),
            ]);
            state.selectedRestaurant = restaurantPayload?.data || null;
            state.menu = Array.isArray(menuPayload?.data) ? menuPayload.data : [];
            state.basket = {};
            state.page = 'restaurants';
            render();
        } catch (error) {
            content.innerHTML = `<div class="fd-card fd-empty">${escapeHtml(error.message)}<p><button class="fd-button fd-button-secondary fd-button-small" type="button" data-action="retry">Try again</button></p></div>`;
        } finally {
            state.busy = false;
        }
    }

    async function loadRestaurantMenuManagement() {
        content.innerHTML = '<div class="fd-loading">Loading your menu…</div>';

        try {
            const [menuPayload, profilePayload] = await Promise.all([
                request('/api/restaurant/menu', token),
                request('/api/restaurant/profile', token),
            ]);
            state.menu = Array.isArray(menuPayload?.data) ? menuPayload.data : [];
            state.restaurantProfile = profilePayload?.data || null;
            render();
        } catch (error) {
            content.innerHTML = `<div class="fd-card fd-empty">${escapeHtml(error.message)}<p><button class="fd-button fd-button-secondary fd-button-small" type="button" data-action="retry">Try again</button></p></div>`;
        }
    }

    async function loadRestaurantPage(page) {
        content.innerHTML = '<div class="fd-loading">Loading restaurant workspace…</div>';
        try {
            if (page === 'orders') {
                const payload = await request('/api/restaurant/orders', token);
                state.orders = Array.isArray(payload?.data) ? payload.data : [];
            } else if (page === 'reviews') {
                const payload = await request('/api/restaurant/reviews', token);
                state.reviews = Array.isArray(payload?.data) ? payload.data : [];
            } else {
                const [profilePayload, menuPayload] = await Promise.all([
                    request('/api/restaurant/profile', token),
                    request('/api/restaurant/menu', token),
                ]);
                state.restaurantProfile = profilePayload?.data || null;
                state.menu = Array.isArray(menuPayload?.data) ? menuPayload.data : [];
            }
            render();
        } catch (error) {
            content.innerHTML = `<div class="fd-card fd-empty">${escapeHtml(error.message)}<p><button class="fd-button fd-button-secondary fd-button-small" type="button" data-action="retry">Try again</button></p></div>`;
        }
    }

    function changePage(page) {
        if (!nav.some((item) => item.id === page) && !(user.role === 'restaurant' && page === 'profile')) {
            return;
        }

        state.page = page;
        state.query = '';
        state.editingItem = null;
        state.showAddItem = false;
        state.selectedRestaurant = null;
        setNotice();

        if (page === 'restaurants') {
            loadRestaurants();
        } else if (page === 'menu') {
            loadRestaurantMenuManagement();
        } else if (user.role === 'restaurant') {
            loadRestaurantPage(page);
        } else {
            render();
        }

        shadow.querySelectorAll('[data-page]').forEach((button) => {
            const active = button.dataset.page === page;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-current', active ? 'page' : 'false');
        });
    }

    shadow.addEventListener('input', (event) => {
        if (event.target.matches('[data-search]')) {
            const selectionStart = event.target.selectionStart;
            state.query = event.target.value;
            const replacement = shadow.querySelector('#portal-content');
            replacement.innerHTML = customerRestaurants();
            const input = shadow.querySelector('[data-search]');
            input.focus();
            input.setSelectionRange(selectionStart, selectionStart);
        }
    });

    shadow.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && state.showAddItem) {
            state.showAddItem = false;
            render();
            shadow.querySelector('[data-action="new-item"]')?.focus();
        }
    });

    shadow.addEventListener('click', async (event) => {
        if (event.target.matches('.fd-modal-backdrop')) {
            state.showAddItem = false;
            render();
            shadow.querySelector('[data-action="new-item"]')?.focus();
            return;
        }

        const button = event.target.closest('button');
        if (!button || state.busy) {
            return;
        }

        if (button.dataset.page) {
            changePage(button.dataset.page);
            return;
        }

        const id = Number(button.dataset.id);

        switch (button.dataset.action) {
            case 'logout':
                button.disabled = true;
                setNotice();
                try {
                    await request('/api/auth/logout', token, { method: 'POST' });
                    onLogout();
                } catch (error) {
                    button.disabled = false;
                    setNotice(error.message, 'error');
                }
                break;
            case 'open-profile':
                if (user.role === 'restaurant') {
                    changePage('profile');
                }
                break;
            case 'open-restaurant':
                if (Number.isInteger(id) && id > 0) {
                    await loadRestaurantMenu(id);
                }
                break;
            case 'back-restaurants':
                state.selectedRestaurant = null;
                await loadRestaurants();
                break;
            case 'clear-search':
                state.query = '';
                render();
                shadow.querySelector('[data-search]')?.focus();
                break;
            case 'go-restaurants':
                changePage('restaurants');
                break;
            case 'retry':
                if (user.role === 'customer') {
                    if (state.selectedRestaurant) {
                        await loadRestaurantMenu(state.selectedRestaurant.id);
                    } else {
                        await loadRestaurants();
                    }
                } else if (user.role === 'restaurant') {
                    if (state.page === 'menu') {
                        await loadRestaurantMenuManagement();
                    } else {
                        await loadRestaurantPage(state.page);
                    }
                }
                break;
            case 'refresh-orders':
                await loadRestaurantPage('orders');
                break;
            case 'update-order-status': {
                state.busy = true;
                try {
                    await request(`/api/restaurant/orders/${id}/status`, token, {
                        method: 'PATCH',
                        body: { status: button.dataset.status },
                    });
                    setNotice('Order status updated.', 'success');
                    await loadRestaurantPage('orders');
                } catch (error) {
                    setNotice(error.message, 'error');
                } finally {
                    state.busy = false;
                }
                break;
            }
            case 'add-basket': {
                const item = state.menu.find((entry) => entry.id === id);
                if (item?.is_available) {
                    state.basket[id] = (state.basket[id] || 0) + 1;
                    state.basketItems[id] = {
                        ...item,
                        restaurant_name: state.selectedRestaurant?.name || '',
                    };
                    setNotice(`${item.name} added to your local basket.`, 'success');
                    render();
                }
                break;
            }
            case 'basket-inc':
                state.basket[id] = (state.basket[id] || 0) + 1;
                render();
                break;
            case 'basket-dec':
                state.basket[id] = Math.max(0, (state.basket[id] || 0) - 1);
                if (state.basket[id] === 0) {
                    delete state.basket[id];
                    delete state.basketItems[id];
                }
                render();
                break;
            case 'basket-remove':
                delete state.basket[id];
                delete state.basketItems[id];
                setNotice('Item removed from your basket.', 'success');
                render();
                break;
            case 'new-item':
                state.showAddItem = true;
                render();
                shadow.querySelector('[data-form="add-menu-item"] [name="name"]')?.focus();
                break;
            case 'cancel-new-item':
                state.showAddItem = false;
                render();
                shadow.querySelector('[data-action="new-item"]')?.focus();
                break;
            case 'edit-item':
                state.editingItem = id;
                render();
                shadow.querySelector(`[data-form="edit-menu-item"] [name="name"]`)?.focus();
                break;
            case 'cancel-edit':
                state.editingItem = null;
                render();
                break;
            case 'toggle-item':
                state.busy = true;
                try {
                    const payload = await request(`/api/restaurant/menu/items/${id}/availability`, token, { method: 'PATCH' });
                    const updated = payload?.data;
                    if (updated) {
                        state.menu = state.menu.map((item) => item.id === id ? updated : item);
                    }
                    setNotice(payload?.message || 'Menu item availability updated.', 'success');
                    render();
                } catch (error) {
                    setNotice(error.message, 'error');
                } finally {
                    state.busy = false;
                }
                break;
            case 'delete-item':
                if (window.confirm('Delete this menu item? This cannot be undone.')) {
                    state.busy = true;
                    try {
                        await request(`/api/restaurant/menu/items/${id}`, token, { method: 'DELETE' });
                        state.menu = state.menu.filter((item) => item.id !== id);
                        setNotice('Menu item deleted.', 'success');
                        render();
                    } catch (error) {
                        setNotice(error.message, 'error');
                    } finally {
                        state.busy = false;
                    }
                }
                break;
            case 'toggle-availability': {
                const nextAvailability = state.availabilityRequest !== true;
                state.busy = true;
                button.disabled = true;
                try {
                    await request('/api/rider/availability', token, {
                        method: 'POST',
                        body: { is_available: nextAvailability },
                    });
                    state.availabilityRequest = nextAvailability;
                    setNotice('The server acknowledged the request, but the current API does not save availability.', 'success');
                } catch (error) {
                    setNotice(error.message, 'error');
                } finally {
                    state.busy = false;
                    render();
                }
                break;
            }
            default:
                break;
        }
    });

    shadow.addEventListener('submit', async (event) => {
        const form = event.target.closest('form[data-form]');
        if (!form) {
            return;
        }

        event.preventDefault();
        if (state.busy) {
            return;
        }

        state.busy = true;
        const formData = new FormData(form);

        if (form.dataset.form === 'restaurant-profile') {
            const openingHours = {};
            ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'].forEach((day) => {
                openingHours[day] = String(formData.get(`hours[${day}]`) || '').trim();
            });
            const body = {
                name: String(formData.get('name') || '').trim(),
                address: String(formData.get('address') || '').trim(),
                phone: String(formData.get('phone') || '').trim() || null,
                cuisine: String(formData.get('cuisine') || '').trim() || null,
                cover_image_url: String(formData.get('cover_image_url') || '').trim() || null,
                profile_image_url: String(formData.get('profile_image_url') || '').trim() || null,
                display_badge: String(formData.get('display_badge') || '').trim() || null,
                prep_time_minutes: Number(formData.get('prep_time_minutes')),
                opening_hours: openingHours,
            };

            try {
                const payload = await request('/api/restaurant/profile', token, { method: 'PUT', body });
                if (!payload?.data) {
                    throw new Error('The server did not return the updated restaurant profile.');
                }
                state.restaurantProfile = payload.data;
                setNotice('Restaurant profile saved.', 'success');
                render();
            } catch (error) {
                setNotice(error.message, 'error');
            } finally {
                state.busy = false;
            }
            return;
        }

        if (form.dataset.form === 'add-menu-item') {
            const body = {
                name: String(formData.get('name') || '').trim(),
                description: String(formData.get('description') || '').trim() || null,
                price: Number(formData.get('price')),
                is_available: true,
            };

            try {
                const payload = await request('/api/restaurant/menu/items', token, { method: 'POST', body });
                if (!payload?.data) {
                    throw new Error('The server did not return the created menu item.');
                }
                state.menu.push(payload.data);
                state.menu.sort((first, second) => first.name.localeCompare(second.name));
                state.showAddItem = false;
                setNotice('Menu item added.', 'success');
                render();
            } catch (error) {
                setNotice(error.message, 'error');
            } finally {
                state.busy = false;
            }
            return;
        }

        if (form.dataset.form === 'edit-menu-item') {
            const id = Number(form.dataset.id);
            const body = {
                name: String(formData.get('name') || '').trim(),
                description: String(formData.get('description') || '').trim() || null,
                price: Number(formData.get('price')),
            };

            try {
                const payload = await request(`/api/restaurant/menu/items/${id}`, token, { method: 'PUT', body });
                if (!payload?.data) {
                    throw new Error('The server did not return the updated menu item.');
                }
                state.menu = state.menu.map((item) => item.id === id ? payload.data : item);
                state.editingItem = null;
                setNotice('Menu item updated.', 'success');
                render();
            } catch (error) {
                setNotice(error.message, 'error');
            } finally {
                state.busy = false;
            }
        }
    });

    if (user.role === 'customer' && user.account_status === 'approved') {
        loadRestaurants();
    } else if (user.role === 'restaurant' && user.account_status === 'approved') {
        loadRestaurantMenuManagement();
    } else {
        render();
    }

    return () => {
        shadow.replaceChildren();
        document.title = originalTitle;
    };
}
