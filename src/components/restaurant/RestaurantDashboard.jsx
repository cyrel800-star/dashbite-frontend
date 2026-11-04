import { RestaurantMetric, RestaurantPrice, RestaurantSectionHeading, RestaurantStatus, formatRestaurantDate } from "./RestaurantUI.jsx";

function isToday(value) {
  const date = new Date(value);
  const today = new Date();
  return date.toDateString() === today.toDateString();
}

export default function RestaurantDashboard({ data, onChange, onNavigate, readOnly }) {
  const pendingOrders = data.orders.filter((order) => order.status === "PENDING");
  const todayOrders = data.orders.filter((order) => isToday(order.createdAt));
  const todaySales = data.orders
    .filter((order) => isToday(order.createdAt) && order.status === "DELIVERED")
    .reduce((sum, order) => sum + order.total, 0);
  const activeOrders = data.orders.filter((order) => ["PENDING", "ACCEPTED", "SEARCHING_RIDER", "PICKED_UP"].includes(order.status));

  function toggleOpen() {
    onChange((current) => ({ ...current, isOpen: !current.isOpen }));
  }

  return <div className="restaurant-page-content">
    <section className="restaurant-welcome-card">
      <div>
        <p className="restaurant-eyebrow">YOUR RESTAURANT AT A GLANCE</p>
        <h2>Good food starts with you.</h2>
        <p>Keep service moving, check the kitchen queue, and make today's menu shine.</p>
        <button className="button button-primary restaurant-welcome-action" type="button" onClick={() => onNavigate("ORDERS")}>
          Review orders <span aria-hidden="true">→</span>
        </button>
      </div>
      <div className="restaurant-welcome-art" aria-hidden="true"><span>🍜</span><i>✦</i><b>✦</b></div>
    </section>

    <div className="restaurant-metric-grid">
      <RestaurantMetric label="Needs your attention" value={pendingOrders.length} note="New orders to review" icon="◷" />
      <RestaurantMetric label="Today's orders" value={todayOrders.length} note="Orders placed today" icon="▤" />
      <RestaurantMetric label="Today's sales" value={<RestaurantPrice value={todaySales} />} note="Delivered preview orders" icon="↗" />
      <RestaurantMetric label="Menu items" value={data.menu.length} note={`${data.menu.filter((item) => item.available).length} available now`} icon="✦" />
    </div>

    <section className="restaurant-panel">
      <div className="restaurant-status-card">
        <div className="restaurant-status-copy">
          <span className={`restaurant-open-dot${data.isOpen ? " is-open" : ""}`} />
          <div><strong>{data.isOpen ? "You're open for orders" : "Your restaurant is closed"}</strong>
            <p>{data.isOpen ? "Customers can see your menu and place an order." : "Customers can't place new orders right now."}</p>
          </div>
        </div>
        <button className={`button ${data.isOpen ? "button-outline" : "button-primary"} restaurant-open-button`} type="button" onClick={toggleOpen} disabled={readOnly}>
          {readOnly ? "Admin view" : data.isOpen ? "Close restaurant" : "Open restaurant"}
        </button>
      </div>
      <p className="restaurant-local-note">Preview setting only. This does not change a live restaurant listing.</p>
    </section>

    <section className="restaurant-panel">
      <RestaurantSectionHeading
        eyebrow="KITCHEN"
        title="Orders in progress"
        description="A quick look at the latest activity."
        action={<button className="restaurant-inline-link" type="button" onClick={() => onNavigate("ORDERS")}>View all orders →</button>}
      />
      {activeOrders.length ? <div className="restaurant-order-list">
        {activeOrders.slice(0, 4).map((order) => <article className="restaurant-order-row" key={order.id}>
          <div className="restaurant-order-mark" aria-hidden="true">▤</div>
          <div className="restaurant-order-summary"><strong>{order.id}</strong><span>{order.customer} · {order.items.join(", ")}</span></div>
          <div className="restaurant-order-trailing"><RestaurantStatus value={order.status} /><strong><RestaurantPrice value={order.total} /></strong><small>{formatRestaurantDate(order.createdAt)}</small></div>
        </article>)}
      </div> : <p className="restaurant-local-note">No active orders right now.</p>}
    </section>

    <div className="restaurant-quick-grid">
      <button className="restaurant-quick-card" type="button" onClick={() => onNavigate("MENU")}><span>＋</span><strong>Update your menu</strong><small>Manage dishes and availability</small><b aria-hidden="true">→</b></button>
      <button className="restaurant-quick-card" type="button" onClick={() => onNavigate("HOURS")}><span>◷</span><strong>Set operating hours</strong><small>Keep your weekly schedule current</small><b aria-hidden="true">→</b></button>
      <button className="restaurant-quick-card" type="button" onClick={() => onNavigate("ANALYTICS")}><span>↗</span><strong>View performance</strong><small>Explore your preview sales trends</small><b aria-hidden="true">→</b></button>
    </div>
  </div>;
}
