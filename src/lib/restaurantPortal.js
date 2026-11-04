import { ORDER_STATUSES, PAYMENT_STATUSES } from "../data/constants.js";

const STORAGE_PREFIX = "dashbite.restaurantPortal.v1:";
const RESTAURANT_ORDER_STATUSES = new Set(ORDER_STATUSES);
const RESTAURANT_PAYMENT_STATUSES = new Set(PAYMENT_STATUSES);
const DAYS = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

function isValidDate(value) {
  return typeof value === "string" && !Number.isNaN(new Date(value).getTime());
}

function createDefaultData(account = {}) {
  const today = new Date();
  const dateAt = (daysAgo, hour = 12) => {
    const date = new Date(today);
    date.setDate(date.getDate() - daysAgo);
    date.setHours(hour, 0, 0, 0);
    return date.toISOString();
  };

  return {
    profile: {
      name: account.restaurant_name || "Miso & Co.",
      address: account.restaurant_address || "18 Market Street, Downtown",
      phone: account.phone || "",
      email: account.email || "",
      cuisine: "Japanese",
      description: "Comforting bowls, fresh ingredients, and neighborhood favorites made with care.",
    },
    isOpen: true,
    hours: DAYS.map((day) => ({
      day,
      isOpen: !["Sunday"].includes(day),
      openTime: "09:00",
      closeTime: "21:00",
    })),
    menu: [
      { id: "menu-1", name: "Miso Ramen", category: "Main dishes", price: 14.5, description: "Rich miso broth, noodles, greens, and a soft egg.", available: true },
      { id: "menu-2", name: "Crispy Chicken Bowl", category: "Main dishes", price: 12.75, description: "Crispy chicken, rice, pickled vegetables, and house sauce.", available: true },
      { id: "menu-3", name: "Gyoza (6 pcs)", category: "Sides", price: 7.25, description: "Pan-seared dumplings served with dipping sauce.", available: true },
      { id: "menu-4", name: "Yuzu Iced Tea", category: "Drinks", price: 3.5, description: "Bright citrus tea, served chilled.", available: false },
    ],
    orders: [
      { id: "ORD-2050", customer: "Jamie Rivera", items: ["2 × Miso Ramen", "1 × Yuzu Iced Tea"], total: 32.5, status: "PENDING", payment: "UNPAID", createdAt: dateAt(0, 12) },
      { id: "ORD-2048", customer: "Alex Morgan", items: ["1 × Crispy Chicken Bowl", "1 × Gyoza (6 pcs)"], total: 20, status: "ACCEPTED", payment: "UNPAID", createdAt: dateAt(0, 11) },
      { id: "ORD-2047", customer: "Taylor Chen", items: ["2 × Miso Ramen"], total: 29, status: "SEARCHING_RIDER", payment: "UNPAID", createdAt: dateAt(0, 10) },
      { id: "ORD-2046", customer: "Sam Lee", items: ["1 × Gyoza (6 pcs)", "1 × Yuzu Iced Tea"], total: 10.75, status: "DELIVERED", payment: "SETTLED_TO_RESTAURANT", createdAt: dateAt(1, 18) },
      { id: "ORD-2045", customer: "Jordan Park", items: ["1 × Crispy Chicken Bowl"], total: 12.75, status: "DELIVERED", payment: "SETTLED_TO_RESTAURANT", createdAt: dateAt(2, 13) },
    ],
    reviews: [
      { id: "review-1", customer: "Mia Santos", rating: 5, title: "A new favorite", body: "The ramen broth was rich and comforting. Everything arrived warm and fresh.", createdAt: dateAt(0, 9), reply: "" },
      { id: "review-2", customer: "Noah Kim", rating: 4, title: "Great lunch", body: "The chicken bowl was delicious and the portions were generous.", createdAt: dateAt(1, 14), reply: "Thank you for stopping by, Noah. We're glad you enjoyed your lunch!" },
      { id: "review-3", customer: "Avery Brown", rating: 5, title: "Lovely service", body: "Quick delivery and excellent food. The gyoza was perfect.", createdAt: dateAt(3, 17), reply: "" },
    ],
  };
}

function getStorageKey(email) {
  const normalizedEmail = typeof email === "string" ? email.trim().toLowerCase() : "";
  return `${STORAGE_PREFIX}${normalizedEmail || "admin-preview"}`;
}

function isValidData(data) {
  return Boolean(
    data
    && typeof data === "object"
    && data.profile
    && ["name", "address", "phone", "email", "cuisine", "description"].every((key) => typeof data.profile[key] === "string")
    && typeof data.isOpen === "boolean"
    && Array.isArray(data.hours)
    && data.hours.length === DAYS.length
    && data.hours.every((day, index) => (
      day
      && day.day === DAYS[index]
      && typeof day.isOpen === "boolean"
      && typeof day.openTime === "string"
      && typeof day.closeTime === "string"
    ))
    && Array.isArray(data.menu)
    && data.menu.every((item) => (
      item
      && typeof item.id === "string"
      && typeof item.name === "string"
      && typeof item.category === "string"
      && typeof item.description === "string"
      && Number.isFinite(item.price)
      && item.price > 0
      && typeof item.available === "boolean"
    ))
    && Array.isArray(data.orders)
    && data.orders.every((order) => (
      order
      && typeof order.id === "string"
      && typeof order.customer === "string"
      && Array.isArray(order.items)
      && order.items.every((item) => typeof item === "string")
      && Number.isFinite(order.total)
      && RESTAURANT_ORDER_STATUSES.has(order.status)
      && RESTAURANT_PAYMENT_STATUSES.has(order.payment)
      && isValidDate(order.createdAt)
    ))
    && Array.isArray(data.reviews)
    && data.reviews.every((review) => (
      review
      && typeof review.id === "string"
      && typeof review.customer === "string"
      && Number.isInteger(review.rating)
      && review.rating >= 1
      && review.rating <= 5
      && typeof review.title === "string"
      && typeof review.body === "string"
      && typeof review.reply === "string"
      && isValidDate(review.createdAt)
    ))
  );
}

export function loadRestaurantPortal(email, account) {
  const key = getStorageKey(email);
  const saved = window.localStorage.getItem(key);
  if (!saved) {
    const initialData = createDefaultData(account);
    window.localStorage.setItem(key, JSON.stringify(initialData));
    return initialData;
  }

  let data;
  try {
    data = JSON.parse(saved);
  } catch {
    throw new Error("Saved restaurant preview data could not be read. Clear this site's local storage and try again.");
  }
  if (!isValidData(data)) {
    throw new Error("Saved restaurant preview data has an invalid format. Clear this site's local storage and try again.");
  }
  return data;
}

export function saveRestaurantPortal(email, data) {
  if (!isValidData(data)) {
    throw new Error("The restaurant preview changes are incomplete or invalid.");
  }
  window.localStorage.setItem(getStorageKey(email), JSON.stringify(data));
  window.dispatchEvent(new CustomEvent("dashbite:restaurant-portal-updated", { detail: { email } }));
  return data;
}
