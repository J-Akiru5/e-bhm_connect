# E-BHM Connect Design System

## Design Philosophy
The E-BHM Connect design system is built on **Glassmorphism** principles combined with **Mobile-First** responsive design. Our goal is to create a modern, professional healthcare interface that is accessible, beautiful, and functional.

## Core Principles

### 1. Glassmorphism
- **Frosted Glass Effect**: Use `backdrop-filter: blur(20px)` for glass panels
- **Transparency Layers**: Background opacity between 0.05-0.15 for depth
- **Subtle Borders**: Use `rgba(255, 255, 255, 0.15)` for glass borders
- **Layered Depth**: Create visual hierarchy through overlapping glass panels

### 2. Color Palette

#### Primary Colors
- **Primary Green**: `#20c997` (rgb: 32, 201, 151)
- **Primary Dark**: `#0f5132`
- **Primary Light**: `#e6fffa`

#### Secondary Colors
- **Secondary**: `#6366f1` (Indigo)
- **Accent**: `#f59e0b` (Amber)

#### Grayscale
- **Dark**: `#0f172a`
- **Gray 900**: `#1e293b`
- **Gray 700**: `#475569`
- **Gray 500**: `#94a3b8`
- **Gray 300**: `#e2e8f0`
- **Gray 100**: `#f8fafc`
- **White**: `#ffffff`

### 3. Typography
- **Font Family**: Poppins (300, 400, 500, 600, 700, 800)
- **Base Size**: 16px
- **Scale**: 
  - xs: 0.75rem (12px)
  - sm: 0.875rem (14px)
  - base: 1rem (16px)
  - lg: 1.125rem (18px)
  - xl: 1.25rem (20px)
  - 2xl: 1.5rem (24px)
  - 3xl: 1.875rem (30px)
  - 4xl: 2.25rem (36px)

### 4. Spacing
- **Base Unit**: 4px
- **Scale**: 4px, 8px, 12px, 16px, 24px, 32px, 48px, 64px, 96px

### 5. Border Radius
- **sm**: 8px - Small elements (badges, tags)
- **md**: 12px - Buttons, inputs
- **lg**: 16px - Cards, modals
- **xl**: 24px - Large containers
- **2xl**: 32px - Hero sections
- **full**: 9999px - Pills, avatars

### 6. Shadows
```css
--shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
--shadow-md: 0 8px 24px rgba(0, 0, 0, 0.12);
--shadow-lg: 0 16px 48px rgba(0, 0, 0, 0.16);
--shadow-xl: 0 24px 64px rgba(0, 0, 0, 0.20);
```

## Component Guidelines

### Input Fields & Forms

#### Border Rules
1. **Default State**:
   - Border: `1px solid rgba(255, 255, 255, 0.15)` (glass border)
   - Background: `rgba(255, 255, 255, 0.08)` (semi-transparent)
   - Border Radius: `12px`
   - Padding: `12px 16px`

2. **Hover State**:
   - Border: `1px solid rgba(255, 255, 255, 0.25)`
   - Background: `rgba(255, 255, 255, 0.12)`

3. **Focus State**:
   - Border: `2px solid var(--primary)` or `rgba(32, 201, 151, 0.5)`
   - Background: `rgba(255, 255, 255, 0.15)`
   - Box Shadow: `0 0 0 4px rgba(32, 201, 151, 0.15)`
   - No outline

4. **Error State**:
   - Border: `1px solid #ef4444`
   - Box Shadow: `0 0 0 4px rgba(239, 68, 68, 0.15)`

5. **Disabled State**:
   - Background: `rgba(255, 255, 255, 0.03)`
   - Border: `1px solid rgba(255, 255, 255, 0.05)`
   - Opacity: 0.5
   - Cursor: not-allowed

#### Typography in Forms
- **Labels**: 
  - Font Weight: 500
  - Color: `rgba(255, 255, 255, 0.9)` on dark backgrounds
  - Font Size: 0.875rem (14px)
  - Margin Bottom: 8px

- **Input Text**:
  - Font Weight: 400
  - Color: `#ffffff` (white on dark) or `#1e293b` (dark on light)
  - Font Size: 1rem (16px)

- **Placeholder**:
  - Color: `rgba(255, 255, 255, 0.4)`
  - Font Style: normal

