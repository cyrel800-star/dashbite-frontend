import { RiderOfferForm } from "../Forms.jsx";
import { acceptRiderOffer } from "../../lib/riderPortal.js";
import { RiderEmptyState, RiderMetric, RiderMoney, RiderSectionHeading, RiderStatus } from "./RiderUI.jsx";

const ACTIVE_STATUSES = new Set(["SEARCHING_RIDER", "PICKED_UP"]);

function updateOrder(data, orderId, status) {
  const completed = status === "DELIVERED";
  return {
    ...data,
    activeOrderId: completed ? null : data.activeOrderId,
    orders: data.orders.map((order) => order.id === orderId
      ? { ...order, status, completedAt: completed ? new Date().toISOString() : order.completedAt }
      : order),
  };
}

export default function RiderDashboard({ data, onChange, onNavigate, readOnly = false }) {
  const activeOrder = data.orders.find((order) => order.id === data.activeOrderId && ACTIVE_STATUSES.has(order.status));
  const completedOrders = data.orders.filter((order) => order.status === "DELIVERED");
  const earnings = completedOrders.reduce((sum, order) => sum + order.deliveryFee, 0);
  const offers = data.offers.filter((offer) => offer.status === "BLASTED");

  function toggleOnline() {
    if (readOnly) return;
    if (!data.isOnline && activeOrder) return;
    onChange((current) => ({ ...current, isOnline: !current.isOnline }));
  }

  function moveToNextStatus(order) {
    const nextStatus = order.status === "SEARCHING_RIDER" ? "PICKED_UP"
      : order.status === "PICKED_UP" ? "DELIVERED" : null;
    if (!nextStatus) return;
    onChange((current) => updateOrder(current, order.id, nextStatus));
  }

  return <div className="rider-page-content">
    <section className="rider-welcome-card">
      <div className="rider-welcome-copy">
        <p className="rider-eyebrow">YOUR RIDER WORKSPACE</p>
        <h2>Ready for the next<br /><span>good delivery?</span></h2>
        <p>Manage your delivery shift, follow your route, and keep every customer in the loop.</p>
        <button className={`rider-online-toggle${data.isOnline ? " is-online" : ""}`} type="button" onClick={toggleOnline} disabled={readOnly || Boolean(activeOrder)} aria-pressed={data.isOnline}>
          <i />{data.isOnline ? "You're online" : "Go online"}
          {activeOrder && <small>Finish your active delivery first</small>}
        </button>
      </div>
      <div className="rider-welcome-art" aria-hidden="true"><span>🛵</span><i>✦</i><b>➤</b></div>
      <span className="rider-welcome-chip"><i />{data.isOnline ? "Available for offers" : "You're offline"}</span>
    </section>

    <div className="rider-metric-grid">
      <RiderMetric icon="✓" label="Completed deliveries" value={completedOrders.length} note="In this preview" accent="dark" />
      <RiderMetric icon="₱" label="Delivery earnings" value={<RiderMoney value={earnings} digits={0} />} note="Sample delivery fees" />
      <RiderMetric icon="➤" label="Available offers" value={offers.length} note={data.isOnline ? "Ready to review" : "Go online to accept"} accent="green" />
      <RiderMetric icon="★" label="Rider rating" value="5.0" note="Illustrative rating" accent="gold" />
    </div>

    <div className="rider-dashboard-grid">
      <section className="rider-panel rider-offers-panel">
        <RiderSectionHeading eyebrow="DISPATCH" title="Delivery offers" description="Review sample offers near your area." action={<span className="rider-count-pill">{offers.length} available</span>} />
        {!offers.length
          ? <RiderEmptyState icon="⌕" title="No new offers right now" description="Stay online and check back when another delivery is ready." />
          : <div className="rider-offer-list">{offers.map((offer) => <article className="rider-offer-card" key={offer.id}>
            <div className="rider-offer-top"><div><span className="rider-offer-icon" aria-hidden="true">🍽</span><div><strong>{offer.restaurant}</strong><small>Offer {offer.orderId}</small></div></div><RiderStatus value={offer.status} /></div>
            <div className="rider-route-points"><div><i className="is-pickup" /><span><small>PICKUP</small><strong>{offer.pickupAddress}</strong></span></div><div><i className="is-dropoff" /><span><small>DROP-OFF · {offer.distanceKm.toFixed(1)} km</small><strong>{offer.deliveryAddress}</strong></span></div></div>
            <div className="rider-offer-foot"><span>Delivery fee <strong><RiderMoney value={offer.deliveryFee} /></strong></span><button className="button button-primary button-small" type="button" disabled={readOnly || !data.isOnline || Boolean(activeOrder)} onClick={() => onChange((current) => acceptRiderOffer(current, offer.id))}>{!data.isOnline ? "Go online to accept" : activeOrder ? "Finish active delivery" : "Accept preview offer"} <span aria-hidden="true">→</span></button></div>
          </article>)}</div>}
        <p className="rider-inline-note">Offers shown here are sample data. Accepting one updates this browser preview only.</p>
      </section>

      <div className="rider-side-stack">
        <section className="rider-panel">
          <RiderSectionHeading eyebrow="CURRENT DELIVERY" title="On your route" description="Your active order and next delivery step." />
          {activeOrder ? <article className="rider-active-order">
            <div className="rider-active-order-head"><div><span className="rider-order-icon">➤</span><div><strong>{activeOrder.restaurant}</strong><small>Order {activeOrder.id}</small></div></div><RiderStatus value={activeOrder.status} /></div>
            <div className="rider-route-points"><div><i className="is-pickup" /><span><small>PICKUP</small><strong>{activeOrder.pickupAddress}</strong></span></div><div><i className="is-dropoff" /><span><small>DELIVER TO · {activeOrder.customer}</small><strong>{activeOrder.deliveryAddress}</strong></span></div></div>
            <p className="rider-inline-note">Order and customer details are illustrative preview data.</p>
            <button className="button button-primary rider-full-button" type="button" disabled={readOnly} onClick={() => moveToNextStatus(activeOrder)}>{activeOrder.status === "SEARCHING_RIDER" ? "Confirm pickup" : "Mark delivered"} <span aria-hidden="true">→</span></button>
            <button className="rider-text-link" type="button" onClick={() => onNavigate(`CHAT/${activeOrder.id}`)}>Message customer →</button>
          </article> : <RiderEmptyState icon="✓" title="No active delivery" description="Accept an available offer when you're online." />}
        </section>
        <RiderOfferForm disabled={readOnly} disabledMessage="Read-only Admin monitoring: API actions are disabled in this view." />
      </div>
    </div>
  </div>;
}
