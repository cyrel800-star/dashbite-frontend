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

The customer order form uses `POST /api/orders`; the rider claim form uses `POST /api/dispatch/accept-offer`. Forms validate their inputs and show API failures, including the contract error codes. Configure an API base URL from a dashboard's **Configure API** control. The default is `/api`.

Dashboard figures and queue entries are sample preview data, not live records. The v1.0 contract does not define request fields for login, restaurant order decisions, delivery-fee changes, or account approvals, so those actions are not implemented.

## Project layout

```text
package.json            React/Vite dependencies and commands
package-lock.json       Locked npm dependency versions
vite.config.js          Development server and production build settings
scripts/deploy-build.js  Deploys the static build into the XAMPP root
src/
  App.jsx                 React pages and dashboard components
  components/Forms.jsx    API-connected customer and rider forms
  data/constants.js       v1.0 roles and status vocabulary
  index.html              Vite development entry point
  lib/api.js              API client and base URL configuration
  main.jsx                React application entry point
assets/
  css/styles.css          Shared responsive styles
  logo.svg                DashBite logo
pages/                    Friendly URLs for the four role dashboards
dist/                     Generated build output (ignored by Git)
```