### Buttons

#### Primary Button
```css
background: linear-gradient(135deg, var(--primary), var(--primary-dark));
color: white;
border-radius: 12px;
padding: 12px 24px;
font-weight: 600;
box-shadow: 0 4px 16px rgba(32, 201, 151, 0.35);
transition: all 250ms ease;
```

**Hover**: `transform: translateY(-2px); box-shadow: 0 8px 24px rgba(32, 201, 151, 0.45);`

#### Secondary Button (Glass)
```css
background: rgba(255, 255, 255, 0.1);
border: 1px solid rgba(255, 255, 255, 0.15);
color: white;
backdrop-filter: blur(20px);
```

### Cards

#### Glass Card
```css
background: rgba(255, 255, 255, 0.08);
backdrop-filter: blur(20px);
border: 1px solid rgba(255, 255, 255, 0.15);
border-radius: 16px;
box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
```

**Hover**: `transform: translateY(-6px); box-shadow: 0 16px 48px rgba(0, 0, 0, 0.16);`

### Modals

#### Glass Modal Structure
```html
<!-- Overlay with blur backdrop -->
<div class="modal-overlay" id="exampleModal">
  <!-- Modal container -->
  <div class="modal-content">
    <!-- Header with title and close button -->
    <div class="modal-header">
      <h3 class="modal-title">
        <i class="fas fa-icon me-2" style="color: #20c997;"></i>
        Modal Title
      </h3>
      <button class="modal-close" onclick="closeModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <!-- Form content -->
    <form method="post" action="">
      <div class="modal-body">
        <!-- Form fields go here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary-glass" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn-primary-glass">
          <i class="fas fa-save"></i>
          Save
        </button>
      </div>
    </form>
  </div>
</div>
```

#### Modal Overlay
```css
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
  padding: 16px;
}

.modal-overlay.active {
  opacity: 1;
  visibility: visible;
}
```

#### Modal Content Container
```css
.modal-content {
  background: rgba(30, 41, 59, 0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 24px;
  width: 100%;
  max-width: 560px;
  max-height: 90vh;
  overflow-y: auto;
  transform: translateY(20px) scale(0.95);
  transition: all 0.3s ease;
  box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4);
}

.modal-overlay.active .modal-content {
  transform: translateY(0) scale(1);
}
```

#### Modal Header
```css
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 28px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #ffffff;
  margin: 0;
}

.modal-close {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: rgba(255, 255, 255, 0.7);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.modal-close:hover {
  background: rgba(239, 68, 68, 0.2);
  color: #ef4444;
}
```

#### Modal Body & Footer
```css
.modal-body {
  padding: 28px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 28px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}
```

#### Modal JavaScript
```javascript
// Open modal
function openModal() {
  document.getElementById('exampleModal').classList.add('active');
  document.body.style.overflow = 'hidden';
}

// Close modal
function closeModal() {
  document.getElementById('exampleModal').classList.remove('active');
  document.body.style.overflow = '';
}

// Close on outside click
document.getElementById('exampleModal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeModal();
});
```

### Glass Tables

#### Table Structure
```html
<div class="glass-card table-container">
  <table class="glass-table">
    <thead>
      <tr>
        <th>Column 1</th>
        <th>Column 2</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td data-label="Column 1">Value</td>
        <td data-label="Column 2">Value</td>
        <td data-label="Actions">
          <div class="actions-cell">
            <button class="btn-secondary-glass btn-sm-glass">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn-danger-glass btn-sm-glass">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

#### Table Styles
```css
.glass-table {
  width: 100%;
  border-collapse: collapse;
}

.glass-table thead th {
  padding: 16px 20px;
  text-align: left;
  font-weight: 600;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: rgba(255, 255, 255, 0.7);
  background: rgba(255, 255, 255, 0.05);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.glass-table tbody td {
  padding: 16px 20px;
  color: #ffffff;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  vertical-align: middle;
}

.glass-table tbody tr {
  transition: background 0.15s ease;
}

.glass-table tbody tr:hover {
  background: rgba(255, 255, 255, 0.05);
}
```

#### Responsive Table (Mobile)
```css
@media (max-width: 767px) {
  .glass-table thead { display: none; }
  
  .glass-table tbody tr {
    display: block;
    padding: 16px;
    margin-bottom: 12px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
  }
  
  .glass-table tbody td {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  }
  
  .glass-table tbody td::before {
    content: attr(data-label);
    font-weight: 600;
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.75rem;
    text-transform: uppercase;
  }
}
```

### Status Badges

#### Badge Styles
```css
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.75rem;
}

