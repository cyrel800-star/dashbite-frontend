import { useEffect, useMemo, useState } from "react";
import AdminApprovalQueue from "../AdminApprovalQueue.jsx";
import { ORDER_STATUSES } from "../../data/constants.js";
import { listPreviewAccounts, subscribeToPreviewAccounts } from "../../lib/previewAccounts.js";
import {
  ADMIN_SAMPLE_ACCOUNTS,
  ADMIN_SAMPLE_ORDERS,
  ADMIN_SAMPLE_REVIEWS,
  getAdminOrderStatus,
  getAdminPaymentStatus,
} from "../../lib/adminPortal.js";
import { AdminEmptyState, AdminMoney, AdminSectionHeading, AdminStatus } from "./AdminUI.jsx";

function AdminMetric({ icon, label, value, note, highlighted = false }) {
  return <article className={`admin-metric${highlighted ? " is-highlighted" : ""}`}>
    <div><p>{label}</p><span aria-hidden="true">{icon}</span></div><strong>{value}</strong><small>{note}</small>
  </article>;
}

export function AdminDashboard({ data, onNavigate }) {
  const orders = ADMIN_SAMPLE_ORDERS;
  const delivered = orders.filter((order) => order.status === "DELIVERED");
  const active = orders.filter((order) => !["DELIVERED", "CANCELLED", "REJECTED", "NO_RIDER"].includes(order.status));
  const sales = delivered.reduce((sum, order) => sum + order.foodCost, 0);
  const commission = delivered.reduce((sum, order) => sum + order.commission, 0);
  const flagged = orders.filter((order) => order.paymentStatus === "FLAGGED_DISCREPANCY").length;
  const newestOrders = [...orders].sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt)).slice(0, 5);

  return <div className="admin-page-content">
    <section className="admin-welcome-card"><div><p className="admin-eyebrow">DASHBITE OPERATIONS</p><h2>One clear view<br /><span>of your platform.</span></h2><p>Review system activity and keep each part of the DashBite preview in view.</p><div className="admin-welcome-actions"><button className="button admin-dark-button" type="button" onClick={() => onNavigate("ORDERS")}>Review orders <span aria-hidden="true">→</span></button><button className="button admin-light-button" type="button" onClick={() => onNavigate("ACCOUNTS")}>Manage accounts</button></div></div><div className="admin-welcome-visual" aria-hidden="true"><span>▦</span><i>✦</i><b>●</b></div></section>
    <div className="admin-metric-grid">
      <AdminMetric icon="▤" label="Total orders" value={orders.length} note="Sample records" />
      <AdminMetric icon="➤" label="Active deliveries" value={active.length} note="Illustrative order flow" />
      <AdminMetric icon="₱" label="Food sales" value={<AdminMoney value={sales} digits={0} />} note="Completed sample orders" />
      <AdminMetric icon="✦" label="Platform commission" value={<AdminMoney value={commission} digits={0} />} note="Illustrative estimate" highlighted />
      <AdminMetric icon="♧" label="Restaurants" value="3" note="Sample catalog" />
      <AdminMetric icon="!" label="Flagged payments" value={flagged} note="Requires review in a real system" />
    </div>
    <div className="admin-dashboard-grid">
      <section className="admin-panel admin-recent-orders">
        <div className="admin-panel-heading"><div><h3>Recent orders</h3><p>Latest sample order activity</p></div><button className="admin-link-button" type="button" onClick={() => onNavigate("ORDERS")}>All orders →</button></div>
        <div className="admin-table-scroll"><table className="admin-table"><thead><tr><th>ORDER</th><th>CUSTOMER</th><th>RESTAURANT</th><th>STATUS</th><th>TOTAL</th></tr></thead><tbody>
          {newestOrders.map((order) => <tr key={order.id} onClick={() => onNavigate(`ORDER/${order.id}`)} tabIndex="0" onKeyDown={(event) => { if (event.key === "Enter") onNavigate(`ORDER/${order.id}`); }}><td><strong>{order.id}</strong></td><td>{order.customer}</td><td>{order.restaurant}</td><td><AdminStatus value={getAdminOrderStatus(order)} /></td><td><AdminMoney value={order.total} /></td></tr>)}
        </tbody></table></div>
      </section>
      <section className="admin-panel admin-quick-actions">
        <div className="admin-panel-heading"><div><h3>Operations</h3><p>Quick access to admin tools</p></div></div>
        <button type="button" onClick={() => onNavigate("ACCOUNTS")}><span>♙</span><span><strong>Account approvals</strong><small>Review Restaurant and Rider applications</small></span><b>→</b></button>
        <button type="button" onClick={() => onNavigate("REPORTS")}><span>↗</span><span><strong>Platform reports</strong><small>Explore sample sales and order trends</small></span><b>→</b></button>
        <button type="button" onClick={() => onNavigate("REVIEWS")}><span>★</span><span><strong>Review moderation</strong><small>Inspect and manage sample reviews</small></span><b>→</b></button>
        <button type="button" onClick={() => onNavigate("SETTINGS")}><span>⚙</span><span><strong>System settings</strong><small>Preview branding and platform options</small></span><b>→</b></button>
      </section>
    </div>
    <section className="admin-panel">
      <div className="admin-panel-heading"><div><h3>Pending account applications</h3><p>Approve local Restaurant and Rider preview accounts.</p></div><button className="admin-link-button" type="button" onClick={() => onNavigate("ACCOUNTS")}>Account directory →</button></div>
      <AdminApprovalQueue onPendingCountChange={() => {}} />
    </section>
    <p className="admin-inline-note">All dashboard counts, orders, financial figures, and operational data are illustrative. No live system is monitored by this frontend.</p>
  </div>;
}

