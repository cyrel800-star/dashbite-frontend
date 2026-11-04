import { useState } from "react";
import { ORDER_STATUSES, PAYMENT_METHODS } from "../data/constants.js";
import { api, formatError } from "../lib/api.js";

const uuidPattern = /^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;

function Notice({ notice }) {
  if (!notice) return null;
  return <div className={`notice notice-${notice.type} visible`} role={notice.type === "error" ? "alert" : "status"}>{notice.message}</div>;
}

function isUuid(value) {
  return uuidPattern.test(value);
}

export function CustomerOrderForm({ disabled = false, disabledMessage = "Your account is pending admin approval. Operational actions are blocked until your account is approved." }) {
  const [busy, setBusy] = useState(false);
  const [notice, setNotice] = useState(null);
  const [subtotal, setSubtotal] = useState("");
  const [deliveryFee, setDeliveryFee] = useState("");
  const total = Number(subtotal || 0) + Number(deliveryFee || 0);

  async function handleSubmit(event) {
    event.preventDefault();
    setNotice(null);
    const form = event.currentTarget;
    const values = new FormData(form);
    const customerId = String(values.get("customer_id")).trim();
    const restaurantId = String(values.get("restaurant_id")).trim();
    const foodSubtotal = Number(values.get("food_subtotal"));
    const fee = Number(values.get("delivery_fee"));

    if (!isUuid(customerId) || !isUuid(restaurantId)) {
      setNotice({ type: "error", message: "Customer ID and restaurant ID must be valid UUIDs." });
      return;
    }
    if (!Number.isFinite(foodSubtotal) || foodSubtotal <= 0 || !Number.isFinite(fee) || fee < 0) {
      setNotice({ type: "error", message: "Enter a food subtotal greater than 0 and a delivery fee of 0 or more." });
      return;
    }

    setBusy(true);
    try {
      const result = await api.createOrder({
        customer_id: customerId,
        restaurant_id: restaurantId,
        delivery_address: String(values.get("delivery_address")).trim(),
        food_subtotal: foodSubtotal,
        delivery_fee: fee,
        total_amount: Number((foodSubtotal + fee).toFixed(2)),
        payment_method: String(values.get("payment_method")),
      });
      if (!result || typeof result.order_id !== "string" || !ORDER_STATUSES.includes(result.status)) {
        throw new Error("The server response must include an order_id and a v1.0 order status.");
      }
      setNotice({ type: "success", message: `Order ${result.order_id} created with status ${result.status}.` });
      form.reset();
      setSubtotal("");
      setDeliveryFee("");
    } catch (error) {
      setNotice({ type: "error", message: formatError(error) });
    } finally {
      setBusy(false);
    }
  }

  return (
    <section className="panel">
      <div className="panel-heading"><h2>Create an order</h2><small>POST /api/orders</small></div>
      <form id="create-order-form" onSubmit={handleSubmit}>
        {disabled && <p className="permission-note">{disabledMessage}</p>}
        <fieldset className="form-fieldset" disabled={disabled}>
          <div className="form-grid">
            <div className="field"><label htmlFor="customer-id">Customer ID (UUID)</label><input id="customer-id" name="customer_id" autoComplete="off" required placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" /></div>
            <div className="field"><label htmlFor="restaurant-id">Restaurant ID (UUID)</label><input id="restaurant-id" name="restaurant_id" autoComplete="off" required placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" /></div>
            <div className="field field-full"><label htmlFor="delivery-address">Delivery address</label><input id="delivery-address" name="delivery_address" required maxLength={500} placeholder="Street address and delivery details" /></div>
            <div className="field"><label htmlFor="food-subtotal">Food subtotal</label><input id="food-subtotal" name="food_subtotal" type="number" min="0.01" step="0.01" required placeholder="0.00" value={subtotal} onChange={(event) => setSubtotal(event.target.value)} /></div>
            <div className="field"><label htmlFor="delivery-fee">Delivery fee</label><input id="delivery-fee" name="delivery_fee" type="number" min="0" step="0.01" required placeholder="0.00" value={deliveryFee} onChange={(event) => setDeliveryFee(event.target.value)} /></div>
            <div className="field"><label htmlFor="payment-method">Payment method</label><select id="payment-method" name="payment_method" required>{PAYMENT_METHODS.map((method) => <option key={method} value={method}>{method}</option>)}</select></div>
            <div className="field"><label htmlFor="order-total">Total amount</label><input id="order-total" name="total_amount" type="number" readOnly value={Number.isFinite(total) ? total.toFixed(2) : ""} aria-describedby="total-help" /></div>
          </div>
          <p className="form-note" id="total-help">The total is calculated as food subtotal + delivery fee. Customer and restaurant IDs must be valid UUIDs.</p>
          <div className="form-actions"><button className="button button-primary button-small" type="submit" disabled={busy}>{busy ? "Placing order..." : "Place order"}</button></div>
        </fieldset>
        <Notice notice={notice} />
      </form>
    </section>
  );
}

export function RiderOfferForm({ disabled = false, disabledMessage = "Your account is pending admin approval. Operational actions are blocked until your account is approved." }) {
  const [busy, setBusy] = useState(false);
  const [notice, setNotice] = useState(null);

  async function handleSubmit(event) {
    event.preventDefault();
    setNotice(null);
    const values = new FormData(event.currentTarget);
    const orderId = String(values.get("order_id")).trim();
    const riderId = String(values.get("rider_id")).trim();
    if (!isUuid(orderId) || !isUuid(riderId)) {
      setNotice({ type: "error", message: "Order ID and rider ID must be valid UUIDs." });
      return;
    }

    setBusy(true);
    try {
      const result = await api.acceptOffer({ order_id: orderId, rider_id: riderId });
      if (!result || result.status !== "ASSIGNED") {
        throw new Error('The server response must include the v1.0 success status "ASSIGNED".');
      }
      setNotice({ type: "success", message: result.message || `Offer response: ${result.status}.` });
    } catch (error) {
      setNotice({ type: "error", message: formatError(error) });
    } finally {
      setBusy(false);
    }
  }

  return (
    <section className="panel">
      <div className="panel-heading"><h2>Claim an offer</h2><small>POST /api/dispatch/accept-offer</small></div>
      <p className="form-note">The server atomically assigns an offer. If another rider claims it first, the API returns OFFER_ALREADY_CLAIMED.</p>
      {disabled && <p className="permission-note">{disabledMessage}</p>}
      <form id="accept-offer-form" onSubmit={handleSubmit}>
        <fieldset className="form-fieldset" disabled={disabled}>
          <div className="form-grid">
            <div className="field"><label htmlFor="offer-order-id">Order ID (UUID)</label><input id="offer-order-id" name="order_id" required autoComplete="off" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" /></div>
            <div className="field"><label htmlFor="offer-rider-id">Rider ID (UUID)</label><input id="offer-rider-id" name="rider_id" required autoComplete="off" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" /></div>
          </div>
          <div className="form-actions"><button className="button button-primary button-small" type="submit" disabled={busy}>{busy ? "Claiming offer..." : "Accept offer"}</button></div>
        </fieldset>
        <Notice notice={notice} />
      </form>
    </section>
  );
}
