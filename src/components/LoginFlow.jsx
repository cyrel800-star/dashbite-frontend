import { useEffect, useState } from "react";
import logoUrl from "../../assets/logo.svg";
import { clearPreviewSession, startPreviewSession } from "../lib/previewSession.js";
import { findPreviewAccount, subscribeToPreviewAccounts } from "../lib/previewAccounts.js";

const DEMO_OTP = "123456";
const MAX_OTP_REQUESTS = 5;
const COOLDOWN_SECONDS = 60;

function Brand() {
  return <a className="brand" href="#/" aria-label="DashBite home">
    <img src={logoUrl} alt="" width="42" height="42" />
    <span>dash<span className="brand-accent">bite</span></span>
  </a>;
}

function ErrorNotice({ message, type }) {
  if (!message) return null;
  return <div className={`notice notice-${type} visible`} role={type === "error" ? "alert" : "status"}>{message}</div>;
}

export default function LoginFlow() {
  const [step, setStep] = useState("email");
  const [email, setEmail] = useState("");
  const [otp, setOtp] = useState("");
  const [otpRequests, setOtpRequests] = useState(0);
  const [cooldownUntil, setCooldownUntil] = useState(0);
  const [now, setNow] = useState(Date.now());
  const [notice, setNotice] = useState("");
  const [noticeType, setNoticeType] = useState("error");
  const [waitingForApproval, setWaitingForApproval] = useState(false);

  useEffect(() => {
    clearPreviewSession();
  }, []);

  useEffect(() => {
    if (!cooldownUntil) return undefined;
    const timer = window.setInterval(() => {
      const currentTime = Date.now();
      setNow(currentTime);
      if (currentTime >= cooldownUntil) {
        setCooldownUntil(0);
        setOtpRequests(0);
      }
    }, 250);
    return () => window.clearInterval(timer);
  }, [cooldownUntil]);

  useEffect(() => {
    if (!waitingForApproval) return undefined;

    function checkApproval() {
      try {
        const account = findPreviewAccount(email);
        if (!account || account.account_status === "PENDING_APPROVAL") return;
        if (account.account_status !== "APPROVED") {
          setWaitingForApproval(false);
          setNotice("This account is disabled and cannot log in.");
          setNoticeType("error");
          return;
        }

        startPreviewSession({ role: account.role, status: account.account_status, email: account.email });
        setWaitingForApproval(false);
        setNotice("Your account is approved. Opening your dashboard...");
        setNoticeType("success");
        window.location.hash = `#/${account.role}`;
      } catch (error) {
        setWaitingForApproval(false);
        setNotice(error instanceof Error ? error.message : "Could not check this preview account's approval status.");
        setNoticeType("error");
      }
    }

    const unsubscribe = subscribeToPreviewAccounts(checkApproval);
    checkApproval();
    return unsubscribe;
  }, [email, waitingForApproval]);

  const secondsRemaining = Math.max(0, Math.ceil((cooldownUntil - now) / 1000));

  function requestPreviewOtp() {
    setWaitingForApproval(false);
    setNotice("");
    setNoticeType("error");
    if (secondsRemaining > 0) return;

    const nextRequestCount = otpRequests + 1;
    setOtpRequests(nextRequestCount);
    setOtp("");
    setStep("otp");

    if (nextRequestCount >= MAX_OTP_REQUESTS) {
      setCooldownUntil(Date.now() + COOLDOWN_SECONDS * 1000);
      setNow(Date.now());
      setNotice("Preview limit reached. Try another preview request in 60 seconds. No email was sent.");
      return;
    }

    setNotice("UI-only preview: no OTP was sent to Gmail or anywhere else. Use the demo code below.");
  }

  function requestOtp(event) {
    event.preventDefault();
    event.currentTarget.elements.password.value = "";
    requestPreviewOtp();
  }

  function verifyOtp(event) {
    event.preventDefault();
    setNotice("");
    if (otp !== DEMO_OTP) {
      setNoticeType("error");
      setNotice("We couldn't verify those details. Check your email and code, then try again.");
      return;
    }
    setNoticeType("error");
    setNotice("");
    try {
      const account = findPreviewAccount(email);
      if (!account) {
        setNotice("No preview account was found for this email. Sign up first to create a local preview account.");
        return;
      }
      if (account.account_status === "DISABLED") {
        setWaitingForApproval(false);
        setNotice("This account is disabled and cannot log in.");
        return;
      }
      if (account.account_status === "PENDING_APPROVAL") {
        setWaitingForApproval(true);
        setNoticeType("info");
        setNotice(`This ${account.role.toLowerCase()} account is pending admin approval. Sign-in and dashboard access are blocked until the account is approved.`);
        return;
      }
      startPreviewSession({ role: account.role, status: account.account_status, email: account.email });
      setWaitingForApproval(false);
      window.location.hash = `#/${account.role}`;
    } catch (error) {
      setNotice(error instanceof Error ? error.message : "Could not verify this preview account.");
    }
  }

  function goBack() {
    setWaitingForApproval(false);
    setNotice("");
    setNoticeType("error");
    setOtp("");
    setStep("email");
  }

  return <main className="login-page">
    <header className="login-topbar">
      <Brand />
      <a className="login-home-link" href="#/">Back to home</a>
    </header>
    <section className="login-layout">
      <aside className="login-aside">
        <p className="eyebrow"><span className="eyebrow-dot" /> DASHBITE ACCOUNT</p>
        <h1>Good food<br />is <span>one step</span><br />away.</h1>
        <p>Sign in to pick up where you left off. This local preview walks through the login flow without contacting an authentication server.</p>
        <div className="login-progress login-progress-two" aria-label="Login flow steps">
          <span className={step === "email" ? "current" : "complete"}>1</span><i />
          <span className={step === "otp" ? "current" : ""}>2</span>
        </div>
        <div className="login-progress-labels login-progress-labels-two"><span>Email & password</span><span>Verify & continue</span></div>
        <div className="login-aside-note"><span aria-hidden="true">✦</span> Secure sign-in, designed around your account status.</div>
      </aside>
      <section className="login-card" aria-live="polite">
        <div className="login-card-heading">
          <span className="login-step-label">STEP {step === "email" ? "01" : "02"} / 02</span>
          <span className="preview-label">UI PREVIEW</span>
        </div>
        {step === "email" && <>
          <h2>Welcome back</h2>
          <p className="login-subtitle">Enter your email and password to preview the sign-in flow.</p>
          <form onSubmit={requestOtp}>
            <div className="field login-field">
              <label htmlFor="login-email">Email address</label>
              <input id="login-email" name="email" type="email" autoComplete="email" required maxLength={254} placeholder="you@example.com" value={email} onChange={(event) => setEmail(event.target.value)} />
            </div>
            <div className="field login-field">
              <label htmlFor="login-password">Password</label>
              <input id="login-password" name="password" type="password" autoComplete="current-password" required placeholder="Enter your password" />
            </div>
            <button className="button button-primary login-submit" type="submit">Continue <span aria-hidden="true">→</span></button>
          </form>
          <p className="login-signup-link">New to DashBite? <a href="#/SIGNUP">Create an account</a></p>
        </>}
        {step === "otp" && <>
          <h2>Preview code verification</h2>
          <p className="login-subtitle">This UI-only demo did not send an email to <strong>{email}</strong>. Enter the demo code to continue.</p>
          <form onSubmit={verifyOtp}>
            <div className="field login-field">
              <label htmlFor="login-otp">One-time code</label>
              <input id="login-otp" name="otp" type="text" inputMode="numeric" autoComplete="one-time-code" pattern="[0-9]{6}" maxLength={6} required placeholder="6-digit code" value={otp} onChange={(event) => setOtp(event.target.value.replace(/\D/g, "").slice(0, 6))} />
            </div>
            <div className="demo-code"><span className="demo-code-icon" aria-hidden="true">i</span><span>Demo code <strong>{DEMO_OTP}</strong><small>No email is sent in this UI-only preview.</small></span></div>
            <button className="button button-primary login-submit" type="submit">Verify code <span aria-hidden="true">→</span></button>
          </form>
          <div className="otp-actions">
            <button className="text-button" type="button" onClick={goBack}>Change email</button>
            <button className="text-button" type="button" onClick={requestPreviewOtp} disabled={secondsRemaining > 0}>
              {secondsRemaining > 0 ? `Try again in ${secondsRemaining}s` : "Preview another code request"}
            </button>
          </div>
          <p className="request-count">{Math.min(otpRequests, MAX_OTP_REQUESTS)} of {MAX_OTP_REQUESTS} preview code requests</p>
        </>}
        <ErrorNotice message={notice} type={noticeType} />
        <div className="login-contract-note"><strong>Flow preview only.</strong> Passwords are cleared when you continue. No OTP email is sent, and no credentials or codes are sent to an API. Production email delivery requires a backend.</div>
      </section>
    </section>
    <footer className="login-footer"><span>© 2026 DashBite</span><span>Core account flow · v1.0</span></footer>
  </main>;
}
