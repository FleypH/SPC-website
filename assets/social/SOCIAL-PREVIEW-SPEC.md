# Shield Point Capital — Social Preview Image Spec

Default Open Graph / Twitter Card image for sitewide link previews (WhatsApp, Facebook, LinkedIn, X, iMessage).

## Deliverable file

| Property | Value |
|----------|-------|
| **Filename** | `og-image.png` |
| **Path** | `assets/social/og-image.png` |
| **Public URL** | `https://shieldpointcapital.co.zw/assets/social/og-image.png` |
| **Format** | PNG (preferred) or JPEG |
| **Dimensions** | **1200 × 630 px** (required) |
| **Aspect ratio** | **1.91:1** |
| **Safe zone** | Keep logo and headline inside **1120 × 580 px** center area (40 px margin) |
| **Max file size** | Under **300 KB** (aim for 150–200 KB) |
| **Color profile** | sRGB |

## Platform requirements

| Platform | Recommended size | Notes |
|----------|------------------|-------|
| Facebook / LinkedIn | 1200 × 630 | Primary target |
| WhatsApp | 1200 × 630 | Uses Open Graph |
| X (Twitter) | 1200 × 630 | `summary_large_image` if upgraded later |
| Telegram / Slack | 1200 × 630 | OG fallback |

## Brand palette

| Role | Hex | Usage |
|------|-----|-------|
| Primary green | `#006c49` | Accent bar, subtitle, highlights |
| Navy | `#0b1c30` | Headline, dark panels |
| Cream | `#FAF9F6` | Main background |
| Soft mint | `#E8F4F0` | Secondary panel / gradient |
| White | `#FFFFFF` | Logo backdrop, contrast text |
| Muted text | `#555555` | Tagline only |

**Typography:** Montserrat (Bold 700–800 for headline, Medium 500–600 for subtitle, Regular 400 for tagline).

## Layout (1200 × 630)

```
┌──────────────────────────────────────────────────────────────┐
│ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ │  3px green top accent (#006c49)
├──────────────┬───────────────────────────────────────────────┤
│              │  SHIELD POINT CAPITAL                          │
│   [Logo]     │  FinEra Inclusive Credit Marketplace           │
│   ~200px     │  Building Financial Access for Everyone        │
│   on mint    │                                                │
│   panel      │  (optional: thin green rule under subtitle)    │
│   ~380px     │                                                │
│   wide       │                                                │
└──────────────┴───────────────────────────────────────────────┘
```

### Content hierarchy

1. **Logo** — `assets/main/spclogo.png`, max height ~120 px, clear space around it
2. **Headline** — `SHIELD POINT CAPITAL` — 48–56 px, navy, uppercase or title case
3. **Subtitle** — `FinEra Inclusive Credit Marketplace` — 28–32 px, green
4. **Tagline** — `Building Financial Access for Everyone` — 20–22 px, muted gray

### Do

- Use flat, square corners (matches site `--radius: 0`)
- Keep text left-aligned on the right panel
- Ensure strong contrast for mobile thumbnail legibility
- Test preview at **400 px wide** (how most phones show link cards)

### Avoid

- Small text below 18 px
- Busy photography behind text
- Putting critical copy in outer 40 px edges (may crop on some apps)
- Relying on transparency (use solid PNG background)

## Production workflow

1. Open `assets/social/og-template.html` in Chrome at 100% zoom.
2. Export at exactly **1200 × 630**:
   - **Option A:** DevTools → screenshot the `#og-canvas` node
   - **Option B:** Print to PDF then export frame (less ideal)
   - **Option C:** Recreate in Figma/Canva using this spec
3. Save as `assets/social/og-image.png`
4. Compress with [Squoosh](https://squoosh.app) or TinyPNG if over 300 KB
5. Verify with [Facebook Sharing Debugger](https://developers.facebook.com/tools/debug/) and [LinkedIn Post Inspector](https://www.linkedin.com/post-inspector/)

## Sitewide meta tags (already configured)

All HTML pages reference:

```html
<meta property="og:image" content="https://shieldpointcapital.co.zw/assets/social/og-image.png" />
<meta name="twitter:image" content="https://shieldpointcapital.co.zw/assets/social/og-image.png" />
```

Optional additions after image is live:

```html
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta property="og:image:alt" content="Shield Point Capital — FinEra Inclusive Credit Marketplace" />
```

## Page-specific variants (optional, future)

| Page | Override subtitle suggestion |
|------|------------------------------|
| `investors.html` | Invest in Africa's FinTech Infrastructure |
| `partners.html` | Partner for Financial Inclusion |
| `products.html` | FinEra Inclusive Credit Marketplace |
| `loan-services.html` | Loan Facilitation & Credit Analytics |

Use the default `og-image.png` sitewide unless a page needs a unique campaign asset.

## QA checklist

- [ ] Image is exactly 1200 × 630 px
- [ ] File exists at `assets/social/og-image.png`
- [ ] Headline readable at thumbnail size
- [ ] Logo is sharp (not upscaled from tiny source)
- [ ] URL loads over HTTPS after deploy
- [ ] Facebook debugger shows correct preview
- [ ] WhatsApp link preview tested on mobile
