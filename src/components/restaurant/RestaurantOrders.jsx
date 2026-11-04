import { useMemo, useState } from "react";
import { RestaurantEmptyState, RestaurantPrice, RestaurantSectionHeading, RestaurantStatus, formatRestaurantDate } from "./RestaurantUI.jsx";

const ORDER_FILTERS = [
  { value: "ALL", label: "All orders" },
  { value: "PENDING", label: "New" },
  { value: "ACCEPTED", label: "Preparing" },
  { value: "SEARCHING_RIDER", label: "Ready for pickup" },
  { value: "DELIVERED", label: "Completed" },
  { value: "REJECTED", label: "Rejected" },
  { value: "CANCELLED", label: "Cancelled" },
];

export default function RestaurantOrders({ data, onChange, readOnly }) {
  const [filter, setFilter] = useState("ALL");
  const [query, setQuery] = useState("");
  const orders = useMemo(() => data.orders.filter((order) => {
    const matchesStatus = filter === "ALL" || order.status === filter;
    const searchText = `${order.id} ${order.customer} ${order.items.join(" ")}`.toLowerCase();
    return matchesStatus && searchText.includes(query.trim().toLowerCase());
  }), [data.orders, filter, query]);
  const waitingCount = data.orders.filter((order) => order.status === "PENDING").length;

  function updateOrder(orderId, status) {
    onChange((current) => ({
      ...current,
      orders: current.orders.map((order) => order.id === orderId ? { ...order, status } : order),
    }));
  }

  function actionsFor(order) {
    if (readOnly) return <span className="restaurant-read-only">Admin view</span>;
    if (order.status === "PENDING") return <div className="restaurant-row-actions">
      <button className="button button-primary button-small" type="button" onClick={() => updateOrder(order.id, "ACCEPTED")}>Accept</button>
      <button className="button button-outline button-small" type="button" onClick={() => updateOrder(order.id, "REJECTED")}>Reject</button>
    </div>;
    if (order.status === "ACCEPTED") return <button className="button button-primary button-small" type="button" onClick={() => updateOrder(order.id, "SEARCHING_RIDER")}>Ready for pickup</button>;
    return <span className="restaurant-read-only">No action</span>;
  }

  return <div className="restaurant-page-content">
    <RestaurantSectionHeading
      eyebrow="KITCHEN"
      title="Orders"
      description="Review incoming orders and follow them through delivery."
      action={<span className="restaurant-count-pill">{waitingCount} new</span>}
    />
    <div className="restaurant-filter-panel">
      <label className="restaurant-search-field"><span aria-hidden="true">⌕</span><input type="search" value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Search order or customer" aria-label="Search order or customer" /></label>
      <label className="restaurant-filter-select"><span className="sr-only">Filter order status</span><select value={filter} onChange={(event) => setFilter(event.target.value)}>{ORDER_FILTERS.map((item) => <option value={item.value} key={item.value}>{item.label}</option>)}</select></label>
    </div>
    <section className="restaurant-panel">
      {orders.length ? <div className="restaurant-order-list">
        {orders.map((order) => <article className="restaurant-order-card" key={order.id}>
          <div className="restaurant-order-card-head">
            <div><strong>{order.id}</strong><span>{formatRestaurantDate(order.createdAt)}</span></div>
            <RestaurantStatus value={order.status} />
          </div>
          <div className="restaurant-order-card-body">
            <div className="restaurant-order-summary"><strong>{order.customer}</strong><span>{order.items.join(" · ")}</span></div>
            <strong><RestaurantPrice value={order.total} /></strong>
          </div>
          <div className="restaurant-order-card-foot">
            <span>Payment <RestaurantStatus value={order.payment} /></span>
            {actionsFor(order)}
          </div>
        </article>)}
      </div> : <RestaurantEmptyState icon="⌕" title="No orders match your filters" description="Try a different status or search term." />}
    </section>
    <p className="restaurant-local-note">Order changes are saved in this browser preview. Restaurant order decision endpoints are not included in the v1.0 API contract.</p>
  </div>;
}
