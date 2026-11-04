import { OFFER_STATUSES, ORDER_STATUSES } from "../data/constants.js";

const STORAGE_PREFIX = "dashbite.riderPortal.v1:";
const OFFER_STATUS_SET = new Set(OFFER_STATUSES);
const ORDER_STATUS_SET = new Set(ORDER_STATUSES);
export const RIDER_VEHICLES = Object.freeze(["Motorcycle", "Bicycle", "Car", "Other"]);

function daysAgo(days, hour = 12) {
  const date = new Date();
  date.setDate(date.getDate() - days);
  date.setHours(hour, 0, 0, 0);
  return date.toISOString();
}

function createDefaultData(account = {}) {
  account = account || {};
  const activeOrderId = "RDR-2048";
  const completedOrderId = "RDR-2046";
  return {
    profile: {
      name: typeof account.name === "string" ? account.name : "DashBite Rider",
      email: typeof account.email === "string" ? account.email : "",
      phone: typeof account.phone === "string" ? account.phone : "",
      vehicleType: "Motorcycle",
      vehiclePlate: "",
    },
    isOnline: true,
    activeOrderId,
    orders: [
      {
        id: activeOrderId,
        restaurant: "Miso & Co.",
        customer: "Mia Santos",
        pickupAddress: "24 Market Street, Downtown",
        deliveryAddress: "18 Market Street, Downtown",
        status: "PICKED_UP",
        paymentMethod: "COD",
        paymentStatus: "UNPAID",
        deliveryFee: 55,
        totalAmount: 420,
        rating: null,
        createdAt: daysAgo(0, 11),
        completedAt: null,
      },
      {
        id: completedOrderId,
        restaurant: "The Green Fork",
        customer: "Noah Reyes",
        pickupAddress: "8 Garden Lane, Midtown",
        deliveryAddress: "63 Elm Avenue, Midtown",
        status: "DELIVERED",
        paymentMethod: "ONLINE",
        paymentStatus: "SETTLED_TO_RESTAURANT",
        deliveryFee: 62,
        totalAmount: 365,
        rating: 5,
        createdAt: daysAgo(1, 13),
        completedAt: daysAgo(1, 14),
      },
      {
        id: "RDR-2041",
        restaurant: "Pasta Corner",
        customer: "Ava Cruz",
        pickupAddress: "12 Olive Street, Central",
        deliveryAddress: "9 Pine Road, Central",
        status: "DELIVERED",
        paymentMethod: "COD",
        paymentStatus: "COLLECTED_BY_RIDER",
        deliveryFee: 48,
        totalAmount: 510,
        rating: 5,
        createdAt: daysAgo(3, 18),
        completedAt: daysAgo(3, 19),
      },
    ],
    offers: [
      {
        id: "OFFER-2052",
        orderId: "RDR-2052",
        restaurant: "The Green Fork",
        customer: "Sam Rivera",
        pickupAddress: "8 Garden Lane, Midtown",
        deliveryAddress: "27 Cedar Street, Midtown",
        distanceKm: 1.2,
        deliveryFee: 58,
        totalAmount: 385,
        status: "BLASTED",
        createdAt: daysAgo(0, 12),
      },
      {
        id: "OFFER-2053",
        orderId: "RDR-2053",
        restaurant: "Pasta Corner",
        customer: "Jordan Lee",
        pickupAddress: "12 Olive Street, Central",
        deliveryAddress: "41 Lakeview Drive, Central",
        distanceKm: 2.1,
        deliveryFee: 64,
        totalAmount: 495,
        status: "BLASTED",
        createdAt: daysAgo(0, 12),
      },
    ],
    chats: {
      [activeOrderId]: [
        { id: "rider-message-1", sender: "customer", text: "Hi! Please call when you arrive at the front entrance.", createdAt: daysAgo(0, 11) },
        { id: "rider-message-2", sender: "rider", text: "Sure, I’ll let you know when I’m nearby.", createdAt: daysAgo(0, 11) },
      ],
      [completedOrderId]: [
        { id: "rider-message-3", sender: "rider", text: "I’ve arrived with your order. Thank you!", createdAt: daysAgo(1, 14) },
      ],
    },
  };
}

function getStorageKey(email) {
  const normalizedEmail = typeof email === "string" ? email.trim().toLowerCase() : "";
  return `${STORAGE_PREFIX}${normalizedEmail || "admin-preview"}`;
}

