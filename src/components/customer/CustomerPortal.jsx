import { useEffect, useState } from "react";
import logoUrl from "../../../assets/logo.svg";
import { clearPreviewSession, getPreviewSession, isAdminMonitoring } from "../../lib/previewSession.js";
import { findPreviewAccount } from "../../lib/previewAccounts.js";
import { loadCustomerPortal, saveCustomerPortal } from "../../lib/customerPortal.js";
import CustomerBrowse, { CustomerRestaurants } from "./CustomerBrowse.jsx";
import CustomerRestaurant from "./CustomerRestaurant.jsx";
import { CustomerChat, CustomerChatInbox, CustomerTrackOrder } from "./CustomerActivity.jsx";
import CustomerOrders, { CustomerCart } from "./CustomerOrders.jsx";
import CustomerSaved, { CustomerProfile } from "./CustomerSaved.jsx";
import { CustomerEmptyState, CustomerNotice } from "./CustomerUI.jsx";

const PAGES = [
  { id: "DASHBOARD", label: "Discover", icon: "⌂" },
  { id: "RESTAURANTS", label: "Restaurants", icon: "♧" },
  { id: "FAVORITES", label: "Favorites", icon: "♡" },
  { id: "CART", label: "Your cart", icon: "🛍" },
  { id: "ORDERS", label: "My orders", icon: "▤" },
  { id: "CHATS", label: "Messages", icon: "✉" },
  { id: "PROFILE", label: "Profile", icon: "◉" },
];

