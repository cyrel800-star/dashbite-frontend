import { useMemo, useState } from "react";
import { findCustomerRestaurant } from "../../lib/customerPortal.js";
import { CustomerEmptyState, CustomerNotice, CustomerSectionHeading, CustomerStatus } from "./CustomerUI.jsx";

const ORDER_STEPS = [
  { status: "PENDING", title: "Order received", description: "The restaurant is reviewing your order." },
  { status: "ACCEPTED", title: "Being prepared", description: "The kitchen has started making your meal." },
  { status: "SEARCHING_RIDER", title: "Finding your rider", description: "We're looking for a rider nearby." },
  { status: "PICKED_UP", title: "On the way", description: "Your rider has picked up the order." },
  { status: "DELIVERED", title: "Delivered", description: "Enjoy your meal!" },
];

export function CustomerTrackOrder({ orderId, data, onChange, onNavigate }) {
  const [notice, setNotice] = useState(null);
  const order = data.orders.find((item) => item.id === orderId);
  if (!order) return <section className="customer-panel"><CustomerEmptyState icon="⌕" title="We couldn't find that order" description="It may have been cleared from this browser's preview data." action={<button className="button button-primary" type="button" onClick={() => onNavigate("ORDERS")}>Back to orders</button>} /></section>;

  const cancelled = ["CANCELLED", "REJECTED", "NO_RIDER"].includes(order.status);
  const currentStep = ORDER_STEPS.findIndex((step) => step.status === order.status);
  const restaurant = findCustomerRestaurant(order.restaurantId);

  function advancePreview() {
    const nextStatus = order.status === "PENDING" ? "ACCEPTED"
      : order.status === "ACCEPTED" ? "SEARCHING_RIDER"
      : order.status === "SEARCHING_RIDER" ? "PICKED_UP"
        : order.status === "PICKED_UP" ? "DELIVERED" : null;
    if (!nextStatus) return;
    onChange((current) => ({
      ...current,
      orders: current.orders.map((item) => item.id === order.id ? {
        ...item,
        status: nextStatus,
        paymentStatus: nextStatus === "DELIVERED" && item.paymentMethod !== "COD" ? "SETTLED_TO_RESTAURANT" : item.paymentStatus,
      } : item),
    }));
    setNotice({ type: "success", message: `Preview order status updated to ${nextStatus.replaceAll("_", " ")}.` });
  }

  return <div className="customer-page-content">
    <CustomerSectionHeading eyebrow="ORDER PROGRESS" title={`Track ${order.id}`} description={`From ${order.restaurantName}, placed ${new Intl.DateTimeFormat(undefined, { month: "short", day: "numeric", hour: "numeric", minute: "2-digit" }).format(new Date(order.createdAt))}.`} action={<button className="customer-text-link" type="button" onClick={() => onNavigate("ORDERS")}>← All orders</button>} />
    <CustomerNotice notice={notice} onDismiss={() => setNotice(null)} />
    <div className="customer-tracking-layout">
      <section className="customer-panel customer-tracking-panel">
        <div className="customer-tracking-top"><div><span className="customer-tracking-icon">➤</span><div><strong>{cancelled ? "This order won't be delivered" : order.status === "DELIVERED" ? "Your order is complete" : order.status === "PICKED_UP" ? "Your meal is on its way" : "We're on it"}</strong><p>{cancelled ? "Check your order history for more details." : order.eta && order.status !== "DELIVERED" ? `Estimated arrival around ${order.eta}` : "We'll keep your order progress right here."}</p></div></div><CustomerStatus value={order.status} /></div>
        {cancelled
          ? <div className="customer-cancelled-state"><span>!</span><p>This order status is <strong>{order.status.replaceAll("_", " ").toLowerCase()}</strong>. You can browse other restaurants to find your next meal.</p></div>
          : <ol className="customer-tracking-steps">{ORDER_STEPS.map((step, index) => {
            const complete = order.status === "DELIVERED" || index < currentStep;
            const active = order.status !== "DELIVERED" && index === currentStep;
            return <li className={`${complete ? "is-complete" : ""}${active ? " is-active" : ""}`} key={step.status}>
              <span className="customer-step-marker">{complete ? "✓" : index + 1}</span><div><strong>{step.title}</strong><p>{step.description}</p></div>
            </li>;
          })}</ol>}
        {!cancelled && order.status !== "DELIVERED" && <button className="button button-outline customer-simulate-button" type="button" onClick={advancePreview}>Simulate next update <span aria-hidden="true">→</span></button>}
        <p className="customer-inline-note">Status changes are simulated in this browser preview; they do not contact a restaurant or rider.</p>
      </section>
      <aside className="customer-panel customer-tracking-summary">
        <h3>Order details</h3>
        <div className="customer-tracking-restaurant"><span>{restaurant?.emoji || "🍽"}</span><div><strong>{order.restaurantName}</strong><button type="button" onClick={() => onNavigate(`RESTAURANT/${order.restaurantId}`)}>View restaurant →</button></div></div>
        <div className="customer-tracking-items">{order.items.map((item) => <div key={item.menuItemId}><span>{item.quantity} × {item.name}</span><strong>₱{(item.price * item.quantity).toFixed(2)}</strong></div>)}</div>
        <div className="customer-price-summary"><span>Subtotal</span><strong>₱{order.subtotal.toFixed(2)}</strong><span>Delivery</span><strong>₱{order.deliveryFee.toFixed(2)}</strong><span className="is-total">Total</span><strong className="is-total">₱{order.total.toFixed(2)}</strong></div>
        <p className="customer-tracking-address">Delivering to<br /><strong>{order.address}</strong></p>
        <button className="button button-primary customer-full-button" type="button" onClick={() => onNavigate(`CHAT/${order.id}`)}>Message restaurant</button>
      </aside>
    </div>
  </div>;
}

