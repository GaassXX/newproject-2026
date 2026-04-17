# 📋 Blade Views Architecture Documentation

## 📁 Complete File Structure

```
resources/views/
├── layout/                          # Layout templates (extends)
│   ├── app.blade.php               # Main navigation layout
│   ├── sidebar.blade.php           # Dashboard sidebar layout
│   └── auth.blade.php              # Authentication layout
│
├── components/                     # Reusable UI components (includes)
│   ├── input.blade.php            # Form input field
│   ├── select.blade.php           # Select dropdown
│   ├── textarea.blade.php         # Textarea field
│   ├── button.blade.php           # Button with variants
│   ├── alert.blade.php            # Alert messages
│   ├── card.blade.php             # Card container
│   └── table.blade.php            # Table wrapper
│
├── dashboard/
│   └── index.blade.php            # Dashboard main page
│
├── auth/
│   ├── login.blade.php            # Login page
│   └── register.blade.php         # Register page
│
├── trades/
│   ├── index.blade.php            # Trades list view
│   ├── form.blade.php             # Create/Edit form
│   └── show.blade.php             # Trade detail page
│
└── welcome.blade.php              # Landing page
```

## 🎨 Styling & Framework
- **CSS Framework**: Tailwind CSS v3
- **Color Scheme**: Blue primary (#3B82F6), Gray neutral
- **Icons**: Inline SVG (Heroicons style)
- **Responsive**: Mobile-first design

## 🔧 Component API

### Form Input
```blade
<x-input 
    name="field"
    label="Label"
    type="text|email|number"
    placeholder="..."
    value="old value"
    required
/>
```

### Select
```blade
<x-select 
    name="field"
    label="Label"
    :options="['value' => 'Label']"
    value="selected-value"
    required
/>
```

### Button
```blade
<x-button 
    type="submit"
    variant="primary|secondary|danger|success|outline"
    size="sm|md|lg"
>
    Button Text
</x-button>
```

### Alert
```blade
<x-alert type="success|error|warning|info" dismissible>
    Alert message
</x-alert>
```

### Card
```blade
<x-card title="Title" icon="{{ $icon }}">
    Content
</x-card>
```

## 📄 Page Views Details

### Dashboard (`dashboard/index.blade.php`)
- **Layout**: `layout.sidebar`
- **Purpose**: Main overview page
- **Sections**:
  - 4 stat cards (Total Trades, P/L, Win Rate, Daily Limit)
  - Performance chart placeholder
  - Top trades widget
  - Recent trades table
- **Variables Expected**: `$totalTrades`, `$totalProfit`, `$winRate`, `$dailyLimit`, `$topTrades`, `$recentTrades`

### Trades List (`trades/index.blade.php`)
- **Layout**: `layout.sidebar`
- **Purpose**: Display all trades with filtering & pagination
- **Features**:
  - Search by symbol
  - Filter by status
  - Sortable columns
  - Profit/loss badge coloring
  - Pagination support
- **Variables Expected**: `$trades` (Paginated collection)

### Trade Form (`trades/form.blade.php`)
- **Layout**: `layout.sidebar`
- **Purpose**: Create or edit trade
- **Fields**:
  - Symbol (text, required)
  - Entry Price (number, required)
  - Exit Price (number, optional)
  - Quantity (number, required)
  - Type (long/short select)
  - Status (open/closed, edit only)
  - Notes (textarea)
- **Variables Expected**: `$trade` (null for create, Trade model for edit)

### Trade Show (`trades/show.blade.php`)
- **Layout**: `layout.sidebar`
- **Purpose**: Display single trade details
- **Sections**:
  - Trade information grid
  - Profit/Loss analysis
  - Metadata sidebar (dates, status)
  - Action buttons
- **Variables Expected**: `$trade` (Trade model instance)

### Login (`auth/login.blade.php`)
- **Layout**: `layout.auth`
- **Purpose**: User authentication
- **Fields**: Email, Password, Remember Me checkbox
- **Routes**: POST to `route('login')`

### Register (`auth/register.blade.php`)
- **Layout**: `layout.auth`
- **Purpose**: New user registration
- **Fields**: Name, Email, Password, Password Confirmation, Terms agreement
- **Routes**: POST to `route('register')`

## 🚀 Usage Examples

### Extending a Layout
```blade
@extends('layout.sidebar')

@section('title', 'Page Title')
@section('content')
    <!-- Your content -->
@endsection
```

### Using Components in Forms
```blade
<form action="{{ route('store') }}" method="POST">
    @csrf
    
    <x-input name="email" label="Email" type="email" required />
    <x-select name="status" label="Status" :options="$statusOptions" />
    <x-textarea name="notes" label="Notes" :rows="4" />
    
    <x-button type="submit">Submit</x-button>
</form>
```

### Flash Messages in Blade
```blade
@if (session('success'))
    <x-alert type="success">{{ session('success') }}</x-alert>
@endif
```

## 🔄 Data Flow

```
Route Controller → View (extends Layout)
                        ↓
                   @section('content')
                        ↓
                   Use x-components
                        ↓
                   Include partials
                        ↓
                   Render HTML
```

## 🎯 Key Features Implemented

✅ **Responsive Design** - Works on mobile, tablet, desktop
✅ **Form Validation** - Server-side error display
✅ **Authentication** - User login/register pages
✅ **CRUD Operations** - Full trades management UI
✅ **Component System** - DRY, reusable UI elements
✅ **Consistent Styling** - Tailwind CSS throughout
✅ **Accessibility** - Proper labels, semantic HTML
✅ **Error Handling** - Form errors, flash messages
✅ **Data Formatting** - Currency, dates, status badges

## 📝 Best Practices

1. **Always use components** for form fields instead of writing HTML
2. **Extend the correct layout** - Use `sidebar` for admin/dashboard, `app` for public, `auth` for login/register
3. **Pass variables explicitly** - Don't rely on globals, pass data from controller
4. **Use old() helper** - For repopulating form fields on validation errors
5. **Handle empty states** - Use `@forelse` for lists that might be empty
6. **Consistent naming** - Use kebab-case for component names

## 🔗 Related Models

Based on the workspace, the following models exist:
- `User` - User authentication
- `Trade` - Trading transactions
- `DailyLimit` - Trading limits per day
- `Screenshot` - Trade screenshots
- `Tag` - Trade tags

## 📌 Next Steps (Ready to Build)

1. **Daily Limits Views** - Create CRUD views for DailyLimit
2. **Screenshots Views** - Create gallery/list for Trade screenshots
3. **Tags Management** - Create tag management interface
4. **Advanced Filtering** - Add date range, profit range filters
5. **Charts & Analytics** - Integrate charting library (Chart.js, etc.)
6. **Export/Reports** - CSV export, PDF reports
7. **Settings Page** - User preferences, API keys, notifications
8. **Mobile Menu** - Hamburger menu for mobile sidebar
