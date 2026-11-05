import { useState } from "react";
import PortalFrame from "../PortalFrame.jsx";
import { CATEGORIES, RESTAURANTS } from "../../data/sample.js";
import { heroBurger } from "../../data/images.js";
import { Empty, Food, RestaurantCard, Tag, toast } from "../../ui/components.jsx";
import { INITIAL_MENU, LOCAL_KEYS, usePersistentState } from "../../lib/localData.js";

// Customer browsing, order placement, favorites, reviews, and chat.
export default function CustomerPortal(props) {
  const [cat, setCat] = useState("All");
  const [favs, setFavs] = usePersistentState(LOCAL_KEYS.favorites, ["Burger House"]);
  const [menu] = usePersistentState(LOCAL_KEYS.menu, INITIAL_MENU);
  const [orders, setOrders] = usePersistentState(LOCAL_KEYS.orders, []);
  const [reviews, setReviews] = usePersistentState(LOCAL_KEYS.reviews, []);
  const [messages, setMessages] = usePersistentState(LOCAL_KEYS.messages, []);
  const [selectedRestaurant, setSelectedRestaurant] = useState(null);
  const [reviewOrder, setReviewOrder] = useState("");
  const [reviewText, setReviewText] = useState("");
  const [rating, setRating] = useState("5");
  const [chatText, setChatText] = useState("");
  const [chatOrder, setChatOrder] = useState("");

  // Customer actions update shared browser-local records for other demo roles.
  const toggleFav = (name) => setFavs((saved) => saved.includes(name) ? saved.filter((item) => item !== name) : [...saved, name]);
  const cards = (list, onSelect) => <div className="grid4">{list.map((r) => <RestaurantCard key={r.name} r={r} fav={favs.includes(r.name)} onFav={toggleFav} onViewMenu={() => onSelect(r.name)} />)}</div>;
  const placeOrder = (restaurant, item) => {
    const newOrder = {
      id: `DB${Date.now().toString().slice(-6)}`,
      restaurant,
      items: [{ id: item.id, name: item.name, price: item.price, quantity: 1 }],
      total: item.price,
      status: "PENDING",
      createdAt: new Date().toISOString(),
    };
    setOrders((saved) => [...saved, newOrder]);
    setSelectedRestaurant(null);
    toast(`Order ${newOrder.id} placed. The restaurant can now accept it.`);
    return newOrder;
  };
  const findRestaurants = (q = "") => RESTAURANTS.filter((restaurant) =>
    (cat === "All" || restaurant.cuisine === cat) &&
    `${restaurant.name} ${restaurant.cuisine}`.toLowerCase().includes(q.toLowerCase()));
  const openMenu = (name) => setSelectedRestaurant(name);
  const submitReview = (event) => {
    event.preventDefault();
    const order = orders.find((item) => item.id === reviewOrder && item.status === "DELIVERED");
    if (!order || !reviewText.trim()) return;
    setReviews((saved) => [...saved, { id: `review-${Date.now()}`, orderId: order.id, restaurant: order.restaurant, author: props.user.name, rating: Number(rating), text: reviewText.trim(), status: "PUBLISHED", createdAt: new Date().toISOString() }]);
    setReviewText("");
    setReviewOrder("");
    toast("Your review was saved on this device.");
  };
  const sendMessage = (event) => {
    event.preventDefault();
    const text = chatText.trim();
    if (!chatOrder || !text) return;
    setMessages((saved) => [...saved, { id: `message-${Date.now()}`, orderId: chatOrder, sender: "customer", name: props.user.name, text, createdAt: new Date().toISOString() }]);
    setChatText("");
    toast("Message sent to the restaurant.");
  };
  const completedOrders = orders.filter((order) => order.status === "DELIVERED" && !reviews.some((review) => review.orderId === order.id));

  // Restaurant selection and order forms are reused by the dashboard and browse screen.
  const menuView = (name) => {
    const availableItems = menu.filter((item) => item.available);
    return <section className="card"><div className="row"><div><h3>{name} Menu</h3><small>Select an item to place a local demo order.</small></div><button className="btn gh sm" onClick={() => setSelectedRestaurant(null)}>Back to Restaurants</button></div>
      {availableItems.length ? <div className="mg menu-grid">{availableItems.map((item) => <article className="mi menu-item" key={item.id}><Food src={item.image} alt={item.name} /><b>{item.name}</b><span>₱{item.price}</span><button className="btn sm" onClick={() => placeOrder(name, item)}>Order Now</button></article>)}</div> : <Empty icon="🍽️">There are no available menu items right now.</Empty>}
    </section>;
  };

  // Map navigation keys to customer views.
  const screens = {
    dash: ({ q, setView }) => {
      const list = findRestaurants(q);
      return (
        <div className="two">
          <div className="col">
            <div className="ban">
              <div><span className="or">Fast · Fresh · Local</span><h2>Your Favorite Food <span className="or">Delivered Fast</span></h2>
                <p>Order from the best local restaurants and get it delivered to your door.</p>
                <button className="btn" onClick={() => setView("browse")}>Order Now →</button></div>
              <Food src={heroBurger} alt="" />
            </div>
            <div><h3>Food Categories</h3>
              <div className="cats">{CATEGORIES.map(([c, e]) => <button key={c} className={`cat ${cat === c ? "on" : ""}`} onClick={() => setCat(c)}><em>{e}</em>{c}</button>)}</div></div>
            <div><h3>Popular Restaurants</h3>
              {selectedRestaurant ? menuView(selectedRestaurant) : list.length ? cards(list, openMenu) : <Empty icon="🔎">No restaurants match your search. Try another food or category.</Empty>}</div>
          </div>
          <div className="col">
            <div className="card"><h3>My Orders</h3>
              {orders.length ? [...orders].reverse().slice(0, 3).map((order) => <div className="li" key={order.id}><Food src={RESTAURANTS.find((restaurant) => restaurant.name === order.restaurant)?.img || RESTAURANTS[0].img} style={{ width: 50, minHeight: 50, borderRadius: 12 }} /><div className="f"><b>Order #{order.id}</b><small>₱{order.total}</small><span className="or">{order.status.replaceAll("_", " ")}</span></div></div>) : <Empty icon="🛍️">Your orders will appear here after checkout.</Empty>}
            </div>
            <div className="card"><h3>Track Your Order</h3>
              {orders.some((order) => ["READY", "ON_THE_WAY"].includes(order.status)) ? <div className="food" style={{ minHeight: 90, fontSize: 34, background: "linear-gradient(135deg,#dfeadc,#f3e8d4)" }}>🍴 ⋯ 🛵 ⋯ 🏠</div> : <p>Your delivery tracker will activate when an order is ready for pickup.</p>}
            </div>
            <div className="card"><h3>My Favorites</h3>
              <div className="qa">{favs.map((name) => <button key={name} onClick={() => openMenu(name)}>❤️ {name}</button>)}</div>
              {!favs.length && <small>No favorites yet. Tap a heart on any restaurant.</small>}</div>
            <div className="promo"><b>Get 20% OFF on your next order!</b><span>Use code: DASH20</span></div>
          </div>
        </div>
      );
    },
    browse: ({ q }) => {
      const list = findRestaurants(q);
      return selectedRestaurant ? menuView(selectedRestaurant) : list.length ? cards(list, openMenu) : <Empty icon="🔎">No restaurants match your search.</Empty>;
    },
    favorites: ({ q }) => { const list = findRestaurants(q).filter((restaurant) => favs.includes(restaurant.name)); return selectedRestaurant ? menuView(selectedRestaurant) : list.length ? cards(list, openMenu) : <Empty icon="🤍">No favorites match. Tap a heart on a restaurant to save it.</Empty>; },
    orders: () => orders.length ? <section className="card"><h3>My Orders</h3>{[...orders].reverse().map((order) => <div className="li" key={order.id}><div className="f"><b>{order.id} · {order.restaurant}</b><small>{order.items.map((item) => `${item.quantity}× ${item.name}`).join(", ")}</small><small>₱{order.total}</small></div><span className="tg w">{order.status.replaceAll("_", " ")}</span></div>)}</section> : <Empty icon="🛍️">You have no orders yet. Browse restaurants to place your first order.</Empty>,
    reviews: () => <section className="card"><h3>Reviews</h3>
      {completedOrders.length > 0 && <form className="local-form" onSubmit={submitReview}><label>Completed order<select className="text-input" value={reviewOrder} onChange={(event) => setReviewOrder(event.target.value)} required><option value="">Select an order</option>{completedOrders.map((order) => <option key={order.id} value={order.id}>{order.id} · {order.restaurant}</option>)}</select></label><label>Rating<select className="text-input" value={rating} onChange={(event) => setRating(event.target.value)}><option value="5">★★★★★</option><option value="4">★★★★☆</option><option value="3">★★★☆☆</option><option value="2">★★☆☆☆</option><option value="1">★☆☆☆☆</option></select></label><label>Your review<textarea className="text-input" value={reviewText} onChange={(event) => setReviewText(event.target.value)} maxLength={500} required /></label><button className="btn">Save Review</button></form>}
      {reviews.filter((review) => review.author === props.user.name).map((review) => <div className="li" key={review.id}><div className="f"><b>{review.restaurant} · {"★".repeat(review.rating)}</b><small>{review.text}</small></div><Tag tone={review.status === "HIDDEN" ? "w" : "a"}>{review.status}</Tag></div>)}
      {!completedOrders.length && !reviews.some((review) => review.author === props.user.name) && <Empty icon="⭐">Reviews unlock after a delivery is marked complete.</Empty>}
    </section>,
    chat: () => <section className="card"><h3>Restaurant Chat</h3>
      {orders.length ? <><form className="local-form" onSubmit={sendMessage}><label>Order<select className="text-input" value={chatOrder} onChange={(event) => setChatOrder(event.target.value)} required><option value="">Select an order</option>{orders.map((order) => <option key={order.id} value={order.id}>{order.id} · {order.restaurant}</option>)}</select></label><label>Message<textarea className="text-input" value={chatText} onChange={(event) => setChatText(event.target.value)} maxLength={500} required /></label><button className="btn">Send Message</button></form>
        {messages.filter((message) => orders.some((order) => order.id === message.orderId)).map((message) => <div className="li" key={message.id}><div className="f"><b>{message.name} · {message.orderId}</b><small>{message.text}</small></div><small>{new Date(message.createdAt).toLocaleString()}</small></div>)}</> : <Empty icon="💬">Place an order to start a conversation with the restaurant.</Empty>}
    </section>,
  };
  return <PortalFrame role="CUSTOMER" {...props} placeholder="Search for food, restaurants…" screens={screens} />;
}
