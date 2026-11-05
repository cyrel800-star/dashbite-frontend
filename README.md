# DashBite frontend

DashBite is a React 19 + Vite 7 food-delivery demo. The main customer, restaurant, rider, and admin flows work in the browser without an API server. Accounts and activity are stored in the current browser and do not sync across devices.

## Run locally

```bash
npm install
npm run dev
```

Open the localhost URL printed by Vite (normally `http://localhost:5173/`). To create a production build:

```bash
npm run build
```

The build creates `dist/`; the `postbuild` script copies build output to the project root for XAMPP.

### Demo sign-in

Use any valid email and any password, choose a role, and enter OTP `123456`. This is a local demo flow, not secure authentication. Restaurant and rider sign-ups wait for approval by the local admin; customer sign-ups are immediately available. Demo accounts, approvals, and saved records exist only in the same browser profile.

## Demo features and limitations

- Customers can browse menus, place local orders, manage favorites, message restaurants, and review delivered orders.
- Restaurant owners can add, edit, pause, and remove menu items, upload food images, update order status and hours, reply to customers, and see reviews.
- Riders can go online, accept ready orders, mark deliveries complete, chat, and see estimated demo earnings.
- Admins can manage local accounts and orders, moderate reviews, view activity summaries, and upload homepage feature images from Settings.
- Uploaded images are resized and saved in browser storage. Clearing site data removes local demo changes.
- Maps, real payments, external chat, and secure authentication require online services and are not implemented as real services.

## Project map

| Path | Responsibility |
| --- | --- |
| `src/main.jsx` | React application entry point and stylesheet loading |
| `src/App.jsx` | Restores demo session state and routes landing, auth, and role portals |
| `src/components/Landing.jsx` | Public home/about sections and restaurant previews |
| `src/components/LoginFlow.jsx`, `SignupFlow.jsx`, `Forms.jsx` | Demo sign-in, OTP, registration, and shared auth fields |
| `src/components/PortalFrame.jsx` | Shared role portal navigation, profile/settings routes, and shell |
| `src/components/ProfileSettings.jsx` | Profile editing, preferences, dark theme, and admin feature-image uploads |
| `src/components/customer/CustomerPortal.jsx` | Customer browsing, orders, favorites, reviews, and chat |
| `src/components/restaurant/RestaurantPortal.jsx` | Menu editing, food-image uploads, order management, hours, and chat |
| `src/components/rider/RiderPortal.jsx` | Rider status, offers, deliveries, earnings, and chat |
| `src/components/admin/AdminPortal.jsx` | Local account, order, review, report, and activity management |
| `src/ui/components.jsx`, `src/ui/ui.css` | Reusable UI primitives and global/responsive styling |
| `src/data/` | Role/status constants, sample restaurant data, and bundled image references |
| `src/lib/localData.js` | Browser storage, persistent React state, and image validation/resizing |
| `src/lib/previewSession.js` | Session-only demo login state |
| `src/lib/api.js` | Optional API client retained for future backend integration; unused by current demo screens |
| `assets/` | Bundled logo and food photography |
| `scripts/deploy-build.js` | Copies production build output into the project root for XAMPP |

## Code comments

Short comments mark the main logical groups in application code and stylesheets, such as state setup, user actions, screen routing, shared UI, storage helpers, and responsive CSS. Use these section comments as landmarks when locating a feature; keep them concise and update them when moving or substantially changing a group.

## Backend integration

The current screens use browser-local data and do not require a backend. `src/lib/api.js` is a future integration helper only; maps, payment processing, secure authentication, and cross-device synchronization are not provided by this demo.
