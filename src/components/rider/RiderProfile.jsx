import { useEffect, useState } from "react";
import { RIDER_VEHICLES } from "../../lib/riderPortal.js";
import { RiderNotice, RiderSectionHeading } from "./RiderUI.jsx";

export default function RiderProfile({ data, onChange, readOnly = false }) {
  const [draft, setDraft] = useState({
    vehicleType: data.profile.vehicleType,
    vehiclePlate: data.profile.vehiclePlate,
  });
  const [notice, setNotice] = useState(null);

  useEffect(() => {
    setDraft({ vehicleType: data.profile.vehicleType, vehiclePlate: data.profile.vehiclePlate });
  }, [data.profile.vehicleType, data.profile.vehiclePlate]);

  function submit(event) {
    event.preventDefault();
    if (!RIDER_VEHICLES.includes(draft.vehicleType)) {
      setNotice({ type: "error", message: "Choose a vehicle type from the list." });
      return;
    }
    onChange((current) => ({
      ...current,
      profile: { ...current.profile, vehicleType: draft.vehicleType, vehiclePlate: draft.vehiclePlate.trim().toUpperCase() },
    }));
    setNotice({ type: "success", message: "Vehicle details saved in this browser preview." });
  }

  return <div className="rider-page-content">
    <RiderSectionHeading eyebrow="YOUR RIDER ACCOUNT" title="Profile & vehicle" description="Keep your rider and delivery vehicle details up to date." />
    <section className="rider-profile-hero">
      <div className="rider-profile-avatar">{(data.profile.name.trim()[0] || "R").toUpperCase()}</div>
      <div><p>RIDER PROFILE</p><h3>{data.profile.name || "DashBite Rider"}</h3><span>{data.profile.email || "No sign-in email"}</span></div>
      <span className={`rider-profile-online${data.isOnline ? " is-online" : ""}`}><i />{data.isOnline ? "Online" : "Offline"}</span>
    </section>
    <RiderNotice notice={notice} onDismiss={() => setNotice(null)} />
    <section className="rider-panel rider-profile-panel">
      <div className="rider-panel-title"><div><h3>Vehicle information</h3><p>Used to identify your delivery vehicle in the preview.</p></div><span className="rider-profile-vehicle-icon">🛵</span></div>
      <form className="rider-profile-form" onSubmit={submit}>
        <fieldset disabled={readOnly}>
          <label className="rider-field">Vehicle type<select value={draft.vehicleType} onChange={(event) => setDraft((current) => ({ ...current, vehicleType: event.target.value }))}>
            {RIDER_VEHICLES.map((vehicle) => <option value={vehicle} key={vehicle}>{vehicle}</option>)}
          </select></label>
          <label className="rider-field">Plate number <span className="rider-field-optional">Optional</span><input value={draft.vehiclePlate} onChange={(event) => setDraft((current) => ({ ...current, vehiclePlate: event.target.value }))} maxLength={20} autoCapitalize="characters" placeholder="ABC-1234" /><small>Leave blank if your vehicle does not have a plate.</small></label>
        </fieldset>
        <div className="rider-profile-form-footer"><p className="rider-inline-note">Changes are stored only in this browser. Sign-in details cannot be edited here.</p><button className="button button-primary" type="submit" disabled={readOnly}>Save vehicle details</button></div>
      </form>
    </section>
    <section className="rider-panel rider-account-panel">
      <div className="rider-panel-title"><div><h3>Account information</h3><p>Details associated with your preview sign-up.</p></div></div>
      <dl className="rider-account-details">
        <div><dt>Name</dt><dd>{data.profile.name || "Not set"}</dd></div>
        <div><dt>Email</dt><dd>{data.profile.email || "Not set"}</dd></div>
        <div><dt>Phone</dt><dd>{data.profile.phone || "Not set"}</dd></div>
      </dl>
    </section>
    <p className="rider-inline-note">This frontend preview does not update a rider account service or upload profile images.</p>
  </div>;
}