export function CustomerChatInbox({ data, onNavigate }) {
  const chats = useMemo(() => data.orders.filter((order) => data.chats[order.id]?.length), [data.chats, data.orders]);
  return <div className="customer-page-content">
    <CustomerSectionHeading eyebrow="STAY IN THE LOOP" title="Messages" description="Your conversations with neighborhood restaurants." />
    <section className="customer-panel">
      {chats.length ? <div className="customer-chat-list">{chats.map((order) => {
        const messages = data.chats[order.id];
        const lastMessage = messages[messages.length - 1];
        const restaurant = findCustomerRestaurant(order.restaurantId);
        return <button className="customer-chat-row" type="button" key={order.id} onClick={() => onNavigate(`CHAT/${order.id}`)}>
          <span className="customer-chat-avatar">{restaurant?.emoji || "🍽"}</span>
          <span className="customer-chat-copy"><strong>{order.restaurantName}</strong><small>{lastMessage.text}</small></span>
          <span className="customer-chat-meta"><small>{new Intl.DateTimeFormat(undefined, { hour: "numeric", minute: "2-digit" }).format(new Date(lastMessage.createdAt))}</small><CustomerStatus value={order.status} /></span>
        </button>;
      })}</div> : <CustomerEmptyState icon="✉" title="No conversations yet" description="Once you place an order, you can chat with the restaurant about it." action={<button className="button button-primary" type="button" onClick={() => onNavigate("RESTAURANTS")}>Find a restaurant</button>} />}
    </section>
    <p className="customer-inline-note">Messages are stored only in this browser preview. No restaurant receives them.</p>
  </div>;
}

export function CustomerChat({ orderId, data, onChange, onNavigate }) {
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");
  const order = data.orders.find((item) => item.id === orderId);
  if (!order) return <section className="customer-panel"><CustomerEmptyState icon="✉" title="Conversation not found" description="This order doesn't have a chat in the current preview." action={<button className="button button-primary" type="button" onClick={() => onNavigate("CHATS")}>Back to messages</button>} /></section>;
  const messages = data.chats[orderId] || [];
  const restaurant = findCustomerRestaurant(order.restaurantId);

  function sendMessage(event) {
    event.preventDefault();
    const text = message.trim();
    if (!text) {
      setError("Type a message before sending.");
      return;
    }
    onChange((current) => ({
      ...current,
      chats: {
        ...current.chats,
        [orderId]: [...(current.chats[orderId] || []), { id: `message-${Date.now()}`, sender: "customer", text, createdAt: new Date().toISOString() }],
      },
    }));
    setMessage("");
    setError("");
  }

  return <div className="customer-page-content">
    <CustomerSectionHeading eyebrow={`ORDER ${order.id}`} title={`Chat with ${order.restaurantName}`} description="A friendly place to share order notes." action={<button className="customer-text-link" type="button" onClick={() => onNavigate("CHATS")}>← All messages</button>} />
    <section className="customer-panel customer-chat-panel">
      <div className="customer-chat-header"><span>{restaurant?.emoji || "🍽"}</span><div><strong>{order.restaurantName}</strong><small>Conversation about order {order.id} · <CustomerStatus value={order.status} /></small></div></div>
      <div className="customer-chat-messages" aria-live="polite">
        {messages.length ? messages.map((item) => <div className={`customer-message-row${item.sender === "customer" ? " from-customer" : ""}`} key={item.id}>
          {item.sender === "restaurant" && <span className="customer-message-avatar">{restaurant?.emoji || "🍽"}</span>}
          <div className="customer-message-bubble"><p>{item.text}</p><small>{item.sender === "customer" ? "You" : order.restaurantName} · {new Intl.DateTimeFormat(undefined, { hour: "numeric", minute: "2-digit" }).format(new Date(item.createdAt))}</small></div>
        </div>) : <div className="customer-empty-state"><span aria-hidden="true">✉</span><strong>Start the conversation</strong><p>Your note will be saved in this local preview.</p></div>}
      </div>
      {error && <p className="customer-form-error" role="alert">{error}</p>}
      <form className="customer-chat-compose" onSubmit={sendMessage}><label className="sr-only" htmlFor="customer-message">Message</label><input id="customer-message" value={message} onChange={(event) => setMessage(event.target.value)} maxLength={500} placeholder="Write a message..." /><button className="button button-primary" type="submit">Send <span aria-hidden="true">→</span></button></form>
      <p className="customer-inline-note">Local preview only. Your message is not delivered to the restaurant.</p>
    </section>
  </div>;
}
