import { useMemo, useState } from "react";
import { CUSTOMER_RESTAURANTS } from "../../lib/customerPortal.js";
import { CustomerFoodCard, CustomerNotice, CustomerRestaurantCard, CustomerSectionHeading } from "./CustomerUI.jsx";

const CUISINES = ["All", ...new Set(CUSTOMER_RESTAURANTS.map((restaurant) => restaurant.cuisine.split(" · ")[0]))];

export default function CustomerBrowse({ data, onChange, onNavigate, initialSearch = "" }) {
  const [query, setQuery] = useState(initialSearch);
  const [cuisine, setCuisine] = useState("All");
  const [notice, setNotice] = useState(null);
  const restaurants = useMemo(() => CUSTOMER_RESTAURANTS.filter((restaurant) => {
    const matchesText = `${restaurant.name} ${restaurant.cuisine} ${restaurant.description} ${restaurant.menu.map((item) => item.name).join(" ")}`.toLowerCase().includes(query.trim().toLowerCase());
    const matchesCuisine = cuisine === "All" || restaurant.cuisine.includes(cuisine);
    return matchesText && matchesCuisine;
  }), [cuisine, query]);
  const popularItems = CUSTOMER_RESTAURANTS.flatMap((restaurant) => restaurant.menu.slice(0, 2).map((item) => ({
    ...item, restaurantId: restaurant.id, restaurantName: restaurant.name,
  }))).slice(0, 5);

  function toggleFavorite(type, id) {
    onChange((current) => {
      const currentItems = current.favorites[type];
      return {
        ...current,
        favorites: {
          ...current.favorites,
          [type]: currentItems.includes(id) ? currentItems.filter((item) => item !== id) : [...currentItems, id],
        },
      };
    });
  }

  function addToCart(item) {
    const currentRestaurantId = data.cart.restaurantId;
    if (currentRestaurantId && currentRestaurantId !== item.restaurantId && data.cart.items.length) {
      setNotice({ type: "error", message: "Your cart has items from another restaurant. Clear your cart before switching restaurants." });
      return;
    }
    onChange((current) => {
      const existing = current.cart.items.find((cartItem) => cartItem.menuItemId === item.id);
      return {
        ...current,
        cart: {
          restaurantId: item.restaurantId,
          items: existing
            ? current.cart.items.map((cartItem) => cartItem.menuItemId === item.id ? { ...cartItem, quantity: cartItem.quantity + 1 } : cartItem)
            : [...current.cart.items, { menuItemId: item.id, quantity: 1 }],
        },
      };
    });
    setNotice({ type: "success", message: `${item.name} added to your cart.` });
  }

  const cartCount = data.cart.items.reduce((sum, item) => sum + item.quantity, 0);
  const greetingName = data.profile.name.trim().split(/\s+/)[0] || "there";

  return <div className="customer-page-content">
    <section className="customer-welcome-card">
      <div className="customer-welcome-copy">
        <p className="customer-eyebrow">GOOD FOOD, GOOD DAY</p>
        <h2>What sounds good,<br /><span>{greetingName}?</span></h2>
        <p>Find something delicious from the neighborhood spots you love.</p>
        <label className="customer-hero-search"><span aria-hidden="true">⌕</span><input value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Search dishes or restaurants" aria-label="Search dishes or restaurants" /><button type="button" onClick={() => document.querySelector(".customer-browse-section")?.scrollIntoView({ behavior: "smooth" })}>Explore</button></label>
      </div>
      <div className="customer-welcome-art" aria-hidden="true"><span>🍔</span><i>✦</i><b>🥤</b></div>
      <div className="customer-floating-note"><span>★</span><strong>Local favorites</strong><small>made with care</small></div>
    </section>
    <CustomerNotice notice={notice} onDismiss={() => setNotice(null)} />
    {cartCount > 0 && <button className="customer-cart-banner" type="button" onClick={() => onNavigate("CART")}><span>🛍</span><strong>{cartCount} item{cartCount === 1 ? "" : "s"} in your cart</strong><span>View cart →</span></button>}
    <section className="customer-page-content">
      <CustomerSectionHeading eyebrow="NEIGHBORHOOD FAVORITES" title="Restaurants near you" description="A few good places to start your next meal." action={<button className="customer-text-link" type="button" onClick={() => onNavigate("RESTAURANTS")}>See all restaurants →</button>} />
      <div className="customer-restaurant-grid customer-browse-section">
        {restaurants.slice(0, 3).map((restaurant) => <CustomerRestaurantCard
          key={restaurant.id}
          restaurant={restaurant}
          favorite={data.favorites.restaurants.includes(restaurant.id)}
          onFavorite={() => toggleFavorite("restaurants", restaurant.id)}
          onOpen={() => onNavigate(`RESTAURANT/${restaurant.id}`)}
        />)}
      </div>
    </section>
    <section className="customer-page-content">
      <CustomerSectionHeading eyebrow="A LITTLE INSPIRATION" title="Popular picks" description="Tasty favorites from the menus around you." />
      <div className="customer-food-grid">{popularItems.slice(0, 4).map((item) => <CustomerFoodCard
        key={item.id}
        item={item}
        favorite={data.favorites.foods.includes(item.id)}
        onFavorite={() => toggleFavorite("foods", item.id)}
        onAdd={() => addToCart(item)}
      />)}</div>
    </section>
    <section className="customer-promo-card"><span aria-hidden="true">✦</span><div><strong>Good things are growing nearby.</strong><p>Explore a new place and find your next favorite.</p></div><button className="button button-outline" type="button" onClick={() => onNavigate("RESTAURANTS")}>Browse local spots</button></section>
  </div>;
}

