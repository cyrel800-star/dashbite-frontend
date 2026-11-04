import { useMemo, useState } from "react";
import { RestaurantEmptyState, RestaurantNotice, RestaurantPrice, RestaurantSectionHeading } from "./RestaurantUI.jsx";

const EMPTY_ITEM = { name: "", category: "Main dishes", price: "", description: "", available: true };
const CATEGORIES = ["Main dishes", "Sides", "Drinks", "Desserts"];

export default function RestaurantMenu({ data, onChange, readOnly }) {
  const [formOpen, setFormOpen] = useState(false);
  const [editingId, setEditingId] = useState(null);
  const [form, setForm] = useState(EMPTY_ITEM);
  const [query, setQuery] = useState("");
  const [category, setCategory] = useState("ALL");
  const [notice, setNotice] = useState(null);
  const items = useMemo(() => data.menu.filter((item) => {
    const matchesText = `${item.name} ${item.description}`.toLowerCase().includes(query.trim().toLowerCase());
    return matchesText && (category === "ALL" || item.category === category);
  }), [category, data.menu, query]);
  const availableCount = data.menu.filter((item) => item.available).length;

  function openNewItem() {
    setEditingId(null);
    setForm(EMPTY_ITEM);
    setNotice(null);
    setFormOpen(true);
  }

  function editItem(item) {
    setEditingId(item.id);
    setForm({ ...item, price: String(item.price) });
    setNotice(null);
    setFormOpen(true);
  }

  function handleSubmit(event) {
    event.preventDefault();
    const price = Number(form.price);
    if (!form.name.trim() || !Number.isFinite(price) || price <= 0) {
      setNotice({ type: "error", message: "Enter an item name and a price greater than zero." });
      return;
    }

    const item = { ...form, name: form.name.trim(), price: Number(price.toFixed(2)), description: form.description.trim() };
    if (editingId) {
      onChange((current) => ({ ...current, menu: current.menu.map((entry) => entry.id === editingId ? { ...item, id: editingId } : entry) }));
      setNotice({ type: "success", message: "Menu item updated." });
    } else {
      onChange((current) => ({ ...current, menu: [{ ...item, id: `menu-${Date.now()}` }, ...current.menu] }));
      setNotice({ type: "success", message: "Menu item added." });
    }
    setForm(EMPTY_ITEM);
    setEditingId(null);
    setFormOpen(false);
  }

  function toggleAvailability(itemId) {
    onChange((current) => ({
      ...current,
      menu: current.menu.map((item) => item.id === itemId ? { ...item, available: !item.available } : item),
    }));
  }

  function removeItem(item) {
    if (!window.confirm(`Remove "${item.name}" from the preview menu?`)) return;
    onChange((current) => ({ ...current, menu: current.menu.filter((entry) => entry.id !== item.id) }));
    setNotice({ type: "success", message: "Menu item removed." });
  }

  return <div className="restaurant-page-content">
    <RestaurantSectionHeading
      eyebrow="YOUR CATALOG"
      title="Menu"
      description="Keep your dishes, prices, and availability up to date."
      action={!readOnly && <button className="button button-primary" type="button" onClick={openNewItem}>＋ Add item</button>}
    />
    <div className="restaurant-metric-grid restaurant-menu-metrics">
      <article className="restaurant-mini-metric"><span>Total items</span><strong>{data.menu.length}</strong></article>
      <article className="restaurant-mini-metric"><span>Available</span><strong>{availableCount}</strong></article>
      <article className="restaurant-mini-metric"><span>Unavailable</span><strong>{data.menu.length - availableCount}</strong></article>
    </div>
    <RestaurantNotice notice={notice} onDismiss={() => setNotice(null)} />
    {formOpen && !readOnly && <section className="restaurant-panel restaurant-menu-form-panel">
      <div className="restaurant-panel-heading"><div><h3>{editingId ? "Edit menu item" : "Add a menu item"}</h3><p>Share the details customers need to choose.</p></div><button className="restaurant-icon-button" type="button" aria-label="Close item form" onClick={() => setFormOpen(false)}>×</button></div>
      <form className="restaurant-form-grid" onSubmit={handleSubmit}>
        <label className="restaurant-field">Item name<input required maxLength={100} value={form.name} onChange={(event) => setForm({ ...form, name: event.target.value })} placeholder="e.g. Miso Ramen" /></label>
        <label className="restaurant-field">Category<select value={form.category} onChange={(event) => setForm({ ...form, category: event.target.value })}>{CATEGORIES.map((item) => <option key={item}>{item}</option>)}</select></label>
        <label className="restaurant-field">Price (₱)<input type="number" min="0.01" step="0.01" required value={form.price} onChange={(event) => setForm({ ...form, price: event.target.value })} placeholder="0.00" /></label>
        <label className="restaurant-field restaurant-field-wide">Description<textarea rows="3" maxLength={300} value={form.description} onChange={(event) => setForm({ ...form, description: event.target.value })} placeholder="Describe the dish, ingredients, or portion." /></label>
        <label className="restaurant-check-field"><input type="checkbox" checked={form.available} onChange={(event) => setForm({ ...form, available: event.target.checked })} /> Available to order</label>
        <div className="restaurant-form-actions"><button className="button button-primary" type="submit">{editingId ? "Save changes" : "Add to menu"}</button><button className="button button-outline" type="button" onClick={() => { setFormOpen(false); setForm(EMPTY_ITEM); setEditingId(null); }}>Cancel</button></div>
      </form>
    </section>}
    <div className="restaurant-filter-panel">
      <label className="restaurant-search-field"><span aria-hidden="true">⌕</span><input type="search" value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Search menu" aria-label="Search menu items" /></label>
      <label className="restaurant-filter-select"><span className="sr-only">Filter menu category</span><select value={category} onChange={(event) => setCategory(event.target.value)}><option value="ALL">All categories</option>{CATEGORIES.map((item) => <option value={item} key={item}>{item}</option>)}</select></label>
    </div>
    {items.length ? <div className="restaurant-menu-grid">
      {items.map((item) => <article className="restaurant-menu-card" key={item.id}>
        <div className="restaurant-menu-art" aria-hidden="true"><span>{item.category === "Drinks" ? "🥤" : item.category === "Desserts" ? "🍰" : item.category === "Sides" ? "🥟" : "🍜"}</span><small>{item.category}</small></div>
        <div className="restaurant-menu-card-body">
          <div className="restaurant-menu-title"><h3>{item.name}</h3><strong><RestaurantPrice value={item.price} /></strong></div>
          <p>{item.description || "No description added yet."}</p>
          <div className="restaurant-menu-card-actions">
            <span className={`restaurant-availability${item.available ? " is-available" : ""}`}><i />{item.available ? "Available" : "Unavailable"}</span>
            {!readOnly && <div className="restaurant-row-actions">
              <button className="text-button" type="button" onClick={() => editItem(item)}>Edit</button>
              <button className="text-button" type="button" onClick={() => toggleAvailability(item.id)}>{item.available ? "Hide" : "Show"}</button>
              <button className="text-button restaurant-danger-link" type="button" onClick={() => removeItem(item)}>Remove</button>
            </div>}
          </div>
        </div>
      </article>)}
    </div> : <section className="restaurant-panel"><RestaurantEmptyState icon="🍽" title="No menu items found" description={data.menu.length ? "Try another search or category." : "Add a dish to get your menu started."} action={!readOnly && <button className="button button-primary" type="button" onClick={openNewItem}>＋ Add your first item</button>} /></section>}
    <p className="restaurant-local-note">Menu edits are saved in this browser preview and are not published to a live customer menu.</p>
  </div>;
}
