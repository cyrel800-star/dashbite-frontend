import { useMemo, useState } from "react";
import { RestaurantMetric, RestaurantPrice, RestaurantSectionHeading, formatRestaurantDate } from "./RestaurantUI.jsx";

function dayStart(value) {
  const date = new Date(value);
  date.setHours(0, 0, 0, 0);
  return date;
}

export default function RestaurantAnalytics({ data }) {
  const [period, setPeriod] = useState(7);
  const today = dayStart(new Date());
  const start = new Date(today);
  start.setDate(start.getDate() - (period - 1));
  const orders = data.orders.filter((order) => {
    const date = new Date(order.createdAt);
    return date >= start && date <= new Date(today.getTime() + 86400000) && !["REJECTED", "CANCELLED"].includes(order.status);
  });
  const completedOrders = orders.filter((order) => order.status === "DELIVERED");
  const sales = completedOrders.reduce((total, order) => total + order.total, 0);
  const averageOrder = completedOrders.length ? sales / completedOrders.length : 0;
  const chartDays = useMemo(() => Array.from({ length: period }, (_, index) => {
    const date = new Date(start);
    date.setDate(start.getDate() + index);
    const dayOrders = completedOrders.filter((order) => dayStart(order.createdAt).getTime() === date.getTime());
    return { date, sales: dayOrders.reduce((sum, order) => sum + order.total, 0), count: dayOrders.length };
  }), [completedOrders, period, start.getTime()]);
  const maxSales = Math.max(...chartDays.map((day) => day.sales), 1);
  const topItems = data.menu.map((item) => ({
    ...item,
    count: data.orders.reduce((count, order) => count + order.items.filter((orderedItem) => orderedItem.includes(item.name)).length, 0),
  })).sort((a, b) => b.count - a.count).slice(0, 4);
  const averageRating = data.reviews.length
    ? (data.reviews.reduce((sum, review) => sum + review.rating, 0) / data.reviews.length).toFixed(1)
    : "—";

  return <div className="restaurant-page-content">
    <RestaurantSectionHeading
      eyebrow="UNDERSTAND YOUR BUSINESS"
      title="Sales analytics"
      description="A quick read on the sample orders saved in this browser."
      action={<label className="restaurant-filter-select"><span className="sr-only">Analytics period</span><select value={period} onChange={(event) => setPeriod(Number(event.target.value))}><option value={7}>Last 7 days</option><option value={30}>Last 30 days</option></select></label>}
    />
    <div className="restaurant-metric-grid restaurant-analytics-metrics">
      <RestaurantMetric label="Sales" value={<RestaurantPrice value={sales} />} note={`${completedOrders.length} completed preview orders`} icon="↗" />
      <RestaurantMetric label="Average order" value={<RestaurantPrice value={averageOrder} />} note="Per completed order" icon="◉" />
      <RestaurantMetric label="Orders received" value={orders.length} note={`In the last ${period} days`} icon="▤" />
      <RestaurantMetric label="Customer rating" value={averageRating} note={`${data.reviews.length} preview reviews`} icon="★" />
    </div>
    <section className="restaurant-panel">
      <div className="restaurant-panel-heading"><div><h3>Sales trend</h3><p>Completed orders by day · illustrative only</p></div><span className="restaurant-chart-legend"><i /> Sales</span></div>
      <div className="restaurant-chart" role="img" aria-label={`Sales chart for the last ${period} days`}>
        {chartDays.map((day) => <div className="restaurant-chart-column" key={day.date.toISOString()}>
          <div className="restaurant-chart-bar-wrap" title={`${formatRestaurantDate(day.date, { month: "short", day: "numeric" })}: ${day.sales.toFixed(2)} sales`}>
            <span className="restaurant-chart-bar" style={{ height: `${day.sales ? Math.max((day.sales / maxSales) * 100, 8) : 3}%` }} />
          </div>
          <small>{new Intl.DateTimeFormat(undefined, { weekday: period > 7 ? undefined : "short", day: period > 7 ? "numeric" : undefined }).format(day.date)}</small>
        </div>)}
      </div>
      <div className="restaurant-chart-summary"><span>Period total</span><strong><RestaurantPrice value={sales} /></strong></div>
    </section>
    <section className="restaurant-panel">
      <div className="restaurant-panel-heading"><div><h3>Popular menu items</h3><p>Based on items listed in preview orders</p></div><button className="restaurant-inline-link" type="button" onClick={() => { window.location.hash = "#/RESTAURANT/MENU"; }}>Manage menu →</button></div>
      {topItems.length ? <div className="restaurant-popular-list">{topItems.map((item, index) => <div className="restaurant-popular-row" key={item.id}>
        <span className="restaurant-popular-rank">{index + 1}</span><div><strong>{item.name}</strong><small>{item.category}</small></div><span>{item.count} ordered</span>
      </div>)}</div> : <p className="restaurant-local-note">Add menu items to see an item summary.</p>}
    </section>
    <p className="restaurant-local-note">Analytics use local sample order data. They are not actual financial records or a live report.</p>
  </div>;
}
