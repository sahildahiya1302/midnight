# Admin Themes Section Redesign

The following plan outlines a comprehensive UI/UX update for the `themes/` portion of the admin panel. Use these guidelines when refining the interface or discussing design requirements.

---

## Color Scheme

| Role          | Color       |
| ------------- | ----------- |
| Primary       | `#008060`   |
| Accent        | `#00a86b`   |
| Text Dark     | `#202223`   |
| Text Light    | `#6b7280`   |
| Background    | `#f6f6f7`   |
| Panel Surface | `#ffffff`   |
| Borders       | `#d2d5d8`   |
| Danger        | `#dc3545`   |
| Info          | `#0dcaf0`   |

---

## `themes/themes.php` — All Themes

```
Header: Theme Manager           [ + Add New Theme ] [ Import ▼ ]

───────────────────────────────────────────────────────────────
|  🎨  "Summer 2025"                     [ Live ]
|       Last edited: Jul 15
|       [Customize] [Rename] [Duplicate] [Delete]
|
|  🎨  "Diwali V1"
|       Draft – not published
|       [Customize] [Rename] [Set Live] [Delete]
───────────────────────────────────────────────────────────────
```

### UX Details

| Element        | Details                                          |
| -------------- | ------------------------------------------------ |
| Theme Card     | Title, status badge (Live, Draft), preview image |
| Action Buttons | Horizontal row of contextual actions             |
| Set Live       | Single click → auto reload preview               |
| Rename         | Inline input + save icon                         |
| Add New        | Modal with base templates or blank theme option  |
| Import         | CSV/ZIP with validation (file type, structure)   |

---

## `themes/theme-editor.php` — Visual Editor

This is the **heart of customization**. It should feel like Shopify or Webflow but lighter.

```
┌────────────── Sidebar (Left) ────────────────┐    ┌──────── Preview ────────┐
│ 📄 Sections (Homepage)                       │    │  [Device Toggle]        │
│   ▾ Header                                   │    │                          │
│     • Announcement bar                       │    │                          │
│     • Navigation                             │    │     [Live Preview]       │
│   ▾ Template                                 │    │                          │
│     • Hero slider                            │    │                          │
│     • Product grid                           │    │                          │
│                                              │    └─────────────────────────┘
│ + Add Section                                │
│                                              │
│ Theme Settings • Global Styles               │
└──────────────────────────────────────────────┘
```

### Functional Features

| Feature                 | UX Behavior                                          |
| ----------------------- | ---------------------------------------------------- |
| Section Collapse/Expand | Arrow toggle + chevron animation                     |
| Drag to Reorder         | Grab handle on each section and block                |
| Live Preview Panel      | Right-aligned, with desktop/mobile toggle            |
| Add Section (+)         | Appears between sections on hover, loads modal       |
| Section Schema          | Clicking a section opens settings drawer             |
| Global Settings         | Button opens fixed overlay for fonts, colors, layout |

### Schema Panel (Slide-in Right)

```
┌──────────────── Schema Settings ───────────────┐
│ ← Back to Sections                            │
│                                               │
│ [Text] Homepage Headline                      │
│ [Image] Upload Desktop Banner                 │
│ [Image] Upload Mobile Banner                  │
│ [Select] Banner Style ▼                       │
│ [Toggle] Show Button                          │
│ [Button Text] "Shop Now"                      │
│                                               │
│                [ Save Changes ]               │
└───────────────────────────────────────────────┘
```

| Feature      | Description                                         |
| ------------ | --------------------------------------------------- |
| Field Types  | Text, toggle, upload, color picker, select dropdown |
| Auto Preview | Changes instantly reflected in iframe               |
| Save Button  | Shows success toast on click                        |
| Scroll Secs  | Sticky section name header                          |

---

## `themes/code-editor.php` — Code Editor

```
┌ File Browser (Left) ┐   ┌────────── Code Panel ─────────────┐
│ sections/           │   │ Filename: hero-slider.liquid       │
│ ├ hero-slider.php   │   │ ┌──── Tabs: [Liquid] [CSS] [JS] ┐ │
│ ├ product-grid.php  │   │ │                                │ │
│ assets/             │   │ │   [Monaco Editor]               │ │
│ ├ global.css        │   │ │                                │ │
│ └───────────────────┘   └──────────────────────────────────┘
```

### Features

| Feature       | Description                                    |
| ------------- | ---------------------------------------------- |
| Monaco Editor | Syntax highlighting, line numbers, error hints |
| File Tree     | Nested, searchable                             |
| Tabs          | Switch views if applicable                     |
| Save Button   | Confirm dialog + success feedback              |
| Reset/Undo    | Button for reverting to last saved version     |

---

## Extra Enhancements (Global)

| Feature         | UX Value                                       |
| --------------- | ---------------------------------------------- |
| Toasts          | "Changes saved successfully" / "Upload failed" |
| Shimmer loading | While preview or files load                    |
| Sticky Buttons  | Save button at bottom of schema panel          |
| Shortcut Keys   | `Ctrl + S` = save code / theme layout          |
| Dark Mode       | Optional toggle with localStorage save         |

---

## Summary Table

| File               | Purpose                            | UX Focus                          |
| ------------------ | ---------------------------------- | --------------------------------- |
| `themes.php`       | Theme management                   | Clean list view + actions         |
| `theme-editor.php` | Visual editor (drag/drop + schema) | Real-time preview + mobile toggle |
| `code-editor.php`  | Developer mode editor              | Monaco interface + file tree      |

