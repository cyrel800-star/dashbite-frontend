export function RiderSectionHeading({ eyebrow, title, description, action }) {
  return <div className="rider-section-heading">
    <div><p>{eyebrow}</p><h2>{title}</h2><span>{description}</span></div>
    {action}
  </div>;
}

export function RiderNotice({ notice, onDismiss }) {
  if (!notice) return null;
  return <div className={`rider-notice rider-notice-${notice.type}`} role={notice.type === "error" ? "alert" : "status"}>
    <span>{notice.message}</span>{onDismiss && <button type="button" aria-label="Dismiss message" onClick={onDismiss}>×</button>}
  </div>;
}

export function RiderStatus({ value }) {
  return <span className={`status status-${String(value).toLowerCase()}`}>{String(value).replaceAll("_", " ")}</span>;
}

export function RiderEmptyState({ icon = "➤", title, description, action }) {
  return <div className="rider-empty-state"><span aria-hidden="true">{icon}</span><strong>{title}</strong><p>{description}</p>{action}</div>;
}

export function RiderMetric({ icon, label, value, note, accent = "orange" }) {
  return <article className="rider-metric-card">
    <span className={`rider-metric-icon is-${accent}`} aria-hidden="true">{icon}</span>
    <div><p>{label}</p><strong>{value}</strong>{note && <small>{note}</small>}</div>
  </article>;
}

export function RiderMoney({ value, digits = 2 }) {
  const amount = Number.isFinite(Number(value)) ? Number(value) : 0;
  return <span>₱{amount.toLocaleString(undefined, { minimumFractionDigits: digits, maximumFractionDigits: digits })}</span>;
}
