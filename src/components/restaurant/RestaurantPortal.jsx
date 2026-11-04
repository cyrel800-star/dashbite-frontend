import { useEffect, useState } from "react";
import logoUrl from "../../../assets/logo.svg";
import { clearPreviewSession, getPreviewSession, isAdminMonitoring } from "../../lib/previewSession.js";
import { findPreviewAccount } from "../../lib/previewAccounts.js";
import { loadRestaurantPortal, saveRestaurantPortal } from "../../lib/restaurantPortal.js";
import RestaurantAnalytics from "./RestaurantAnalytics.jsx";
import RestaurantDashboard from "./RestaurantDashboard.jsx";
import RestaurantHours from "./RestaurantHours.jsx";
import RestaurantMenu from "./RestaurantMenu.jsx";
import RestaurantOrders from "./RestaurantOrders.jsx";
import RestaurantProfile from "./RestaurantProfile.jsx";
import RestaurantReviews from "./RestaurantReviews.jsx";
import { RestaurantNotice } from "./RestaurantUI.jsx";

const PAGES = [
  { id: "DASHBOARD", label: "Overview", icon: "⌂" },
  { id: "ORDERS", label: "Orders", icon: "▤" },
  { id: "MENU", label: "Menu", icon: "☷" },
  { id: "HOURS", label: "Operating hours", icon: "◷" },
  { id: "ANALYTICS", label: "Analytics", icon: "↗" },
  { id: "REVIEWS", label: "Reviews", icon: "★" },
  { id: "PROFILE", label: "Profile", icon: "◉" },
];

