# Screenshot Guide (WordPress.org + GitHub)

Place screenshots in this folder with the exact filenames below. They will be used by:
- WordPress.org (picked up automatically as `screenshot-#.png`)
- GitHub README (we will reference them relatively)

## Filenames
- screenshot-1.png — Checkout: browser validation bubble on a required custom user field
- screenshot-2.png — Checkout: required PMPro custom user field (attributes present)
- screenshot-3.png — Member Profile Edit: required field missing error
- screenshot-4.png (optional) — Custom message via filter
- screenshot-5.png (optional) — Localized message example

## Suggested Captions (readme.txt)
1. Example browser validation message at checkout.
2. Required PMPro custom user field with native HTML5 attributes.
3. Member Profile Edit shows a clear error when a required field is missing.
4. Custom validation message via filter.
5. Localized validation message.

## Capture tips
- Use a clean theme (e.g., Twenty Twenty-Four) and a test checkout page with at least two required custom fields.
- For screenshot-1, leave a required custom user field blank and trigger the native HTML5 bubble.
- For screenshot-2, you can optionally open DevTools (Elements) to highlight `required` / `aria-required="true"` on the relevant input; keep the crop tight and readable.
- For screenshot-3, on the Member Profile Edit screen, leave a required field empty and click update to show the error.
- For screenshot-4, apply a simple filter to change the message (we can provide code in the GitHub README) and capture the result.
- For screenshot-5, switch the site language and capture the localized message.

## Image format & quality
- PNG preferred for crisp UI.
- Crop tightly around the relevant UI. Avoid noisy background UI.
- Keep consistent widths, ensure text is readable (100% or 125% zoom is usually good).
- Anonymize any personal data.

## After adding screenshots
- Commit them to this folder.
- We will update `readme.txt` > Screenshots section with the captions above and create a GitHub `README.md` embedding these images.
