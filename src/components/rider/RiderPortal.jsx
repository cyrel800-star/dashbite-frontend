import { useState } from "react";
import PortalFrame from "../PortalFrame.jsx";
import { heroRider } from "../../data/images.js";
import { Chart, Empty, Food, Tag, toast } from "../../ui/components.jsx";
import { LOCAL_KEYS, usePersistentState } from "../../lib/localData.js";

// Rider availability, delivery offers, delivery status, earnings, and chat.
export default function RiderPortal(props) {
  const [preferences, setPreferences] = usePersistentState("dashbite.rider.preferences", { online: true });
  const [orders, setOrders] = usePersistentState(LOCAL_KEYS.orders, []);
  const [messages, setMessages] = usePersistentState(LOCAL_KEYS.messages, []);
  const [chatText, setChatText] = useState("");
  const [chatOrder, setChatOrder] = useState("");
  const online = preferences.online;
  const offers = orders.filter((order) => order.status === "READY");
  const deliveries = orders.filter((order) => order.status === "ON_THE_WAY" || order.status === "DELIVERED");
  const delivered = orders.filter((order) => order.status === "DELIVERED");

  // Rider actions update local order records shared with customer and restaurant portals.
  const toggleOnline = () => setPreferences((current) => ({ ...current, online: !current.online }));
  const accept = (order) => {
    setOrders((saved) => saved.map((item) => item.id === order.id ? { ...item, status: "ON_THE_WAY", rider: props.user.name } : item));
    toast(`Delivery ${order.id} accepted.`);
  };
  const complete = (order) => {
    setOrders((saved) => saved.map((item) => item.id === order.id ? { ...item, status: "DELIVERED" } : item));
    toast(`Delivery ${order.id} marked complete.`);
  };
  const sendMessage = (event) => {
    event.preventDefault();
    const text = chatText.trim();
    if (!chatOrder || !text) return;
    setMessages((saved) => [...saved, { id: `message-${Date.now()}`, orderId: chatOrder, sender: "rider", name: props.user.name, text, createdAt: new Date().toISOString() }]);
    setChatText("");
    toast("Message sent to the customer.");
  };
  const orderRow = (order, action) => <div className="li order-row" key={order.id}><div className="f"><b>{order.id} · {order.restaurant}</b><small>{order.items.map((item) => `${item.quantity}× ${item.name}`).join(", ")}</small><small>₱{order.total}</small></div><Tag tone={order.status === "DELIVERED" ? "a" : "w"}>{order.status.replaceAll("_", " ")}</Tag>{action}</div>;

  // Map rider navigation keys to dashboard and delivery screens.
  const screens = {
    dash: ({ setView }) => (
      <div className="two">
        <div className="col">
          <div className="ban"><div><h2>Welcome Back, <span className="or">{props.user.name.split(" ")[0]}!</span></h2><p>Accept local delivery requests and update their status as you go.</p><button className="btn" onClick={toggleOnline}>{online ? "🟢 You’re Online" : "⚪ Go Online"}</button></div><Food src={heroRider} /></div>
          <div className="card"><div className="row"><h3>Available Delivery Offers</h3><Tag tone="w">{online ? `${offers.length} available` : "Offline"}</Tag></div>
            {!online ? <Empty icon="😴">You’re offline. Go online to see orders ready for pickup.</Empty> : offers.length ? offers.map((order) => orderRow(order, <button className="btn sm" onClick={() => accept(order)}>Accept Offer →</button>)) : <Empty icon="🛵">No offers yet. Orders appear after a restaurant marks them ready for pickup.</Empty>}
          </div>
          <div className="card"><div className="row"><h3>Recent Deliveries</h3><button className="or text-button" onClick={() => setView("deliveries")}>View All →</button></div>{deliveries.slice(-3).reverse().map((order) => orderRow(order))}{!deliveries.length && <small>No deliveries yet.</small>}</div>
        </div>
        <div className="col">
          <div className="card"><h3>Rider Status</h3><div className="row"><b>{online ? "Online" : "Offline"}</b><button className={`sw ${online ? "on" : ""}`} role="switch" aria-checked={online} aria-label="Online status" onClick={toggleOnline} /></div><div className="row" style={{ marginTop: 12 }}><span>🛵 <b>{delivered.length}</b><small style={{ display: "block" }}>Completed Deliveries</small></span><span>⭐ <b>5.0</b><small style={{ display: "block" }}>Demo Rating</small></span></div></div>
          <div className="card"><h3>Live Delivery Map</h3><div className="food map-placeholder">🍴 ⋯ 🛵 ⋯ 🏠</div><small style={{ color: "var(--mu)" }}>Live maps require a map provider; order status works offline.</small></div>
          <div className="card"><h3>Today’s Earnings</h3><b style={{ fontSize: 28 }}>₱{delivered.length * 45}.00</b><small className="subtle">Estimated demo earnings at ₱45 per delivery</small><Chart points={delivered.length ? delivered.slice(-7).map((_, index) => 45 * (index + 1)) : [0, 0, 0, 0, 0, 0, 0]} h={70} /></div>
        </div>
      </div>
    ),
    offers: () => online && offers.length ? <section className="card"><h3>Delivery Offers</h3>{offers.map((order) => orderRow(order, <button className="btn sm" onClick={() => accept(order)}>Accept →</button>))}</section> : <Empty icon="🛵">{online ? "No ready orders are available. Check back after a restaurant prepares an order." : "Go online to receive delivery offers."}</Empty>,
    deliveries: () => deliveries.length ? <section className="card"><h3>My Deliveries</h3>{deliveries.map((order) => orderRow(order, order.status === "ON_THE_WAY" ? <button className="btn sm" onClick={() => complete(order)}>Mark Delivered</button> : null))}</section> : <Empty icon="📦">Accepted deliveries will appear here.</Empty>,
    earnings: () => <section className="card"><h3>Earnings</h3><div className="stats"><div><small>Completed deliveries</small><h2>{delivered.length}</h2></div><div><small>Estimated earnings</small><h2>₱{delivered.length * 45}.00</h2></div></div><Chart points={delivered.length ? delivered.map((_, index) => 45 * (index + 1)) : [0, 0, 0, 0, 0, 0, 0]} /></section>,
    chat: () => {
      const myDeliveries = deliveries.filter((order) => order.rider === props.user.name);
      return myDeliveries.length ? <section className="card"><h3>Customer Chat</h3><form className="local-form" onSubmit={sendMessage}><label>Delivery<select className="text-input" value={chatOrder} onChange={(event) => setChatOrder(event.target.value)} required><option value="">Select a delivery</option>{myDeliveries.map((order) => <option key={order.id} value={order.id}>{order.id} · {order.restaurant}</option>)}</select></label><label>Message<textarea className="text-input" value={chatText} onChange={(event) => setChatText(event.target.value)} maxLength={500} required /></label><button className="btn">Send Message</button></form>{messages.filter((message) => myDeliveries.some((order) => order.id === message.orderId)).map((message) => <div className="li" key={message.id}><div className="f"><b>{message.name} · {message.orderId}</b><small>{message.text}</small></div><small>{new Date(message.createdAt).toLocaleString()}</small></div>)}</section> : <Empty icon="💬">Chat becomes available after you accept a delivery.</Empty>;
    },
  };
  return <PortalFrame role="RIDER" {...props} placeholder="Search orders, customers or locations…" screens={screens} />;
}
