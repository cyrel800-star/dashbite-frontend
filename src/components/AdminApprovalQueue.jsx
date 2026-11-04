import { useEffect, useState } from "react";
import {
  approvePreviewAccount,
  listPreviewAccounts,
  subscribeToPreviewAccounts,
} from "../lib/previewAccounts.js";

function Status({ value }) {
  return <span className={`status status-${value.toLowerCase()}`}>{value}</span>;
}

export default function AdminApprovalQueue({ onPendingCountChange }) {
  const [accounts, setAccounts] = useState([]);
  const [filter, setFilter] = useState("PENDING_APPROVAL");
  const [error, setError] = useState("");
  const [notice, setNotice] = useState("");

  useEffect(() => {
    function refreshAccounts() {
      try {
        setAccounts(listPreviewAccounts());
        setError("");
      } catch (loadError) {
        setError(loadError instanceof Error ? loadError.message : "Could not load preview account applications.");
      }
    }

    refreshAccounts();
    return subscribeToPreviewAccounts(refreshAccounts);
  }, []);

  const pendingCount = accounts.filter((account) => account.account_status === "PENDING_APPROVAL").length;
  useEffect(() => {
    onPendingCountChange(pendingCount);
  }, [onPendingCountChange, pendingCount]);

  const visibleAccounts = filter === "PENDING_APPROVAL"
    ? accounts.filter((account) => account.account_status === "PENDING_APPROVAL")
    : accounts;

  function handleApprove(email) {
    setError("");
    setNotice("");
    try {
      const account = approvePreviewAccount(email);
      setNotice(`${account.name} (${account.role.toLowerCase()}) is approved and can now sign in.`);
    } catch (approvalError) {
      setError(approvalError instanceof Error ? approvalError.message : "Could not approve this account.");
    }
  }

  return <section className="panel">
    <div className="panel-heading">
      <div>
        <h2>Account approval requests</h2>
        <small>{pendingCount} pending {pendingCount === 1 ? "application" : "applications"}</small>
      </div>
      <div className="approval-filters" aria-label="Filter account applications">
        <button
          className={`button button-small${filter === "PENDING_APPROVAL" ? " button-primary" : " button-outline"}`}
          type="button"
          aria-pressed={filter === "PENDING_APPROVAL"}
          onClick={() => setFilter("PENDING_APPROVAL")}
        >Pending</button>
        <button
          className={`button button-small${filter === "ALL" ? " button-primary" : " button-outline"}`}
          type="button"
          aria-pressed={filter === "ALL"}
          onClick={() => setFilter("ALL")}
        >All</button>
      </div>
    </div>
    {error && <div className="notice notice-error visible" role="alert">{error}</div>}
    {notice && <div className="notice notice-success visible" role="status">{notice}</div>}
    {visibleAccounts.length ? <div className="table-wrap">
      <table>
        <thead><tr><th>APPLICANT</th><th>CONTACT</th><th>ROLE</th><th>APPLICATION DETAILS</th><th>STATUS</th><th>ACTION</th></tr></thead>
        <tbody>{visibleAccounts.map((account) => {
          const isPending = account.account_status === "PENDING_APPROVAL";
          return <tr key={account.email}>
            <td className="approval-applicant">
              <strong>{account.name}</strong>
              {account.role === "RESTAURANT" && <small>{account.restaurant_name || "Restaurant name not provided"}</small>}
            </td>
            <td className="approval-contact">{account.email}<small>{account.phone || "No phone provided"}</small></td>
            <td>{account.role}</td>
            <td className="approval-details">{account.role === "RESTAURANT" ? account.restaurant_address || "No address provided" : "Rider account application"}</td>
            <td><Status value={account.account_status} /></td>
            <td>{isPending
              ? <button className="button button-primary button-small" type="button" onClick={() => handleApprove(account.email)}>Approve account</button>
              : <span className="disabled-action">Approved</span>}</td>
          </tr>;
        })}</tbody>
      </table>
    </div> : <div className="empty-state">{filter === "PENDING_APPROVAL" ? "There are no restaurant or rider applications waiting for approval." : "No restaurant or rider applications have been registered in this browser."}</div>}
    <p className="permission-note">Approvals update this browser's preview account data. A waiting applicant is allowed to continue automatically when approval is detected. Production approval requires an authenticated admin and backend API.</p>
  </section>;
}