function readPage() {
  const parts = window.location.hash.replace(/^#\/CUSTOMER\/?/i, "").split("/").filter(Boolean);
  const page = (parts[0] || "DASHBOARD").toUpperCase();
  const allowed = PAGES.some((item) => item.id === page)
    || ["RESTAURANT", "TRACK", "CHAT"].includes(page);
  return allowed ? { page, id: parts[1] || "" } : { page: "DASHBOARD", id: "" };
}

function Brand() {
  return <a className="brand customer-brand" href="#/" aria-label="DashBite home">
    <img src={logoUrl} alt="" width="38" height="38" />
    <span>dash<span className="brand-accent">bite</span></span>
  </a>;
}

export default function CustomerPortal() {
  const session = getPreviewSession("CUSTOMER");
  const email = session?.email || "";
  const monitorMode = isAdminMonitoring();
  const [route, setRoute] = useState(readPage);
  const [portalState, setPortalState] = useState(() => {
    try {
      const account = email ? findPreviewAccount(email) : null;
      return { data: loadCustomerPortal(email, account), error: "" };
    } catch (error) {
      return { data: null, error: error instanceof Error ? error.message : "Could not load customer preview data." };
    }
  });

  useEffect(() => {
    const updateRoute = () => setRoute(readPage());
    window.addEventListener("hashchange", updateRoute);
    return () => window.removeEventListener("hashchange", updateRoute);
  }, []);

  function navigate(path) {
    window.location.hash = `#/CUSTOMER/${path}`;
  }

  function updateData(updater) {
    if (!portalState.data || monitorMode) return;
    try {
      const nextData = updater(portalState.data);
      saveCustomerPortal(email, nextData);
      setPortalState({ data: nextData, error: "" });
    } catch (error) {
      setPortalState((current) => ({ ...current, error: error instanceof Error ? error.message : "Could not save customer preview data." }));
    }
  }

  function logOut() {
    clearPreviewSession();
    window.location.hash = monitorMode ? "#/" : "#/LOGIN";
  }

  const activePage = ["RESTAURANT", "TRACK", "CHAT"].includes(route.page)
    ? route.page === "RESTAURANT" ? "RESTAURANTS" : route.page === "TRACK" ? "ORDERS" : "CHATS"
    : route.page;
  const page = PAGES.find((item) => item.id === activePage) || PAGES[0];
  const data = portalState.data;
  let content = null;
  if (data) {
    if (route.page === "DASHBOARD") content = <CustomerBrowse data={data} onChange={updateData} onNavigate={navigate} />;
    else if (route.page === "RESTAURANTS") content = <CustomerRestaurants data={data} onChange={updateData} onNavigate={navigate} />;
    else if (route.page === "RESTAURANT") content = <CustomerRestaurant restaurantId={route.id} data={data} onChange={updateData} onNavigate={navigate} />;
    else if (route.page === "FAVORITES") content = <CustomerSaved data={data} onChange={updateData} onNavigate={navigate} />;
    else if (route.page === "CART") content = <CustomerCart data={data} onChange={updateData} onNavigate={navigate} />;
    else if (route.page === "ORDERS") content = <CustomerOrders data={data} onNavigate={navigate} />;
    else if (route.page === "TRACK") content = <CustomerTrackOrder orderId={route.id} data={data} onChange={updateData} onNavigate={navigate} />;
    else if (route.page === "CHATS") content = <CustomerChatInbox data={data} onNavigate={navigate} />;
    else if (route.page === "CHAT") content = <CustomerChat orderId={route.id} data={data} onChange={updateData} onNavigate={navigate} />;
    else if (route.page === "PROFILE") content = <CustomerProfile data={data} onChange={updateData} />;
    else content = <CustomerEmptyState title="Page not found" description="Choose a section from the customer menu." />;
  }

  return <div className={`restaurant-shell customer-shell${monitorMode ? " is-read-only" : ""}`}>
    <aside className="restaurant-sidebar customer-sidebar">
      <Brand />
      <div className="restaurant-sidebar-identity">
        <div className="restaurant-profile-avatar">{(data?.profile.name || "C").trim().charAt(0).toUpperCase()}</div>
        <div><strong>{data?.profile.name || "Customer preview"}</strong><span>Customer account</span></div>
      </div>
      <p className="restaurant-nav-label">{monitorMode ? "ADMIN MONITORING" : "CUSTOMER PORTAL"}</p>
      <nav className="restaurant-nav customer-nav" aria-label="Customer portal">
        {PAGES.map((item) => <a
          className={`restaurant-nav-link${activePage === item.id ? " is-active" : ""}`}
          href={`#/CUSTOMER/${item.id}`}
          aria-current={activePage === item.id ? "page" : undefined}
          key={item.id}
        ><span aria-hidden="true">{item.icon}</span>{item.label}{item.id === "CART" && data?.cart.items.length > 0 && <i aria-label={`${data.cart.items.reduce((sum, entry) => sum + entry.quantity, 0)} items in cart`} />}</a>)}
      </nav>
      <div className="restaurant-sidebar-bottom">
        {monitorMode
          ? <a className="restaurant-workspace-link" href="#/ADMIN"><span aria-hidden="true">▦</span>Back to Admin<b aria-hidden="true">↗</b></a>
          : <a className="restaurant-workspace-link" href="#/"><span aria-hidden="true">⌂</span>DashBite home<b aria-hidden="true">↗</b></a>}
        <span>DashBite · Core v1.0</span>
      </div>
    </aside>
    <main className="restaurant-main customer-main">
      <header className="restaurant-topbar">
        <div className="restaurant-topbar-title"><span>Customer</span><b aria-hidden="true">/</b><strong>{page.label}</strong></div>
        <div className="restaurant-topbar-actions">
          {monitorMode && <span className="restaurant-monitor-pill">Read-only monitoring</span>}
          <span className="restaurant-account-chip"><i>{(data?.profile.name || "C").trim().charAt(0).toUpperCase()}</i><span>{email || "Customer preview"}</span></span>
          <button className="restaurant-logout" type="button" onClick={logOut}>{monitorMode ? "Back to Admin" : "Log out"}</button>
        </div>
      </header>
      <div className="restaurant-main-inner customer-main-inner">
        {monitorMode && <div className="restaurant-preview-banner"><span aria-hidden="true">i</span><p><strong>Admin monitoring view.</strong> Customer information is illustrative and read-only.</p></div>}
        {!monitorMode && <div className="restaurant-preview-banner"><span aria-hidden="true">i</span><p><strong>Frontend preview.</strong> Restaurants, messages, favorites, and orders are sample browser-local data; actions do not contact restaurants or payment services.</p></div>}
        <CustomerNotice notice={portalState.error ? { type: "error", message: portalState.error } : null} />
        {content}
        <footer className="restaurant-footer"><span>© 2026 DashBite</span><span>Customer portal · Preview data</span></footer>
      </div>
    </main>
  </div>;
}
