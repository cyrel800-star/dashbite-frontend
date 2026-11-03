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
    metrics: [["Pending accounts", "4", "Approval queue", "♙"], ["Active orders", "12", "Currently in progress", "▤"], ["Flagged payments", "2", "Needs review", "$"], ["Restaurants", "36", "Illustrative total", "⌂"]],
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
  const route = window.location.hash.match(/^#\/(CUSTOMER|RESTAURANT|RIDER|ADMIN)$/i);
  return route ? route[1].toUpperCase() : null;
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
        <span className="version-pill">CORE CONTRACT <strong>v1.0</strong></span>
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
            {cards.map(([role, iconClass, detail]) => <a className="role-card" href={`#/${role}`} key={role}>
              <span className={`role-icon ${iconClass}`} aria-hidden="true">{roleConfig[role].icon}</span>
              <span className="role-card-copy"><strong>{roleConfig[role].label}</strong><small>{detail}</small></span>
              <span className="role-arrow" aria-hidden="true">↗</span>
            </a>)}
          </div>
          <p className="preview-disclaimer"><strong>Preview only.</strong> Sample dashboard data is illustrative. API actions require a reachable backend; sign-in is not wired because the v1.0 contract does not define its request fields.</p>
        </section>
      </main>
      <footer className="home-footer"><span>© 2026 DashBite</span><span>Made for good food days <span className="footer-heart">♥</span></span></footer>
    </div>
  );
}

