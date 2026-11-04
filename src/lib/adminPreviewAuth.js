import { getPreviewSession, startPreviewSession } from "./previewSession.js";

export const ADMIN_PREVIEW_CREDENTIALS = Object.freeze({
  email: "admin@dashbite.local",
  password: "DashBiteAdmin!2026",
});

export function signInAdminPreview(email, password) {
  if (
    typeof email !== "string"
    || typeof password !== "string"
    || email.trim().toLowerCase() !== ADMIN_PREVIEW_CREDENTIALS.email
    || password !== ADMIN_PREVIEW_CREDENTIALS.password
  ) {
    throw new Error("The admin email or password is incorrect.");
  }
  startPreviewSession({
    role: "ADMIN",
    status: "APPROVED",
    email: ADMIN_PREVIEW_CREDENTIALS.email,
  });
}

export function hasAdminPreviewSession() {
  const session = getPreviewSession("ADMIN");
  return Boolean(session && session.status === "APPROVED" && session.email === ADMIN_PREVIEW_CREDENTIALS.email);
}
