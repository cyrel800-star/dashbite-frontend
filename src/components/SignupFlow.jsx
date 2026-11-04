import { useState } from "react";
import logoUrl from "../../assets/logo.svg";
import { startPreviewSession } from "../lib/previewSession.js";
import { createPreviewAccount } from "../lib/previewAccounts.js";

const signupRoles = [
  { value: "CUSTOMER", label: "Customer", icon: "⌂", description: "Order from local favorites" },
  { value: "RESTAURANT", label: "Restaurant", icon: "▤", description: "Bring your menu to DashBite" },
  { value: "RIDER", label: "Rider", icon: "➤", description: "Deliver meals in your area" },
];

function Brand() {
  return <a className="brand" href="#/" aria-label="DashBite home">
    <img src={logoUrl} alt="" width="42" height="42" />
    <span>dash<span className="brand-accent">bite</span></span>
  </a>;
}

function PreviewNotice({ message }) {
  if (!message) return null;
  return <div className="notice notice-error visible" role="alert">{message}</div>;
}

function RegistrationStatus({ registration }) {
  const isPending = registration.status === "PENDING_APPROVAL";
  return <section className="signup-success" aria-live="polite">
    <div className={`signup-success-icon${isPending ? " signup-success-icon-pending" : ""}`} aria-hidden="true">{isPending ? "◷" : "✓"}</div>
    <p className="eyebrow">{isPending ? "ADMIN REVIEW REQUIRED" : "CUSTOMER PREVIEW READY"}</p>
    <h2>{isPending ? "Your application is awaiting approval." : "Your customer preview is ready."}</h2>
    <p className="signup-success-copy">
      {isPending
        ? `The ${registration.label.toLowerCase()} application for ${registration.email} is saved in this browser as PENDING_APPROVAL. Sign-in stays blocked until it is approved in the Admin preview.`
        : `The customer preview for ${registration.email} is ready. This account is saved only in this browser.`}
    </p>
    <span className={`status ${isPending ? "status-pending_approval" : "status-approved"}`}>{registration.status}</span>
    {isPending && <div className="signup-approval-note"><strong>What happens next?</strong><span>An administrator can approve this saved application from the Admin portal in the same browser. In production, registration and approval must use a backend with admin authentication.</span></div>}
    <a className="button button-primary login-submit" href={isPending ? "#/LOGIN" : "#/CUSTOMER"}>
      {isPending ? "Go to sign in" : "Open customer preview"} <span aria-hidden="true">→</span>
    </a>
    <a className="signup-home-link" href={isPending ? "#/LOGIN" : "#/"}>{isPending ? "Go to sign in" : "Back to DashBite home"}</a>
  </section>;
}