function isValidDate(value) {
  return typeof value === "string" && !Number.isNaN(new Date(value).getTime());
}

function isValidData(data) {
  const orderIds = new Set(Array.isArray(data?.orders) ? data.orders.map((order) => order?.id) : []);
  return Boolean(
    data
    && typeof data === "object"
    && data.profile
    && ["name", "email", "phone", "vehiclePlate"].every((key) => typeof data.profile[key] === "string")
    && (data.profile.vehicleType === "" || RIDER_VEHICLES.includes(data.profile.vehicleType))
    && typeof data.isOnline === "boolean"
    && (data.activeOrderId === null || orderIds.has(data.activeOrderId))
    && Array.isArray(data.orders)
    && data.orders.every((order) => (
      order
      && typeof order.id === "string"
      && typeof order.restaurant === "string"
      && typeof order.customer === "string"
      && typeof order.pickupAddress === "string"
      && typeof order.deliveryAddress === "string"
      && ORDER_STATUS_SET.has(order.status)
      && Number.isFinite(order.deliveryFee)
      && Number.isFinite(order.totalAmount)
      && (order.rating === null || (Number.isInteger(order.rating) && order.rating >= 1 && order.rating <= 5))
      && isValidDate(order.createdAt)
      && (order.completedAt === null || isValidDate(order.completedAt))
    ))
    && Array.isArray(data.offers)
    && data.offers.every((offer) => (
      offer
      && typeof offer.id === "string"
      && typeof offer.orderId === "string"
      && typeof offer.restaurant === "string"
      && typeof offer.customer === "string"
      && typeof offer.pickupAddress === "string"
      && typeof offer.deliveryAddress === "string"
      && Number.isFinite(offer.distanceKm)
      && Number.isFinite(offer.deliveryFee)
      && Number.isFinite(offer.totalAmount)
      && OFFER_STATUS_SET.has(offer.status)
      && isValidDate(offer.createdAt)
    ))
    && data.chats
    && typeof data.chats === "object"
    && Object.entries(data.chats).every(([orderId, messages]) => (
      orderIds.has(orderId)
      && Array.isArray(messages)
      && messages.every((message) => (
        typeof message.id === "string"
        && ["customer", "rider"].includes(message.sender)
        && typeof message.text === "string"
        && isValidDate(message.createdAt)
      ))
    ))
  );
}

export function loadRiderPortal(email, account) {
  const saved = window.localStorage.getItem(getStorageKey(email));
  if (!saved) {
    const data = createDefaultData(account);
    window.localStorage.setItem(getStorageKey(email), JSON.stringify(data));
    return data;
  }
  let data;
  try {
    data = JSON.parse(saved);
  } catch {
    throw new Error("Saved rider preview data could not be read. Clear this site's local storage and try again.");
  }
  if (!isValidData(data)) {
    throw new Error("Saved rider preview data has an invalid format. Clear this site's local storage and try again.");
  }
  return data;
}

export function saveRiderPortal(email, data) {
  if (!isValidData(data)) {
    throw new Error("The rider preview changes are incomplete or invalid.");
  }
  window.localStorage.setItem(getStorageKey(email), JSON.stringify(data));
  return data;
}

export function acceptRiderOffer(data, offerId) {
  if (!data.isOnline) throw new Error("Go online before accepting a delivery offer.");
  if (data.activeOrderId) throw new Error("Complete your active delivery before accepting another offer.");
  const offer = data.offers.find((item) => item.id === offerId);
  if (!offer || offer.status !== "BLASTED") throw new Error("This offer is no longer available.");
  if (data.orders.some((order) => order.id === offer.orderId)) throw new Error("This offer already exists in your delivery list.");

  const order = {
    id: offer.orderId,
    restaurant: offer.restaurant,
    customer: offer.customer,
    pickupAddress: offer.pickupAddress,
    deliveryAddress: offer.deliveryAddress,
    status: "SEARCHING_RIDER",
    paymentMethod: "COD",
    paymentStatus: "UNPAID",
    deliveryFee: offer.deliveryFee,
    totalAmount: offer.totalAmount,
    rating: null,
    createdAt: new Date().toISOString(),
    completedAt: null,
  };
  return {
    ...data,
    activeOrderId: order.id,
    offers: data.offers.map((item) => item.id === offer.id ? { ...item, status: "ACCEPTED" } : item),
    orders: [order, ...data.orders],
    chats: { ...data.chats, [order.id]: [] },
  };
}
