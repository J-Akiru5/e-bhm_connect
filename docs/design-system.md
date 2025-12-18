# E-BHM Connect Design System v2.0

## Design Philosophy
The E-BHM Connect design system is built on **Glassmorphism** principles combined with **Mobile-First** responsive design and a **Unified Color Approach** for professional consistency.

---

## 🎨 Unified Color Philosophy

> **Core Principle**: Use a single primary color (Teal/Green) for all interactive UI elements to create a cohesive, professional appearance. Reserve distinct colors ONLY for semantic meaning.

### Primary Interactive Color
**All buttons, links, form focus states, icons, and interactive elements use the Primary Color.**

| Element | Color | Usage |
|---------|-------|-------|
| **Primary** | `#20c997` | Buttons, links, focus rings, icons, active states |
| **Primary Dark** | `#0f5132` | Gradients, hover states |
| **Primary Light** | `rgba(32, 201, 151, 0.1)` | Hover backgrounds, subtle highlights |

### Exception Colors (Semantic Only)
**Only use these colors when there is clear semantic meaning:**

| Semantic | Color | When to Use |
|----------|-------|-------------|
| **Danger** | `#ef4444` (Red) | Delete, remove, cancel destructive actions |
| **Warning** | `#f59e0b` (Amber) | Warnings, low stock alerts, caution notices |
| **Success** | `#10b981` (Green) | Success messages, completed status |
| **Info** | `#3b82f6` (Blue) | Information notices, help tooltips |

### ❌ What NOT to Do
- Don't use secondary colors (indigo, purple, etc.) for general buttons
- Don't mix button colors arbitrarily - stick to primary
- Don't use different colors just for visual variety on buttons

---

## Color Palette

### Primary Colors
```css
--primary: #20c997;           /* Main interactive color */
--primary-dark: #0f5132;      /* Gradient end, hover */
--primary-light: #e6fffa;     /* Light backgrounds */
--primary-rgb: 32, 201, 151;  /* For rgba() */
```

### Semantic Colors (Use Sparingly)
```css
--success: #10b981;   /* Success states */
--warning: #f59e0b;   /* Warning states */
--danger: #ef4444;    /* Destructive actions ONLY */
--info: #3b82f6;      /* Informational */
```

### Grayscale
```css
--dark: #0f172a;      /* Darkest */
--gray-900: #1e293b;
--gray-700: #475569;
--gray-500: #94a3b8;
--gray-300: #e2e8f0;
--gray-100: #f8fafc;
--white: #ffffff;
```

---

## Button Guidelines

### Standard Button (Primary) ✅ USE THIS
All general action buttons use the primary color:
```css
.btn-primary {
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  color: white;
  border: none;
  border-radius: 12px;
  padding: 12px 24px;
  font-weight: 600;
  box-shadow: 0 4px 16px rgba(32, 201, 151, 0.35);
}
```

**Use for**: Save, Submit, Add, Create, Update, Confirm, Continue, Next, Apply

### Glass Button (Secondary Actions)
For less prominent actions, use transparent glass style but KEEP primary color accents:
```css
.btn-glass {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: var(--text-primary);
  border-radius: 12px;
}

.btn-glass:hover {
  background: rgba(32, 201, 151, 0.1);   /* Primary tint on hover */
  border-color: var(--primary);
  color: var(--primary);
}
```

**Use for**: Cancel, Close, View More, Secondary navigation

### Danger Button (EXCEPTION)
Only for destructive actions:
```css
.btn-danger {
  background: linear-gradient(135deg, #ef4444, #b91c1c);
  color: white;
  box-shadow: 0 4px 16px rgba(239, 68, 68, 0.35);
}
```

**Use ONLY for**: Delete, Remove, Deactivate, Revoke, Reject

### Success Button (EXCEPTION)
Only for explicit approval actions:
```css
.btn-success {
  background: linear-gradient(135deg, #10b981, #047857);
  color: white;
}
```

**Use ONLY for**: Approve, Verify, Activate (when contrasting with reject)

---

## Form Elements

### Input Fields
Focus state uses primary color:
```css
.form-control:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 4px rgba(32, 201, 151, 0.15);
}
```

### Checkboxes & Radio Buttons
Use primary color when checked:
```css
input[type="checkbox"]:checked {
  background-color: var(--primary);
  border-color: var(--primary);
}
```

### Toggle Switches
Active state uses primary:
```css
.toggle-switch.active {
  background-color: var(--primary);
}
```

---

## Icons & Links

### Interactive Icons
All clickable icons use primary color:
```css
.icon-btn {
  color: var(--primary);
}

.icon-btn:hover {
  color: var(--primary-dark);
  background: rgba(32, 201, 151, 0.1);
}
```

### Text Links
```css
a {
  color: var(--primary);
}

a:hover {
  color: var(--primary-dark);
}
```

---

## Quick Action Buttons

All quick action buttons use unified styling:
```css
.quick-action-btn {
  background: var(--glass-bg);
  border: 1px solid var(--border-color);
  color: var(--text-primary);
}

.quick-action-btn svg {
  color: var(--primary);  /* Icon always primary */
}

.quick-action-btn:hover {
  background: rgba(32, 201, 151, 0.1);
  border-color: var(--primary);
  color: var(--primary);
}
```

---

## Status Badges

Use colors only for semantic meaning:
```css
.badge-active, .badge-approved { background: rgba(16, 185, 129, 0.2); color: #10b981; }
.badge-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.badge-inactive, .badge-rejected { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
.badge-info { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
```

---

## Component Reference

### Glass Card
```css
.glass-card {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 16px;
}
```

### Typography
- **Font**: Poppins (300, 400, 500, 600, 700)
- **Base Size**: 16px
- **Scale**: 0.75rem, 0.875rem, 1rem, 1.125rem, 1.25rem, 1.5rem, 1.875rem

### Spacing
- **Base Unit**: 4px
- **Scale**: 4, 8, 12, 16, 24, 32, 48, 64px

### Border Radius
- **sm**: 8px (badges)
- **md**: 12px (buttons, inputs)
- **lg**: 16px (cards)
- **xl**: 24px (modals)

### Transitions
- **Fast**: 150ms (hover)
- **Base**: 250ms (standard)
- **Slow**: 400ms (modals)

---

## Summary Cheat Sheet

| Component | Color Rule |
|-----------|-----------|
| **All Buttons** | Primary green (except delete = red) |
| **Form Focus** | Primary green ring |
| **Links** | Primary green |
| **Icons** | Primary green |
| **Hover States** | Primary green tint |
| **Delete/Remove** | Danger red |
| **Status Badges** | Semantic colors |

---

**Version**: 2.0.0  
**Last Updated**: December 18, 2025  
**Maintainer**: E-BHM Connect Design Team
