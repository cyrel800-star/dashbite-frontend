# DashBite frontend

React frontend preview for the DashBite Core Data Model & API Contract Pack v1.0. This project contains frontend code only; it does not include an API server or authentication service.

## Requirements

- Node.js 20.19+ or 22.12+
- npm
- XAMPP/Apache for serving the production build at `http://localhost/dashbite/`

## Install and develop

From `C:\xampp\htdocs\dashbite`, install the dependencies and start Vite:

```powershell
npm install
npm run dev
```

Open the local URL printed by Vite. Choose Customer, Restaurant, Rider, or Admin from the home page.

## Build for XAMPP

Build the static React app into this project folder:

```powershell
npm run build
```

The build is generated in `dist/` and deployed to the XAMPP project root. Vite writes the generated `index.html` and bundled files under `assets/build/`; it preserves the source files and existing project assets.

Open `http://localhost/dashbite/` after the build completes.

## Dashboard links

- Customer: `http://localhost/dashbite/pages/customer/dashboard.html`
- Restaurant: `http://localhost/dashbite/pages/restaurant/dashboard.html`
- Rider: `http://localhost/dashbite/pages/rider/dashboard.html`
- Admin: `http://localhost/dashbite/pages/admin/dashboard.html`

Each dashboard URL opens its corresponding role in the React app.

## API preview

The Rider contract claim form uses `POST /api/dispatch/accept-offer` and validates order/rider UUIDs. The Rider portal also includes sample local offer actions, which do not call that endpoint. Customer checkout and the restaurant, chat, tracking, favorites, and review screens are local previews; they do not call the API or process payments. Configure the API base URL from a dashboard's **Configure API** control. The default is `/api`.

The sign-in screen previews the email/password → OTP flow. After entering the demo code `123456`, it looks up the registered preview account by email and routes approved users to their saved role. Accounts marked `PENDING_APPROVAL` or `DISABLED` cannot sign in. The flow is UI-only: passwords are cleared and never stored, no email is sent, and no login or OTP API is called because the request fields and OTP endpoint are not defined. Five preview code requests trigger a 60-second cooldown. Do not treat this preview as authentication or production rate limiting.

The sign-up screen is available from the home page's **Sign up** button or at `http://localhost/dashbite/#/SIGNUP`. It previews Customer, Restaurant, and Rider registration. Preview account details (excluding passwords) are saved in this browser's local storage so a later sign-in can automatically find the account role. Customer signup opens the local customer preview; Restaurant and Rider accounts are saved as `PENDING_APPROVAL` and cannot sign in until approved.

The Admin portal has a separate sign-in at `http://localhost/dashbite/#/ADMIN/LOGIN`. Demo credentials are `admin@dashbite.local` / `DashBiteAdmin!2026`. Direct access to `#/ADMIN` redirects to that sign-in unless an Admin preview session is active. Admin is intentionally not an option on the registration form; signup only offers Customer, Restaurant, and Rider.

The Admin preview includes an overview, account directory and Restaurant/Rider approval queue, order list and details, sample reports, review moderation, and local settings. **Approve account** changes the selected Restaurant or Rider preview account to `APPROVED` in this browser's local storage. A waiting applicant can continue when approval is detected; other applicants can sign in normally. Changes are shared across tabs in the same browser profile, not across devices or users. Orders, reports, reviews, and settings are illustrative or browser-local; this does not manage a live platform.

The Admin credentials and session check are implemented in frontend code and are visible to users. They are for preview navigation only, not security. Do not use them to protect production data or approve real applicants. Production Admin authentication, authorization, registration, and approvals require a secured backend.

From the Admin portal, the **Workspace Monitoring** links open each role dashboard in a read-only Admin monitoring view, so Admin can inspect the role-specific preview without signing in as that user. Operational forms are disabled in monitoring mode. Opening Customer, Restaurant, or Rider directly outside the Admin monitoring flow still requires an approved role session; pending approvals remain blocked.

The Admin dashboard and its monitoring views include **Log out**. Logging out clears the current preview session and Admin monitoring state, then returns to the home page.

The Restaurant portal is organized into Overview, Orders, Menu, Operating Hours, Analytics, Reviews, and Profile screens. Restaurant order acceptance/rejection, menu edits, hours, profile changes, and review replies are local preview interactions saved per account in this browser; they do not call a server. Analytics and seeded orders/reviews are illustrative sample data. The v1.0 contract does not define restaurant order-decision, menu, hours, profile, review, or analytics endpoints.

## Customer portal

Approved Customer previews open at `http://localhost/dashbite/#/CUSTOMER`. The portal includes restaurant discovery and menus, favorites, a cart and local checkout, order history and simulated tracking, order chat, restaurant reviews, and profile details. Its data is kept per customer email in this browser's local storage. Sample restaurants, orders, chats, reviews, status changes, and checkout are illustrative only; they are not sent to restaurants, riders, or payment providers. Admin monitoring can inspect the Customer portal in read-only mode.

## Rider portal

Approved Rider previews open at `http://localhost/dashbite/#/RIDER`. The portal includes an online/offline shift control, sample delivery offers and active delivery steps, earnings summaries, delivery history, customer chat, and vehicle details. Rider preview data is saved per rider email in this browser. Sample offer acceptance and delivery progress are local only; the separate dispatch contract form is the only Rider action that calls the API. Earnings, chats, addresses, ratings, and delivery records are illustrative and are not live dispatch or payout information. Admin monitoring is read-only.

## Project layout

```text
package.json            React/Vite dependencies and commands
package-lock.json       Locked npm dependency versions
vite.config.js          Development server and production build settings
scripts/deploy-build.js  Deploys the static build into the XAMPP root
src/
  App.jsx                 React pages and dashboard components
  components/AdminApprovalQueue.jsx Admin review queue for local preview applications
  components/admin/          Admin sign-in, portal shell, screens, and shared UI
  components/Forms.jsx    API-connected customer and rider forms
  components/LoginFlow.jsx Email/OTP login and account-flow preview
  components/SignupFlow.jsx Customer, Restaurant, and Rider signup preview
  components/restaurant/  Restaurant portal shell and dedicated section screens
  components/customer/    Customer portal shell and dedicated customer screens
  components/rider/       Rider portal shell, dispatch, earnings, history, chat, and profile
  data/constants.js       v1.0 roles and status vocabulary
  index.html              Vite development entry point
  lib/api.js              API client and base URL configuration
  lib/adminPreviewAuth.js Demo-only Admin credentials and session check
  lib/adminPortal.js      Admin sample records and browser-local settings
  lib/previewAccounts.js  Browser-local signup and approval data; passwords are never saved
  lib/previewSession.js   UI-only preview account state
  lib/restaurantPortal.js Per-restaurant preview profile, orders, menu, and hours data
  lib/customerPortal.js   Customer catalog, validation, and browser-local portal data
  lib/riderPortal.js      Rider offers, validation, and browser-local portal data
  main.jsx                React application entry point
assets/
  css/styles.css          Shared responsive styles
  logo.svg                DashBite logo
pages/                    Friendly URLs for the four role dashboards
dist/                     Generated build output (ignored by Git)
```
