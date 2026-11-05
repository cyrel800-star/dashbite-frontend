import { useRef, useState } from "react";

const MAX_FILE_BYTES = 5 * 1024 * 1024;
const ACCEPTED_TYPES = ["image/jpeg", "image/png", "image/webp"];

function readFile(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = () => reject(new Error("The selected image could not be read."));
    reader.readAsDataURL(file);
  });
}

function resizeImage(source) {
  return new Promise((resolve, reject) => {
    const image = new Image();
    image.onload = () => {
      const size = 420;
      const scale = Math.min(size / image.width, size / image.height, 1);
      const width = Math.max(1, Math.round(image.width * scale));
      const height = Math.max(1, Math.round(image.height * scale));
      const canvas = document.createElement("canvas");
      canvas.width = width;
      canvas.height = height;
      const context = canvas.getContext("2d");
      if (!context) return reject(new Error("Image processing is unavailable in this browser."));
      context.drawImage(image, 0, 0, width, height);
      resolve(canvas.toDataURL("image/jpeg", 0.78));
    };
    image.onerror = () => reject(new Error("Please choose a valid image file."));
    image.src = source;
  });
}

export default function ProfilePhotoUpload({ image, name, onChange, disabled = false, label = "Profile photo" }) {
  const inputRef = useRef(null);
  const [error, setError] = useState("");
  const [busy, setBusy] = useState(false);
  const initials = (name || "D").trim().split(/\s+/).map((part) => part[0]).join("").slice(0, 2).toUpperCase();

  async function handleFile(event) {
    const file = event.target.files?.[0];
    event.target.value = "";
    if (!file) return;
    if (!ACCEPTED_TYPES.includes(file.type)) {
      setError("Choose a JPG, PNG, or WEBP image.");
      return;
    }
    if (file.size > MAX_FILE_BYTES) {
      setError("The image must be 5 MB or smaller.");
      return;
    }
    setBusy(true);
    setError("");
    try {
      const dataUrl = await resizeImage(await readFile(file));
      onChange(dataUrl);
    } catch (uploadError) {
      setError(uploadError instanceof Error ? uploadError.message : "Could not process this image.");
    } finally {
      setBusy(false);
    }
  }

  return <div className="profile-photo-editor card border-0">
    <div className="card-body d-flex align-items-center gap-3 flex-wrap">
      <div className="profile-photo-preview" aria-label={`${label} preview`}>
        {image ? <img src={image} alt={`${name || "Account"} profile`} /> : <span>{initials || "D"}</span>}
      </div>
      <div className="profile-photo-copy flex-grow-1">
        <strong className="d-block">{label}</strong>
        <small className="text-secondary d-block">JPG, PNG or WEBP · Up to 5 MB. Images are resized before saving.</small>
        <div className="d-flex gap-2 flex-wrap mt-2">
          <button className="btn btn-sm btn-primary" type="button" disabled={disabled || busy} onClick={() => inputRef.current?.click()}>
            <i className="bi bi-camera me-1" aria-hidden="true" />{busy ? "Processing…" : image ? "Change photo" : "Upload photo"}
          </button>
          {image && <button className="btn btn-sm btn-outline-secondary" type="button" disabled={disabled || busy} onClick={() => { onChange(""); setError(""); }}>Remove</button>}
        </div>
        <input ref={inputRef} className="form-control d-none" type="file" accept="image/jpeg,image/png,image/webp" onChange={handleFile} disabled={disabled} aria-label={`Choose ${label.toLowerCase()}`} />
        {error && <div className="text-danger small mt-2" role="alert">{error}</div>}
      </div>
    </div>
  </div>;
}
