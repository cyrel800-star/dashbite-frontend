import { useEffect, useState } from "react";
import { RestaurantNotice, RestaurantSectionHeading } from "./RestaurantUI.jsx";

export default function RestaurantProfile({ data, onChange, readOnly }) {
  const [form, setForm] = useState(data.profile);
  const [notice, setNotice] = useState(null);

  useEffect(() => {
    setForm(data.profile);
  }, [data.profile]);

  function saveProfile(event) {
    event.preventDefault();
    if (!form.name.trim() || !form.address.trim()) {
      setNotice({ type: "error", message: "Restaurant name and address are required." });
      return;
    }
    onChange((current) => ({ ...current, profile: { ...form, name: form.name.trim(), address: form.address.trim(), phone: form.phone.trim(), cuisine: form.cuisine.trim(), description: form.description.trim() } }));
    setNotice({ type: "success", message: "Restaurant profile saved in this browser preview." });
  }

  return <div className="restaurant-page-content">
    <RestaurantSectionHeading eyebrow="YOUR STOREFRONT" title="Restaurant profile" description="Keep the details customers see clear and welcoming." />
    <section className="restaurant-profile-hero">
      <div className="restaurant-profile-cover"><span>🍜</span><div><small>RESTAURANT PREVIEW</small><strong>{form.name || "Your restaurant"}</strong></div></div>
      <div className="restaurant-profile-summary">
        <div className="restaurant-profile-avatar">{(form.name || "R").trim().charAt(0).toUpperCase()}</div>
        <div><strong>{form.name || "Your restaurant"}</strong><span>{form.cuisine || "Cuisine not specified"} · {data.isOpen ? "Open now" : "Closed"}</span></div>
      </div>
    </section>
    <RestaurantNotice notice={notice} onDismiss={() => setNotice(null)} />
    <form className="restaurant-panel restaurant-form-grid" onSubmit={saveProfile}>
      <div className="restaurant-form-heading"><h3>Business details</h3><p>This information is stored locally for the preview.</p></div>
      <label className="restaurant-field">Restaurant name<input required maxLength={160} disabled={readOnly} value={form.name} onChange={(event) => setForm({ ...form, name: event.target.value })} /></label>
      <label className="restaurant-field">Cuisine<input maxLength={80} disabled={readOnly} value={form.cuisine} onChange={(event) => setForm({ ...form, cuisine: event.target.value })} placeholder="e.g. Filipino, Japanese" /></label>
      <label className="restaurant-field restaurant-field-wide">Address<input required maxLength={300} disabled={readOnly} value={form.address} onChange={(event) => setForm({ ...form, address: event.target.value })} /></label>
      <label className="restaurant-field">Contact phone<input type="tel" maxLength={32} disabled={readOnly} value={form.phone} onChange={(event) => setForm({ ...form, phone: event.target.value })} placeholder="Add a contact number" /></label>
      <label className="restaurant-field">Account email<input type="email" disabled value={form.email} /></label>
      <label className="restaurant-field restaurant-field-wide">About your restaurant<textarea rows="4" maxLength={500} disabled={readOnly} value={form.description} onChange={(event) => setForm({ ...form, description: event.target.value })} placeholder="Tell customers what makes your restaurant special." /></label>
      {!readOnly && <div className="restaurant-form-actions"><button className="button button-primary" type="submit">Save profile</button><button className="button button-outline" type="button" onClick={() => { setForm(data.profile); setNotice(null); }}>Reset</button></div>}
      {readOnly && <p className="restaurant-local-note restaurant-field-wide">Profile is read-only while Admin is monitoring this dashboard.</p>}
    </form>
  </div>;
}
