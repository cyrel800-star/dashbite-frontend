<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2D2D2D">
    <title>Sign in | DashBite</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="login-layout">
        <aside class="brand-panel" aria-label="About DashBite">
            <a class="brand" href="/" aria-label="DashBite home">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 40 40" fill="none">
                        <path d="M8 16.5h24l-2 17H10l-2-17Z" fill="currentColor"/>
                        <path d="M14 17v-4a6 6 0 0 1 12 0v4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M16 23h8" stroke="#2D2D2D" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <span>Dash<span class="brand-accent">Bite</span></span>
            </a>

            <div class="brand-copy">
                <span class="eyebrow"><span class="eyebrow-dot"></span> GOOD FOOD, GOOD MOOD</span>
                <h1>Your next<br>great meal<br>starts <span>here.</span></h1>
                <p>Sign in to pick up where you left off and get back to the good stuff.</p>
            </div>

            <div class="brand-art" aria-hidden="true">
                <div class="art-orbit orbit-one"></div>
                <div class="art-orbit orbit-two"></div>
                <div class="art-card">
                    <svg viewBox="0 0 120 100" fill="none">
                        <path d="M26 61h49l-5 24H31l-5-24Z" fill="#FF6B35"/>
                        <path d="M34 61c1-13 9-21 17-21s16 8 17 21" stroke="#fff" stroke-width="4" stroke-linecap="round"/>
                        <path d="M42 40c-4-7 2-10 0-15m13 14c-3-7 3-10 1-15" stroke="#FFB18F" stroke-width="3" stroke-linecap="round"/>
                        <path d="M77 72h17a8 8 0 0 0 8-8V49H83v15a8 8 0 0 1-8 8Z" stroke="#2D2D2D" stroke-width="4" stroke-linejoin="round"/>
                        <path d="M88 49v-8m8 8v-8" stroke="#2D2D2D" stroke-width="4" stroke-linecap="round"/>
                        <path d="M19 87h82" stroke="#D8D5D2" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                    <span>Made for your cravings</span>
                </div>
                <span class="art-spark spark-one">✦</span>
                <span class="art-spark spark-two">✦</span>
            </div>

            <p class="brand-footer">A little happiness, delivered.</p>
        </aside>

        <section class="form-panel" aria-labelledby="form-title">
            <div class="form-wrap">
                <div class="mobile-brand" aria-hidden="true">
                    <span class="brand-mark">
                        <svg viewBox="0 0 40 40" fill="none">
                            <path d="M8 16.5h24l-2 17H10l-2-17Z" fill="currentColor"/>
                            <path d="M14 17v-4a6 6 0 0 1 12 0v4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                            <path d="M16 23h8" stroke="#2D2D2D" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span>Dash<span class="brand-accent">Bite</span></span>
                </div>

                <div id="login-view">
                    <div class="form-heading">
                        <span class="form-kicker">WELCOME BACK</span>
                        <h2 id="form-title">Sign in to DashBite</h2>
                        <p>Enter your account details to continue.</p>
                    </div>

                    <form id="login-form" novalidate>
                        <div class="field">
                            <label for="email">Email address</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="username"
                                placeholder="you@example.com"
                                required
                                autofocus
                            >
                        </div>

                        <div class="field">
                            <div class="field-label-row">
                                <label for="password">Password</label>
                            </div>
                            <div class="password-wrap">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    placeholder="Enter your password"
                                    required
                                >
                                <button class="password-toggle" id="password-toggle" type="button" aria-label="Show password" aria-pressed="false">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                        <circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <p class="form-message" id="form-message" role="alert" aria-live="polite" hidden></p>

                        <button class="submit-button" id="submit-button" type="submit">
                            <span class="button-label">Sign in</span>
                            <span class="button-spinner" aria-hidden="true"></span>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>

                    <div class="oauth-divider" aria-hidden="true"><span>OR</span></div>

                    <button class="google-button" type="button" disabled aria-label="Sign in with Google, coming soon">
                        <svg viewBox="0 0 48 48" aria-hidden="true">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5Z" transform="translate(0 5)"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.76 7.18l7.73 6C44.42 38.05 46.98 31.91 46.98 24.55Z"/>
                            <path fill="#FBBC05" d="M10.53 28.59a14.4 14.4 0 0 1-.75-4.59c0-1.59.27-3.13.75-4.59l-7.98-6.19A23.9 23.9 0 0 0 0 24c0 3.87.93 7.52 2.56 10.78l7.97-6.19Z" transform="translate(0 0)"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.91-5.8l-7.73-6c-2.14 1.45-4.89 2.3-8.18 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48Z" transform="translate(0 -5)"/>
                        </svg>
                        <span>Sign in with Google</span>
                        <span class="coming-soon">Coming soon</span>
                    </button>

                    <p class="signup-prompt">
                        New to DashBite?
                        <button class="signup-button" type="button" disabled aria-label="Create an account, coming soon">Create an account</button>
                        <span class="signup-coming-soon">Coming soon</span>
                    </p>

                    <p class="form-note">
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <rect x="4" y="8.5" width="12" height="9" rx="2" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M7 8.5V6a3 3 0 1 1 6 0v2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        Your sign-in is securely handled by DashBite.
                    </p>
                </div>

                <div id="authenticated-view" class="authenticated-view" hidden>
                    <span class="success-mark" aria-hidden="true">
                        <svg viewBox="0 0 32 32" fill="none">
                            <path d="m8 16 5 5L24 10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="form-kicker">SIGNED IN</span>
                    <h2 id="success-title">Welcome back!</h2>
                    <p class="success-copy" id="success-copy"></p>
                    <div class="account-details">
                        <span class="account-label">ACCOUNT</span>
                        <span id="account-email"></span>
                        <span class="account-label">ROLE</span>
                        <span id="account-role"></span>
                        <span class="account-label">STATUS</span>
                        <span class="status-value" id="account-status"></span>
                    </div>
                    <p class="form-message" id="logout-message" role="alert" aria-live="polite" hidden></p>
                    <button class="secondary-button" id="logout-button" type="button">Sign out</button>
                </div>

                <p class="copyright">© {{ date('Y') }} DashBite. All rights reserved.</p>
            </div>
        </section>
    </main>
    <div id="portal-app" hidden></div>
</body>
</html>
