# Theme Editor Wireframe

The following text-based wireframe outlines the planned UI/UX structure for the Theme Editor. Use this as a reference when working on the frontend implementation or when discussing layout with the design team.

---

## 1. Header Bar (Fixed Top)

```
┌──────────────────────────────────────────────────────────────────────────────┐
│ Theme Editor                                               [v1.2]            │
│ ┌ Page Selector ▼ ┐   [Preview: Desktop | Mobile]   [Edit Code] [Save] [Live]│
└──────────────────────────────────────────────────────────────────────────────┘
```

* Page selector: dropdown of templates (e.g., Index, Product Page, etc.)
* Version badge: top-right corner
* Device toggle: pill style toggle (only icon, active highlighted)
* Save buttons styled by importance (Save = Primary, Live = Green)

---

## 2. Sidebar – Page Sections (Sticky Left)

```
┌──────────────────────── Sidebar ───────────────────────┐
│ 🔍 [ Search sections and blocks... ]                  │
│                                                        │
│ ▾ HEADER                                               │
│   ┌──────────── Section: announcement-bar ───────────┐ │
│   │ ▸ Tile 1                                          │ │
│   │ ▸ Tile 2                                          │ │
│   └───────────────────────────────────────────────────┘ │
│   + (line between sections)                             │
│ ▾ TEMPLATE                                              │
│   ┌──────────── Section: testimonial-slider ──────────┐ │
│   │ ▸ Tile A                                          │ │
│   │ ▸ Tile B                                          │ │
│   └───────────────────────────────────────────────────┘ │
│   + (line between sections)                             │
│                                                        │
│ Version History ▼                                      │
└────────────────────────────────────────────────────────┘
```

* Arrow toggles section expand/collapse
* Tiles hidden/shown per section state
* `+` line shown on hover between sections or tiles
* Sections and tiles are reorderable by drag
* On hover: icon buttons for delete and duplicate

---

## 3. Live Preview (Center Top Pane)

```
┌──────────────────────────── Live Preview ─────────────────────────────┐
│ [Device frame with iframe]                                            │
│ ┌──────────────────────────────────────────────────────────────────┐ │
│ │                Your Store - Real-Time Preview                    │ │
│ └──────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────┘
```

* Actual theme shown inside iframe
* Responsive to device toggle
* Always reflects latest change in schema panel

---

## 4. Schema Panel (Right or Slide-In Panel)

```
┌──────────── Customizer Panel ─────────────┐
│ ← Back to Section                         │
│ Title: Tile Name / Section Name           │
│ ┌ Tabs: [Content] [Style]                ┐ │
│ │                                        │ │
│ │ Label                                  │ │
│ │ [ Text input ]                         │ │
│ │ Label                                  │ │
│ │ [ Image Upload ]                       │ │
│ │ Label                                  │ │
│ │ [ Dropdown ]                           │ │
│ └────────────────────────────────────────┘ │
│                                            │
│         [Save Block]     [Reset]           │
└────────────────────────────────────────────┘
```

* Panel loads when section or tile is clicked
* Tabs separate Content and Style
* Inputs with rounded styling and focus effects
* File upload shows thumbnail preview
* Save button fixed or sticky at the bottom

---

## 5. Add Modal (Triggered by `+` line)

```
┌────────────── Add Section ──────────────┐
│ 🔍 Search sections...                   │
│                                          │
│ Categories: [All] [Hero] [Product] ...   │
│                                          │
│ ┌──────────── Card ────────────┐         │
│ │ [Image] Section Name         │         │
│ └──────────────────────────────┘         │
│                                          │
└──────────────────────────────────────────┘
```

* Reusable for both sections and tiles
* Card shows thumbnail with name
* Clicking a card inserts instantly

---

## 6. Version History Dropdown

```
┌─── Version History ───┐
│ v1.2 - July 18, 2025  │
│ v1.1 - July 17, 2025  │
│ v1.0 - July 15, 2025  │
└───────────────────────┘
```

* Collapsed by default
* Can be used for optional rollback

---

## 7. Toast Notification (Feedback System)

```
[✓ Changes saved successfully]  ⏱  (auto-hide after 3s)
```

* Appears bottom left
* Types: success, warning, error

---

## Summary Hierarchy View

```
Header
├── Page Dropdown
├── Device Toggle
├── Buttons: Edit Code | Save | Live
├── Version Badge

Sidebar
├── Search Bar
├── Section Group (collapsible)
│   └── Section Items
│       └── Tile Items
├── Add Section (+ hover line)
└── Version History

Main Panel
├── Live Preview (iframe)
└── Schema Panel (right slide-in)
    ├── Title + Back Button
    ├── Tabs: Content / Style
    ├── Inputs, Uploads, Toggles
    └── Save Block / Reset

Floating
├── Add Modal (section/tile)
└── Toast Notification
```

