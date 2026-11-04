import { useState } from "react";
import { RestaurantNotice, RestaurantSectionHeading } from "./RestaurantUI.jsx";

export default function RestaurantHours({ data, onChange, readOnly }) {
  const [hours, setHours] = useState(() => data.hours.map((day) => ({ ...day })));
  const [notice, setNotice] = useState(null);

  function updateDay(index, changes) {
    setHours((current) => current.map((day, dayIndex) => dayIndex === index ? { ...day, ...changes } : day));
  }

  function saveHours(event) {
    event.preventDefault();
    const invalidSchedule = hours.find((day) => day.isOpen && (!day.openTime || !day.closeTime || day.openTime >= day.closeTime));
    if (invalidSchedule) {
      setNotice({ type: "error", message: `Check the opening and closing times for ${invalidSchedule.day}. Closing time must be later than opening time.` });
      return;
    }
    onChange((current) => ({ ...current, hours: hours.map((day) => ({ ...day })) }));
    setNotice({ type: "success", message: "Your preview operating hours have been saved." });
  }

  function copyMonday() {
    const monday = hours[0];
    setHours((current) => current.map((day) => ({ ...day, isOpen: monday.isOpen, openTime: monday.openTime, closeTime: monday.closeTime })));
  }

  return <div className="restaurant-page-content">
    <RestaurantSectionHeading
      eyebrow="PLAN YOUR WEEK"
      title="Operating hours"
      description="Set the times customers can expect your kitchen to be open."
      action={!readOnly && <button className="button button-outline" type="button" onClick={copyMonday}>Copy Monday to all days</button>}
    />
    <section className="restaurant-info-callout"><span aria-hidden="true">◷</span><p><strong>Keep your hours accurate.</strong> These preview hours help organize your storefront schedule. No automatic open/close service is connected.</p></section>
    <RestaurantNotice notice={notice} onDismiss={() => setNotice(null)} />
    <form className="restaurant-panel restaurant-hours-form" onSubmit={saveHours}>
      <div className="restaurant-hours-list">{hours.map((day, index) => <div className={`restaurant-hours-row${day.isOpen ? "" : " is-closed"}`} key={day.day}>
        <div className="restaurant-hours-day"><strong>{day.day}</strong><span>{day.isOpen ? "Open" : "Closed"}</span></div>
        <label className="restaurant-switch"><input type="checkbox" checked={day.isOpen} disabled={readOnly} onChange={(event) => updateDay(index, { isOpen: event.target.checked })} /><span aria-hidden="true" /></label>
        <div className="restaurant-hours-times">
          <label className="restaurant-field"><span>Opens</span><input type="time" value={day.openTime} disabled={readOnly || !day.isOpen} onChange={(event) => updateDay(index, { openTime: event.target.value })} /></label>
          <span className="restaurant-hours-separator" aria-hidden="true">to</span>
          <label className="restaurant-field"><span>Closes</span><input type="time" value={day.closeTime} disabled={readOnly || !day.isOpen} onChange={(event) => updateDay(index, { closeTime: event.target.value })} /></label>
        </div>
      </div>)}</div>
      {!readOnly && <div className="restaurant-form-actions"><button className="button button-primary" type="submit">Save operating hours</button><button className="button button-outline" type="button" onClick={() => setHours(data.hours.map((day) => ({ ...day })))}>Discard changes</button></div>}
      {readOnly && <p className="restaurant-local-note">Hours are shown in read-only Admin monitoring mode.</p>}
    </form>
  </div>;
}
