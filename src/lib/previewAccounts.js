import { ACCOUNT_STATUSES } from "../data/constants.js";

const STORAGE_KEY = "dashbite.previewAccounts";
export const PREVIEW_ACCOUNTS_CHANGED_EVENT = "dashbite:preview-accounts-changed";
const SIGNUP_ROLES = ["CUSTOMER", "RESTAURANT", "RIDER"];
const APPROVAL_ROLES = ["RESTAURANT", "RIDER"];

function readAccounts() {
  const saved = window.localStorage.getItem(STORAGE_KEY);
  if (!saved) return [];

  let accounts;
  try {
    accounts = JSON.parse(saved);
  } catch {
    throw new Error("Saved preview accounts could not be read. Clear this site's local storage and try again.");
  }
  if (!Array.isArray(accounts)) {
    throw new Error("Saved preview accounts have an invalid format. Clear this site's local storage and try again.");
  }
  return accounts;
}

function normalizeEmail(email) {
  return email.trim().toLowerCase();
}

function notifyAccountsChanged() {
  window.dispatchEvent(new Event(PREVIEW_ACCOUNTS_CHANGED_EVENT));
}

export function subscribeToPreviewAccounts(listener) {
  const handleStorage = (event) => {
    if (event.key === STORAGE_KEY || event.key === null) listener();
  };
  window.addEventListener(PREVIEW_ACCOUNTS_CHANGED_EVENT, listener);
  window.addEventListener("storage", handleStorage);
  return () => {
    window.removeEventListener(PREVIEW_ACCOUNTS_CHANGED_EVENT, listener);
    window.removeEventListener("storage", handleStorage);
  };
}

export function createPreviewAccount({ role, name, email, phone = "", restaurantName = "", restaurantAddress = "" }) {
  if (!SIGNUP_ROLES.includes(role) || !name.trim() || !email.trim()) {
    throw new Error("The preview account details are incomplete.");
  }
  if (role === "RESTAURANT" && (!restaurantName.trim() || !restaurantAddress.trim())) {
    throw new Error("Restaurant name and address are required.");
  }

  const accounts = readAccounts();
  const normalizedEmail = normalizeEmail(email);
  if (accounts.some((account) => typeof account?.email === "string" && normalizeEmail(account.email) === normalizedEmail)) {
    throw new Error("A preview account already exists for this email. Sign in or use another email address.");
  }

  const account = {
    name: name.trim(),
    email: normalizedEmail,
    phone: phone.trim(),
    role,
    account_status: role === "CUSTOMER" ? "APPROVED" : "PENDING_APPROVAL",
    ...(role === "RESTAURANT" ? {
      restaurant_name: restaurantName.trim(),
      restaurant_address: restaurantAddress.trim(),
    } : {}),
  };
  window.localStorage.setItem(STORAGE_KEY, JSON.stringify([...accounts, account]));
  notifyAccountsChanged();
  return account;
}

export function listPreviewAccounts() {
  const accounts = readAccounts().filter((account) => APPROVAL_ROLES.includes(account?.role));
  if (accounts.some((account) => (
    typeof account.name !== "string"
    || typeof account.email !== "string"
    || !ACCOUNT_STATUSES.includes(account.account_status)
  ))) {
    throw new Error("A restaurant or rider application has invalid account data. Clear this site's local storage and try again.");
  }
  return accounts;
}

export function approvePreviewAccount(email) {
  const normalizedEmail = normalizeEmail(email);
  const accounts = readAccounts();
  const accountIndex = accounts.findIndex((item) => typeof item?.email === "string" && normalizeEmail(item.email) === normalizedEmail);
  if (accountIndex < 0) {
    throw new Error("This preview account could not be found. Refresh the approval queue and try again.");
  }

  const account = accounts[accountIndex];
  if (!APPROVAL_ROLES.includes(account.role)) {
    throw new Error("Only restaurant and rider accounts can be approved here.");
  }
  if (!ACCOUNT_STATUSES.includes(account.account_status)) {
    throw new Error("This preview account has an invalid status. Clear this site's local storage and try again.");
  }
  if (account.account_status !== "PENDING_APPROVAL") {
    throw new Error("This account is no longer waiting for approval. Refresh the approval queue.");
  }

  const approvedAccount = { ...account, account_status: "APPROVED" };
  const updatedAccounts = [...accounts];
  updatedAccounts[accountIndex] = approvedAccount;
  window.localStorage.setItem(STORAGE_KEY, JSON.stringify(updatedAccounts));
  notifyAccountsChanged();
  return approvedAccount;
}

export function findPreviewAccount(email) {
  const normalizedEmail = normalizeEmail(email);
  const account = readAccounts().find((item) => typeof item?.email === "string" && normalizeEmail(item.email) === normalizedEmail);
  if (!account) return null;

  if (!SIGNUP_ROLES.includes(account.role) || !ACCOUNT_STATUSES.includes(account.account_status)) {
    throw new Error("This preview account has invalid role or status data. Sign up again with a different email.");
  }
  return account;
}
