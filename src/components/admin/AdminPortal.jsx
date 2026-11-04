import { useEffect, useState } from "react";
import logoUrl from "../../../assets/logo.svg";
import { clearAdminMonitoring, clearPreviewSession, getPreviewSession, startAdminMonitoring } from "../../lib/previewSession.js";
import { loadAdminPortalData, saveAdminPortalData } from "../../lib/adminPortal.js";
import { AdminDashboard, AdminAccounts, AdminOrderDetails, AdminOrders, AdminReports, AdminReviews, AdminSettings } from "./AdminScreens.jsx";

const PAGES = [
  { id: "DASHBOARD", label: "Overview", icon: "⌂" },
  { id: "ACCOUNTS", label: "Accounts", icon: "♙" },
  { id: "ORDERS", label: "Orders", icon: "▤" },
  { id: "REPORTS", label: "Reports", icon: "↗" },
  { id: "REVIEWS", label: "Reviews", icon: "★" },
  { id: "SETTINGS", label: "Settings", icon: "⚙" },
];

const MONITOR_ROLES = [
  { id: "CUSTOMER", label: "Customer", icon: "⌂" },
  { id: "RESTAURANT", label: "Restaurant", icon: "▤" },
  { id: "RIDER", label: "Rider", icon: "➤" },
];

function readPage() {
  const parts = window.location.hash.replace(/^#\/ADMIN\/?/i, "").split("/").filter(Boolean);
  const page = (parts[0] || "DASHBOARD").toUpperCase();
  return { page, id: parts[1] || "" };
}

function Brand() {
  return <a className="brand admin-brand" href="#/" aria-label="DashBite home">
    <img src={logoUrl} alt="" width="38" height="38" />
    <span>dash<span className="brand-accent">bite</span></span>
  </a>;
}

export default function AdminPortal() {
  const session = getPreviewSession("ADMIN");
  const [route, setRoute] = useState(readPage);
  const [portalState, setPortalState] = useState(() => {
    try {
      return { data: loadAdminPortalData(), error: "" };
    } catch (error) {
      return { data: null, error: error instanceof Error ? error.message : "Could not load Admin preview settings." };
    }
  });

  useEffect(() => {
    const updateRoute = () => setRoute(readPage());
    window.addEventListener("hashchange", updateRoute);
    return () => window.removeEventListener("hashchange", updateRoute);
  }, []);

  function navigate(path) {
    window.location.hash = `#/ADMIN/${path}`;
  }

  function updateData(updater) {
    if (!portalState.data) return;
    try {
      const next = updater(portalState.data);
      saveAdminPortalData(next);
      setPortalState({ data: next, error: "" });
    } catch (error) {
      setPortalState((current) => ({ ...current, error: error instanceof Error ? error.message : "Could not save Admin preview settings." }));
    }
  }

  function monitorRole(role) {
    startAdminMonitoring();
    window.location.hash = `#/${role}`;
  }

  function logOut() {
    clearPreviewSession();
    clearAdminMonitoring();
    window.location.hash = "#/";
  }

  const routePage = route.page === "ORDER" ? "ORDERS" : route.page;
  const activePage = PAGES.find((item) => item.id === routePage) || PAGES[0];
  const data = portalState.data;
  let content = null;
  if (data) {
    if (route.page === "DASHBOARD") content = <AdminDashboard data={data} onNavigate={navigate} />;
    else if (route.page === "ACCOUNTS") content = <AdminAccounts />;
    else if (routePage === "ORDERS") content = route.id ? <AdminOrderDetails orderId={route.id} onNavigate={navigate} /> : <AdminOrders onNavigate={navigate} />;
    else if (route.page === "REPORTS") content = <AdminReports />;
    else if (route.page === "REVIEWS") content = <AdminReviews data={data} onChange={updateData} />;
    else if (route.page === "SETTINGS") content = <AdminSettings data={data} onChange={updateData} />;
  }

  return <div className="restaurant-shell admin-shell">
    <aside className="restaurant-sidebar admin-sidebar">
      <Brand />
      <div className="restaurant-sidebar-identity">
        <div className="restaurant-profile-avatar">A</div>
        <div><strong>DashBite Admin</strong><span>Operations workspace</span></div>
      </div>
      <p className="restaurant-nav-label">ADMIN PORTAL</p>
      <nav className="restaurant-nav admin-nav" aria-label="Admin portal">
        {PAGES.map((item) => <a className={`restaurant-nav-link${activePage.id === item.id ? " is-active" : ""}`} href={`#/ADMIN/${item.id}`} aria-current={activePage.id === item.id ? "page" : undefined} key={item.id}>
          <span aria-hidden="true">{item.icon}</span>{item.label}
        </a>)}
      </nav>
      <p className="admin-monitor-label">WORKSPACE MONITORING</p>
      <nav className="restaurant-nav admin-monitor-nav" aria-label="Monitor role previews">
        {MONITOR_ROLES.map((role) => <a className="restaurant-nav-link" href={`#/${role.id}`} onClick={() => startAdminMonitoring()} key={role.id}><span aria-hidden="true">{role.icon}</span>{role.label}<b aria-hidden="true">↗</b></a>)}
      </nav>
      <div className="restaurant-sidebar-bottom"><a className="restaurant-workspace-link" href="#/"><span aria-hidden="true">⌂</span>DashBite home<b aria-hidden="true">↗</b></a><span>DashBite · Core v1.0</span></div>
    </aside>
    <main className="restaurant-main admin-main">
      <header className="restaurant-topbar">
        <div className="restaurant-topbar-title"><span>DashBite Admin</span><b aria-hidden="true">/</b><strong>{route.page === "ORDER" ? "Order details" : activePage.label}</strong></div>
        <div className="restaurant-topbar-actions"><span className="admin-preview-pill"><i />Preview mode</span><span className="restaurant-account-chip"><i>A</i><span>{session?.email || "Admin preview"}</span></span><button className="restaurant-logout" type="button" onClick={logOut}>Log out</button></div>
      </header>
      <div className="restaurant-main-inner admin-main-inner">
        <div className="admin-preview-banner"><span aria-hidden="true">i</span><p><strong>Frontend-only Admin preview.</strong> The credentials are demo-only. Accounts, approvals, reviews, settings, and reports are browser-local sample data; no live platform is managed.</p></div>
        {portalState.error && <div className="admin-notice admin-notice-error" role="alert">{portalState.error}</div>}
        {content}
        <footer className="restaurant-footer"><span>© 2026 DashBite</span><span>Admin portal · Preview data</span></footer>
      </div>
    </main>
  </div>;
}
