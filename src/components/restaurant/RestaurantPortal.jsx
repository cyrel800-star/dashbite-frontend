import { useState } from "react";
import PortalFrame from "../PortalFrame.jsx";
import { heroBurger } from "../../data/images.js";
import { Chart, Empty, Food, StatCard, Tag, toast } from "../../ui/components.jsx";
import { INITIAL_MENU, LOCAL_KEYS, readLocalImage, usePersistentState } from "../../lib/localData.js";

const ORDER_STEPS = {
  PENDING: ["Pending", "w", "Accept Order"],
  ACCEPTED: ["Accepted", "a", "Start Preparing"],
  PREPARING: ["Preparing", "b", "Mark Ready"],
  READY: ["Ready for pickup", "w", "Waiting for rider"],
  ON_THE_WAY: ["On the way", "c", "Delivery in progress"],
  DELIVERED: ["Delivered", "a", "Completed"],
  CANCELLED: ["Cancelled", "c", "Cancelled"],
};

const newId = () => `food-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`;

// Restaurant menu, order, hours, review, and customer-message management.
export default function RestaurantPortal(props) {
  const [menu, setMenu] = usePersistentState(LOCAL_KEYS.menu, INITIAL_MENU);
  const [orders, setOrders] = usePersistentState(LOCAL_KEYS.orders, []);
  const [reviews] = usePersistentState(LOCAL_KEYS.reviews, []);
  const [messages, setMessages] = usePersistentState(LOCAL_KEYS.messages, []);
  const [editor, setEditor] = useState(null);
  const [form, setForm] = useState({ name: "", price: "", category: "Burgers", image: "", available: true });
  const [formError, setFormError] = useState("");
  const [replyOrder, setReplyOrder] = useState("");
  const [replyText, setReplyText] = useState("");

  // Menu editor actions and image selection.
  const startAdd = () => {
    setForm({ name: "", price: "", category: "Burgers", image: "", available: true });
    setFormError("");
    setEditor("new");
  };
  const startEdit = (item) => {
    setForm({ ...item, price: String(item.price) });
    setFormError("");
    setEditor(item.id);
  };
  const saveItem = (event) => {
    event.preventDefault();
    const name = form.name.trim();
    const price = Number(form.price);
    if (!name) return setFormError("Enter a food name.");
    if (!Number.isFinite(price) || price <= 0) return setFormError("Enter a valid price greater than zero.");
    const item = { ...form, id: editor === "new" ? newId() : form.id, name, price };
    setMenu((items) => editor === "new" ? [...items, item] : items.map((entry) => entry.id === editor ? item : entry));
    setEditor(null);
    toast(editor === "new" ? "Food item added and saved on this device." : "Food item updated.");
  };
  const chooseImage = async (event) => {
    const file = event.target.files?.[0];
    if (!file) return;
    try {
      const image = await readLocalImage(file);
      setForm((current) => ({ ...current, image }));
      setFormError("");
    } catch (error) {
      setFormError(error.message);
    }
    event.target.value = "";
  };
  const advanceOrder = (order) => {
    const next = { PENDING: "ACCEPTED", ACCEPTED: "PREPARING", PREPARING: "READY" }[order.status];
    if (!next) return;
    setOrders((items) => items.map((item) => item.id === order.id ? { ...item, status: next } : item));
    toast(`${order.id} is now ${ORDER_STEPS[next][0].toLowerCase()}.`);
  };
  const removeItem = (id) => {
    if (window.confirm("Remove this food item from your menu?")) {
      setMenu((items) => items.filter((item) => item.id !== id));
      toast("Food item removed.");
    }
  };
  const sendReply = (event) => {
    event.preventDefault();
    const text = replyText.trim();
    if (!replyOrder || !text) return;
    setMessages((saved) => [...saved, { id: `message-${Date.now()}`, orderId: replyOrder, sender: "restaurant", name: "Burger House", text, createdAt: new Date().toISOString() }]);
    setReplyText("");
    toast("Reply saved on this device.");
  };
  const restaurantOrders = orders.filter((order) => order.restaurant === "Burger House");

  // Role-specific dashboard and management screens.
  const screens = {
    dash: ({ setView }) => (
      <div className="two">
        <div className="col">
          <div className="ban"><div><span className="pill">Welcome Back!</span><h2>Burger <span className="or">House</span></h2><p>Manage your restaurant orders and grow your business with DashBite.</p><button className="btn" onClick={() => setView("orders")}>View Orders →</button></div><Food src={heroBurger} /></div>
          <div className="stats">
            <StatCard icon="🛍️" label="Total Orders" value={String(restaurantOrders.length)} delta="Local orders" />
            <StatCard icon="🍽️" label="Menu Items" value={String(menu.length)} delta="Manage menu" />
            <StatCard icon="⏳" label="To Prepare" value={String(restaurantOrders.filter((order) => ["PENDING", "ACCEPTED", "PREPARING"].includes(order.status)).length)} delta="Open orders" />
          </div>
          <div className="card"><div className="row"><h3>Your Menu</h3><button className="or text-button" onClick={() => setView("menu")}>View All →</button></div>
            <div className="mg">{menu.slice(0, 4).map((item) => (
              <div className="mi" key={item.id}><Food src={item.image} alt={item.name} /><b>{item.name}</b>₱{item.price}<small className={`dot ${item.available ? "" : "off"}`}>● {item.available ? "Available" : "Unavailable"}</small>
                <span><button className="btn gh sm" onClick={() => startEdit(item)} aria-label={`Edit ${item.name}`}>✏️</button> <button className="btn gh sm" onClick={() => removeItem(item.id)} aria-label={`Delete ${item.name}`}>🗑️</button></span></div>
            ))}
              <button className="card add-item" onClick={startAdd}>➕<br />Add Menu Item</button></div></div>
          <div className="card"><div className="row"><h3>Performance Overview</h3><Tag tone="w">This Week</Tag></div><Chart points={[35, 60, 80, 75, 100, 132, 105]} /><div className="steps">{["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"].map((day) => <span key={day}>{day}</span>)}</div></div>
        </div>
        <div className="col">
          <OrderList orders={restaurantOrders.slice(0, 4)} advanceOrder={advanceOrder} />
          <div className="card"><h3>Restaurant Information</h3><b>Burger House</b> <Tag>Open</Tag><p>📍 123 Food Street, Manila, PH<br />🕒 Open: 9:00 AM – 11:00 PM</p><button className="btn ol sm" onClick={() => setView("profile")}>Edit Profile →</button></div>
        </div>
      </div>
    ),
    menu: ({ q }) => {
      const filteredMenu = menu.filter((item) => `${item.name} ${item.category}`.toLowerCase().includes(q.toLowerCase()));
      return (
      <section className="col">
        <div className="card"><div className="row"><div><h3>Menu Management</h3><small>Changes are saved in this browser and visible to customers.</small></div><button className="btn" onClick={startAdd}>＋ Add Food</button></div>
          {filteredMenu.length ? <div className="mg menu-grid">{filteredMenu.map((item) => (
            <article className="mi menu-item" key={item.id}>
              <Food src={item.image} alt={item.name} />
              <b>{item.name}</b><span>₱{item.price} · {item.category}</span>
              <small className={`dot ${item.available ? "" : "off"}`}>● {item.available ? "Available" : "Unavailable"}</small>
              <div className="row"><button className="btn gh sm" onClick={() => startEdit(item)}>Edit</button><button className="btn gh sm" onClick={() => setMenu((items) => items.map((entry) => entry.id === item.id ? { ...entry, available: !entry.available } : entry))}>{item.available ? "Pause" : "Enable"}</button><button className="btn gh sm" onClick={() => removeItem(item.id)}>Delete</button></div>
            </article>
          ))}</div> : <Empty icon="🍽️">No food items match your search. Add a new item or change your search.</Empty>}
        </div>
      </section>
      );
    },
    orders: () => <OrderList orders={restaurantOrders} advanceOrder={advanceOrder} />,
    hours: () => <HoursPanel />,
    analytics: () => <div className="card"><h3>Restaurant Analytics</h3><p>Local activity from this device.</p><div className="stats"><StatCard icon="🛍️" label="Orders" value={String(restaurantOrders.length)} /><StatCard icon="₱" label="Sales" value={`₱${restaurantOrders.reduce((sum, order) => sum + order.total, 0)}`} /><StatCard icon="🍽️" label="Available items" value={String(menu.filter((item) => item.available).length)} /></div><Chart points={restaurantOrders.length ? restaurantOrders.slice(-7).map((order) => order.total) : [0, 20, 10, 30, 20, 40, 35]} /></div>,
    reviews: () => <section className="card"><h3>Customer Reviews</h3>{reviews.filter((review) => review.restaurant === "Burger House" && review.status !== "HIDDEN").length ? reviews.filter((review) => review.restaurant === "Burger House" && review.status !== "HIDDEN").map((review) => <div className="li" key={review.id}><div className="f"><b>{review.author} · {"★".repeat(review.rating)}</b><small>{review.text}</small><small>{review.orderId}</small></div><small>{new Date(review.createdAt).toLocaleDateString()}</small></div>) : <Empty icon="⭐">Customer reviews appear here after a delivered order is reviewed.</Empty>}</section>,
    chat: () => <section className="card"><h3>Customer Chat</h3>
      {messages.length ? <><form className="local-form" onSubmit={sendReply}><label>Order<select className="text-input" value={replyOrder} onChange={(event) => setReplyOrder(event.target.value)} required><option value="">Select an order</option>{restaurantOrders.map((order) => <option key={order.id} value={order.id}>{order.id}</option>)}</select></label><label>Reply<textarea className="text-input" value={replyText} onChange={(event) => setReplyText(event.target.value)} maxLength={500} required /></label><button className="btn">Send Reply</button></form>
        {messages.filter((message) => restaurantOrders.some((order) => order.id === message.orderId)).map((message) => <div className="li" key={message.id}><div className="f"><b>{message.name} · {message.orderId}</b><small>{message.text}</small></div><small>{new Date(message.createdAt).toLocaleString()}</small></div>)}</> : <Empty icon="💬">Customer messages about your orders will appear here.</Empty>}
    </section>,
  };

  // Shared modal for adding or editing a menu item.
  return (
    <>
      <PortalFrame role="RESTAURANT" {...props} placeholder="Search orders, menu or customers…" screens={screens} />
      {editor && <div className="modal-backdrop" role="presentation" onMouseDown={(event) => event.target === event.currentTarget && setEditor(null)}>
        <form className="card item-editor" onSubmit={saveItem}>
          <div className="row"><h3>{editor === "new" ? "Add Food Item" : "Edit Food Item"}</h3><button type="button" className="ib" onClick={() => setEditor(null)} aria-label="Close">×</button></div>
          <label>Food name<input className="text-input" value={form.name} onChange={(event) => setForm({ ...form, name: event.target.value })} maxLength={70} /></label>
          <label>Price (₱)<input className="text-input" type="number" min="1" step="0.01" value={form.price} onChange={(event) => setForm({ ...form, price: event.target.value })} /></label>
          <label>Category<select className="text-input" value={form.category} onChange={(event) => setForm({ ...form, category: event.target.value })}>{["Burgers", "Pizza", "Noodles", "Chicken", "Dessert", "Drinks", "Other"].map((category) => <option key={category}>{category}</option>)}</select></label>
          <label className="upload-field">Food image (JPG, PNG or WEBP)<input type="file" accept="image/jpeg,image/png,image/webp" onChange={chooseImage} /></label>
          {form.image && <Food src={form.image} alt="Selected food preview" className="upload-preview" />}
          <div className="msg" role="alert">{formError}</div>
          <button className="btn" type="submit">Save Food Item</button>
        </form>
      </div>}
    </>
  );
}

