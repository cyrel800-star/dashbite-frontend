import { useEffect, useRef, useState } from "react";
import { AuthLayout, toast } from "../ui/components.jsx";
import { Field, PasswordField, isEmail } from "./Forms.jsx";
import { ROLES } from "../data/constants.js";
import { LOCAL_KEYS, readLocalData } from "../lib/localData.js";

const DEMO_OTP = "123456"; // Demo only, matching the project notes. Not real security.

// Validate local demo credentials, then continue to the OTP step.
export default function LoginFlow({ onSignedIn, onSwitch }) {
  const [step, setStep] = useState("credentials");
  const [form, setForm] = useState({ email: "", password: "", role: "CUSTOMER" });
  const [error, setError] = useState("");
  const set = (k) => (e) => setForm({ ...form, [k]: e.target.value });

  const submit = (e) => {
    e.preventDefault();
    if (!isEmail(form.email)) return setError("Enter a valid email address.");
    if (!form.password) return setError("Enter your password.");
    const account = readLocalData(LOCAL_KEYS.accounts, []).find((item) =>
      item.email.toLowerCase() === form.email.trim().toLowerCase() && item.role === form.role);
    if (account?.status === "PENDING_APPROVAL") return setError("This account is waiting for admin approval on this device.");
    if (account?.status === "DISABLED") return setError("This account has been disabled by the local admin.");
    setError(""); setStep("otp");
  };

  if (step === "otp") return <AuthLayout><Otp onBack={() => setStep("credentials")} onVerified={() => onSignedIn({ role: form.role, email: form.email })} /></AuthLayout>;
  return (
    <AuthLayout>
      <form className="box" onSubmit={submit} noValidate>
        <button type="button" className="ib" onClick={() => onSwitch("home")} aria-label="Back">←</button>
        <h2>Sign In</h2><p>Enter your email and password to get your OTP</p>
        <Field icon="✉️"><input type="email" value={form.email} onChange={set("email")} placeholder="Email address" autoComplete="email" /></Field>
        <PasswordField value={form.password} onChange={set("password")} autoComplete="current-password" />
        <Field icon="🏪"><select value={form.role} onChange={set("role")}>{ROLES.map((r) => <option key={r} value={r}>{r[0] + r.slice(1).toLowerCase()}</option>)}</select></Field>
        <div className="msg" role="alert">{error}</div>
        <button type="button" className="or" style={{ background: "none", border: 0, textAlign: "right" }} onClick={() => toast("Offline demo sign-in does not verify passwords. Use OTP 123456.")}>Forgot Password?</button>
        <button className="btn" style={{ justifyContent: "center" }}>Sign In →</button>
        <p>Don’t have an account? <button type="button" className="or" style={{ background: "none", border: 0, fontWeight: 700 }} onClick={() => onSwitch("signup")}>Sign Up →</button></p>
      </form>
    </AuthLayout>
  );
}

// Demo OTP input, countdown, and code validation.
function Otp({ onBack, onVerified }) {
  const [digits, setDigits] = useState(Array(6).fill(""));
  const [secs, setSecs] = useState(59);
  const [error, setError] = useState("");
  const refs = useRef([]);
  useEffect(() => { refs.current[0]?.focus(); }, []);
  useEffect(() => { if (secs <= 0) return; const t = setTimeout(() => setSecs(secs - 1), 1000); return () => clearTimeout(t); }, [secs]);

  const change = (i, v) => {
    const d = v.replace(/\D/g, "").slice(-1);
    setDigits(digits.map((x, k) => (k === i ? d : x)));
    if (d && refs.current[i + 1]) refs.current[i + 1].focus();
  };
  const submit = (e) => {
    e.preventDefault();
    const code = digits.join("");
    if (code.length < 6) return setError("Enter all 6 digits.");
    if (code !== DEMO_OTP) return setError("That code is incorrect. Try again or resend.");
    onVerified();
  };
  return (
    <form className="box" onSubmit={submit} noValidate>
      <button type="button" className="ib" onClick={onBack} aria-label="Back">←</button>
      <h2>OTP Verification</h2><p>We’ve sent a 6-digit code to your phone.</p>
      <div className="otp">{digits.map((d, i) => (
        <input key={i} ref={(el) => (refs.current[i] = el)} value={d} inputMode="numeric" maxLength={1} aria-label={`Digit ${i + 1}`}
          onChange={(e) => change(i, e.target.value)} onKeyDown={(e) => e.key === "Backspace" && !d && refs.current[i - 1]?.focus()} />
      ))}</div>
      <div className="msg" role="alert" style={{ textAlign: "center" }}>{error}</div>
      <p>🕒 <b className="or">00:{String(secs).padStart(2, "0")}</b>{" "}
        <button type="button" className="btn gh sm" disabled={secs > 0} onClick={() => { setSecs(59); toast("New code sent (demo code: 123456)"); }}>Resend OTP</button></p>
      <button className="btn" style={{ justifyContent: "center" }}>Verify &amp; Continue →</button>
      <p style={{ fontSize: 12 }}>Demo code: 123456</p>
    </form>
  );
}