function readPage() {
  const match = window.location.hash.match(/^#\/RESTAURANT(?:\/(DASHBOARD|ORDERS|MENU|HOURS|ANALYTICS|REVIEWS|PROFILE))?$/i);
  return match?.[1]?.toUpperCase() || "DASHBOARD";
}

function Brand() {
  return <a className="brand restaurant-brand" href="#/" aria-label="DashBite home">
    <img src={logoUrl} alt="" width="38" height="38" />
    <span>dash<span className="brand-accent">bite</span></span>
  </a>;
}

function WorkspaceLink({ href, icon, children }) {
  return <a className="restaurant-workspace-link" href={href}><span aria-hidden="true">{icon}</span>{children}<b aria-hidden="true">↗</b></a>;
}

export default function RestaurantPortal() {
  const session = getPreviewSession("RESTAURANT");
  const email = session?.email || "";
  const monitorMode = isAdminMonitoring();
  const [page, setPage] = useState(readPage);
  const [portalState, setPortalState] = useState(() => {
    try {
      const account = email ? findPreviewAccount(email) : null;
      return { data: loadRestaurantPortal(email, account), error: "" };
    } catch (error) {
      return { data: null, error: error instanceof Error ? error.message : "Could not load restaurant preview data." };
    }
  });

  useEffect(() => {
    const updatePage = () => setPage(readPage());
    window.addEventListener("hashchange", updatePage);
    return () => window.removeEventListener("hashchange", updatePage);
  }, []);

  function navigate(nextPage) {
    window.location.hash = `#/RESTAURANT/${nextPage}`;
  }

  function updateData(updater) {
    if (!portalState.data) return;
    try {
      const nextData = updater(portalState.data);
      saveRestaurantPortal(email, nextData);
      setPortalState({ data: nextData, error: "" });
    } catch (error) {
      setPortalState((current) => ({ ...current, error: error instanceof Error ? error.message : "Could not save restaurant preview data." }));
    }
  }

  function logOut() {
    clearPreviewSession();
    window.location.hash = monitorMode ? "#/" : "#/LOGIN";
  }

  const currentPage = PAGES.find((item) => item.id === page) || PAGES[0];
  const content = portalState.data && ({
    DASHBOARD: <RestaurantDashboard data={portalState.data} onChange={updateData} onNavigate={navigate} readOnly={monitorMode} />,
    ORDERS: <RestaurantOrders data={portalState.data} onChange={updateData} readOnly={monitorMode} />,
    MENU: <RestaurantMenu data={portalState.data} onChange={updateData} readOnly={monitorMode} />,
    HOURS: <RestaurantHours data={portalState.data} onChange={updateData} readOnly={monitorMode} />,
    ANALYTICS: <RestaurantAnalytics data={portalState.data} />,
    REVIEWS: <RestaurantReviews data={portalState.data} onChange={updateData} readOnly={monitorMode} />,
    PROFILE: <RestaurantProfile data={portalState.data} onChange={updateData} readOnly={monitorMode} />,
  })[page] || null;

  return <div className="restaurant-shell">
    <aside className="restaurant-sidebar">
      <Brand />
      <div className="restaurant-sidebar-identity">
        <div className="restaurant-profile-avatar">{(portalState.data?.profile.name || "R").trim().charAt(0).toUpperCase()}</div>
        <div><strong>{portalState.data?.profile.name || "Restaurant preview"}</strong><span>Restaurant workspace</span></div>
      </div>
      <p className="restaurant-nav-label">{monitorMode ? "ADMIN MONITORING" : "RESTAURANT PORTAL"}</p>
      <nav className="restaurant-nav" aria-label="Restaurant portal">
        {PAGES.map((item) => <a
          className={`restaurant-nav-link${page === item.id ? " is-active" : ""}`}
          href={`#/RESTAURANT/${item.id}`}
          aria-current={page === item.id ? "page" : undefined}
          key={item.id}
        ><span aria-hidden="true">{item.icon}</span>{item.label}{item.id === "ORDERS" && portalState.data?.orders.some((order) => order.status === "PENDING") && <i aria-label="New orders" />}</a>)}
      </nav>
      <div className="restaurant-sidebar-bottom">
        {monitorMode
          ? <WorkspaceLink href="#/ADMIN" icon="▦">Back to Admin</WorkspaceLink>
          : <WorkspaceLink href="#/" icon="⌂">DashBite home</WorkspaceLink>}
        <span>DashBite · Core v1.0</span>
      </div>
    </aside>
    <main className="restaurant-main">
      <header className="restaurant-topbar">
        <div className="restaurant-topbar-title"><span>Restaurant</span><b aria-hidden="true">/</b><strong>{currentPage.label}</strong></div>
        <div className="restaurant-topbar-actions">
          {monitorMode
            ? <span className="restaurant-monitor-pill">Read-only monitoring</span>
            : <span className={`restaurant-live-pill${portalState.data?.isOpen ? " is-open" : ""}`}><i />{portalState.data?.isOpen ? "Open" : "Closed"}</span>}
          <span className="restaurant-account-chip"><i>{(portalState.data?.profile.name || "R").trim().charAt(0).toUpperCase()}</i><span>{email || "Admin preview"}</span></span>
          <button className="restaurant-logout" type="button" onClick={logOut}>Log out</button>
        </div>
      </header>
      <div className="restaurant-main-inner">
        {monitorMode && <div className="demo-banner admin-monitor-banner"><strong>Admin monitoring view:</strong> restaurant data is illustrative and read-only.</div>}
        <div className="restaurant-page-title"><div><p>{currentPage.id === "DASHBOARD" ? "RESTAURANT WORKSPACE" : "MANAGE YOUR BUSINESS"}</p><h1>{currentPage.id === "DASHBOARD" ? "Welcome back" : currentPage.label}</h1></div><span className="restaurant-preview-label">LOCAL PREVIEW</span></div>
        <div className="restaurant-preview-banner"><span aria-hidden="true">i</span><p><strong>Frontend preview.</strong> Changes are saved in this browser only. Live orders, customer listings, and analytics require backend APIs.</p></div>
        <RestaurantNotice notice={portalState.error ? { type: "error", message: portalState.error } : null} />
        {content}
        <footer className="restaurant-footer"><span>© 2026 DashBite</span><span>Restaurant portal · Preview data</span></footer>
      </div>
    </main>
  </div>;
}