// Order status display and state transitions.
function OrderList({ orders, advanceOrder }) {
  return <section className="card"><h3>Restaurant Orders</h3>
    {orders.length ? <div className="order-list">{orders.map((order) => {
      const [label, tone, action] = ORDER_STEPS[order.status] || ["Unknown", "w", "Refresh"];
      const next = ["PENDING", "ACCEPTED", "PREPARING"].includes(order.status);
      return <div className="li order-row" key={order.id}><div className="f"><b>{order.id}</b><small>{order.items.map((item) => `${item.quantity}× ${item.name}`).join(", ")}</small><small>₱{order.total} · {new Date(order.createdAt).toLocaleString()}</small></div><Tag tone={tone}>{label}</Tag>{next && <button className="btn sm" onClick={() => advanceOrder(order)}>{action}</button>}</div>;
    })}</div> : <Empty icon="🛍️">New customer orders will appear here. Orders are stored on this device only.</Empty>}
  </section>;
}

// Persist the restaurant's opening hours and accepting-orders state.
function HoursPanel() {
  const [hours, setHours] = usePersistentState("dashbite.restaurant.hours", { opening: "09:00", closing: "23:00", open: true });
  return <section className="card"><h3>Operating Hours</h3><p>Set the hours customers can order from your restaurant.</p><div className="hours-form"><label>Opening time<input className="text-input" type="time" value={hours.opening} onChange={(event) => setHours({ ...hours, opening: event.target.value })} /></label><label>Closing time<input className="text-input" type="time" value={hours.closing} onChange={(event) => setHours({ ...hours, closing: event.target.value })} /></label><label className="li"><span className="f">Accepting orders</span><button className={`sw ${hours.open ? "on" : ""}`} role="switch" aria-checked={hours.open} aria-label="Accepting orders" onClick={() => setHours({ ...hours, open: !hours.open })} /></label></div><p className="msg ok">Hours are saved on this device.</p></section>;
}
