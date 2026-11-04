import { ACCOUNT_STATUSES, ROLES } from "../data/constants.js";

const SESSION_KEYS = {
  role: "dashbite.previewRole",
  status: "dashbite.previewAccountStatus",
  email: "dashbite.previewEmail",
  adminMonitoring: "dashbite.adminMonitoring",
};

export function startPreviewSession({ role, status, email }) {
  if (!ROLES.includes(role) || !ACCOUNT_STATUSES.includes(status) || !email) {
    throw new Error("The preview account selection is invalid.");
  }
  window.sessionStorage.setItem(SESSION_KEYS.role, role);
  window.sessionStorage.setItem(SESSION_KEYS.status, status);
  window.sessionStorage.setItem(SESSION_KEYS.email, email);
}

export function getPreviewSession(role) {
  const savedRole = window.sessionStorage.getItem(SESSION_KEYS.role);
  const status = window.sessionStorage.getItem(SESSION_KEYS.status);
  const email = window.sessionStorage.getItem(SESSION_KEYS.email);
  if (savedRole !== role || !ACCOUNT_STATUSES.includes(status) || !email) return null;
  return { role: savedRole, status, email };
}

export function startAdminMonitoring() {
  window.sessionStorage.setItem(SESSION_KEYS.adminMonitoring, "true");
}

export function isAdminMonitoring() {
  return window.sessionStorage.getItem(SESSION_KEYS.adminMonitoring) === "true";
}

export function clearAdminMonitoring() {
  window.sessionStorage.removeItem(SESSION_KEYS.adminMonitoring);
}

export function clearPreviewSession() {
  Object.values(SESSION_KEYS).forEach((key) => window.sessionStorage.removeItem(key));
}