export function AdminAccounts() {
  const [accounts, setAccounts] = useState([...ADMIN_SAMPLE_ACCOUNTS]);
  const [filter, setFilter] = useState("ALL");
  const [query, setQuery] = useState("");
  const [error, setError] = useState("");

  useEffect(() => {
    function refresh() {
      try {
        const registered = listPreviewAccounts();
        const customers = ADMIN_SAMPLE_ACCOUNTS.filter((sample) => !registered.some((account) => account.email === sample.email));
        setAccounts([...customers, ...registered]);
        setError("");
      } catch (loadError) {
        setError(loadError instanceof Error ? loadError.message : "Could not load preview accounts.");
      }
    }
    refresh();
    return subscribeToPreviewAccounts(refresh);
  }, []);

  const visible = accounts.filter((account) => {
    const roleMatches = filter === "ALL" || (filter === "PENDING" ? account.account_status === "PENDING_APPROVAL" : account.role === filter);
    const queryMatches = `${account.name} ${account.email} ${account.role}`.toLowerCase().includes(query.trim().toLowerCase());
    return roleMatches && queryMatches;
  });
  const filters = [
    ["ALL", "All accounts"],
    ["CUSTOMER", "Customers"],
    ["RESTAURANT", "Restaurants"],
    ["RIDER", "Riders"],
    ["PENDING", "Pending"],
  ];

  return <div className="admin-page-content">
    <AdminSectionHeading eyebrow="PEOPLE & ACCESS" title="Account management" description="Review local preview accounts and pending partner applications." />
    <section className="admin-panel admin-account-queue"><div className="admin-panel-heading"><div><h3>Approval queue</h3><p>Restaurant and Rider accounts need approval before sign-in.</p></div></div><AdminApprovalQueue onPendingCountChange={() => {}} /></section>
    <section className="admin-panel admin-account-directory">
      <div className="admin-panel-heading"><div><h3>Account directory</h3><p>{accounts.length} sample and browser-local accounts · Admin is never registered here.</p></div><label className="admin-search"><span aria-hidden="true">⌕</span><input type="search" value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Search name or email" aria-label="Search accounts" /></label></div>
      <div className="admin-filter-tabs" role="tablist" aria-label="Filter accounts">{filters.map(([id, label]) => <button type="button" role="tab" aria-selected={filter === id} className={filter === id ? "is-active" : ""} key={id} onClick={() => setFilter(id)}>{label}</button>)}</div>
      {error && <div className="admin-notice admin-notice-error" role="alert">{error}</div>}
      {visible.length ? <div className="admin-table-scroll"><table className="admin-table"><thead><tr><th>ACCOUNT</th><th>CONTACT</th><th>ROLE</th><th>STATUS</th><th>ACCESS</th></tr></thead><tbody>{visible.map((account) => <tr key={account.email}><td><div className="admin-person"><span>{account.name.trim()[0]?.toUpperCase() || "?"}</span><strong>{account.name}</strong></div></td><td>{account.email}<small>{account.phone || "No phone on file"}</small></td><td>{account.role}</td><td><AdminStatus value={account.account_status} /></td><td>{account.account_status === "APPROVED" ? "Preview access enabled" : "Blocked until approved"}</td></tr>)}</tbody></table></div> : <AdminEmptyState icon="⌕" title="No accounts match" description="Try another search or account filter." />}
    </section>
    <p className="admin-inline-note">The account directory combines illustrative sample customers with Restaurant/Rider preview applications saved in this browser.</p>
  </div>;
}

