import { useEffect, useState } from "react";
import logoUrl from "../assets/logo.svg";
import {
  ACCOUNT_STATUSES,
  OFFER_STATUSES,
  ORDER_STATUSES,
  PAYMENT_METHODS,
  PAYMENT_STATUSES,
  ROLES,
} from "./data/constants.js";
import { formatAmount } from "./lib/format.js";
import { getApiBaseUrl, setApiBaseUrl } from "./lib/api.js";
import { CustomerOrderForm, RiderOfferForm } from "./components/Forms.jsx";
import LoginFlow from "./components/LoginFlow.jsx";
import SignupFlow from "./components/SignupFlow.jsx";
import AdminApprovalQueue from "./components/AdminApprovalQueue.jsx";
import AdminLogin from "./components/admin/AdminLogin.jsx";
import AdminPortal from "./components/admin/AdminPortal.jsx";
import RestaurantPortal from "./components/restaurant/RestaurantPortal.jsx";
import CustomerPortal from "./components/customer/CustomerPortal.jsx";
import RiderPortal from "./components/rider/RiderPortal.jsx";
import {
  clearAdminMonitoring,
  clearPreviewSession,
  getPreviewSession,
  isAdminMonitoring,
  startAdminMonitoring,
} from "./lib/previewSession.js";
import { hasAdminPreviewSession } from "./lib/adminPreviewAuth.js";

const roleConfig = {
  CUSTOMER: {
    label: "Customer",
    title: "Good afternoon, Alex",
    subtitle: "Here's what's happening with your orders today.",
    icon: "⌂",
    metrics: [["Active orders", "2", "Orders on the way", "▤"], ["Delivered", "18", "All-time orders", "✓"], ["Saved places", "3", "Your delivery spots", "⌖"], ["Rewards", "240", "Points available", "✦"]],
  },
  RESTAURANT: {
    label: "Restaurant",
    title: "Kitchen overview",
    subtitle: "Review incoming orders and keep service moving.",
    icon: "▤",
    metrics: [["New orders", "3", "Waiting for review", "▤"], ["Preparing", "5", "In the kitchen", "◷"], ["Completed today", "24", "Orders fulfilled", "✓"], ["Account", "APPROVED", "Current account status", "●"]],
  },
  RIDER: {
    label: "Rider",
    title: "Ready for your next run?",
    subtitle: "Check active delivery offers and your rider status.",
    icon: "➤",
    metrics: [["Available offers", "2", "Offers in your area", "➤"], ["Completed today", "7", "Successful deliveries", "✓"], ["Today's earnings", "86.50", "Illustrative preview · currency not specified", "$"], ["Account", "APPROVED", "Current account status", "●"]],
  },
  ADMIN: {
    label: "Admin",
    title: "Operations overview",
    subtitle: "Monitor accounts, order flow, and payment reconciliation.",
    icon: "▦",
    metrics: [["Pending accounts", "0", "Approval queue", "♙"], ["Active orders", "12", "Currently in progress", "▤"], ["Flagged payments", "2", "Needs review", "$"], ["Restaurants", "36", "Illustrative total", "⌂"]],
  },
};

const sampleOrders = [
  { id: "ORD-2050", restaurant: "Miso & Co.", total: 19.5, status: "PENDING", payment: "UNPAID" },
  { id: "ORD-2048", restaurant: "Miso & Co.", total: 26.4, status: "SEARCHING_RIDER", payment: "UNPAID" },
  { id: "ORD-2047", restaurant: "The Green Fork", total: 18.75, status: "PICKED_UP", payment: "COLLECTED_BY_RIDER" },
  { id: "ORD-2046", restaurant: "Pasta Corner", total: 32.1, status: "DELIVERED", payment: "SETTLED_TO_RESTAURANT" },
];

const sampleOffers = [
  { orderId: "ORD-2048", restaurant: "Miso & Co.", distance: "0.6 km", radius: 1, status: "BLASTED" },
  { orderId: "ORD-2049", restaurant: "The Green Fork", distance: "0.9 km", radius: 1, status: "BLASTED" },
];

