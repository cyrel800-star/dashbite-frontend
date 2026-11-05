# DashBite frontend redesign changes

## Design and UI
- Added Bootstrap 5, Bootstrap Icons, Poppins font files, and React-Bootstrap to the frontend dependencies.
- Added a shared DashBite theme layer in `src/styles/dashbite/`, loaded after the existing project CSS so the current page styles remain the foundation.
- Refreshed shared portal navigation, sidebars, topbars, cards, panels, forms, and responsive layouts for the customer, restaurant, rider, and admin workspaces.
- Kept the existing React/Vite application, hash routes, portal components, preview data stores, and API utility modules.

## Profile photo uploads
- Added `src/components/shared/ProfilePhotoUpload.jsx` as a reusable profile-image editor.
- Customer Profile, Rider Profile & Vehicle, Restaurant Profile, and Admin Settings now support JPG, PNG, and WEBP images up to 5 MB.
- Images are resized in the browser to a 420 px maximum dimension and saved as compressed JPEG data URLs in the existing browser-local profile data.
- Added profile image previews to the portal identity areas and account chips.
- Existing saved local data remains compatible; Admin profile defaults are migrated when the old Admin settings object is loaded.

## Scope and limitations
- No backend source, database schema, API route, request payload, environment variable, or authentication implementation was changed.
- Image changes are browser-local preview data. They are not uploaded to a server or shared between browsers/devices. A real persistent upload requires a backend endpoint and server-side storage.
- `package.json` now declares Bootstrap-related dependencies. Run `npm install` in the project folder to install them and refresh `package-lock.json` before building.
