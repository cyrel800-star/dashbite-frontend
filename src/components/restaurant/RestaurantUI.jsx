export function RestaurantStatus({ value }) {
  return <span className={`status status-${String(value).toLowerCase()}`}>{value.replaceAll("_", " ")}</span>;
}

export function RestaurantPrice({ value }) {
  return <>{new Intl.NumberFormat(undefined, { style: "currency", currency: "PHP", maximumFractionDigits: 2 }).format(Number(value) || 0)}</>;
}

export function RestaurantMetric({ label, value, note, icon }) {
  return <article className="restaurant-metric">
    <div className="restaurant-metric-top"><span>{label}</span><span className="restaurant-metric-icon" aria-hidden="true">{icon}</span></div>
    <strong>{value}</strong>
    <small>{note}</small>
  </article>;
}

export function RestaurantNotice({ notice, onDismiss }) {
  if (!notice) return null;
  return <div className={`restaurant-notice restaurant-notice-${notice.type}`} role={notice.type === "error" ? "alert" : "status"}>
    <span>{notice.message}</span>
    {onDismiss && <button type="button" className="restaurant-notice-dismiss" aria-label="Dismiss message" onClick={onDismiss}>×</button>}
  </div>;
}

export function RestaurantSectionHeading({ eyebrow, title, description, action }) {
  return <div className="restaurant-section-heading">
    <div><p>{eyebrow}</p><h2>{title}</h2><span>{description}</span></div>
    {action}
  </div>;
}

export function RestaurantEmptyState({ icon = "▤", title, description, action }) {
  return <div className="restaurant-empty">
    <span aria-hidden="true">{icon}</span>
    <strong>{title}</strong>
    <p>{description}</p>
    {action}
  </div>;
}

export function formatRestaurantDate(value, options = { month: "short", day: "numeric", hour: "numeric", minute: "2-digit" }) {
  const date = new Date(value);
  return Number.isNaN(date.getTime()) ? "Date unavailable" : new Intl.DateTimeFormat(undefined, options).format(date);
}
