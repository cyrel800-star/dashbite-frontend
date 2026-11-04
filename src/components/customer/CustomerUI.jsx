import { RestaurantPrice } from "../restaurant/RestaurantUI.jsx";

export function CustomerSectionHeading({ eyebrow, title, description, action }) {
  return <div className="customer-section-heading">
    <div><p>{eyebrow}</p><h2>{title}</h2><span>{description}</span></div>
    {action}
  </div>;
}

export function CustomerStatus({ value }) {
  return <span className={`status status-${String(value).toLowerCase()}`}>{String(value).replaceAll("_", " ")}</span>;
}

export function CustomerEmptyState({ icon = "⌕", title, description, action }) {
  return <div className="customer-empty-state">
    <span aria-hidden="true">{icon}</span><strong>{title}</strong><p>{description}</p>{action}
  </div>;
}

export function CustomerNotice({ notice, onDismiss }) {
  if (!notice) return null;
  return <div className={`customer-notice customer-notice-${notice.type}`} role={notice.type === "error" ? "alert" : "status"}>
    <span>{notice.message}</span>{onDismiss && <button type="button" aria-label="Dismiss message" onClick={onDismiss}>×</button>}
  </div>;
}

export function CustomerStars({ value, size = "normal" }) {
  const rating = Number(value) || 0;
  return <span className={`customer-stars customer-stars-${size}`} aria-label={`${rating} out of 5 stars`}>
    {Array.from({ length: 5 }, (_, index) => <span className={index < rating ? "is-filled" : ""} key={index}>★</span>)}
  </span>;
}

export function CustomerRestaurantCard({ restaurant, favorite, onFavorite, onOpen }) {
  return <article className="customer-restaurant-card">
    <button className={`customer-restaurant-art customer-art-${restaurant.accent}`} type="button" onClick={onOpen} aria-label={`View ${restaurant.name}`}>
      <span>{restaurant.emoji}</span><small>{restaurant.cuisine}</small>
      <span className={`customer-open-chip${restaurant.isOpen ? " is-open" : ""}`}><i />{restaurant.isOpen ? "Open" : "Closed"}</span>
    </button>
    <div className="customer-restaurant-card-body">
      <div className="customer-card-title-row">
        <button className="customer-card-title" type="button" onClick={onOpen}>{restaurant.name}</button>
        <button className={`customer-favorite-button${favorite ? " is-favorite" : ""}`} type="button" onClick={onFavorite} aria-label={favorite ? `Remove ${restaurant.name} from favorites` : `Add ${restaurant.name} to favorites`}>{favorite ? "♥" : "♡"}</button>
      </div>
      <div className="customer-restaurant-meta"><span>★ {restaurant.rating}</span><i>·</i><span>{restaurant.deliveryMinutes}</span><i>·</i><span>₱{restaurant.deliveryFee.toFixed(2)} delivery</span></div>
      <p>{restaurant.description}</p>
      <button className="customer-menu-link" type="button" onClick={onOpen}>Explore menu <span aria-hidden="true">→</span></button>
    </div>
  </article>;
}

export function CustomerFoodCard({ item, favorite, onFavorite, onAdd }) {
  return <article className="customer-food-card">
    <div className="customer-food-art"><span>{item.emoji}</span><button className={`customer-favorite-button${favorite ? " is-favorite" : ""}`} type="button" onClick={onFavorite} aria-label={favorite ? `Remove ${item.name} from favorites` : `Add ${item.name} to favorites`}>{favorite ? "♥" : "♡"}</button></div>
    <div className="customer-food-body"><span className="customer-food-restaurant">{item.restaurantName}</span><strong>{item.name}</strong><p>{item.description}</p><div><b><RestaurantPrice value={item.price} /></b><button className="customer-add-button" type="button" onClick={onAdd} aria-label={`Add ${item.name} to cart`}>＋</button></div></div>
  </article>;
}

export function CustomerRestaurantPrice({ value }) {
  return <RestaurantPrice value={value} />;
}
