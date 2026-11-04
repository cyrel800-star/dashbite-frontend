import { useEffect, useState } from "react";
import { findCustomerMenuItem, findCustomerRestaurant } from "../../lib/customerPortal.js";
import { CustomerEmptyState, CustomerRestaurantCard, CustomerSectionHeading, CustomerFoodCard } from "./CustomerUI.jsx";

export default function CustomerSaved({ data, onChange, onNavigate }) {
  const restaurants = data.favorites.restaurants.map(findCustomerRestaurant).filter(Boolean);
  const foods = data.favorites.foods.map((id) => {
    const item = findCustomerMenuItem(id);
    return item ? { item, restaurant: findCustomerRestaurant(item.restaurantId) } : null;
  }).filter(Boolean);

  function toggleFavorite(type, id) {
    onChange((current) => ({
      ...current,
      favorites: {
        ...current.favorites,
        [type]: current.favorites[type].filter((item) => item !== id),
      },
    }));
  }

  function addToCart(item, restaurant) {
    if (data.cart.restaurantId && data.cart.restaurantId !== restaurant.id && data.cart.items.length) {
      onNavigate("CART");
      return;
    }
    onChange((current) => {
      const existing = current.cart.items.find((entry) => entry.menuItemId === item.id);
      return {
        ...current,
        cart: {
          restaurantId: restaurant.id,
          items: existing
            ? current.cart.items.map((entry) => entry.menuItemId === item.id ? { ...entry, quantity: entry.quantity + 1 } : entry)
            : [...current.cart.items, { menuItemId: item.id, quantity: 1 }],
        },
      };
    });
  }

  return <div className="customer-page-content">
    <CustomerSectionHeading eyebrow="SAVED FOR LATER" title="Your favorites" description="The restaurants and dishes you want to come back to." />
    <section className="customer-page-content">
      <CustomerSectionHeading eyebrow="LOCAL SPOTS" title="Favorite restaurants" description="A quick way back to the menus you love." />
      {restaurants.length
        ? <div className="customer-restaurant-grid">{restaurants.map((restaurant) => <CustomerRestaurantCard
          key={restaurant.id}
          restaurant={restaurant}
          favorite
          onFavorite={() => toggleFavorite("restaurants", restaurant.id)}
          onOpen={() => onNavigate(`RESTAURANT/${restaurant.id}`)}
        />)}</div>
        : <section className="customer-panel"><CustomerEmptyState icon="♡" title="No favorite restaurants yet" description="Save a restaurant to find its menu here." action={<button className="button button-primary" type="button" onClick={() => onNavigate("RESTAURANTS")}>Explore restaurants</button>} /></section>}
    </section>
    <section className="customer-page-content">
      <CustomerSectionHeading eyebrow="TASTY PICKS" title="Favorite dishes" description="Saved menu items, ready for another order." />
      {foods.length
        ? <div className="customer-food-grid">{foods.map(({ item, restaurant }) => <CustomerFoodCard
          key={item.id}
          item={{ ...item, restaurantName: restaurant.name }}
          favorite
          onFavorite={() => toggleFavorite("foods", item.id)}
          onAdd={() => addToCart(item, restaurant)}
        />)}</div>
        : <section className="customer-panel"><CustomerEmptyState icon="♡" title="No favorite dishes yet" description="Tap the heart on a dish to save it here." action={<button className="button button-primary" type="button" onClick={() => onNavigate("RESTAURANTS")}>Browse menus</button>} /></section>}
    </section>
    <p className="customer-inline-note">Your favorites are saved in this browser preview and are not synced to an account service.</p>
  </div>;
}

export function CustomerProfile({ data, onChange }) {
  const [notice, setNotice] = useState(null);
  const [draft, setDraft] = useState(data.profile);

  useEffect(() => setDraft(data.profile), [data.profile]);

  function submit(event) {
    event.preventDefault();
    const name = draft.name.trim();
    const phone = draft.phone.trim();
    const address = draft.address.trim();
    if (!name) {
      setNotice({ type: "error", message: "Enter your name before saving." });
      return;
    }
    onChange((current) => ({ ...current, profile: { ...current.profile, name, phone, address } }));
    setNotice({ type: "success", message: "Profile details saved in this browser preview." });
  }

  return <div className="customer-page-content">
    <CustomerSectionHeading eyebrow="YOUR ACCOUNT" title="Profile" description="Manage the delivery details used in your preview orders." />
    <section className="customer-panel customer-profile-panel">
      <div className="customer-profile-intro"><span>{(data.profile.name.trim()[0] || "C").toUpperCase()}</span><div><strong>{data.profile.name || "DashBite Guest"}</strong><small>Customer account · preview</small></div></div>
      {notice && <div className={`customer-notice customer-notice-${notice.type}`} role={notice.type === "error" ? "alert" : "status"}>{notice.message}</div>}
      <form className="customer-profile-form" onSubmit={submit}>
        <label className="customer-field">Full name<input required value={draft.name} maxLength={100} onChange={(event) => setDraft((current) => ({ ...current, name: event.target.value }))} autoComplete="name" /></label>
        <label className="customer-field">Email address<input value={data.profile.email} readOnly aria-describedby="customer-email-note" autoComplete="email" /><small id="customer-email-note">Your sign-in email can't be changed in the preview.</small></label>
        <label className="customer-field">Phone number<input type="tel" value={draft.phone} maxLength={40} onChange={(event) => setDraft((current) => ({ ...current, phone: event.target.value }))} autoComplete="tel" placeholder="Add a contact number" /></label>
        <label className="customer-field customer-field-wide">Delivery address<textarea value={draft.address} maxLength={300} rows="3" onChange={(event) => setDraft((current) => ({ ...current, address: event.target.value }))} autoComplete="street-address" placeholder="Street address and delivery notes" /></label>
        <div className="customer-profile-actions"><p className="customer-inline-note">Profile changes are saved locally. No account or delivery service is contacted.</p><button className="button button-primary" type="submit">Save changes</button></div>
      </form>
    </section>
  </div>;
}