export default function SignupFlow() {
  const [role, setRole] = useState("CUSTOMER");
  const [error, setError] = useState("");
  const [registration, setRegistration] = useState(null);

  function handleSubmit(event) {
    event.preventDefault();
    setError("");

    const form = event.currentTarget;
    const values = new FormData(form);
    const password = String(values.get("password"));
    const passwordConfirmation = String(values.get("password_confirmation"));
    if (password !== passwordConfirmation) {
      setError("Passwords do not match. Please check both password fields.");
      return;
    }

    const email = String(values.get("email")).trim();
    try {
      const account = createPreviewAccount({
        role,
        name: String(values.get("name")),
        email,
        phone: String(values.get("phone") || ""),
        restaurantName: String(values.get("restaurant_name") || ""),
        restaurantAddress: String(values.get("restaurant_address") || ""),
      });

      if (account.role === "CUSTOMER") {
        startPreviewSession({ role: account.role, status: account.account_status, email: account.email });
        window.location.hash = "#/CUSTOMER";
        return;
      }

      setRegistration({
        label: signupRoles.find((item) => item.value === role).label,
        email: account.email,
        status: account.account_status,
      });
    } catch (signupError) {
      setError(signupError instanceof Error ? signupError.message : "Could not save this preview account.");
    }
  }

  return <main className="login-page">
    <header className="login-topbar">
      <Brand />
      <a className="login-home-link" href="#/">Back to home</a>
    </header>
    <section className="signup-layout">
      <div className="signup-heading">
        <p className="eyebrow"><span className="eyebrow-dot" /> JOIN DASHBITE</p>
        <h1>A place for<br />every <span>good food</span><br />moment.</h1>
        <p>Create a customer account or apply as a restaurant or rider. Restaurant and rider dashboards stay locked until admin approval.</p>
        <div className="signup-assurance"><span aria-hidden="true">✓</span> One account type. A smoother way to get started.</div>
      </div>
      <section className="signup-card">
        {registration ? <RegistrationStatus registration={registration} /> : <>
          <div className="signup-card-heading">
            <div><h2>Create your account</h2><p>Choose how you want to use DashBite.</p></div>
            <span className="preview-label">UI PREVIEW</span>
          </div>
          <form id="signup-form" onSubmit={handleSubmit}>
            <fieldset className="signup-role-fieldset">
              <legend>Account type</legend>
              <div className="signup-role-grid">
                {signupRoles.map((item) => <label className="signup-role-option" key={item.value}>
                  <input type="radio" name="role" value={item.value} checked={role === item.value} onChange={() => { setRole(item.value); setError(""); }} />
                  <span className="signup-role-card">
                    <span className={`role-icon ${item.value === "RESTAURANT" ? "role-icon-dark" : "role-icon-orange"}`} aria-hidden="true">{item.icon}</span>
                    <span className="signup-role-copy"><strong>{item.label}</strong><small>{item.description}</small></span>
                    {item.value !== "CUSTOMER" && <span className="signup-role-lock" aria-label="Admin approval required">Approval required</span>}
                  </span>
                </label>)}
              </div>
            </fieldset>
            <div className="signup-fields">
              <div className="field signup-field">
                <label htmlFor="signup-name">Full name</label>
                <input id="signup-name" name="name" type="text" autoComplete="name" required maxLength={120} placeholder="Your full name" />
              </div>
              <div className="field signup-field">
                <label htmlFor="signup-email">Email address</label>
                <input id="signup-email" name="email" type="email" autoComplete="email" required maxLength={254} placeholder="you@example.com" />
              </div>
              <div className="field signup-field">
                <label htmlFor="signup-phone">Phone number <span className="optional-label">Optional</span></label>
                <input id="signup-phone" name="phone" type="tel" autoComplete="tel" maxLength={32} placeholder="Your contact number" />
              </div>
              {role === "RESTAURANT" && <div className="signup-role-details">
                <div className="signup-details-heading"><span className="role-icon role-icon-orange" aria-hidden="true">▤</span><span><strong>Restaurant details</strong><small>These details are part of your approval application.</small></span></div>
                <div className="field signup-field">
                  <label htmlFor="restaurant-name">Restaurant name</label>
                  <input id="restaurant-name" name="restaurant_name" type="text" required maxLength={160} placeholder="Your restaurant's name" />
                </div>
                <div className="field signup-field">
                  <label htmlFor="restaurant-address">Restaurant address</label>
                  <input id="restaurant-address" name="restaurant_address" type="text" required maxLength={300} autoComplete="street-address" placeholder="Street address and city" />
                </div>
              </div>}
              <div className="field signup-field">
                <label htmlFor="signup-password">Password</label>
                <input id="signup-password" name="password" type="password" autoComplete="new-password" minLength={8} maxLength={128} required placeholder="At least 8 characters" />
              </div>
              <div className="field signup-field">
                <label htmlFor="signup-password-confirmation">Confirm password</label>
                <input id="signup-password-confirmation" name="password_confirmation" type="password" autoComplete="new-password" minLength={8} maxLength={128} required placeholder="Enter your password again" />
              </div>
            </div>
            {role !== "CUSTOMER" && <div className="signup-approval-note"><strong>Admin approval required</strong><span>Your account will stay in PENDING_APPROVAL. Dashboard access is not available until an administrator approves it.</span></div>}
            <div className="signup-preview-note"><strong>Frontend preview only.</strong> Account details are saved in this browser; passwords are never stored. Restaurant and rider applications appear in the local Admin portal. No server account or notification is created.</div>
            <button className="button button-primary login-submit signup-submit" type="submit">{role === "CUSTOMER" ? "Create customer preview" : "Preview application"} <span aria-hidden="true">→</span></button>
            <PreviewNotice message={error} />
          </form>
          <div className="signup-card-footer">Already have an account? <a href="#/LOGIN">Sign in</a></div>
        </>}
      </section>
    </section>
    <footer className="login-footer"><span>© 2026 DashBite</span><span>Account registration · v1.0</span></footer>
  </main>;
}
