import { useMemo, useState } from "react";
import { PAYMENT_METHODS } from "../../data/constants.js";
import { createCustomerOrder, findCustomerRestaurant } from "../../lib/customerPortal.js";
import { RestaurantPrice } from "../restaurant/RestaurantUI.jsx";
import { CustomerEmptyState, CustomerNotice, CustomerSectionHeading, CustomerStatus } from "./CustomerUI.jsx";

const ORDER_TABS = [
  { id: "ACTIVE", label: "In progress" },
  { id: "DELIVERED", label: "Delivered" },
  { id: "OTHER", label: "Cancelled / rejected" },
  { id: "ALL", label: "All orders" },
];

export function CustomerCart({ data, onChange, onNavigate }) {
  const [notice, setNotice] = useState(null);
  const restaurant = data.cart.restaurantId ? findCustomerRestaurant(data.cart.restaurantId) : null;
  const cartItems = data.cart.items.map((cartItem) => {
    const menuItem = restaurant?.menu.find((item) => item.id === cartItem.menuItemId);
    return menuItem ? { ...menuItem, quantity: cartItem.quantity } : null;
  }).filter(Boolean);
  const subtotal = cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0);

  function updateQuantity(itemId, quantity) {
    onChange((current) => ({
      ...current,
      cart: {
        ...current.cart,
        items: quantity <= 0
          ? current.cart.items.filter((item) => item.menuItemId !== itemId)
          : current.cart.items.map((item) => item.menuItemId === itemId ? { ...item, quantity } : item),
      },
    }));
  }

  function clearCart() {
    onChange((current) => ({ ...current, cart: { restaurantId: null, items: [] } }));
  }

  function checkout(event) {
    event.preventDefault();
    try {
      const values = new FormData(event.currentTarget);
      const next = createCustomerOrder(data, String(values.get("payment_method")));
      onChange(() => next);
      setNotice(null);
      onNavigate(`TRACK/${next.orders[0].id}`);
    } catch (error) {
      setNotice({ type: "error", message: error instanceof Error ? error.message : "Could not place this preview order." });
    }
  }

  if (!restaurant || cartItems.length === 0) return <div className="customer-page-content">
    <CustomerSectionHeading eyebrow="READY WHEN YOU ARE" title="Your cart" description="Your next great meal starts with a good pick." />
    <section className="customer-panel"><CustomerEmptyState icon="🛍" title="Your cart is waiting for something tasty" description="Browse local restaurants and add a dish to get started." action={<button className="button button-primary" type="button" onClick={() => onNavigate("RESTAURANTS")}>Explore restaurants</button>} /></section>
  </div>;

  return <div className="customer-page-content">
    <CustomerSectionHeading eyebrow="ALMOST THERE" title="Your cart" description={`A little something from ${restaurant.name}.`} action={<button className="customer-text-link" type="button" onClick={() => onNavigate(`RESTAURANT/${restaurant.id}`)}>← Back to menu</button>} />
    <CustomerNotice notice={notice} onDismiss={() => setNotice(null)} />
    <div className="customer-cart-layout">
      <section className="customer-panel">
        <div className="customer-panel-heading"><div><h3>{restaurant.name}</h3><p>{restaurant.deliveryMinutes} · ₱{restaurant.deliveryFee.toFixed(2)} delivery fee</p></div><button className="customer-text-link customer-danger-link" type="button" onClick={clearCart}>Clear cart</button></div>
        <div className="customer-cart-items">{cartItems.map((item) => <article className="customer-cart-item" key={item.id}>
          <span className="customer-cart-emoji" aria-hidden="true">{item.emoji}</span><div className="customer-cart-item-info"><strong>{item.name}</strong><small>₱{item.price.toFixed(2)} each</small></div>
          <div className="customer-quantity-control"><button type="button" onClick={() => updateQuantity(item.id, item.quantity - 1)} aria-label={`Remove one ${item.name}`}>−</button><span>{item.quantity}</span><button type="button" onClick={() => updateQuantity(item.id, item.quantity + 1)} aria-label={`Add one ${item.name}`}>＋</button></div>
          <strong className="customer-cart-line-total">₱{(item.price * item.quantity).toFixed(2)}</strong>
        </article>)}</div>
      </section>
      <form className="customer-panel customer-checkout" onSubmit={checkout}>
        <h3>Delivery details</h3>
        <label className="customer-field">Delivery address<input required maxLength={300} value={data.profile.address} onChange={(event) => onChange((current) => ({ ...current, profile: { ...current.profile, address: event.target.value } }))} placeholder="Street address and delivery notes" /></label>
        <label className="customer-field">Payment method<select defaultValue="COD" name="payment_method">{PAYMENT_METHODS.map((method) => <option value={method} key={method}>{method === "COD" ? "Cash on delivery" : "Online payment (preview only)"}</option>)}</select></label>
        <div className="customer-price-summary"><span>Subtotal</span><strong>₱{subtotal.toFixed(2)}</strong><span>Delivery fee</span><strong>₱{restaurant.deliveryFee.toFixed(2)}</strong><span className="is-total">Estimated total</span><strong className="is-total">₱{(subtotal + restaurant.deliveryFee).toFixed(2)}</strong></div>
        <p className="customer-inline-note">Minimum order: ₱{restaurant.minimum.toFixed(2)}. Placing an order creates a local preview entry only; there is no payment or restaurant notification.</p>
        <button className="button button-primary customer-checkout-button" type="submit">Place preview order <span aria-hidden="true">→</span></button>
      </form>
    </div>
  </div>;
}