export function CustomerRestaurants({ data, onChange, onNavigate }) {
  const [query, setQuery] = useState("");
  const [cuisine, setCuisine] = useState("All");
  const restaurants = useMemo(() => CUSTOMER_RESTAURANTS.filter((restaurant) => {
    const matchesText = `${restaurant.name} ${restaurant.cuisine} ${restaurant.description}`.toLowerCase().includes(query.trim().toLowerCase());
    return matchesText && (cuisine === "All" || restaurant.cuisine.includes(cuisine));
  }), [cuisine, query]);

  function toggleFavorite(id) {
    onChange((current) => ({
      ...current,
      favorites: {
        ...current.favorites,
        restaurants: current.favorites.restaurants.includes(id)
          ? current.favorites.restaurants.filter((item) => item !== id)
          : [...current.favorites.restaurants, id],
      },
    }));
  }

  return <div className="customer-page-content">
    <CustomerSectionHeading eyebrow="FIND YOUR NEXT FAVORITE" title="Restaurants" description="Browse local kitchens and see what's on the menu." />
    <div className="customer-listing-hero"><span aria-hidden="true">🥡</span><div><strong>Something delicious is close by.</strong><p>Explore sample neighborhood favorites. Availability and delivery times are illustrative.</p></div></div>
    <div className="customer-filter-panel">
      <label className="customer-search-field"><span aria-hidden="true">⌕</span><input type="search" value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Restaurant, dish, or cuisine" aria-label="Search restaurants" /></label>
      <label className="customer-filter-select"><span className="sr-only">Filter cuisine</span><select value={cuisine} onChange={(event) => setCuisine(event.target.value)}>{CUISINES.map((item) => <option key={item}>{item}</option>)}</select></label>
    </div>
    <div className="customer-restaurant-grid">
      {restaurants.map((restaurant) => <CustomerRestaurantCard
        key={restaurant.id}
        restaurant={restaurant}
        favorite={data.favorites.restaurants.includes(restaurant.id)}
        onFavorite={() => toggleFavorite(restaurant.id)}
        onOpen={() => onNavigate(`RESTAURANT/${restaurant.id}`)}
      />)}
    </div>
    {!restaurants.length && <div className="customer-empty-state"><span aria-hidden="true">⌕</span><strong>No restaurants found</strong><p>Try a different search or cuisine filter.</p></div>}
  </div>;
}
