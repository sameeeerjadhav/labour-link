# Bootstrap 5.3.3 Integration Guide

## ✅ Bootstrap is Now Fully Integrated

All pages in your LabourLink application now have:
- **Bootstrap 5.3.3 CSS** - For styling and layout
- **Bootstrap 5.3.3 JS Bundle** - For interactive components
- **Font Awesome 6.5.0** - For icons

## 📦 What's Included

### CSS Framework
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
```

### JavaScript Bundle (includes Popper.js)
```html
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
```

## 🎨 Bootstrap Components You Can Use

### 1. Grid System
```html
<div class="container">
  <div class="row">
    <div class="col-md-6">Column 1</div>
    <div class="col-md-6">Column 2</div>
  </div>
</div>
```

### 2. Buttons
```html
<button class="btn btn-primary">Primary</button>
<button class="btn btn-success">Success</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-outline-primary">Outline</button>
```

### 3. Cards
```html
<div class="card">
  <div class="card-body">
    <h5 class="card-title">Card Title</h5>
    <p class="card-text">Card content</p>
  </div>
</div>
```

### 4. Alerts
```html
<div class="alert alert-success" role="alert">
  Success message!
</div>
<div class="alert alert-danger" role="alert">
  Error message!
</div>
```

### 5. Forms
```html
<div class="mb-3">
  <label for="email" class="form-label">Email</label>
  <input type="email" class="form-control" id="email">
</div>

<div class="mb-3">
  <select class="form-select">
    <option>Choose...</option>
    <option>Option 1</option>
  </select>
</div>
```

### 6. Modals
```html
<!-- Button trigger -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
  Open Modal
</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal Title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Modal content here
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>
```

### 7. Toasts (Notifications)
```html
<div class="toast" role="alert">
  <div class="toast-header">
    <strong class="me-auto">Notification</strong>
    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
  </div>
  <div class="toast-body">
    Your message here
  </div>
</div>

<script>
// Show toast
const toast = new bootstrap.Toast(document.querySelector('.toast'));
toast.show();
</script>
```

### 8. Dropdowns
```html
<div class="dropdown">
  <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
    Dropdown
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Action</a></li>
    <li><a class="dropdown-item" href="#">Another action</a></li>
  </ul>
</div>
```

### 9. Badges
```html
<span class="badge bg-primary">Primary</span>
<span class="badge bg-success">Success</span>
<span class="badge bg-danger">Danger</span>
```

### 10. Spinners
```html
<div class="spinner-border text-primary" role="status">
  <span class="visually-hidden">Loading...</span>
</div>

<div class="spinner-grow text-success" role="status">
  <span class="visually-hidden">Loading...</span>
</div>
```

## 🎯 Utility Classes

### Spacing
```html
<!-- Margin -->
<div class="m-3">Margin all sides</div>
<div class="mt-3">Margin top</div>
<div class="mb-3">Margin bottom</div>
<div class="mx-auto">Margin horizontal auto (center)</div>

<!-- Padding -->
<div class="p-3">Padding all sides</div>
<div class="pt-3">Padding top</div>
<div class="pb-3">Padding bottom</div>
```

### Display
```html
<div class="d-none">Hidden</div>
<div class="d-block">Block</div>
<div class="d-flex">Flexbox</div>
<div class="d-grid">Grid</div>

<!-- Responsive -->
<div class="d-none d-md-block">Hidden on mobile, visible on tablet+</div>
```

### Text
```html
<p class="text-start">Left aligned</p>
<p class="text-center">Center aligned</p>
<p class="text-end">Right aligned</p>

<p class="text-primary">Primary color</p>
<p class="text-success">Success color</p>
<p class="text-danger">Danger color</p>

<p class="fw-bold">Bold text</p>
<p class="fw-normal">Normal text</p>
<p class="fst-italic">Italic text</p>
```

### Colors
```html
<!-- Background -->
<div class="bg-primary text-white">Primary background</div>
<div class="bg-success text-white">Success background</div>
<div class="bg-light">Light background</div>

<!-- Text -->
<p class="text-primary">Primary text</p>
<p class="text-success">Success text</p>
<p class="text-danger">Danger text</p>
```

### Flexbox
```html
<div class="d-flex justify-content-between align-items-center">
  <div>Left</div>
  <div>Right</div>
</div>

<div class="d-flex flex-column">
  <div>Item 1</div>
  <div>Item 2</div>
</div>
```

## 📱 Responsive Breakpoints

Bootstrap uses these breakpoints:
- `xs` - Extra small (< 576px) - Mobile
- `sm` - Small (≥ 576px) - Mobile landscape
- `md` - Medium (≥ 768px) - Tablet
- `lg` - Large (≥ 992px) - Desktop
- `xl` - Extra large (≥ 1200px) - Large desktop
- `xxl` - Extra extra large (≥ 1400px) - Extra large desktop

### Example Usage
```html
<!-- Different columns on different screens -->
<div class="col-12 col-md-6 col-lg-4">
  Full width on mobile, half on tablet, third on desktop
</div>

<!-- Hide/show on different screens -->
<div class="d-none d-md-block">
  Hidden on mobile, visible on tablet+
</div>
```

## 🚀 Quick Tips

1. **Use Bootstrap Grid** for responsive layouts instead of custom CSS
2. **Use Bootstrap Utilities** for spacing, colors, and display
3. **Use Bootstrap Components** for modals, dropdowns, and alerts
4. **Combine with Custom CSS** - Your custom styles will override Bootstrap
5. **Mobile-First** - Bootstrap is mobile-first, so design for mobile first

## 📖 Resources

- **Official Docs**: https://getbootstrap.com/docs/5.3/
- **Examples**: https://getbootstrap.com/docs/5.3/examples/
- **Icons**: https://fontawesome.com/icons
- **Cheat Sheet**: https://bootstrap-cheatsheet.themeselection.com/

## 💡 Example: Improve Your Forms

Instead of custom form styling, use Bootstrap:

```html
<form>
  <div class="mb-3">
    <label for="jobTitle" class="form-label">Job Title</label>
    <input type="text" class="form-control" id="jobTitle" required>
  </div>
  
  <div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control" id="description" rows="3"></textarea>
  </div>
  
  <div class="mb-3">
    <label for="wage" class="form-label">Daily Wage</label>
    <div class="input-group">
      <span class="input-group-text">₹</span>
      <input type="number" class="form-control" id="wage">
    </div>
  </div>
  
  <button type="submit" class="btn btn-success w-100">
    <i class="fa-solid fa-check me-2"></i>Submit
  </button>
</form>
```

## 🎨 Color Customization

Your app uses custom colors. You can create Bootstrap-compatible classes:

```css
/* Farmer theme (green) */
.btn-farmer {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  border: none;
}

.text-farmer {
  color: #10b981;
}

.bg-farmer {
  background-color: #10b981;
}

/* Labour theme (orange) */
.btn-labour {
  background: linear-gradient(135deg, #f97316, #ea580c);
  color: white;
  border: none;
}

.text-labour {
  color: #f97316;
}

.bg-labour {
  background-color: #f97316;
}
```

---

**Bootstrap is now fully integrated and ready to use throughout your application!**