export function AdminOrders({ onNavigate }) {
  const [status, setStatus] = useState("ALL");
  const [query, setQuery] = useState("");
  const visible = useMemo(() => ADMIN_SAMPLE_ORDERS.filter((order) => {
    const statusMatches = status === "ALL" || order.status === status;
    const queryMatches = `${order.id} ${order.customer} ${order.restaurant} ${order.rider}`.toLowerCase().includes(query.trim().toLowerCase());
    return statusMatches && queryMatches;
  }), [query, status]);
  const statuses = ["ALL", ...ORDER_STATUSES];

  function exportCsv() {
    const escape = (value) => `"${String(value).replaceAll('"', '""')}"`;
    const rows = [
      ["Order", "Customer", "Restaurant", "Rider", "Status", "Payment", "Total", "Created"],
      ...visible.map((order) => [order.id, order.customer, order.restaurant, order.rider, order.status, order.paymentStatus, order.total.toFixed(2), order.createdAt]),
    ];
    const content = rows.map((row) => row.map(escape).join(",")).join("\r\n");
    const url = URL.createObjectURL(new Blob([content], { type: "text/csv;charset=utf-8" }));
    const link = document.createElement("a");
    link.href = url;
    link.download = "dashbite-preview-orders.csv";
    link.click();
    URL.revokeObjectURL(url);
  }

  return <div className="admin-page-content">
    <AdminSectionHeading eyebrow="ORDER OPERATIONS" title="All orders" description={`${visible.length} of ${ADMIN_SAMPLE_ORDERS.length} illustrative records.`} action={<button className="button admin-outline-button" type="button" onClick={exportCsv}>Export CSV ↓</button>} />
    <section className="admin-panel">
      <div className="admin-order-filters"><label className="admin-search"><span aria-hidden="true">⌕</span><input type="search" value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Order, customer, restaurant..." aria-label="Search orders" /></label><label className="admin-select"><span className="sr-only">Order status</span><select value={status} onChange={(event) => setStatus(event.target.value)}>{statuses.map((item) => <option value={item} key={item}>{item === "ALL" ? "All statuses" : item.replaceAll("_", " ")}</option>)}</select></label></div>
      {visible.length ? <div className="admin-table-scroll"><table className="admin-table"><thead><tr><th>ORDER</th><th>PLACED</th><th>CUSTOMER</th><th>RESTAURANT</th><th>RIDER</th><th>STATUS</th><th>PAYMENT</th><th>TOTAL</th><th></th></tr></thead><tbody>{visible.map((order) => <tr key={order.id} onClick={() => onNavigate(`ORDER/${order.id}`)} tabIndex="0" onKeyDown={(event) => { if (event.key === "Enter") onNavigate(`ORDER/${order.id}`); }}><td><strong>{order.id}</strong></td><td>{new Intl.DateTimeFormat(undefined, { month: "short", day: "numeric", hour: "numeric", minute: "2-digit" }).format(new Date(order.createdAt))}</td><td>{order.customer}</td><td>{order.restaurant}</td><td>{order.rider}</td><td><AdminStatus value={getAdminOrderStatus(order)} /></td><td><AdminStatus value={getAdminPaymentStatus(order)} /></td><td><AdminMoney value={order.total} /></td><td><button className="admin-link-button" type="button" onClick={(event) => { event.stopPropagation(); onNavigate(`ORDER/${order.id}`); }}>Details →</button></td></tr>)}</tbody></table></div> : <AdminEmptyState icon="⌕" title="No matching orders" description="Try a different search or order status." />}
    </section>
    <p className="admin-inline-note">Export creates a CSV from these sample records in your browser. It is not a report of live orders.</p>
  </div>;
}

