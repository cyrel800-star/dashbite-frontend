import { useMemo, useState } from "react";
import { CUSTOMER_RESTAURANTS } from "../../lib/customerPortal.js";
import { CustomerFoodCard, CustomerNotice, CustomerSectionHeading, CustomerStars } from "./CustomerUI.jsx";

function RestaurantReviewForm({ restaurant, data, onChange, onComplete }) {
  const [rating, setRating] = useState(5);
  const [title, setTitle] = useState("");
  const [body, setBody] = useState("");
  const [error, setError] = useState("");
  const eligibleOrder = data.orders.some((order) => order.restaurantId === restaurant.id && order.status === "DELIVERED");

  function submitReview(event) {
    event.preventDefault();
    if (!eligibleOrder) {
      setError("Reviews are available after a completed preview order from this restaurant.");
      return;
    }
    if (!title.trim() || !body.trim()) {
      setError("Add a short title and share a few details about your experience.");
      return;
    }
    onChange((current) => ({
      ...current,
      reviews: [{
        id: `review-${Date.now()}`,
        restaurantId: restaurant.id,
        customerName: current.profile.name || "DashBite Guest",
        rating,
        title: title.trim(),
        body: body.trim(),
        createdAt: new Date().toISOString(),
      }, ...current.reviews],
    }));
    setTitle("");
    setBody("");
    setRating(5);
    setError("");
    onComplete();
  }

  return <form className="customer-review-form" onSubmit={submitReview}>
    <div><h3>Rate your experience</h3><p>Your feedback helps neighbors find a great meal.</p></div>
    {!eligibleOrder && <p className="customer-inline-note">Place a preview order and mark it delivered before leaving a review.</p>}
    <fieldset className="customer-rating-picker" disabled={!eligibleOrder}>
      <legend>Your rating</legend>
      {Array.from({ length: 5 }, (_, index) => {
        const value = index + 1;
        return <button type="button" className={value <= rating ? "is-selected" : ""} key={value} onClick={() => setRating(value)} aria-label={`${value} star${value === 1 ? "" : "s"}`}>★</button>;
      })}
    </fieldset>
    <label className="customer-field">Review title<input value={title} onChange={(event) => setTitle(event.target.value)} maxLength={100} placeholder="What stood out?" disabled={!eligibleOrder} /></label>
    <label className="customer-field">Your review<textarea value={body} onChange={(event) => setBody(event.target.value)} rows="3" maxLength={600} placeholder="How was the food and service?" disabled={!eligibleOrder} /></label>
    {error && <p className="customer-form-error" role="alert">{error}</p>}
    <button className="button button-primary" type="submit" disabled={!eligibleOrder}>Post review</button>
  </form>;
}

