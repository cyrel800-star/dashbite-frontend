import { useMemo, useState } from "react";
import { RiderEmptyState, RiderSectionHeading, RiderStatus } from "./RiderUI.jsx";

export function RiderChatInbox({ data, onNavigate }) {
  const conversations = useMemo(() => Object.entries(data.chats)
    .map(([orderId, messages]) => ({
      order: data.orders.find((item) => item.id === orderId),
      messages,
    }))
    .filter((conversation) => conversation.order && conversation.messages.length)
    .sort((a, b) => new Date(b.messages[b.messages.length - 1].createdAt) - new Date(a.messages[a.messages.length - 1].createdAt)), [data.chats, data.orders]);

  return <div className="rider-page-content">
    <RiderSectionHeading eyebrow="CUSTOMER CONVERSATIONS" title="Messages" description="Keep in touch about your current and recent deliveries." />
    <section className="rider-panel rider-chat-inbox">
      {conversations.length ? conversations.map(({ order, messages }) => {
        const latest = messages[messages.length - 1];
        return <button className="rider-chat-row" type="button" key={order.id} onClick={() => onNavigate(`CHAT/${order.id}`)}>
          <span className="rider-chat-avatar">{order.customer.trim()[0]?.toUpperCase() || "C"}</span>
          <span className="rider-chat-copy"><strong>{order.customer}</strong><small>{latest.sender === "rider" ? "You: " : ""}{latest.text}</small><small>Order {order.id} · {order.restaurant}</small></span>
          <span className="rider-chat-meta"><small>{new Intl.DateTimeFormat(undefined, { hour: "numeric", minute: "2-digit" }).format(new Date(latest.createdAt))}</small><RiderStatus value={order.status} /></span>
        </button>;
      }) : <RiderEmptyState icon="✉" title="No conversations yet" description="Customer chats linked to your delivery orders will appear here." action={<button className="button button-primary" type="button" onClick={() => onNavigate("DASHBOARD")}>Back to dashboard</button>} />}
    </section>
    <p className="rider-inline-note">Messages are saved in this browser preview and are not delivered to customers.</p>
  </div>;
}

export function RiderChat({ orderId, data, onChange, onNavigate, readOnly = false }) {
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");
  const order = data.orders.find((item) => item.id === orderId);
  if (!order) return <section className="rider-panel"><RiderEmptyState icon="✉" title="Conversation not found" description="This delivery has no customer chat in the current preview." action={<button className="button button-primary" type="button" onClick={() => onNavigate("CHATS")}>Back to messages</button>} /></section>;
  const messages = data.chats[orderId] || [];

  function send(event) {
    event.preventDefault();
    const text = message.trim();
    if (!text) {
      setError("Enter a message before sending.");
      return;
    }
    if (text.length > 500) {
      setError("Messages can be up to 500 characters.");
      return;
    }
    onChange((current) => ({
      ...current,
      chats: {
        ...current.chats,
        [orderId]: [...(current.chats[orderId] || []), {
          id: `rider-message-${Date.now()}`,
          sender: "rider",
          text,
          createdAt: new Date().toISOString(),
        }],
      },
    }));
    setMessage("");
    setError("");
  }

  return <div className="rider-page-content rider-chat-page">
    <RiderSectionHeading eyebrow={`ORDER ${order.id}`} title={`Chat with ${order.customer}`} description={`Conversation about your ${order.restaurant} delivery.`} action={<button className="rider-text-link" type="button" onClick={() => onNavigate("CHATS")}>← All messages</button>} />
    <section className="rider-panel rider-chat-panel">
      <header className="rider-chat-header"><span className="rider-chat-avatar">{order.customer.trim()[0]?.toUpperCase() || "C"}</span><div><strong>{order.customer}</strong><small>Order {order.id} · {order.deliveryAddress}</small></div><RiderStatus value={order.status} /></header>
      <div className="rider-chat-messages" aria-live="polite">
        {messages.length ? messages.map((item) => <div className={`rider-message-row${item.sender === "rider" ? " from-rider" : ""}`} key={item.id}>
          {item.sender === "customer" && <span className="rider-message-avatar">{order.customer.trim()[0]?.toUpperCase() || "C"}</span>}
          <div className="rider-message-bubble"><p>{item.text}</p><small>{item.sender === "rider" ? "You" : order.customer} · {new Intl.DateTimeFormat(undefined, { hour: "numeric", minute: "2-digit" }).format(new Date(item.createdAt))}</small></div>
        </div>) : <RiderEmptyState icon="✉" title="Start the conversation" description="Send a delivery update to the customer." />}
      </div>
      {!readOnly && <form className="rider-chat-compose" onSubmit={send}>
        <label className="sr-only" htmlFor="rider-message">Message customer</label>
        <input id="rider-message" value={message} onChange={(event) => setMessage(event.target.value)} maxLength={500} placeholder="Write a delivery update..." />
        <button className="button button-primary" type="submit">Send <span aria-hidden="true">→</span></button>
      </form>}
      {error && <p className="rider-form-error" role="alert">{error}</p>}
      <p className="rider-inline-note">Local preview only. Messages are not sent to the customer.</p>
    </section>
  </div>;
}
