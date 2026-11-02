import './bootstrap';
import { mountPortal } from './portal';

const loginForm = document.querySelector('#login-form');

if (loginForm) {
    const loginView = document.querySelector('#login-view');
    const authenticatedView = document.querySelector('#authenticated-view');
    const message = document.querySelector('#form-message');
    const logoutMessage = document.querySelector('#logout-message');
    const submitButton = document.querySelector('#submit-button');
    const passwordInput = document.querySelector('#password');
    const passwordToggle = document.querySelector('#password-toggle');
    const loginLayout = document.querySelector('.login-layout');
    const portalHost = document.querySelector('#portal-app');
    let authToken = null;
    let unmountPortal = () => {};

    const showMessage = (element, text) => {
        element.textContent = text;
        element.hidden = false;
    };

    const setLoading = (isLoading) => {
        submitButton.disabled = isLoading;
        submitButton.classList.toggle('is-loading', isLoading);
        submitButton.querySelector('.button-label').textContent = isLoading ? 'Signing in…' : 'Sign in';
    };

    const showAuthenticatedView = (user) => {
        loginView.hidden = true;
        authenticatedView.hidden = true;
        loginLayout.hidden = true;
        portalHost.hidden = false;
        unmountPortal();
        unmountPortal = mountPortal(portalHost, {
            token: authToken,
            user,
            onLogout: clearSession,
        });
    };

    const clearSession = () => {
        unmountPortal();
        authToken = null;
        sessionStorage.removeItem('dashbite.auth.token');
        sessionStorage.removeItem('dashbite.auth.user');
        portalHost.hidden = true;
        loginLayout.hidden = false;
        authenticatedView.hidden = true;
        loginView.hidden = false;
        logoutMessage.hidden = true;
        loginForm.reset();
        unmountPortal = () => {};
    };

    const savedToken = sessionStorage.getItem('dashbite.auth.token');
    const savedUser = sessionStorage.getItem('dashbite.auth.user');

    if (savedToken && savedUser) {
        try {
            const user = JSON.parse(savedUser);
            if (user && user.name && user.email && user.role && user.account_status) {
                authToken = savedToken;
                showAuthenticatedView(user);
            } else {
                clearSession();
            }
        } catch {
            clearSession();
        }
    } else if (savedToken || savedUser) {
        clearSession();
    }

    passwordToggle.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        passwordToggle.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        passwordToggle.setAttribute('aria-pressed', String(isPassword));
    });

    loginForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        message.hidden = true;

        if (!loginForm.reportValidity()) {
            return;
        }

        const formData = new FormData(loginForm);
        setLoading(true);

        try {
            const response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: formData.get('email'),
                    password: formData.get('password'),
                }),
            });
            const payload = await response.json().catch(() => null);

            if (!response.ok) {
                const validationMessage = payload?.errors
                    ? Object.values(payload.errors).flat().find((error) => typeof error === 'string')
                    : null;

                if (response.status === 429) {
                    showMessage(message, 'Too many sign-in attempts. Please wait a moment and try again.');
                } else {
                    showMessage(message, validationMessage || payload?.message || 'We could not sign you in. Check your details and try again.');
                }
                return;
            }

            if (!payload?.token || !payload?.user?.email || !payload?.user?.role || !payload?.user?.account_status) {
                showMessage(message, 'The server returned an unexpected response. Please try again.');
                return;
            }

            authToken = payload.token;
            sessionStorage.setItem('dashbite.auth.token', authToken);
            sessionStorage.setItem('dashbite.auth.user', JSON.stringify(payload.user));
            showAuthenticatedView(payload.user);
        } catch {
            showMessage(message, 'Unable to reach the sign-in service. Check your connection and try again.');
        } finally {
            setLoading(false);
        }
    });

}
