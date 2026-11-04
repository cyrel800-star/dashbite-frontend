import { ORDER_STATUSES, PAYMENT_METHODS, PAYMENT_STATUSES } from "../data/constants.js";

const STORAGE_PREFIX = "dashbite.customerPortal.v1:";
const ORDER_STATUS_SET = new Set(ORDER_STATUSES);
const PAYMENT_STATUS_SET = new Set(PAYMENT_STATUSES);
const PAYMENT_METHOD_SET = new Set(PAYMENT_METHODS);

export const CUSTOMER_RESTAURANTS = Object.freeze([
  {
    id: "miso-and-co",
    name: "Miso & Co.",
    cuisine: "Japanese",
    rating: 4.9,
    deliveryMinutes: "20–30 min",
    deliveryFee: 2.5,
    minimum: 10,
    isOpen: true,
    description: "Comforting bowls, fresh ingredients, and neighborhood favorites made with care.",
    emoji: "🍜",
    accent: "orange",
    menu: [
      { id: "miso-ramen", name: "Miso Ramen", description: "Rich miso broth, noodles, greens, and a soft egg.", category: "Main dishes", price: 14.5, emoji: "🍜" },
      { id: "chicken-bowl", name: "Crispy Chicken Bowl", description: "Crispy chicken, rice, pickled vegetables, and house sauce.", category: "Main dishes", price: 12.75, emoji: "🍗" },
      { id: "gyoza", name: "Gyoza (6 pcs)", description: "Pan-seared dumplings served with dipping sauce.", category: "Sides", price: 7.25, emoji: "🥟" },
      { id: "yuzu-tea", name: "Yuzu Iced Tea", description: "Bright citrus tea, served chilled.", category: "Drinks", price: 3.5, emoji: "🥤" },
    ],
  },
  {
    id: "green-fork",
    name: "The Green Fork",
    cuisine: "Healthy · Salads",
    rating: 4.8,
    deliveryMinutes: "25–35 min",
    deliveryFee: 1.75,
    minimum: 8,
    isOpen: true,
    description: "Fresh, colorful plates made with seasonal produce and feel-good ingredients.",
    emoji: "🥗",
    accent: "green",
    menu: [
      { id: "harvest-bowl", name: "Harvest Grain Bowl", description: "Warm grains, roasted vegetables, and lemon tahini.", category: "Bowls", price: 13.25, emoji: "🥗" },
      { id: "avocado-toast", name: "Garden Avocado Toast", description: "Sourdough, smashed avocado, herbs, and chili flakes.", category: "Mains", price: 9.5, emoji: "🥑" },
      { id: "berry-smoothie", name: "Berry Smoothie", description: "Strawberries, blueberries, banana, and oat milk.", category: "Drinks", price: 5.75, emoji: "🫐" },
    ],
  },
  {
    id: "pasta-corner",
    name: "Pasta Corner",
    cuisine: "Italian · Pasta",
    rating: 4.7,
    deliveryMinutes: "30–40 min",
    deliveryFee: 2,
    minimum: 12,
    isOpen: true,
    description: "A little taste of Italy, with slow-simmered sauces and handmade comfort.",
    emoji: "🍝",
    accent: "dark",
    menu: [
      { id: "tomato-basil-pasta", name: "Tomato & Basil Pasta", description: "Penne, slow-cooked tomato sauce, basil, and parmesan.", category: "Pasta", price: 15.5, emoji: "🍝" },
      { id: "mushroom-risotto", name: "Creamy Mushroom Risotto", description: "Arborio rice, wild mushrooms, herbs, and parmesan.", category: "Mains", price: 16.25, emoji: "🍄" },
      { id: "tiramisu", name: "Classic Tiramisu", description: "Espresso-soaked layers with mascarpone cream.", category: "Desserts", price: 6.5, emoji: "🍰" },
    ],
  },
]);

const RESTAURANT_IDS = new Set(CUSTOMER_RESTAURANTS.map((restaurant) => restaurant.id));
const MENU_ITEM_IDS = new Set(CUSTOMER_RESTAURANTS.flatMap((restaurant) => restaurant.menu.map((item) => item.id)));

function dateAt(daysAgo, hour = 12) {
  const date = new Date();
  date.setDate(date.getDate() - daysAgo);
  date.setHours(hour, 0, 0, 0);
  return date.toISOString();
}