function readRoute() {
  const route = window.location.hash.match(/^#\/(CUSTOMER|RESTAURANT|RIDER|ADMIN|LOGIN|SIGNUP)(?:\/([^#]*))?$/i);
  if (!route) return null;
  const role = route[1].toUpperCase();
  const page = route[2]?.split("/")[0].toUpperCase();
  return role === "ADMIN" && page === "LOGIN" ? "ADMIN_LOGIN" : role;
}

function readAuthorizedRoute() {
  const route = readRoute();
  if (!route || ["LOGIN", "SIGNUP", "ADMIN_LOGIN"].includes(route)) clearAdminMonitoring();
  if (route === "ADMIN" && !hasAdminPreviewSession()) {
    if (window.location.hash !== "#/ADMIN/LOGIN") window.location.hash = "#/ADMIN/LOGIN";
    return "ADMIN_LOGIN";
  }
  if (["CUSTOMER", "RESTAURANT", "RIDER"].includes(route)) {
    if (isAdminMonitoring()) return route;
    const session = getPreviewSession(route);
    if (!session || session.status !== "APPROVED") {
      if (window.location.hash !== "#/LOGIN") window.location.hash = "#/LOGIN";
      return "LOGIN";
    }
  }
  return route;
}

function Brand({ home = false }) {
  return (
    <a className="brand" href={home ? "#/" : "#/"} aria-label="DashBite home">
      <img src={logoUrl} alt="" width={home ? 42 : 38} height={home ? 42 : 38} />
      <span>dash<span className="brand-accent">bite</span></span>
    </a>
  );
}

function HomePage() {
  const cards = [
    ["CUSTOMER", "role-icon-orange", "Browse orders and track delivery"],
    ["RESTAURANT", "role-icon-dark", "Review your incoming orders"],
    ["RIDER", "role-icon-orange", "See offers and claim a delivery"],
    ["ADMIN", "role-icon-dark", "Review account and payment queues"],
  ];

  return (
    <div className="home-page">
      <header className="topbar home-topbar">
        <Brand home />
        <div className="home-header-actions">
          <span className="version-pill">CORE CONTRACT <strong>v1.0</strong></span>
          <a className="button button-outline home-login-button" href="#/LOGIN">Sign in <span aria-hidden="true">→</span></a>
          <a className="button button-primary home-signup-button" href="#/SIGNUP">Sign up</a>
        </div>
      </header>
      <main className="home-main">
        <section className="hero">
          <div className="hero-copy">
            <p className="eyebrow"><span className="eyebrow-dot" /> GOOD FOOD. RIGHT ON TIME.</p>
            <h1>Your next<br />great meal <span>starts here.</span></h1>
            <p className="hero-description">A fresh way to order from local favorites. Easy for you, better for your neighborhood.</p>
            <a className="button button-primary hero-button" href="#role-preview">Explore DashBite <span aria-hidden="true">→</span></a>
            <div className="hero-note"><span className="hero-note-icon" aria-hidden="true">✓</span><span>Local favorites, delivered with care</span></div>
          </div>
          <div className="hero-art" aria-hidden="true">
            <div className="hero-orbit orbit-one" /><div className="hero-orbit orbit-two" /><div className="hero-sun" />
            <div className="hero-food">🍔</div>
            <div className="floating-card floating-rating"><span>★</span> 4.9 <small>local love</small></div>
            <div className="floating-card floating-delivery"><span className="delivery-dot" /><span><strong>On its way</strong><small>Made fresh for you</small></span></div>
            <div className="hero-spark spark-one">✦</div><div className="hero-spark spark-two">✦</div>
          </div>
        </section>
        <section className="role-section" id="role-preview">
          <div className="section-heading">
            <div><p className="eyebrow">ONE PLATFORM, EVERY ROLE</p><h2>Choose your DashBite view</h2></div>
            <span className="preview-label">FRONTEND PREVIEW</span>
          </div>
          <div className="role-grid">
            {cards.map(([role, iconClass, detail]) => <a className="role-card" href={role === "ADMIN" ? "#/ADMIN/LOGIN" : `#/${role}`} key={role}>
              <span className={`role-icon ${iconClass}`} aria-hidden="true">{roleConfig[role].icon}</span>
              <span className="role-card-copy"><strong>{roleConfig[role].label}</strong><small>{detail}</small></span>
              <span className="role-arrow" aria-hidden="true">↗</span>
            </a>)}
          </div>
          <p className="preview-disclaimer"><strong>Preview only.</strong> Sample dashboard data is illustrative. Sign-in demonstrates the email and OTP flow locally; no code is emailed and no authentication API is called.</p>
        </section>
      </main>
      <footer className="home-footer"><span>© 2026 DashBite</span><span>Made for good food days <span className="footer-heart">♥</span></span></footer>
    </div>
  );
}

function Sidebar({ role, monitorMode = false }) {
  return (
    <aside className="sidebar">
      <Brand />
      <p className="sidebar-label">{monitorMode ? "ADMIN MONITORING" : "WORKSPACE"}</p>
      <nav className="side-nav" aria-label="DashBite roles">
        {ROLES.map((item) => <a className={`side-link${item === role ? " active" : ""}`} href={`#/${item}`} aria-current={item === role ? "page" : undefined} onClick={() => {
          if (role === "ADMIN" || monitorMode) startAdminMonitoring();
        }} key={item}>
          <span className="side-link-icon" aria-hidden="true">{roleConfig[item].icon}</span>{roleConfig[item].label}
        </a>)}
      </nav>
      <div className="sidebar-bottom">
        <div className="help-card"><strong>Contract preview</strong><p>Actions without a v1.0 API endpoint are shown as unavailable.</p></div>
        <div className="sidebar-footer">DashBite Core · v1.0</div>
      </div>
    </aside>
  );
}

function ApiSettings({ onSaved }) {
  const [value, setValue] = useState(getApiBaseUrl());
  const [notice, setNotice] = useState(null);

  function handleSubmit(event) {
    event.preventDefault();
    try {
      setApiBaseUrl(value);
      onSaved(value.trim().replace(/\/+$/, ""));
      setNotice({ type: "success", message: "API base URL saved in this browser." });
    } catch (error) {
      setNotice({ type: "error", message: error.message });
    }
  }

  return (
    <details className="api-settings">
      <summary>Configure API</summary>
      <form id="api-settings-form" onSubmit={handleSubmit}>
        <label htmlFor="api-base-url">API base URL</label>
        <input id="api-base-url" name="api_base_url" value={value} onChange={(event) => setValue(event.target.value)} required />
        <button className="button button-primary button-small" type="submit">Save API URL</button>
        {notice && <div className={`notice notice-${notice.type} visible`} role={notice.type === "error" ? "alert" : "status"}>{notice.message}</div>}
      </form>
    </details>
  );
}

function Header({ role, apiBase, onApiBaseChange, session, monitorMode = false }) {
  const config = roleConfig[role];
  function signOut() {
    clearPreviewSession();
    window.location.hash = role === "ADMIN" || monitorMode ? "#/" : "#/LOGIN";
  }

  return (
    <header className="app-header">
      <div className="breadcrumb">DashBite <span aria-hidden="true">/</span> <strong>{config.label}</strong></div>
      <div className="header-right">
        <span className="api-indicator"><span className="api-indicator-dot" aria-hidden="true" />API: {apiBase}</span>
        <ApiSettings onSaved={onApiBaseChange} />
        <span className="user-chip"><span className="user-avatar" aria-hidden="true">{role[0]}</span>{session?.email || `${config.label} preview`}</span>
        {(session || role === "ADMIN" || monitorMode) && <button className="text-button sign-out-button" type="button" onClick={signOut}>Log out</button>}
      </div>
    </header>
  );
}

function MetricGrid({ role, pendingAccountCount }) {
  const metrics = role === "ADMIN"
    ? roleConfig[role].metrics.map((metric, index) => index === 0 ? [metric[0], String(pendingAccountCount), metric[2], metric[3]] : metric)
    : roleConfig[role].metrics;
  return <section className="metric-grid" aria-label="Dashboard summary">
    {metrics.map(([label, value, note, icon]) => <article className="metric-card" key={label}>
      <div className="metric-top"><span>{label}</span><span className="metric-icon" aria-hidden="true">{icon}</span></div>
      <div className="metric-value">{value}</div><div className="metric-note">{note}</div>
    </article>)}
  </section>;
}

function Status({ value }) {
  return <span className={`status status-${value.toLowerCase()}`}>{value}</span>;
}

function OrderTable({ orders }) {
  return <div className="table-wrap"><table>
    <thead><tr><th>ORDER</th><th>RESTAURANT</th><th>TOTAL</th><th>ORDER STATUS</th><th>PAYMENT</th></tr></thead>
    <tbody>{orders.map((order) => <tr key={order.id}>
      <td className="primary-cell">{order.id}</td><td>{order.restaurant}</td><td>{formatAmount(order.total)}</td>
      <td><Status value={order.status} /></td><td><Status value={order.payment} /></td>
    </tr>)}</tbody>
  </table></div>;
}

function OrderPanel() {
  return <section className="panel">
    <div className="panel-heading"><h2>Recent orders</h2><small>Sample preview data</small></div>
    {sampleOrders.length ? <OrderTable orders={sampleOrders} /> : <div className="empty-state">No sample orders in this queue.</div>}
  </section>;
}

function ActivityPanel({ role }) {
  const items = role === "ADMIN"
    ? [["♙", "Account approval queue", "Review restaurant and rider applications"], ["$", "2 payments flagged", "FLAGGED_DISCREPANCY · sample data"], ["▤", "12 orders in progress", "Illustrative operations data"]]
    : role === "RIDER"
      ? [["➤", "New nearby offer", "SEARCHING_RIDER · 1 km radius"], ["◷", "Recent delivery", "Order ORD-2047 · PICKED_UP"], ["✓", "Today's deliveries", "7 completed · sample data"]]
      : [["▤", "Order update", "ORD-2048 · SEARCHING_RIDER"], ["✓", "Delivered", "ORD-2046 · SETTLED_TO_RESTAURANT"], ["⌖", "Delivery address", "Sample preview information"]];
  return <section className="panel">
    <div className="panel-heading"><h2>{role === "ADMIN" ? "Needs attention" : "Recent activity"}</h2><small>Sample preview data</small></div>
    <div className="activity-list">{items.map(([icon, title, detail]) => <div className="activity-item" key={title}>
      <span className="activity-icon" aria-hidden="true">{icon}</span><div><strong>{title}</strong><small>{detail}</small></div>
    </div>)}</div>
  </section>;
}

function OfferTable() {
  return <section className="panel">
    <div className="panel-heading"><h2>Nearby offers</h2><small>Sample preview data</small></div>
    <div className="table-wrap"><table><thead><tr><th>ORDER</th><th>RESTAURANT</th><th>DISTANCE</th><th>RING RADIUS</th><th>OFFER STATUS</th></tr></thead>
      <tbody>{sampleOffers.map((offer) => <tr key={offer.orderId}>
        <td className="primary-cell">{offer.orderId}</td><td>{offer.restaurant}</td><td>{offer.distance}</td><td>{offer.radius} km</td><td><Status value={offer.status} /></td>
      </tr>)}</tbody>
    </table></div>
    <p className="permission-note">Offer blast progression is system-only. The sample offers do not represent live dispatch data.</p>
  </section>;
}

function AdminQueues({ onPendingCountChange }) {
  const payments = [
    { id: "PAY-031", status: "FLAGGED_DISCREPANCY" },
    { id: "PAY-029", status: "REMITTED_TO_ADMIN" },
  ];
  const unavailable = <span className="disabled-action">Endpoint not defined</span>;
  return <>
    <section className="panel">
      <div className="panel-heading"><h2>Role permissions</h2><small>v1.0 contract</small></div>
      <div className="table-wrap"><table><thead><tr><th>ROLE</th><th>ACTION</th><th>FRONTEND AVAILABILITY</th></tr></thead>
        <tbody>
          <tr><td className="primary-cell">CUSTOMER</td><td>Create Order</td><td><span className="status status-approved">API defined</span></td></tr>
          <tr><td className="primary-cell">RESTAURANT</td><td>Accept/Reject Order</td><td>{unavailable}</td></tr>
          <tr><td className="primary-cell">System Only</td><td>Blast Offer</td><td><span className="disabled-action">System controlled</span></td></tr>
          <tr><td className="primary-cell">RIDER</td><td>Claim Offer</td><td><span className="status status-approved">API defined</span></td></tr>
          <tr><td className="primary-cell">ADMIN</td><td>Change Delivery Fee</td><td>{unavailable}</td></tr>
          <tr><td className="primary-cell">ADMIN</td><td>Approve Restaurant/Rider Accounts</td><td><span className="status status-approved">Local preview</span></td></tr>
        </tbody>
      </table></div>
    </section>
    <AdminApprovalQueue onPendingCountChange={onPendingCountChange} />
    <section className="panel">
      <div className="panel-heading"><h2>Payment reconciliation</h2><small>Sample preview data</small></div>
      <div className="table-wrap"><table><thead><tr><th>REFERENCE</th><th>PAYMENT STATUS</th></tr></thead>
        <tbody>{payments.map((payment) => <tr key={payment.id}><td className="primary-cell">{payment.id}</td><td><Status value={payment.status} /></td></tr>)}</tbody>
      </table></div>
      <p className="permission-note">Payment statuses shown: {PAYMENT_STATUSES.join(", ")}.</p>
    </section>
  </>;
}

function RoleContent({ role, canOperate, disabledMessage, onPendingCountChange }) {
  if (role === "CUSTOMER") return <div className="content-grid"><div><OrderPanel /></div><div><ActivityPanel role={role} /><CustomerOrderForm disabled={!canOperate} disabledMessage={disabledMessage} /></div></div>;
  if (role === "RIDER") return <div className="content-grid"><div><OfferTable /><ActivityPanel role={role} /></div><div><RiderOfferForm disabled={!canOperate} disabledMessage={disabledMessage} /></div></div>;
  return <div className="content-grid"><div><AdminQueues onPendingCountChange={onPendingCountChange} /></div><div><ActivityPanel role={role} /></div></div>;
}

function ContractReference() {
  const group = (title, statuses) => <div className="contract-status-group" key={title}>
    <strong>{title}</strong><div className="status-legend">{statuses.map((status) => <Status value={status} key={status} />)}</div>
  </div>;
  return <details className="contract-reference">
    <summary>v1.0 contract status vocabulary</summary>
    {group("Order statuses", ORDER_STATUSES)}
    {group("Account statuses", ACCOUNT_STATUSES)}
    {group("Payment statuses", PAYMENT_STATUSES)}
    {group("Offer statuses", OFFER_STATUSES)}
    {group("Payment methods", PAYMENT_METHODS)}
  </details>;
}

function Dashboard({ role }) {
  const config = roleConfig[role];
  const [apiBase, setApiBase] = useState(getApiBaseUrl);
  const [pendingAccountCount, setPendingAccountCount] = useState(0);
  const session = getPreviewSession(role);
  const monitorMode = role !== "ADMIN" && isAdminMonitoring();
  const canOperate = !monitorMode && (!session || session.status === "APPROVED");
  return <div className="app-shell">
    <Sidebar role={role} monitorMode={monitorMode} />
    <div className="app-main">
      <Header role={role} apiBase={apiBase} onApiBaseChange={setApiBase} session={session} monitorMode={monitorMode} />
      <div className="page-heading"><div><h1>{config.title}</h1><p>{config.subtitle}</p></div><span className="preview-label">PREVIEW DATA</span></div>
      {monitorMode && <div className="demo-banner admin-monitor-banner"><strong>Admin monitoring view:</strong> you can inspect this role's dashboard without signing in as that user. Controls are read-only, and the displayed records are sample preview data.</div>}
      {session?.status === "PENDING_APPROVAL" && <div className="demo-banner account-pending-banner" role="status"><strong>Pending admin approval:</strong> you can view this dashboard, but operational actions are blocked until your account is approved.</div>}
      <div className="demo-banner"><strong>Frontend preview:</strong> dashboard figures are sample data, not live records. API forms send contract fields to <code>{apiBase}</code>; no backend or authentication is included.</div>
      {role === "ADMIN" && <div className="demo-banner"><strong>Admin preview only:</strong> this portal has no admin authentication. Approvals affect preview accounts saved in this browser only; never use it to approve real applicants.</div>}
      <MetricGrid role={role} pendingAccountCount={pendingAccountCount} />
      <RoleContent
        role={role}
        canOperate={canOperate}
        disabledMessage={monitorMode
          ? "Read-only admin monitoring: operational actions are disabled in this view."
          : "Your account is pending admin approval. Operational actions are blocked until your account is approved."}
        onPendingCountChange={setPendingAccountCount}
      />
      <ContractReference />
    </div>
  </div>;
}

export default function App() {
  const [role, setRole] = useState(readAuthorizedRoute);

  useEffect(() => {
    const updateRoute = () => setRole(readAuthorizedRoute());
    window.addEventListener("hashchange", updateRoute);
    return () => window.removeEventListener("hashchange", updateRoute);
  }, []);

  if (role === "LOGIN") return <LoginFlow />;
  if (role === "SIGNUP") return <SignupFlow />;
  if (role === "ADMIN_LOGIN") return <AdminLogin />;
  if (role === "ADMIN") return <AdminPortal />;
  if (role === "CUSTOMER") return <CustomerPortal />;
  if (role === "RESTAURANT") return <RestaurantPortal />;
  if (role === "RIDER") return <RiderPortal />;
  return role && ROLES.includes(role) ? <Dashboard role={role} /> : <HomePage />;
}