.status-badge.success { background: rgba(32, 201, 151, 0.2); color: #20c997; }
.status-badge.warning { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.status-badge.danger { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
.status-badge.info { background: rgba(99, 102, 241, 0.2); color: #6366f1; }
```

### Backgrounds

#### Animated Orb Background
Use 2-3 animated gradient orbs for dynamic backgrounds:

```css
.orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.6;
  animation: float 20s ease-in-out infinite;
}

.orb-1 {
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
  top: -10%;
  left: -10%;
}

.orb-2 {
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, var(--secondary) 0%, transparent 70%);
  bottom: -15%;
  right: -15%;
  animation-delay: -5s;
}
```

#### Gradient Overlays
- Use subtle gradients for depth: `linear-gradient(135deg, rgba(32, 201, 151, 0.1), rgba(99, 102, 241, 0.1))`

## Layout Patterns

### Full-Screen Auth Pages
```html
<div class="auth-fullscreen">
  <!-- Animated orbs -->
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  
  <!-- Glass card container -->
  <div class="auth-glass-card">
    <!-- Content -->
  </div>
</div>
```

### Responsive Breakpoints
- **Mobile**: < 768px (default, mobile-first)
- **Tablet**: >= 768px
- **Desktop**: >= 1024px
- **Wide**: >= 1280px

## Animation Guidelines

### Transitions
- **Fast**: 150ms - Micro-interactions (hover states)
- **Base**: 250ms - Standard transitions (buttons, links)
- **Slow**: 400ms - Complex animations (modals, drawers)

### Keyframes
```css
@keyframes float {
  0%, 100% { transform: translate(0, 0) scale(1); }
  33% { transform: translate(30px, -30px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
```

## Accessibility

### Focus Indicators
- Always visible focus rings for keyboard navigation
- Contrast ratio: 4.5:1 minimum for text
- Touch targets: 44x44px minimum

### Screen Readers
- Use semantic HTML
- ARIA labels where necessary
- Proper heading hierarchy

## Mobile-First Implementation

### Approach
1. Write base styles for mobile (< 768px)
2. Add `@media (min-width: 768px)` for tablet adjustments
3. Add `@media (min-width: 1024px)` for desktop enhancements

### Example
```css
.auth-card {
  width: 100%;
  padding: 24px;
}

@media (min-width: 768px) {
  .auth-card {
    width: 480px;
    padding: 48px;
  }
}
```

## Do's and Don'ts

### ✅ Do's
- Use glassmorphism for overlays and cards
- Implement smooth transitions (250ms)
- Maintain consistent spacing (4px grid)
- Use semantic HTML
- Test on mobile devices first
- Ensure proper color contrast

### ❌ Don'ts
- Don't use harsh borders (solid black/gray)
- Don't skip hover/focus states
- Don't use multiple font families
- Don't ignore mobile responsiveness
- Don't use default browser form styles
- Don't sacrifice accessibility for aesthetics

## Code Examples

### Complete Input Component
```html
<div class="form-group">
  <label for="email" class="form-label">Email Address</label>
  <input 
    type="email" 
    id="email" 
    class="glass-input" 
    placeholder="your.email@example.com"
    required
  >
</div>
```

```css
.glass-input {
  width: 100%;
  padding: 12px 16px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 12px;
  color: #ffffff;
  font-size: 1rem;
  transition: all 250ms ease;
  backdrop-filter: blur(20px);
}

.glass-input:hover {
  background: rgba(255, 255, 255, 0.12);
  border-color: rgba(255, 255, 255, 0.25);
}

.glass-input:focus {
  outline: none;
  background: rgba(255, 255, 255, 0.15);
  border: 2px solid var(--primary);
  box-shadow: 0 0 0 4px rgba(32, 201, 151, 0.15);
}

.glass-input::placeholder {
  color: rgba(255, 255, 255, 0.4);
}
```

---

**Version**: 1.0.0  
**Last Updated**: December 16, 2025  
**Maintainer**: E-BHM Connect Design Team
