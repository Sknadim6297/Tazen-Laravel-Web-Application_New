# Tazen Design System

This document describes the Tailwind CSS 4 foundation in `resources/css/tazen.css`. The entry is intentionally not included by any Blade layout yet.

## Palette

- **Primary:** `brand-primary` (`#006b67`), a contrast-safe deepened version of Tazen's existing teal (`#039d99`). Use the lighter `brand-500` for non-text accents, not small text on white.
- **Secondary:** `brand-secondary` (`#1f2f6a`), reflecting the existing navy identity.
- **Accent:** `brand-accent` (`#a22c43`), a restrained warm red drawn from the logo's coral/orange character.
- **Surfaces:** white `surface`, pale cool `background` and `surface-muted`.
- **Text and structure:** `text-primary`, `text-secondary`, `text-muted`, `border`, and `divider`.
- **Feedback:** `success`, `warning`, `error`, and `info`, each paired with a `*-surface` token.

Use the dark brand and state colors for text and controls. Their foreground contrast against white is at least 4.5:1; reserve lighter brand shades for large areas, decoration, or paired dark text.

## Typography

The isolated Tazen v2 layout loads Poppins weights 400, 500, 600, and 700 from Google Fonts. `font-sans` and `font-display` use Poppins with platform sans-serif fallbacks if the font resource is unavailable. Legacy layouts do not load this font through the Tazen v2 entry. Use `text-display`, `text-page-title`, `text-section-title`, and `text-card-title` for the named heading levels; use Tailwind's standard text-size utilities for body copy and metadata.

## Spacing and Shape

The spacing base is 0.25rem (4px). Prefer multiples of that scale; use compact 4/8/12px gaps for controls and 24/32/48px for larger sections. Use `radius-control`, `radius-card`, and `radius-panel` consistently, and reserve `shadow-card` and `shadow-popover` for actual elevation rather than decoration.

## Components

Build shared Blade components for repeated controls and surfaces, keeping markup semantic and behavior-specific JavaScript separate. Consume tokens through utilities such as `bg-brand-primary`, `text-text-primary`, `border-border`, `rounded-card`, and `shadow-card`. Keep component variants small and explicit; do not recreate the same color or spacing values as arbitrary utilities.

## Responsive Layout

Use Tailwind's mobile-first defaults: `sm` 40rem, `md` 48rem, `lg` 64rem, `xl` 80rem, and `2xl` 96rem. Add a custom breakpoint only when a real content constraint requires it.

## Accessibility

- Preserve a visible keyboard focus outline using the `focus-ring` token; do not remove outlines without an equally visible replacement.
- Maintain at least 4.5:1 contrast for normal text and 3:1 for large text and meaningful UI boundaries.
- Pair status colors with text or icons; never communicate state by color alone.
- Use semantic elements, associated labels, accessible names, and logical heading order in Blade components.
- Respect reduced-motion preferences when adding transitions or animation.