function createDefaultData(account = {}) {
  return {
    profile: {
      name: typeof account.name === "string" ? account.name : "DashBite Guest",
      email: typeof account.email === "string" ? account.email : "",
      phone: typeof account.phone === "string" ? account.phone : "",
      address: "",
    },
    favorites: {
      restaurants: ["miso-and-co"],
      foods: ["gyoza", "harvest-bowl"],
    },
    cart: { restaurantId: null, items: [] },
    orders: [
      {
        id: "DB-2048",
        restaurantId: "miso-and-co",
        restaurantName: "Miso & Co.",
        items: [{ menuItemId: "miso-ramen", name: "Miso Ramen", quantity: 1, price: 14.5 }],
        subtotal: 14.5,
        deliveryFee: 2.5,
        total: 17,
        paymentMethod: "COD",
        status: "SEARCHING_RIDER",
        paymentStatus: "UNPAID",
        createdAt: dateAt(0, 11),
        address: "18 Market Street, Downtown",
        eta: "12:35 PM",
      },
      {
        id: "DB-2046",
        restaurantId: "green-fork",
        restaurantName: "The Green Fork",
        items: [{ menuItemId: "harvest-bowl", name: "Harvest Grain Bowl", quantity: 1, price: 13.25 }],
        subtotal: 13.25,
        deliveryFee: 1.75,
        total: 15,
        paymentMethod: "ONLINE",
        status: "DELIVERED",
        paymentStatus: "SETTLED_TO_RESTAURANT",
        createdAt: dateAt(2, 13),
        address: "18 Market Street, Downtown",
        eta: null,
      },
    ],
    chats: {
      "DB-2048": [
        { id: "message-1", sender: "restaurant", text: "Hi Alex! We’re preparing your Miso Ramen now.", createdAt: dateAt(0, 11) },
        { id: "message-2", sender: "customer", text: "Thank you! Please leave it at the front desk.", createdAt: dateAt(0, 11) },
      ],
    },
    reviews: [
      { id: "review-seed-1", restaurantId: "miso-and-co", customerName: "Mia S.", rating: 5, title: "A new favorite", body: "The ramen broth was rich and comforting. Everything arrived warm and fresh.", createdAt: dateAt(4, 17) },
      { id: "review-seed-2", restaurantId: "miso-and-co", customerName: "Noah K.", rating: 4, title: "Great lunch", body: "The chicken bowl was delicious and the portions were generous.", createdAt: dateAt(8, 13) },
      { id: "review-seed-3", restaurantId: "green-fork", customerName: "Sam R.", rating: 5, title: "Fresh and filling", body: "Loved the bright flavors and fresh ingredients.", createdAt: dateAt(2, 14) },
    ],
  };
}

function getStorageKey(email) {
  const normalizedEmail = typeof email === "string" ? email.trim().toLowerCase() : "";
  return `${STORAGE_PREFIX}${normalizedEmail || "admin-preview"}`;
}

function validDate(value) {
  return typeof value === "string" && !Number.isNaN(new Date(value).getTime());
}

function isValidData(data) {
  return Boolean(
    data
    && typeof data === "object"
    && data.profile
    && ["name", "email", "phone", "address"].every((key) => typeof data.profile[key] === "string")
    && data.favorites
    && Array.isArray(data.favorites.restaurants)
    && data.favorites.restaurants.every((id) => RESTAURANT_IDS.has(id))
    && Array.isArray(data.favorites.foods)
    && data.favorites.foods.every((id) => MENU_ITEM_IDS.has(id))
    && data.cart
    && (data.cart.restaurantId === null || RESTAURANT_IDS.has(data.cart.restaurantId))
    && Array.isArray(data.cart.items)
    && data.cart.items.every((item) => MENU_ITEM_IDS.has(item.menuItemId) && Number.isInteger(item.quantity) && item.quantity > 0)
    && Array.isArray(data.orders)
    && data.orders.every((order) => (
      order
      && typeof order.id === "string"
      && RESTAURANT_IDS.has(order.restaurantId)
      && typeof order.restaurantName === "string"
      && Array.isArray(order.items)
      && order.items.every((item) => MENU_ITEM_IDS.has(item.menuItemId) && Number.isInteger(item.quantity) && item.quantity > 0 && Number.isFinite(item.price))
      && Number.isFinite(order.subtotal)
      && Number.isFinite(order.deliveryFee)
      && Number.isFinite(order.total)
      && PAYMENT_METHOD_SET.has(order.paymentMethod)
      && ORDER_STATUS_SET.has(order.status)
      && PAYMENT_STATUS_SET.has(order.paymentStatus)
      && validDate(order.createdAt)
      && typeof order.address === "string"
    ))
    && data.chats
    && typeof data.chats === "object"
    && Object.values(data.chats).every((messages) => Array.isArray(messages) && messages.every((message) => (
      typeof message.id === "string"
      && ["customer", "restaurant"].includes(message.sender)
      && typeof message.text === "string"
      && validDate(message.createdAt)
    )))
    && Array.isArray(data.reviews)
    && data.reviews.every((review) => (
      typeof review.id === "string"
      && RESTAURANT_IDS.has(review.restaurantId)
      && typeof review.customerName === "string"
      && Number.isInteger(review.rating)
      && review.rating >= 1
      && review.rating <= 5
      && typeof review.title === "string"
      && typeof review.body === "string"
      && validDate(review.createdAt)
    ))
  );
}

