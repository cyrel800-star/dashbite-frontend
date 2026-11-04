import { useMemo, useState } from "react";
import { RiderEmptyState, RiderMetric, RiderMoney, RiderSectionHeading } from "./RiderUI.jsx";

const PERIODS = [
  { id: "7days", label: "Last 7 days" },
  { id: "30days", label: "Last 30 days" },
  { id: "month", label: "This month" },
];

function periodStart(period) {
  const date = new Date();
  date.setHours(0, 0, 0, 0);
  if (period === "month") date.setDate(1);
  else date.setDate(date.getDate() - (period === "7days" ? 6 : 29));
  return date;
}

export default function RiderEarnings({ data }) {
  const [period, setPeriod] = useState("7days");
  const days = useMemo(() => {
    const start = periodStart(period);
    const count = period === "7days" ? 7 : period === "30days" ? 30 : new Date().getDate();
    return Array.from({ length: count }, (_, index) => {
      const date = new Date(start);
      date.setDate(start.getDate() + index);
      const key = date.toDateString();
      const orders = data.orders.filter((order) => order.status === "DELIVERED" && order.completedAt && new Date(order.completedAt).toDateString() === key);
      return {
        key,
        label: new Intl.DateTimeFormat(undefined, { weekday: count === 7 ? "short" : undefined, month: "short", day: "numeric" }).format(date),
        count: orders.length,
        total: orders.reduce((sum, order) => sum + order.deliveryFee, 0),
      };
    });
  }, [data.orders, period]);
  const earningDays = days.filter((day) => day.count > 0);
  const total = earningDays.reduce((sum, day) => sum + day.total, 0);
  const deliveries = earningDays.reduce((sum, day) => sum + day.count, 0);
  const bestDay = earningDays.reduce((best, day) => !best || day.total > best.total ? day : best, null);
  const maxAmount = Math.max(...days.map((day) => day.total), 1);

  return <div className="rider-page-content">
    <RiderSectionHeading eyebrow="YOUR DELIVERY INCOME" title="Earnings" description="A clear view of your sample delivery fees over time." />
    <div className="rider-period-tabs" role="tablist" aria-label="Earnings period">
      {PERIODS.map((item) => <button key={item.id} type="button" role="tab" aria-selected={period === item.id} className={period === item.id ? "is-active" : ""} onClick={() => setPeriod(item.id)}>{item.label}</button>)}
    </div>
    <div className="rider-metric-grid rider-earnings-metrics">
      <RiderMetric icon="₱" label="Total earnings" value={<RiderMoney value={total} />} note={PERIODS.find((item) => item.id === period)?.label} />
      <RiderMetric icon="▤" label="Completed deliveries" value={deliveries} note="In selected period" accent="dark" />
      <RiderMetric icon="↗" label="Average per delivery" value={<RiderMoney value={deliveries ? total / deliveries : 0} />} note="Delivery fees only" accent="green" />
    </div>
    {bestDay && <section className="rider-best-day"><div><p>✦ BEST DAY</p><strong>{new Intl.DateTimeFormat(undefined, { month: "long", day: "numeric", year: "numeric" }).format(new Date(bestDay.key))}</strong><small>{bestDay.count} completed delivery{bestDay.count === 1 ? "" : "ies"}</small></div><b><RiderMoney value={bestDay.total} /></b></section>}
    <section className="rider-panel rider-earnings-chart">
      <div className="rider-panel-title"><div><h3>Earnings trend</h3><p>Daily preview earnings</p></div><span>₱ PHP</span></div>
      {deliveries ? <div className={`rider-chart-bars${period === "month" ? " is-month" : ""}`} role="img" aria-label={`Daily earnings trend for ${PERIODS.find((item) => item.id === period)?.label}`}>
        {days.map((day) => <div className="rider-chart-column" key={day.key} title={`${day.label}: ₱${day.total.toFixed(2)}`}><div className="rider-chart-track"><i style={{ height: `${day.total ? Math.max(8, day.total / maxAmount * 100) : 0}%` }} /></div><small>{day.label}</small></div>)}
      </div> : <RiderEmptyState icon="▥" title="No earnings yet for this period" description="Completed preview deliveries will show up in this chart." />}
    </section>
    <section className="rider-panel rider-breakdown-panel">
      <div className="rider-panel-title"><div><h3>Daily breakdown</h3><p>Delivery fees grouped by day</p></div></div>
      {earningDays.length ? <div className="rider-breakdown-list">{earningDays.slice().reverse().map((day) => <div className="rider-breakdown-row" key={day.key}><div><strong>{day.label}</strong><small>{day.count} delivery{day.count === 1 ? "" : "ies"}</small></div><b><RiderMoney value={day.total} /></b></div>)}</div> : <RiderEmptyState icon="◷" title="No deliveries in this period" description="Your daily earnings breakdown will appear here after a delivery is completed." />}
      <div className="rider-breakdown-total"><span>Period total</span><strong><RiderMoney value={total} /></strong></div>
    </section>
    <p className="rider-inline-note">Illustrative browser-local preview. Earnings shown are sample delivery fees, not payouts or account balances.</p>
  </div>;
}
