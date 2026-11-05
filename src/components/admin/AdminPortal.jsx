import { useState } from "react";
import PortalFrame from "../PortalFrame.jsx";
import { RESTAURANTS } from "../../data/sample.js";
import { heroSteak } from "../../data/images.js";
import { Chart, Empty, Food, StatCard, Tag, toast } from "../../ui/components.jsx";
import { LOCAL_KEYS, usePersistentState } from "../../lib/localData.js";

const RANGES = { "7 Days": [30, 55, 78, 95, 120, 96, 122], "30 Days": [60, 80, 70, 110, 130, 125, 160], "90 Days": [90, 100, 140, 130, 170, 190, 210] };

// Local account approvals, order oversight, reports, and review moderation.
export default function AdminPortal(props) {
  const [range, setRange] = useState("7 Days");
  const [accounts, setAccounts] = usePersistentState(LOCAL_KEYS.accounts, []);
  const [orders, setOrders] = usePersistentState(LOCAL_KEYS.orders, []);
  const [reviews, setReviews] = usePersistentState(LOCAL_KEYS.reviews, []);

  // Admin actions persist changes to the shared browser-local demo records.
  const decide = (account, status) => {
    setAccounts((saved) => saved.map((item) => item.email === account.email ? { ...item, status } : item));
    toast(`${account.name} ${status === "APPROVED" ? "approved" : "rejected"}.`);
  };
  const changeOrder = (order, status) => {
    setOrders((saved) => saved.map((item) => item.id === order.id ? { ...item, status } : item));
    toast(`${order.id} updated.`);
  };
  const moderateReview = (review, status) => {
    setReviews((saved) => saved.map((item) => item.id === review.id ? { ...item, status } : item));
    toast(`Review ${status === "HIDDEN" ? "hidden" : "restored"}.`);
  };
  const searchable = (query, value) => value.toLowerCase().includes(query.toLowerCase());
  const table = (rows, columns) => <div className="tw"><table><thead><tr>{columns.map((column) => <th key={column}>{column}</th>)}</tr></thead><tbody>{rows}</tbody></table></div>;

  // Map admin navigation keys to dashboard and management screens.
  const screens = {
    dash: ({ setView }) => {
      const pending = accounts.filter((account) => account.status === "PENDING_APPROVAL");
      const sales = orders.filter((order) => order.status !== "CANCELLED").reduce((sum, order) => sum + order.total, 0);
      return <div className="col">
        <div className="ban"><div><span className="pill">Welcome Back, Admin!</span><h2>DashBite <span className="or">Admin Panel</span></h2><p>Manage local demo accounts, menu orders and system features.</p><button className="btn" onClick={() => setView("reports")}>View Reports →</button></div><Food src={heroSteak} /></div>
        <div className="stats">
          <StatCard icon="👥" label="Registered Accounts" value={String(accounts.length)} />
          <StatCard icon="🏪" label="Restaurants" value={String(RESTAURANTS.length)} />
          <StatCard icon="🛍️" label="Local Orders" value={String(orders.length)} />
          <StatCard icon="₱" label="Order Value" value={`₱${sales.toLocaleString()}`} />
        </div>
        <div className="stats admin-grid">
          <div className="card"><div className="row"><h3>Sales Analytics</h3><span className="tabs">{Object.keys(RANGES).map((item) => <button key={item} className={range === item ? "on" : ""} onClick={() => setRange(item)}>{item}</button>)}</span></div><Chart points={RANGES[range]} /><div className="steps">{["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"].map((day) => <span key={day}>{day}</span>)}</div></div>
          <div className="card"><h3>Recent Local Orders</h3>{orders.length ? [...orders].reverse().slice(0, 5).map((order) => <div className="li" key={order.id}><div className="f"><b>{order.id}</b><small>{order.restaurant} · ₱{order.total}</small></div><Tag tone="w">{order.status.replaceAll("_", " ")}</Tag></div>) : <Empty icon="🛍️">New orders will appear here.</Empty>}</div>
          <div className="card"><h3>Pending Approvals</h3>{pending.length ? pending.slice(0, 5).map((account) => <div className="li" key={account.email}><div className="f"><b>{account.name}</b><small>{account.role} · {account.email}</small></div><button className="btn sm" onClick={() => decide(account, "APPROVED")}>Approve</button></div>) : <Empty icon="🎉">No approvals waiting.</Empty>}</div>
        </div>
      </div>;
    },
    users: ({ q }) => {
      const matches = accounts.filter((account) => searchable(q, `${account.name} ${account.email} ${account.role}`));
      return <section className="card"><h3>Manage Users</h3>{matches.length ? table(matches.map((account) => <tr key={account.email}><td>{account.name}</td><td>{account.email}</td><td>{account.role}</td><td><Tag tone={account.status === "APPROVED" ? "a" : "w"}>{account.status.replaceAll("_", " ")}</Tag></td><td>{account.status === "DISABLED" ? <button className="btn gh sm" onClick={() => decide(account, "APPROVED")}>Enable</button> : <button className="btn gh sm" onClick={() => decide(account, "DISABLED")}>Disable</button>}</td></tr>), ["Name", "Email", "Role", "Status", "Action"]) : <Empty icon="👥">No registered users match your search yet.</Empty>}</section>;
    },
    restaurants: ({ q }) => {
      const matches = RESTAURANTS.filter((restaurant) => searchable(q, `${restaurant.name} ${restaurant.cuisine}`));
      return <section className="card"><h3>Restaurants</h3>{table(matches.map((restaurant) => <tr key={restaurant.name}><td>{restaurant.name}</td><td>{restaurant.cuisine}</td><td>⭐ {restaurant.rating}</td><td>₱{restaurant.fee}</td><td><Tag>Open</Tag></td></tr>), ["Name", "Cuisine", "Rating", "Delivery fee", "Status"])}</section>;
    },
    riders: ({ q }) => {
      const riders = accounts.filter((account) => account.role === "RIDER" && searchable(q, `${account.name} ${account.email}`));
      return <section className="card"><h3>Riders</h3>{riders.length ? table(riders.map((rider) => <tr key={rider.email}><td>{rider.name}</td><td>{rider.email}</td><td><Tag tone={rider.status === "APPROVED" ? "a" : "w"}>{rider.status.replaceAll("_", " ")}</Tag></td></tr>), ["Name", "Email", "Status"]) : <Empty icon="🛵">Rider sign-ups will appear here.</Empty>}</section>;
    },
    orders: ({ q }) => {
      const matches = orders.filter((order) => searchable(q, `${order.id} ${order.restaurant} ${order.status}`));
      return <section className="card"><h3>All Orders</h3>{matches.length ? table(matches.map((order) => <tr key={order.id}><td>{order.id}</td><td>{order.restaurant}</td><td>{order.items.map((item) => `${item.quantity}× ${item.name}`).join(", ")}</td><td>₱{order.total}</td><td><Tag tone="w">{order.status.replaceAll("_", " ")}</Tag></td><td>{!["DELIVERED", "CANCELLED"].includes(order.status) && <button className="btn gh sm" onClick={() => changeOrder(order, "CANCELLED")}>Cancel</button>}</td></tr>), ["Order", "Restaurant", "Items", "Total", "Status", "Action"]) : <Empty icon="🛍️">No local orders match your search.</Empty>}</section>;
    },
    reviews: ({ q }) => {
      const matches = reviews.filter((review) => searchable(q, `${review.restaurant} ${review.author} ${review.text}`));
      return <section className="card"><h3>Reviews</h3>{matches.length ? matches.map((review) => <div className="li review-row" key={review.id}><div className="f"><b>{review.restaurant} · {"★".repeat(review.rating)}</b><small>{review.author} · {review.orderId}</small><small>{review.text}</small></div><Tag tone={review.status === "HIDDEN" ? "w" : "a"}>{review.status}</Tag><button className="btn gh sm" onClick={() => moderateReview(review, review.status === "HIDDEN" ? "PUBLISHED" : "HIDDEN")}>{review.status === "HIDDEN" ? "Restore" : "Hide"}</button></div>) : <Empty icon="⭐">Customer reviews will appear here after completed orders are reviewed.</Empty>}</section>;
    },
    reports: () => <section className="card"><h3>Reports</h3><p>Summary of activity saved in this browser.</p><div className="stats"><StatCard icon="👥" label="Accounts" value={String(accounts.length)} /><StatCard icon="🛍️" label="Orders" value={String(orders.length)} /><StatCard icon="₱" label="Sales value" value={`₱${orders.filter((order) => order.status !== "CANCELLED").reduce((sum, order) => sum + order.total, 0).toLocaleString()}`} /><StatCard icon="⏳" label="Awaiting approval" value={String(accounts.filter((account) => account.status === "PENDING_APPROVAL").length)} /></div><Chart points={RANGES[range]} /><div className="tabs">{Object.keys(RANGES).map((item) => <button key={item} className={range === item ? "on" : ""} onClick={() => setRange(item)}>{item}</button>)}</div></section>,
    moderation: () => <section className="card"><h3>Moderation</h3><p>Hide reviews that should not appear to restaurant owners.</p>{reviews.length ? reviews.map((review) => <div className="li" key={review.id}><div className="f"><b>{review.restaurant} · {review.orderId}</b><small>{review.text}</small></div><Tag tone={review.status === "HIDDEN" ? "w" : "a"}>{review.status}</Tag><button className="btn gh sm" onClick={() => moderateReview(review, review.status === "HIDDEN" ? "PUBLISHED" : "HIDDEN")}>{review.status === "HIDDEN" ? "Restore" : "Hide"}</button></div>) : <Empty icon="🛡️">No reviews need moderation.</Empty>}</section>,
    logs: () => {
      const activity = [...orders.map((order) => ({ key: order.id, label: `Order ${order.id} · ${order.status.replaceAll("_", " ")}`, date: order.createdAt })), ...accounts.map((account) => ({ key: account.email, label: `${account.role} account ${account.email} · ${account.status.replaceAll("_", " ")}`, date: account.createdAt }))].sort((a, b) => new Date(b.date) - new Date(a.date));
      return <section className="card"><h3>System Logs</h3>{activity.length ? activity.map((item) => <div className="li" key={item.key}><span className="f">{item.label}</span><small>{new Date(item.date).toLocaleString()}</small></div>) : <Empty icon="📄">Activity will appear here as you use the demo.</Empty>}</section>;
    },
  };
  return <PortalFrame role="ADMIN" {...props} placeholder="Search users, restaurants, or orders…" screens={screens} />;
}