export function AdminOrderDetails({ orderId, onNavigate }) {
  const order = ADMIN_SAMPLE_ORDERS.find((item) => item.id === orderId);
  if (!order) return <section className="admin-panel"><AdminEmptyState icon="⌕" title="Order not found" description="This order ID is not in the current preview data." action={<button className="button button-primary" type="button" onClick={() => onNavigate("ORDERS")}>Back to orders</button>} /></section>;
  return <div className="admin-page-content">
    <AdminSectionHeading eyebrow="ORDER DETAILS" title={`Order ${order.id}`} description={`Placed ${new Intl.DateTimeFormat(undefined, { dateStyle: "long", timeStyle: "short" }).format(new Date(order.createdAt))}.`} action={<button className="admin-link-button" type="button" onClick={() => onNavigate("ORDERS")}>← All orders</button>} />
    <div className="admin-order-detail-grid">
      <section className="admin-panel">
        <div className="admin-panel-heading"><div><h3>Order progress</h3><p>Current sample order status</p></div><AdminStatus value={getAdminOrderStatus(order)} /></div>
        <div className="admin-order-timeline">{ORDER_STATUSES.slice(0, 6).map((item, index) => {
          const currentIndex = ORDER_STATUSES.indexOf(order.status);
          const complete = index <= currentIndex && !["CANCELLED", "REJECTED", "NO_RIDER"].includes(order.status);
          return <div className={complete ? "is-complete" : ""} key={item}><i>{complete ? "✓" : index + 1}</i><span><strong>{item.replaceAll("_", " ")}</strong><small>{complete ? "Included in sample order history" : "Not reached"}</small></span></div>;
        })}</div>
      </section>
      <section className="admin-panel">
        <div className="admin-panel-heading"><div><h3>Customer & delivery</h3><p>Order participants and drop-off</p></div></div>
        <dl className="admin-detail-list"><div><dt>Customer</dt><dd>{order.customer}</dd></div><div><dt>Restaurant</dt><dd>{order.restaurant}</dd></div><div><dt>Rider</dt><dd>{order.rider}</dd></div><div><dt>Delivery address</dt><dd>{order.address}</dd></div></dl>
      </section>
    </div>
    <section className="admin-panel">
      <div className="admin-panel-heading"><div><h3>Items</h3><p>Sample order contents</p></div></div>
      <div className="admin-breakdown-list">{order.items.map((item) => <div key={item.name}><span>{item.quantity} × {item.name}</span><strong><AdminMoney value={item.price * item.quantity} /></strong></div>)}</div>
      <div className="admin-order-totals"><span>Food cost</span><strong><AdminMoney value={order.foodCost} /></strong><span>Delivery fee</span><strong><AdminMoney value={order.deliveryFee} /></strong><span>Platform commission</span><strong><AdminMoney value={order.commission} /></strong><span className="is-total">Order total</span><strong className="is-total"><AdminMoney value={order.total} /></strong></div>
      <div className="admin-order-payment"><div><strong>Payment</strong><small>{order.paymentMethod} · {order.paymentStatus.replaceAll("_", " ")}</small></div><AdminStatus value={getAdminPaymentStatus(order)} /></div>
    </section>
    <p className="admin-inline-note">This order detail is an illustrative preview, not a live transaction record.</p>
  </div>;
}

