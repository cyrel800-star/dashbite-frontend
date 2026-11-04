import { useEffect, useState } from "react";
import logoUrl from "../../../assets/logo.svg";
import { findPreviewAccount } from "../../lib/previewAccounts.js";
import { clearPreviewSession, getPreviewSession, isAdminMonitoring } from "../../lib/previewSession.js";
import { loadRiderPortal, saveRiderPortal } from "../../lib/riderPortal.js";
import RiderDashboard from "./RiderDashboard.jsx";
import RiderEarnings from "./RiderEarnings.jsx";
import RiderHistory from "./RiderHistory.jsx";
import { RiderChat, RiderChatInbox } from "./RiderChat.jsx";
import RiderProfile from "./RiderProfile.jsx";
import { RiderEmptyState, RiderNotice } from "./RiderUI.jsx";

const PAGES = [
  { id: "DASHBOARD", label: "Overview", icon: "⌂" },
  { id: "EARNINGS", label: "Earnings", icon: "₱" },
  { id: "HISTORY", label: "Delivery history", icon: "✓" },
  { id: "CHATS", label: "Messages", icon: "✉" },
  { id: "PROFILE", label: "Profile & vehicle", icon: "◉" },
];

function readPage() {
  const parts = window.location.hash.replace(/^#\/RIDER\/?/i, "").split("/").filter(Boolean);
  const page = (parts[0] || "DASHBOARD").toUpperCase();
  const allowed = PAGES.some((item) => item.id === page) || page === "CHAT";
  return allowed ? { page, id: parts[1] || "" } : { page: "DASHBOARD", id: "" };
}

function Brand() {
  return <a className="brand rider-brand" href="#/" aria-label="DashBite home">
    <img src={logoUrl} alt="" width="38" height="38" />
    <span>dash<span className="brand-accent">bite</span></span>
  </a>;
}

export default function RiderPortal() {
  const session = getPreviewSession("RIDER");
  const email = session?.email || "";
  const monitorMode = isAdminMonitoring();
  const [route, setRoute] = useState(readPage);
  const [portalState, setPortalState] = useState(() => {
    try {
      const account = email ? findPreviewAccount(email) : null;
      return { data: loadRiderPortal(email, account), error: "" };
    } catch (error) {
      return { data: null, error: error instanceof Error ? error.message : "Could not load rider preview data." };
    }
  });

  useEffect(() => {
    const updateRoute = () => setRoute(readPage());
    window.addEventListener("hashchange", updateRoute);
    return () => window.removeEventListener("hashchange", updateRoute);
  }, []);

  function navigate(path) {
    window.location.hash = `#/RIDER/${path}`;
  }

  function updateData(updater) {
    if (!portalState.data || monitorMode) return;
    try {
      const nextData = updater(portalState.data);
      saveRiderPortal(email, nextData);
      setPortalState({ data: nextData, error: "" });
    } catch (error) {
      setPortalState((current) => ({ ...current, error: error instanceof Error ? error.message : "Could not save rider preview data." }));
    }
  }

  function logOut() {
    clearPreviewSession();
    window.location.hash = monitorMode ? "#/" : "#/LOGIN";
  }

  const activePage = route.page === "CHAT" ? "CHATS" : route.page;
  const page = PAGES.find((item) => item.id === activePage) || PAGES[0];
  const data = portalState.data;
  let content = null;
  if (data) {
    if (route.page === "DASHBOARD") content = <RiderDashboard data={data} onChange={updateData} onNavigate={navigate} readOnly={monitorMode} />;
    else if (route.page === "EARNINGS") content = <RiderEarnings data={data} />;
    else if (route.page === "HISTORY") content = <RiderHistory data={data} />;
    else if (route.page === "CHATS") content = <RiderChatInbox data={data} onNavigate={navigate} />;
    else if (route.page === "CHAT") content = <RiderChat orderId={route.id} data={data} onChange={updateData} onNavigate={navigate} readOnly={monitorMode} />;
    else if (route.page === "PROFILE") content = <RiderProfile data={data} onChange={updateData} readOnly={monitorMode} />;
    else content = <RiderEmptyState title="Page not found" description="Choose a section from the Rider portal menu." />;
  }

  return <div className={`restaurant-shell rider-shell${monitorMode ? " is-read-only" : ""}`}>
    <aside className="restaurant-sidebar rider-sidebar">
      <Brand />
      <div className="restaurant-sidebar-identity">
        <div className="restaurant-profile-avatar">{(data?.profile.name || "R").trim().charAt(0).toUpperCase()}</div>
        <div><strong>{data?.profile.name || "Rider preview"}</strong><span>Rider workspace</span></div>
      </div>
      <p className="restaurant-nav-label">{monitorMode ? "ADMIN MONITORING" : "RIDER PORTAL"}</p>
      <nav className="restaurant-nav rider-nav" aria-label="Rider portal">
        {PAGES.map((item) => <a
          className={`restaurant-nav-link${activePage === item.id ? " is-active" : ""}`}
          href={`#/RIDER/${item.id}`}
          aria-current={activePage === item.id ? "page" : undefined}
          key={item.id}
        ><span aria-hidden="true">{item.icon}</span>{item.label}{item.id === "CHATS" && Object.values(data?.chats || {}).some((messages) => messages.length) && <i aria-label="Messages available" />}</a>)}
      </nav>
      <div className="restaurant-sidebar-bottom">
        {monitorMode
          ? <a className="restaurant-workspace-link" href="#/ADMIN"><span aria-hidden="true">▦</span>Back to Admin<b aria-hidden="true">↗</b></a>
          : <a className="restaurant-workspace-link" href="#/"><span aria-hidden="true">⌂</span>DashBite home<b aria-hidden="true">↗</b></a>}
        <span>DashBite · Core v1.0</span>
      </div>
    </aside>
    <main className="restaurant-main rider-main">
      <header className="restaurant-topbar">
        <div className="restaurant-topbar-title"><span>Rider</span><b aria-hidden="true">/</b><strong>{page.label}</strong></div>
        <div className="restaurant-topbar-actions">
          {monitorMode
            ? <span className="restaurant-monitor-pill">Read-only monitoring</span>
            : <span className={`restaurant-live-pill${data?.isOnline ? " is-open" : ""}`}><i />{data?.isOnline ? "Online" : "Offline"}</span>}
          <span className="restaurant-account-chip"><i>{(data?.profile.name || "R").trim().charAt(0).toUpperCase()}</i><span>{email || "Rider preview"}</span></span>
          <button className="restaurant-logout" type="button" onClick={logOut}>{monitorMode ? "Back to Admin" : "Log out"}</button>
        </div>
      </header>
      <div className="restaurant-main-inner rider-main-inner">
        {monitorMode
          ? <div className="restaurant-preview-banner"><span aria-hidden="true">i</span><p><strong>Admin monitoring view.</strong> Rider data is illustrative and read-only.</p></div>
          : <div className="restaurant-preview-banner"><span aria-hidden="true">i</span><p><strong>Frontend preview.</strong> Offers, delivery updates, messages, and earnings are sample browser-local data. The dispatch API form is separate and requires valid contract IDs.</p></div>}
        <RiderNotice notice={portalState.error ? { type: "error", message: portalState.error } : null} />
        {content}
        <footer className="restaurant-footer"><span>© 2026 DashBite</span><span>Rider portal · Preview data</span></footer>
      </div>
    </main>
  </div>;
}
