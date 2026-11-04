import { ORDER_STATUSES, PAYMENT_STATUSES } from "../data/constants.js";

const STORAGE_KEY = "dashbite.adminPortal.v1";
const ORDER_STATUS_SET = new Set(ORDER_STATUSES);
const PAYMENT_STATUS_SET = new Set(PAYMENT_STATUSES);

function dateAt(daysAgo, hour = 12) {
  const date = new Date();
  date.setDate(date.getDate() - daysAgo);
  date.setHours(hour, 0, 0, 0);
  return date.toISOString();
}

export const ADMIN_SAMPLE_ORDERS = Object.freeze([
  { id: "DB-3051", customer: "Mia Santos", restaurant: "Miso & Co.", rider: "Kai Rivera", foodCost: 420, deliveryFee: 55, commission: 42, total: 475, status: "SEARCHING_RIDER", paymentStatus: "UNPAID", paymentMethod: "COD", createdAt: dateAt(0, 12), address: "18 Market Street, Downtown", items: [{ name: "Miso Ramen", quantity: 2, price: 180 }, { name: "Yuzu Iced Tea", quantity: 1, price: 60 }] },
  { id: "DB-3050", customer: "Noah Reyes", restaurant: "The Green Fork", rider: "Alex Dela Cruz", foodCost: 365, deliveryFee: 62, commission: 36.5, total: 427, status: "PICKED_UP", paymentStatus: "COLLECTED_BY_RIDER", paymentMethod: "COD", createdAt: dateAt(0, 11), address: "63 Elm Avenue, Midtown", items: [{ name: "Harvest Grain Bowl", quantity: 1, price: 265 }, { name: "Berry Smoothie", quantity: 1, price: 100 }] },
  { id: "DB-3049", customer: "Ava Cruz", restaurant: "Pasta Corner", rider: "Sam Lee", foodCost: 510, deliveryFee: 48, commission: 51, total: 558, status: "DELIVERED", paymentStatus: "SETTLED_TO_RESTAURANT", paymentMethod: "ONLINE", createdAt: dateAt(1, 18), address: "9 Pine Road, Central", items: [{ name: "Tomato & Basil Pasta", quantity: 2, price: 255 }] },
  { id: "DB-3048", customer: "Jordan Lee", restaurant: "Miso & Co.", rider: "Kai Rivera", foodCost: 330, deliveryFee: 55, commission: 33, total: 385, status: "ACCEPTED", paymentStatus: "UNPAID", paymentMethod: "ONLINE", createdAt: dateAt(2, 13), address: "41 Lakeview Drive, Central", items: [{ name: "Crispy Chicken Bowl", quantity: 1, price: 255 }, { name: "Gyoza (6 pcs)", quantity: 1, price: 75 }] },
  { id: "DB-3047", customer: "Sam Rivera", restaurant: "The Green Fork", rider: "—", foodCost: 290, deliveryFee: 62, commission: 29, total: 352, status: "CANCELLED", paymentStatus: "UNPAID", paymentMethod: "COD", createdAt: dateAt(3, 10), address: "27 Cedar Street, Midtown", items: [{ name: "Garden Avocado Toast", quantity: 2, price: 145 }] },
  { id: "DB-3046", customer: "Mia Santos", restaurant: "Pasta Corner", rider: "Alex Dela Cruz", foodCost: 460, deliveryFee: 48, commission: 46, total: 508, status: "DELIVERED", paymentStatus: "FLAGGED_DISCREPANCY", paymentMethod: "COD", createdAt: dateAt(5, 14), address: "18 Market Street, Downtown", items: [{ name: "Creamy Mushroom Risotto", quantity: 2, price: 230 }] },
  { id: "DB-3045", customer: "Noah Reyes", restaurant: "Miso & Co.", rider: "Sam Lee", foodCost: 275, deliveryFee: 55, commission: 27.5, total: 330, status: "REJECTED", paymentStatus: "UNPAID", paymentMethod: "ONLINE", createdAt: dateAt(8, 16), address: "63 Elm Avenue, Midtown", items: [{ name: "Miso Ramen", quantity: 1, price: 275 }] },
  { id: "DB-3044", customer: "Ava Cruz", restaurant: "The Green Fork", rider: "Kai Rivera", foodCost: 390, deliveryFee: 62, commission: 39, total: 452, status: "DELIVERED", paymentStatus: "REMITTED_TO_ADMIN", paymentMethod: "COD", createdAt: dateAt(12, 12), address: "9 Pine Road, Central", items: [{ name: "Harvest Grain Bowl", quantity: 1, price: 250 }, { name: "Garden Avocado Toast", quantity: 1, price: 140 }] },
]);