export function AdminReports() {
  const [period, setPeriod] = useState("7days");
  const days = useMemo(() => {
    const date = new Date();
    date.setHours(0, 0, 0, 0);
    const count = period === "7days" ? 7 : period === "30days" ? 30 : date.getDate();
    if (period === "month") date.setDate(1);
    else date.setDate(date.getDate() - count + 1);
    return Array.from({ length: count }, (_, index) => {
      const current = new Date(date);
      current.setDate(date.getDate() + index);
      const orders = ADMIN_SAMPLE_ORDERS.filter((order) => new Date(order.createdAt).toDateString() === current.toDateString());
      return {
        key: current.toDateString(),
        label: new Intl.DateTimeFormat(undefined, { month: "short", day: count === 7 ? "numeric" : undefined, weekday: count === 7 ? "short" : undefined }).format(current),
        count: orders.length,
        sales: orders.filter((order) => order.status === "DELIVERED").reduce((sum, order) => sum + order.foodCost, 0),
        commission: orders.filter((order) => order.status === "DELIVERED").reduce((sum, order) => sum + order.commission, 0),
      };
    });
  }, [period]);
  const totals = days.reduce((sum, day) => ({ orders: sum.orders + day.count, sales: sum.sales + day.sales, commission: sum.commission + day.commission }), { orders: 0, sales: 0, commission: 0 });
  const maxSales = Math.max(...days.map((day) => day.sales), 1);
  const restaurantSales = ADMIN_SAMPLE_ORDERS.filter((order) => order.status === "DELIVERED").reduce((groups, order) => ({ ...groups, [order.restaurant]: (groups[order.restaurant] || 0) + order.foodCost }), {});

  return <div className="admin-page-content">
    <AdminSectionHeading eyebrow="PLATFORM PERFORMANCE" title="Reports" description="Explore order volume, sales, and commission in the sample dataset." />
    <div className="admin-report-periods" role="tablist" aria-label="Report period">{[["7days", "Last 7 days"], ["30days", "Last 30 days"], ["month", "This month"]].map(([value, label]) => <button type="button" role="tab" aria-selected={period === value} className={period === value ? "is-active" : ""} key={value} onClick={() => setPeriod(value)}>{label}</button>)}</div>
    <div className="admin-metric-grid admin-report-metrics"><AdminMetric icon="▤" label="Orders" value={totals.orders} note="In selected period" /><AdminMetric icon="₱" label="Food sales" value={<AdminMoney value={totals.sales} digits={0} />} note="Delivered sample orders" /><AdminMetric icon="✦" label="Commission" value={<AdminMoney value={totals.commission} digits={0} />} note="Illustrative amount" highlighted /></div>
    <section className="admin-panel"><div className="admin-panel-heading"><div><h3>Daily sales</h3><p>Food sales from delivered orders in the selected window</p></div><span className="admin-report-unit">₱ PHP</span></div>
      {totals.orders ? <div className={`admin-report-chart${period === "month" ? " is-month" : ""}`} role="img" aria-label="Daily sales chart">{days.map((day) => <div className="admin-report-column" key={day.key} title={`${day.label}: ₱${day.sales.toFixed(2)}`}><div><i style={{ height: `${day.sales ? Math.max(7, day.sales / maxSales * 100) : 0}%` }} /></div><small>{day.label}</small></div>)}</div> : <AdminEmptyState icon="▥" title="No sample sales in this period" description="Change the selected report window to view the sample trend." />}</section>
    <section className="admin-panel"><div className="admin-panel-heading"><div><h3>Sales by restaurant</h3><p>Delivered sample orders only</p></div></div><div className="admin-restaurant-report">{Object.entries(restaurantSales).map(([name, sales]) => <div key={name}><span><strong>{name}</strong><small><AdminMoney value={sales} /> in food sales</small></span><div><i style={{ width: `${sales / Math.max(...Object.values(restaurantSales), 1) * 100}%` }} /></div></div>)}</div></section>
    <p className="admin-inline-note">Report data is illustrative and computed in the browser. It is not a financial statement or platform record.</p>
  </div>;
}

export function AdminReviews({ data, onChange }) {
  const [filter, setFilter] = useState("all");
  const reviews = ADMIN_SAMPLE_REVIEWS.map((review) => ({ ...review, status: data.reviewStatuses[review.id] || review.status }));
  const filtered = reviews.filter((review) => filter === "all" || (filter === "flagged" ? review.status === "flagged" || review.reportCount > 0 : review.status === filter));
  const filters = ["all", "published", "hidden", "flagged"];
  function setStatus(id, status) {
    onChange((current) => ({ ...current, reviewStatuses: { ...current.reviewStatuses, [id]: status } }));
  }
  return <div className="admin-page-content">
    <AdminSectionHeading eyebrow="CUSTOMER FEEDBACK" title="Review moderation" description="Inspect flagged feedback and manage the visibility of sample reviews." />
    <div className="admin-review-filters" role="tablist" aria-label="Review status">{filters.map((item) => <button type="button" role="tab" aria-selected={filter === item} className={filter === item ? "is-active" : ""} key={item} onClick={() => setFilter(item)}>{item[0].toUpperCase() + item.slice(1)} <span>{reviews.filter((review) => item === "all" || (item === "flagged" ? review.status === "flagged" || review.reportCount > 0 : review.status === item)).length}</span></button>)}</div>
    {filtered.length ? <div className="admin-review-list">{filtered.map((review) => <article className="admin-panel admin-review-card" key={review.id}>
      <div className="admin-review-top"><div className="admin-person"><span>{review.customer[0]}</span><div><strong>{review.customer}</strong><small>{review.restaurant} · {new Intl.DateTimeFormat(undefined, { month: "short", day: "numeric", year: "numeric" }).format(new Date(review.createdAt))}</small></div></div><AdminStatus value={review.status} /></div>
      <div className="admin-review-stars" aria-label={`${review.rating} out of 5 stars`}>{"★".repeat(review.rating)}<span>{"★".repeat(5 - review.rating)}</span></div>
      <strong className="admin-review-title">{review.title}</strong><p className="admin-review-body">{review.body}</p>
      <div className="admin-review-foot"><span>{review.reportCount ? `⚑ ${review.reportCount} report${review.reportCount > 1 ? "s" : ""}` : "No reports"} · {review.id}</span><div>{review.status === "published" ? <button className="button admin-small-button admin-danger-outline" type="button" onClick={() => setStatus(review.id, "hidden")}>Hide review</button> : <button className="button admin-small-button admin-outline-button" type="button" onClick={() => setStatus(review.id, "published")}>Publish review</button>}</div></div>
    </article>)}</div> : <section className="admin-panel"><AdminEmptyState icon="★" title="No reviews in this filter" description="Choose another status to see more sample reviews." /></section>}
    <p className="admin-inline-note">Review visibility changes are stored only in this browser. No review or moderation API is connected.</p>
  </div>;
}

