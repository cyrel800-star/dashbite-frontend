import { useMemo, useState } from "react";
import { RestaurantMetric, RestaurantSectionHeading, formatRestaurantDate } from "./RestaurantUI.jsx";

function Stars({ value }) {
  return <span className="restaurant-stars" aria-label={`${value} out of 5 stars`}>{Array.from({ length: 5 }, (_, index) => <span className={index < value ? "is-filled" : ""} key={index}>★</span>)}</span>;
}

export default function RestaurantReviews({ data, onChange, readOnly }) {
  const [ratingFilter, setRatingFilter] = useState("ALL");
  const [replyFilter, setReplyFilter] = useState("ALL");
  const [drafts, setDrafts] = useState({});
  const [message, setMessage] = useState("");
  const reviews = useMemo(() => data.reviews.filter((review) => {
    const matchesRating = ratingFilter === "ALL" || review.rating === Number(ratingFilter);
    const matchesReply = replyFilter === "ALL"
      || (replyFilter === "REPLIED" ? Boolean(review.reply) : !review.reply);
    return matchesRating && matchesReply;
  }), [data.reviews, ratingFilter, replyFilter]);
  const average = data.reviews.length
    ? (data.reviews.reduce((sum, review) => sum + review.rating, 0) / data.reviews.length).toFixed(1)
    : "—";
  const unanswered = data.reviews.filter((review) => !review.reply).length;

  function saveReply(review) {
    const reply = (drafts[review.id] || "").trim();
    if (!reply) {
      setMessage("Write a reply before saving.");
      return;
    }
    onChange((current) => ({
      ...current,
      reviews: current.reviews.map((item) => item.id === review.id ? { ...item, reply } : item),
    }));
    setDrafts((current) => ({ ...current, [review.id]: "" }));
    setMessage("Your reply was saved to the local preview.");
  }

  return <div className="restaurant-page-content">
    <RestaurantSectionHeading eyebrow="CUSTOMER FEEDBACK" title="Reviews" description="Read what customers think and respond with care." />
    <div className="restaurant-metric-grid restaurant-review-metrics">
      <RestaurantMetric label="Average rating" value={<span className="restaurant-rating-value">★ {average}</span>} note="Across preview reviews" icon="★" />
      <RestaurantMetric label="Total reviews" value={data.reviews.length} note="Customer feedback" icon="▤" />
      <RestaurantMetric label="Awaiting reply" value={unanswered} note="An opportunity to connect" icon="↩" />
      <RestaurantMetric label="Replied" value={data.reviews.length - unanswered} note="Responses saved" icon="✓" />
    </div>
    <div className="restaurant-filter-panel">
      <label className="restaurant-filter-select"><span className="sr-only">Filter reviews by rating</span><select value={ratingFilter} onChange={(event) => setRatingFilter(event.target.value)}><option value="ALL">All ratings</option>{[5, 4, 3, 2, 1].map((rating) => <option value={rating} key={rating}>{"★".repeat(rating)} ({rating})</option>)}</select></label>
      <label className="restaurant-filter-select"><span className="sr-only">Filter reviews by reply status</span><select value={replyFilter} onChange={(event) => setReplyFilter(event.target.value)}><option value="ALL">All replies</option><option value="UNREPLIED">Awaiting reply</option><option value="REPLIED">Replied</option></select></label>
    </div>
    {message && <div className="restaurant-inline-message" role="status">{message}</div>}
    {reviews.length ? <div className="restaurant-review-list">{reviews.map((review) => <article className="restaurant-panel restaurant-review-card" key={review.id}>
      <div className="restaurant-review-head">
        <div className="restaurant-review-avatar">{review.customer.trim().charAt(0).toUpperCase()}</div>
        <div className="restaurant-review-person"><strong>{review.customer}</strong><span>{formatRestaurantDate(review.createdAt, { month: "short", day: "numeric", year: "numeric" })}</span></div>
        <span className={`restaurant-reply-status${review.reply ? " is-replied" : ""}`}>{review.reply ? "Replied" : "Awaiting reply"}</span>
      </div>
      <Stars value={review.rating} />
      <h3>{review.title}</h3>
      <p className="restaurant-review-body">{review.body}</p>
      {review.reply && <div className="restaurant-saved-reply"><strong>Your response</strong><p>{review.reply}</p></div>}
      {!review.reply && !readOnly && <div className="restaurant-reply-form">
        <label className="restaurant-field"><span className="sr-only">Reply to {review.customer}</span><textarea rows="2" maxLength={500} value={drafts[review.id] || ""} onChange={(event) => setDrafts((current) => ({ ...current, [review.id]: event.target.value }))} placeholder="Write a thoughtful reply..." /></label>
        <button className="button button-primary button-small" type="button" onClick={() => saveReply(review)}>Post reply</button>
      </div>}
      {!review.reply && readOnly && <p className="restaurant-local-note">Replying is disabled in Admin monitoring mode.</p>}
    </article>)}</div> : <section className="restaurant-panel"><div className="restaurant-empty"><span aria-hidden="true">★</span><strong>No reviews match these filters</strong><p>Try a different rating or reply status.</p></div></section>}
    <p className="restaurant-local-note">Reviews and replies are illustrative preview content saved only in this browser.</p>
  </div>;
}