export function loadCustomerPortal(email, account) {
  const key = getStorageKey(email);
  const saved = window.localStorage.getItem(key);
  if (!saved) {
    const data = createDefaultData(account);
    window.localStorage.setItem(key, JSON.stringify(data));
    return data;
  }
  let data;
  try {
    data = JSON.parse(saved);
  } catch {
    throw new Error("Saved customer preview data could not be read. Clear this site's local storage and try again.");
  }
  if (!isValidData(data)) {
    throw new Error("Saved customer preview data has an invalid format. Clear this site's local storage and try again.");
  }
  return data;
}

export function saveCustomerPortal(email, data) {
  if (!isValidData(data)) {
    throw new Error("The customer preview changes are incomplete or invalid.");
  }
  window.localStorage.setItem(getStorageKey(email), JSON.stringify(data));
  window.dispatchEvent(new CustomEvent("dashbite:customer-portal-updated", { detail: { email } }));
  return data;
}

export function findCustomerRestaurant(id) {
  return CUSTOMER_RESTAURANTS.find((restaurant) => restaurant.id === id) || null;
}

export function findCustomerMenuItem(id) {
  for (const restaurant of CUSTOMER_RESTAURANTS) {
    const item = restaurant.menu.find((menuItem) => menuItem.id === id);
    if (item) return { ...item, restaurantId: restaurant.id, restaurantName: restaurant.name };
  }
  return null;
}

export function createCustomerOrder(data, paymentMethod = "COD") {
  if (!data.cart.restaurantId || data.cart.items.length === 0) {
    throw new Error("Add at least one menu item before placing an order.");
  }
  if (!data.profile.address.trim()) {
    throw new Error("Add a delivery address to your profile before placing an order.");
  }
  if (!PAYMENT_METHOD_SET.has(paymentMethod)) {
    throw new Error("Choose a supported payment method.");
  }
  const restaurant = findCustomerRestaurant(data.cart.restaurantId);
  if (!restaurant || !restaurant.isOpen) {
    throw new Error("This restaurant is not accepting orders right now.");
  }
  const items = data.cart.items.map((cartItem) => {
    const menuItem = restaurant.menu.find((item) => item.id === cartItem.menuItemId);
    if (!menuItem) throw new Error("A menu item in your cart is no longer available. Review your cart and try again.");
    return { menuItemId: menuItem.id, name: menuItem.name, quantity: cartItem.quantity, price: menuItem.price };
  });
  const subtotal = items.reduce((sum, item) => sum + item.price * item.quantity, 0);
  if (subtotal < restaurant.minimum) throw new Error(`The minimum order for ${restaurant.name} is ₱${restaurant.minimum.toFixed(2)}.`);
  const order = {
    id: `DB-${Date.now().toString().slice(-6)}`,
    restaurantId: restaurant.id,
    restaurantName: restaurant.name,
    items,
    subtotal: Number(subtotal.toFixed(2)),
    deliveryFee: restaurant.deliveryFee,
    total: Number((subtotal + restaurant.deliveryFee).toFixed(2)),
    paymentMethod,
    status: "PENDING",
    paymentStatus: "UNPAID",
    createdAt: new Date().toISOString(),
    address: data.profile.address.trim(),
    eta: null,
  };
  return {
    ...data,
    orders: [order, ...data.orders],
    chats: { ...data.chats, [order.id]: [] },
    cart: { restaurantId: null, items: [] },
  };
}