export const ADMIN_SAMPLE_REVIEWS = Object.freeze([
  { id: "REV-081", customer: "Mia Santos", restaurant: "Miso & Co.", rating: 5, title: "Comfort in a bowl", body: "The broth was rich and the order arrived warm. I’ll definitely order again.", status: "published", reportCount: 0, createdAt: dateAt(0, 10) },
  { id: "REV-080", customer: "Noah Reyes", restaurant: "The Green Fork", rating: 2, title: "Order arrived late", body: "The food was good, but the delivery took longer than expected.", status: "flagged", reportCount: 2, createdAt: dateAt(1, 16) },
  { id: "REV-079", customer: "Ava Cruz", restaurant: "Pasta Corner", rating: 4, title: "Lovely pasta", body: "Great portion and a really nice sauce. The packaging was neat.", status: "published", reportCount: 0, createdAt: dateAt(2, 12) },
  { id: "REV-078", customer: "Jordan Lee", restaurant: "Miso & Co.", rating: 1, title: "Not what I ordered", body: "My order was incorrect and I could not reach the restaurant.", status: "hidden", reportCount: 1, createdAt: dateAt(4, 17) },
  { id: "REV-077", customer: "Sam Rivera", restaurant: "The Green Fork", rating: 5, title: "Fresh and filling", body: "Bright flavors, generous portions, and everything tasted fresh.", status: "published", reportCount: 0, createdAt: dateAt(7, 11) },
]);

export const ADMIN_SAMPLE_ACCOUNTS = Object.freeze([
  { name: "Mia Santos", email: "mia.santos@example.test", phone: "+63 917 555 0101", role: "CUSTOMER", account_status: "APPROVED" },
  { name: "Noah Reyes", email: "noah.reyes@example.test", phone: "+63 917 555 0102", role: "CUSTOMER", account_status: "APPROVED" },
  { name: "Ava Cruz", email: "ava.cruz@example.test", phone: "+63 917 555 0103", role: "CUSTOMER", account_status: "APPROVED" },
]);

function defaultData() {
  return {
    reviewStatuses: {},
    settings: {
      platformName: "DashBite",
      supportEmail: "support@dashbite.local",
      commissionPercent: "10",
      maintenanceMode: false,
      newSignupsEnabled: true,
    },
  };
}

function isValidData(data) {
  return Boolean(
    data
    && typeof data === "object"
    && data.reviewStatuses
    && typeof data.reviewStatuses === "object"
    && Object.entries(data.reviewStatuses).every(([id, status]) => (
      ADMIN_SAMPLE_REVIEWS.some((review) => review.id === id)
      && ["published", "hidden", "flagged"].includes(status)
    ))
    && data.settings
    && typeof data.settings.platformName === "string"
    && typeof data.settings.supportEmail === "string"
    && typeof data.settings.commissionPercent === "string"
    && Number.isFinite(Number(data.settings.commissionPercent))
    && Number(data.settings.commissionPercent) >= 0
    && Number(data.settings.commissionPercent) <= 100
    && typeof data.settings.maintenanceMode === "boolean"
    && typeof data.settings.newSignupsEnabled === "boolean"
  );
}

export function loadAdminPortalData() {
  const saved = window.localStorage.getItem(STORAGE_KEY);
  if (!saved) {
    const data = defaultData();
    window.localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    return data;
  }
  let data;
  try {
    data = JSON.parse(saved);
  } catch {
    throw new Error("Saved Admin preview settings could not be read. Clear this site's local storage and try again.");
  }
  if (!isValidData(data)) {
    throw new Error("Saved Admin preview settings have an invalid format. Clear this site's local storage and try again.");
  }
  return data;
}

export function saveAdminPortalData(data) {
  if (!isValidData(data)) throw new Error("The Admin preview settings are incomplete or invalid.");
  window.localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
  return data;
}

export function getAdminOrderStatus(order) {
  return ORDER_STATUS_SET.has(order.status) ? order.status : "PENDING";
}

export function getAdminPaymentStatus(order) {
  return PAYMENT_STATUS_SET.has(order.paymentStatus) ? order.paymentStatus : "UNPAID";
}