export default function CustomerOrders({ data, onNavigate }) {
  const [tab, setTab] = useState("ACTIVE");
  const orders = useMemo(() => data.orders.filter((order) => {
    if (tab === "ACTIVE") return ["PENDING", "ACCEPTED", "SEARCHING_RIDER", "PICKED_UP"].includes(order.status);
    if (tab === "DELIVERED") return order.status === "DELIVERED";
    if (tab === "OTHER") return ["REJECTED", "CANCELLED", "NO_RIDER"].includes(order.status);
    return true;
  }), [data.orders, tab]);
  const activeCount = data.orders.filter((order) => ["PENDING", "ACCEPTED", "SEARCHING_RIDER", "PICKED_UP"].includes(order.status)).length;

  return <div className="customer-page-content">
    <CustomerSectionHeading eyebrow="YOUR DASHBITE HISTORY" title="My orders" description="Follow your meal from the kitchen to your door." action={<span className="customer-count-pill">{activeCount} active</span>} />
    <div className="customer-tabs" role="tablist" aria-label="Filter customer orders">
      {ORDER_TABS.map((item) => <button type="button" role="tab" aria-selected={tab === item.id} className={tab === item.id ? "is-active" : ""} key={item.id} onClick={() => setTab(item.id)}>{item.label}{item.id === "ACTIVE" && activeCount > 0 && <span>{activeCount}</span>}</button>)}
    </div>
    {orders.length ? <div className="customer-order-list">{orders.map((order) => <article className="customer-panel customer-order-card" key={order.id}>
      <div className="customer-order-card-head"><div className="customer-order-restaurant"><span>{findCustomerRestaurant(order.restaurantId)?.emoji || "🍽"}</span><div><strong>{order.restaurantName}</strong><small>Order {order.id} · {new Intl.DateTimeFormat(undefined, { month: "short", day: "numeric", hour: "numeric", minute: "2-digit" }).format(new Date(order.createdAt))}</small></div></div><CustomerStatus value={order.status} /></div>
      <div className="customer-order-card-items">{order.items.map((item) => <span key={item.menuItemId}>{item.quantity} × {item.name}</span>)}</div>
      <div className="customer-order-card-foot"><span><CustomerStatus value={order.paymentStatus} /> · {order.paymentMethod === "COD" ? "Cash on delivery" : "Online"}</span><strong><RestaurantPrice value={order.total} /></strong><div>
        {["PENDING", "ACCEPTED", "SEARCHING_RIDER", "PICKED_UP"].includes(order.status) && <button className="button button-primary button-small" type="button" onClick={() => onNavigate(`TRACK/${order.id}`)}>Track order</button>}
        <button className="button button-outline button-small" type="button" onClick={() => onNavigate(`RESTAURANT/${order.restaurantId}`)}>View restaurant</button>
      </div></div>
    </article>)}</div> : <section className="customer-panel"><CustomerEmptyState icon="▤" title={tab === "ACTIVE" ? "No orders in progress" : "No orders here yet"} description={tab === "ACTIVE" ? "When you place an order, you'll see its progress here." : "Completed and past orders will appear here."} action={<button className="button button-primary" type="button" onClick={() => onNavigate("RESTAURANTS")}>Browse restaurants</button>} /></section>}
    <p className="customer-inline-note">Order history contains browser-local preview records, not live transactions.</p>
  </div>;
}