export default function CustomerRestaurant({ restaurantId, data, onChange, onNavigate }) {
  const restaurant = CUSTOMER_RESTAURANTS.find((item) => item.id === restaurantId);
  const [tab, setTab] = useState("MENU");
  const [notice, setNotice] = useState(null);
  const [category, setCategory] = useState("All");
  const reviews = data.reviews.filter((review) => review.restaurantId === restaurantId);
  const averageRating = reviews.length ? (reviews.reduce((sum, review) => sum + review.rating, 0) / reviews.length).toFixed(1) : restaurant?.rating;
  const categories = ["All", ...new Set((restaurant?.menu || []).map((item) => item.category))];
  const menu = useMemo(() => (restaurant?.menu || []).filter((item) => category === "All" || item.category === category), [category, restaurant]);
  if (!restaurant) return <div className="customer-empty-state"><span aria-hidden="true">⌕</span><strong>Restaurant not found</strong><p>This restaurant isn't part of the current preview menu.</p><button className="button button-primary" type="button" onClick={() => onNavigate("RESTAURANTS")}>Browse restaurants</button></div>;

  function toggleFoodFavorite(itemId) {
    onChange((current) => ({
      ...current,
      favorites: {
        ...current.favorites,
        foods: current.favorites.foods.includes(itemId)
          ? current.favorites.foods.filter((item) => item !== itemId)
          : [...current.favorites.foods, itemId],
      },
    }));
  }

  function addToCart(item) {
    if (data.cart.restaurantId && data.cart.restaurantId !== restaurant.id && data.cart.items.length) {
      setNotice({ type: "error", message: "Your cart has items from another restaurant. Clear your cart before switching restaurants." });
      return;
    }
    onChange((current) => {
      const currentItem = current.cart.items.find((entry) => entry.menuItemId === item.id);
      return {
        ...current,
        cart: {
          restaurantId: restaurant.id,
          items: currentItem
            ? current.cart.items.map((entry) => entry.menuItemId === item.id ? { ...entry, quantity: entry.quantity + 1 } : entry)
            : [...current.cart.items, { menuItemId: item.id, quantity: 1 }],
        },
      };
    });
    setNotice({ type: "success", message: `${item.name} added to your cart.` });
  }

  const favorite = data.favorites.restaurants.includes(restaurant.id);
  return <div className="customer-page-content">
    <section className={`customer-restaurant-hero customer-art-${restaurant.accent}`}>
      <button className="customer-back-link" type="button" onClick={() => onNavigate("RESTAURANTS")}>← All restaurants</button>
      <div className="customer-restaurant-hero-main"><span className="customer-restaurant-hero-emoji" aria-hidden="true">{restaurant.emoji}</span><div><p>{restaurant.cuisine}</p><h2>{restaurant.name}</h2><span>{restaurant.description}</span></div></div>
      <div className="customer-restaurant-hero-footer"><div><CustomerStars value={Math.round(restaurant.rating)} /><span>{averageRating} · {reviews.length} preview reviews</span><i>·</i><span>{restaurant.deliveryMinutes}</span><i>·</i><span>₱{restaurant.deliveryFee.toFixed(2)} delivery</span></div><button className={`customer-favorite-button${favorite ? " is-favorite" : ""}`} type="button" onClick={() => onChange((current) => ({ ...current, favorites: { ...current.favorites, restaurants: favorite ? current.favorites.restaurants.filter((id) => id !== restaurant.id) : [...current.favorites.restaurants, restaurant.id] } }))} aria-label={favorite ? "Remove restaurant from favorites" : "Add restaurant to favorites"}>{favorite ? "♥" : "♡"}</button></div>
    </section>
    <CustomerNotice notice={notice} onDismiss={() => setNotice(null)} />
    <div className="customer-restaurant-tabs" role="tablist" aria-label="Restaurant details">
      <button type="button" role="tab" aria-selected={tab === "MENU"} className={tab === "MENU" ? "is-active" : ""} onClick={() => setTab("MENU")}>Menu <span>{restaurant.menu.length}</span></button>
      <button type="button" role="tab" aria-selected={tab === "REVIEWS"} className={tab === "REVIEWS" ? "is-active" : ""} onClick={() => setTab("REVIEWS")}>Reviews <span>{reviews.length}</span></button>
    </div>
    {tab === "MENU" && <>
      <CustomerSectionHeading eyebrow="MADE FRESH, JUST FOR YOU" title="The menu" description={`A ${restaurant.minimum.toFixed(0)} minimum order · currently ${restaurant.isOpen ? "open" : "closed"}.`} />
      <div className="customer-category-tabs" aria-label="Menu category">
        {categories.map((item) => <button className={category === item ? "is-active" : ""} type="button" key={item} onClick={() => setCategory(item)}>{item}</button>)}
      </div>
      <div className="customer-detail-menu">{menu.map((item) => <article className="customer-detail-menu-item" key={item.id}>
        <div><span className="customer-detail-menu-emoji" aria-hidden="true">{item.emoji}</span><div><strong>{item.name}</strong><p>{item.description}</p><b>₱{item.price.toFixed(2)}</b></div></div>
        <button className="customer-add-button" type="button" disabled={!restaurant.isOpen} onClick={() => addToCart(item)} aria-label={`Add ${item.name} to cart`}>＋</button>
      </article>)}</div>
      {data.cart.items.length > 0 && <button className="customer-cart-banner" type="button" onClick={() => onNavigate("CART")}><span>🛍</span><strong>{data.cart.items.reduce((sum, item) => sum + item.quantity, 0)} items in your cart</strong><span>View cart →</span></button>}
      <div className="customer-food-grid customer-menu-suggestions">{restaurant.menu.slice(0, 2).map((item) => <CustomerFoodCard key={item.id} item={{ ...item, restaurantName: restaurant.name }} favorite={data.favorites.foods.includes(item.id)} onFavorite={() => toggleFoodFavorite(item.id)} onAdd={() => addToCart(item)} />)}</div>
    </>}
    {tab === "REVIEWS" && <div className="customer-review-layout">
      <section className="customer-review-summary"><strong>★ {averageRating}</strong><CustomerStars value={Math.round(Number(averageRating) || 0)} /><span>Based on {reviews.length} preview reviews</span></section>
      <div className="customer-review-list">{reviews.map((review) => <article className="customer-review-card" key={review.id}><div className="customer-review-card-top"><div className="customer-review-avatar">{review.customerName.charAt(0)}</div><div><strong>{review.customerName}</strong><small>{new Intl.DateTimeFormat(undefined, { month: "short", day: "numeric" }).format(new Date(review.createdAt))}</small></div><CustomerStars value={review.rating} size="small" /></div><h3>{review.title}</h3><p>{review.body}</p></article>)}</div>
      <RestaurantReviewForm restaurant={restaurant} data={data} onChange={onChange} onComplete={() => setNotice({ type: "success", message: "Thanks for sharing your review." })} />
    </div>}
  </div>;
}
