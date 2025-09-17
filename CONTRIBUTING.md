# Contributing

Thanks for contributing to HTML Form Validator for PMPro!

## Local development
- Requires PHP 7.4+ and WordPress 6.0+.
- Clone the repository so that the plugin root contains `msl-form-validator.php` and `readme.txt`.

## Coding Standards (PHPCS / WPCS)
We use WordPress Coding Standards via PHPCS.

Install development dependencies:

```
composer install
```

Run lint:

```
composer run lint
```

Auto-fix where possible:

```
composer run lint:fix
```

The ruleset lives in `phpcs.xml.dist`. CI runs PHPCS on pushes and PRs.

## Translations
- Text domain: `msl-form-validator`
- Language files in `languages/`

## WordPress.org assets
- Place plugin assets in `.wordpress-org/`:
  - `icon-128x128.png`, `icon-256x256.png`
  - `banner-772x250.png`, `banner-1544x500.png`
  - Optional: `screenshot-1.png`, `screenshot-2.png`, ...
- The `Update WordPress.org assets` workflow pushes these files to the SVN `/assets/` directory when changed on `main`/`master`.

## Releases

### GitHub release
Use semantic tags prefixed with `v` for GitHub releases:

```
git tag vX.Y.Z
git push origin vX.Y.Z
```

Then create a GitHub Release from that tag with release notes. This will NOT deploy to WordPress.org.

### WordPress.org deploy
Deploy to WordPress.org using tags prefixed with `wporg-` (e.g., `wporg-0.1.0`). Before tagging:

1. Ensure `readme.txt` has the correct `Stable tag` and `Tested up to`.
2. Ensure `msl-form-validator.php` version header matches the release version.
3. Commit all changes to `main`/`master` and push.
4. Tag and push with the `wporg-` prefix:

```
git tag wporg-X.Y.Z
git push origin wporg-X.Y.Z
```

This triggers `.github/workflows/deploy-to-wporg.yml`.

### Required GitHub secrets (for WordPress.org deploy)
Set repository secrets:
- `SVN_USERNAME` (your WordPress.org username)
- `SVN_PASSWORD` (your WordPress.org password or an Application Password)

## Support
For bugs and feature requests, please open an issue on GitHub.