function Sidebar({ role }) {
  return (
    <aside className="sidebar">
      <Brand />
      <p className="sidebar-label">WORKSPACE</p>
      <nav className="side-nav" aria-label="DashBite roles">
        {ROLES.map((item) => <a className={`side-link${item === role ? " active" : ""}`} href={`#/${item}`} aria-current={item === role ? "page" : undefined} key={item}>
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

function Header({ role, apiBase, onApiBaseChange }) {
  const config = roleConfig[role];
  return (
    <header className="app-header">
      <div className="breadcrumb">DashBite <span aria-hidden="true">/</span> <strong>{config.label}</strong></div>
      <div className="header-right">
        <span className="api-indicator"><span className="api-indicator-dot" aria-hidden="true" />API: {apiBase}</span>
        <ApiSettings onSaved={onApiBaseChange} />
        <span className="user-chip"><span className="user-avatar" aria-hidden="true">{role[0]}</span>{config.label} preview</span>
      </div>
    </header>
  );
}

function MetricGrid({ role }) {
  return <section className="metric-grid" aria-label="Dashboard summary">
    {roleConfig[role].metrics.map(([label, value, note, icon]) => <article className="metric-card" key={label}>
      <div className="metric-top"><span>{label}</span><span className="metric-icon" aria-hidden="true">{icon}</span></div>
      <div className="metric-value">{value}</div><div className="metric-note">{note}</div>
    </article>)}
  </section>;
}

function Status({ value }) {
  return <span className={`status status-${value.toLowerCase()}`}>{value}</span>;
}

function OrderTable({ orders, actionColumn = false }) {
  return <div className="table-wrap"><table>
    <thead><tr><th>ORDER</th><th>RESTAURANT</th><th>TOTAL</th><th>ORDER STATUS</th><th>PAYMENT</th>{actionColumn && <th>ACTION</th>}</tr></thead>
    <tbody>{orders.map((order) => <tr key={order.id}>
      <td className="primary-cell">{order.id}</td><td>{order.restaurant}</td><td>{formatAmount(order.total)}</td>
      <td><Status value={order.status} /></td><td><Status value={order.payment} /></td>
      {actionColumn && <td><span className="disabled-action" title="No accept/reject endpoint is defined in v1.0">Unavailable in v1.0</span></td>}
    </tr>)}</tbody>
  </table></div>;
}

function OrderPanel({ role }) {
  const orders = role === "RESTAURANT" ? sampleOrders.filter(({ status }) => ["PENDING", "ACCEPTED"].includes(status)) : sampleOrders;
  return <section className="panel">
    <div className="panel-heading"><h2>{role === "CUSTOMER" ? "Recent orders" : "Order queue"}</h2><small>Sample preview data</small></div>
    {orders.length ? <OrderTable orders={orders} actionColumn={role === "RESTAURANT"} /> : <div className="empty-state">No sample orders in this queue.</div>}
    {role === "RESTAURANT" && <p className="permission-note">Accept/reject controls are intentionally unavailable: the v1.0 contract does not define a restaurant order endpoint.</p>}
  </section>;
}

function ActivityPanel({ role }) {
  const items = role === "ADMIN"
    ? [["♙", "4 accounts awaiting review", "Approval endpoint not defined in v1.0"], ["$", "2 payments flagged", "FLAGGED_DISCREPANCY"], ["▤", "12 orders in progress", "Illustrative operations data"]]
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

function AdminQueues() {
  const accounts = [
    { name: "Sample Restaurant", role: "RESTAURANT", status: "PENDING_APPROVAL" },
    { name: "Sample Rider", role: "RIDER", status: "PENDING_APPROVAL" },
  ];
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
          <tr><td className="primary-cell">ADMIN</td><td>Approve Accounts</td><td>{unavailable}</td></tr>
        </tbody>
      </table></div>
    </section>
    <section className="panel">
      <div className="panel-heading"><h2>Account review queue</h2><small>Sample preview data</small></div>
      <div className="table-wrap"><table><thead><tr><th>ACCOUNT</th><th>ROLE</th><th>ACCOUNT STATUS</th><th>ACTION</th></tr></thead>
        <tbody>{accounts.map((account) => <tr key={account.name}><td className="primary-cell">{account.name}</td><td>{account.role}</td><td><Status value={account.status} /></td><td><span className="disabled-action" title="No account approval endpoint is defined in v1.0">Unavailable in v1.0</span></td></tr>)}</tbody>
      </table></div>
      <p className="permission-note">The ADMIN role may approve accounts, but the v1.0 contract does not define an approval endpoint.</p>
    </section>
    <section className="panel">
      <div className="panel-heading"><h2>Payment reconciliation</h2><small>Sample preview data</small></div>
      <div className="table-wrap"><table><thead><tr><th>REFERENCE</th><th>PAYMENT STATUS</th></tr></thead>
        <tbody>{payments.map((payment) => <tr key={payment.id}><td className="primary-cell">{payment.id}</td><td><Status value={payment.status} /></td></tr>)}</tbody>
      </table></div>
      <p className="permission-note">Payment statuses shown: {PAYMENT_STATUSES.join(", ")}.</p>
    </section>
  </>;
}

function RoleContent({ role }) {
  if (role === "CUSTOMER") return <div className="content-grid"><div><OrderPanel role={role} /></div><div><ActivityPanel role={role} /><CustomerOrderForm /></div></div>;
  if (role === "RESTAURANT") return <div className="content-grid"><div><OrderPanel role={role} /></div><div><ActivityPanel role={role} /></div></div>;
  if (role === "RIDER") return <div className="content-grid"><div><OfferTable /><ActivityPanel role={role} /></div><div><RiderOfferForm /></div></div>;
  return <div className="content-grid"><div><AdminQueues /></div><div><ActivityPanel role={role} /></div></div>;
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
  return <div className="app-shell">
    <Sidebar role={role} />
    <div className="app-main">
      <Header role={role} apiBase={apiBase} onApiBaseChange={setApiBase} />
      <div className="page-heading"><div><h1>{config.title}</h1><p>{config.subtitle}</p></div><span className="preview-label">PREVIEW DATA</span></div>
      <div className="demo-banner"><strong>Frontend preview:</strong> dashboard figures are sample data, not live records. API forms send contract fields to <code>{apiBase}</code>; no backend or authentication is included.</div>
      <MetricGrid role={role} />
      <RoleContent role={role} />
      <ContractReference />
    </div>
  </div>;
}

export default function App() {
  const [role, setRole] = useState(readRoute);

  useEffect(() => {
    const updateRoute = () => setRole(readRoute());
    window.addEventListener("hashchange", updateRoute);
    return () => window.removeEventListener("hashchange", updateRoute);
  }, []);

  return role && ROLES.includes(role) ? <Dashboard role={role} /> : <HomePage />;
}