export function AdminSettings({ data, onChange }) {
  const [draft, setDraft] = useState(data.settings);
  const [notice, setNotice] = useState("");
  useEffect(() => setDraft(data.settings), [data.settings]);
  function submit(event) {
    event.preventDefault();
    const name = draft.platformName.trim();
    const supportEmail = draft.supportEmail.trim();
    const commission = Number(draft.commissionPercent);
    if (!name || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(supportEmail)) {
      setNotice("Enter a platform name and a valid support email.");
      return;
    }
    if (!Number.isFinite(commission) || commission < 0 || commission > 100) {
      setNotice("Commission must be between 0 and 100 percent.");
      return;
    }
    onChange((current) => ({ ...current, settings: { ...draft, platformName: name, supportEmail, commissionPercent: commission.toString() } }));
    setNotice("Admin settings saved in this browser preview.");
  }
  return <div className="admin-page-content">
    <AdminSectionHeading eyebrow="PLATFORM CONFIGURATION" title="Settings" description="Preview basic branding and platform options." />
    <section className="admin-panel admin-settings-panel"><div className="admin-panel-heading"><div><h3>Platform settings</h3><p>Settings are local examples and do not configure a live service.</p></div><span className="admin-settings-icon">⚙</span></div>
      <form className="admin-settings-form" onSubmit={submit}>
        <label className="admin-field">Platform name<input value={draft.platformName} maxLength={80} onChange={(event) => setDraft((current) => ({ ...current, platformName: event.target.value }))} /></label>
        <label className="admin-field">Support email<input type="email" value={draft.supportEmail} maxLength={254} onChange={(event) => setDraft((current) => ({ ...current, supportEmail: event.target.value }))} /></label>
        <label className="admin-field">Platform commission (%)<input type="number" min="0" max="100" step="0.1" value={draft.commissionPercent} onChange={(event) => setDraft((current) => ({ ...current, commissionPercent: event.target.value }))} /><small>Illustrative configuration only. This does not change order calculations.</small></label>
        <div className="admin-setting-switch"><div><strong>Maintenance mode</strong><small>Preview toggle only. It does not affect portal access.</small></div><label><input type="checkbox" checked={draft.maintenanceMode} onChange={(event) => setDraft((current) => ({ ...current, maintenanceMode: event.target.checked }))} /><span>{draft.maintenanceMode ? "On" : "Off"}</span></label></div>
        <div className="admin-setting-switch"><div><strong>New registrations</strong><small>Preview toggle only. Signup remains available in this frontend.</small></div><label><input type="checkbox" checked={draft.newSignupsEnabled} onChange={(event) => setDraft((current) => ({ ...current, newSignupsEnabled: event.target.checked }))} /><span>{draft.newSignupsEnabled ? "Enabled" : "Disabled"}</span></label></div>
        {notice && <p className="admin-settings-notice" role="status">{notice}</p>}
        <div className="admin-settings-actions"><p className="admin-inline-note">Changes are saved to browser storage only.</p><button className="button button-primary" type="submit">Save settings</button></div>
      </form>
    </section>
    <section className="admin-panel admin-security-panel"><div className="admin-panel-heading"><div><h3>Authentication & security</h3><p>Admin preview login details and production guidance.</p></div></div><div><strong>Local demo credentials</strong><p>The Admin email and password are embedded in this frontend build. Anyone who can access the application bundle can inspect them.</p><small>Do not use these credentials for production. A real Admin portal needs server-side identity, secure password storage, authorization, and audit logging.</small></div></section>
  </div>;
}
