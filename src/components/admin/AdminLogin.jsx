import { useEffect, useState } from "react";
import logoUrl from "../../../assets/logo.svg";
import { clearAdminMonitoring, clearPreviewSession } from "../../lib/previewSession.js";
import { ADMIN_PREVIEW_CREDENTIALS, signInAdminPreview } from "../../lib/adminPreviewAuth.js";

export default function AdminLogin() {
  const [email, setEmail] = useState(ADMIN_PREVIEW_CREDENTIALS.email);
  const [password, setPassword] = useState(ADMIN_PREVIEW_CREDENTIALS.password);
  const [error, setError] = useState("");

  useEffect(() => {
    clearSession();
  }, []);

  function submit(event) {
    event.preventDefault();
    setError("");
    try {
      signInAdminPreview(email, password);
      clearAdminMonitoring();
      window.location.hash = "#/ADMIN";
    } catch (loginError) {
      setError(loginError instanceof Error ? loginError.message : "Could not sign in to the Admin preview.");
    }
  }

  function clearSession() {
    clearPreviewSession();
    clearAdminMonitoring();
  }

  return <main className="admin-login-page">
    <header className="admin-login-header">
      <a className="brand admin-brand" href="#/" aria-label="DashBite home">
        <img src={logoUrl} alt="" width="40" height="40" />
        <span>dash<span className="brand-accent">bite</span></span>
      </a>
      <a className="admin-login-home" href="#/">Back to DashBite</a>
    </header>
    <section className="admin-login-layout">
      <aside className="admin-login-aside">
        <p className="admin-eyebrow"><i /> OPERATIONS WORKSPACE</p>
        <h1>Everything<br />in one <span>clear view.</span></h1>
        <p>Review applications, follow order activity, and explore the DashBite system preview.</p>
        <div className="admin-login-aside-points"><span>✓ Account approval queue</span><span>✓ Order and payment overview</span><span>✓ Reports and review moderation</span></div>
      </aside>
      <section className="admin-login-card">
        <div className="admin-login-card-top"><span>ADMIN SIGN IN</span><span className="admin-local-pill">LOCAL PREVIEW</span></div>
        <h2>Welcome, Admin</h2>
        <p>Sign in to open the Admin operations portal.</p>
        <form onSubmit={submit}>
          <label className="admin-field">Admin email<input type="email" autoComplete="username" required maxLength={254} value={email} onChange={(event) => setEmail(event.target.value)} /></label>
          <label className="admin-field">Password<input type="password" autoComplete="current-password" required maxLength={128} value={password} onChange={(event) => setPassword(event.target.value)} /></label>
          {error && <div className="admin-notice admin-notice-error" role="alert">{error}</div>}
          <button className="button button-primary admin-login-submit" type="submit">Sign in to Admin <span aria-hidden="true">→</span></button>
        </form>
        <div className="admin-credentials-note"><strong>Demo preview credentials</strong><span>Email: {ADMIN_PREVIEW_CREDENTIALS.email}</span><span>Password: {ADMIN_PREVIEW_CREDENTIALS.password}</span></div>
        <p className="admin-security-note">Frontend-only demo: these credentials are visible in the app code and do not provide real security. Production Admin authentication must be handled by a secured backend.</p>
        <a className="admin-login-footer-link" href="#/SIGNUP">Not an Admin? Create a Customer, Restaurant, or Rider account</a>
      </section>
    </section>
    <footer className="admin-login-footer">© 2026 DashBite · Core v1.0</footer>
  </main>;
}
