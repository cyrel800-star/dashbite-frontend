export function AdminSectionHeading({ eyebrow, title, description, action }) {
  return <div className="admin-section-heading">
    <div><p>{eyebrow}</p><h2>{title}</h2><span>{description}</span></div>
    {action}
  </div>;
}

export function AdminStatus({ value }) {
  return <span className={`admin-status admin-status-${String(value).toLowerCase()}`}>{String(value).replaceAll("_", " ")}</span>;
}

export function AdminMoney({ value, digits = 2 }) {
  const amount = Number.isFinite(Number(value)) ? Number(value) : 0;
  return <span>₱{amount.toLocaleString(undefined, { minimumFractionDigits: digits, maximumFractionDigits: digits })}</span>;
}

export function AdminEmptyState({ icon = "▤", title, description, action }) {
  return <div className="admin-empty-state"><span aria-hidden="true">{icon}</span><strong>{title}</strong><p>{description}</p>{action}</div>;
}
