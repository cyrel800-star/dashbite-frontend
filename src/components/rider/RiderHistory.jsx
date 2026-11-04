import { useMemo, useState } from "react";
import { RiderEmptyState, RiderMetric, RiderMoney, RiderSectionHeading, RiderStatus } from "./RiderUI.jsx";

export default function RiderHistory({ data }) {
  const [query, setQuery] = useState("");
  const orders = useMemo(() => data.orders
    .filter((order) => order.status === "DELIVERED")
    .filter((order) => `${order.id} ${order.restaurant} ${order.customer} ${order.deliveryAddress}`.toLowerCase().includes(query.trim().toLowerCase()))
    .sort((a, b) => new Date(b.completedAt) - new Date(a.completedAt)), [data.orders, query]);
  const allCompleted = data.orders.filter((order) => order.status === "DELIVERED");
  const totalEarnings = allCompleted.reduce((sum, order) => sum + order.deliveryFee, 0);
  const averageRating = allCompleted.filter((order) => order.rating).length
    ? (allCompleted.reduce((sum, order) => sum + (order.rating || 0), 0) / allCompleted.filter((order) => order.rating).length).toFixed(1)
    : "—";

  return <div className="rider-page-content">
    <RiderSectionHeading eyebrow="EVERY DELIVERY, IN ONE PLACE" title="Delivery history" description="Review completed runs and sample earnings." />
    <div className="rider-history-summary">
      <RiderMetric icon="✓" label="Completed deliveries" value={allCompleted.length} note="All preview records" accent="dark" />
      <RiderMetric icon="₱" label="Delivery earnings" value={<RiderMoney value={totalEarnings} digits={0} />} note="Illustrative fees" />
      <RiderMetric icon="★" label="Average rating" value={averageRating} note="From preview customers" accent="gold" />
    </div>
    <section className="rider-panel rider-history-panel">
      <div className="rider-panel-title"><div><h3>Completed deliveries</h3><p>{allCompleted.length} sample records</p></div><label className="rider-history-search"><span aria-hidden="true">⌕</span><input type="search" value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Search deliveries" aria-label="Search delivery history" /></label></div>
      {orders.length ? <div className="rider-history-list">{orders.map((order) => <article className="rider-history-card" key={order.id}>
        <span className="rider-history-icon" aria-hidden="true">✓</span>
        <div className="rider-history-main"><div className="rider-history-card-top"><div><strong>{order.restaurant}</strong><small>Order {order.id} · {new Intl.DateTimeFormat(undefined, { month: "short", day: "numeric", hour: "numeric", minute: "2-digit" }).format(new Date(order.completedAt))}</small></div><RiderStatus value={order.status} /></div>
          <div className="rider-history-route"><span><small>DELIVERED TO</small>{order.customer} · {order.deliveryAddress}</span><span className="rider-history-payment">{order.paymentMethod === "COD" ? "Cash on delivery" : "Online payment"} · {String(order.paymentStatus).replaceAll("_", " ")}</span></div>
          <div className="rider-history-card-foot"><span>Delivery fee <strong><RiderMoney value={order.deliveryFee} /></strong></span><span className="rider-history-rating">{order.rating ? `★ ${order.rating}.0` : "Not rated"}</span></div>
        </div>
      </article>)}</div> : <RiderEmptyState icon="▤" title={query ? "No matching deliveries" : "No completed deliveries yet"} description={query ? "Try another search term." : "Completed deliveries will appear here in your preview history."} />}
    </section>
    <p className="rider-inline-note">History and amounts are local sample data. They are not confirmed transactions or payouts.</p>
  </div>;
}
