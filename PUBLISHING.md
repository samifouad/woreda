# Publishing Woreda

This document explains how to publish Woreda to npm.

## Prerequisites

1. **npm account**: You need an npm account with publish permissions
2. **npm token**: Set `NPM_TOKEN` as a GitHub secret (Settings → Secrets → Actions)
3. **Permissions**: Must have write access to the repository

## Publishing Process

### Automated via GitHub Actions (Recommended)

1. Go to **Actions** tab in GitHub
2. Select **"Publish Woreda"** workflow
3. Click **"Run workflow"**
4. Choose:
   - **bump**: `major`, `minor`, or `patch`
   - **version**: (optional) override with specific version like `1.0.0`
   - **preview**: check to test without actually publishing

5. Click **"Run workflow"**

The workflow will:
- ✅ Bump version in all package.json files
- ✅ Run type checking
- ✅ Build the package
- ✅ Publish to npm
- ✅ Create git tag
- ✅ Push to GitHub
- ✅ Create GitHub release with changelog

### Manual Publishing (Local)

```bash
# Set environment variables
export WOREDA_BUMP=patch  # or major, minor
export WOREDA_VERSION=1.0.0  # optional override
export NPM_CONFIG_TOKEN=your-npm-token

# Run publish script
chmod +x ./script/publish-woreda.ts
./script/publish-woreda.ts
```

### Preview Mode

To test the publish process without actually publishing:

```bash
export WOREDA_PREVIEW=true
./script/publish-woreda.ts
```

## Version Bumping

Woreda follows semantic versioning:

- **Major** (1.0.0 → 2.0.0): Breaking changes
- **Minor** (1.0.0 → 1.1.0): New features, backward compatible
- **Patch** (1.0.0 → 1.0.1): Bug fixes, backward compatible

## Post-Publishing

After publishing:

1. **npm**: Package appears at https://www.npmjs.com/package/woreda
2. **GitHub Release**: Created at https://github.com/samifouad/woreda/releases
3. **Install**: Users can install with `npm install -g woreda`

## Troubleshooting

### "Not authorized to publish"

Make sure `NPM_TOKEN` secret is set in GitHub Actions:
1. Go to Settings → Secrets and variables → Actions
2. Add `NPM_TOKEN` with your npm authentication token
3. Get token from: https://www.npmjs.com/settings/[username]/tokens

### "Version already exists"

You're trying to publish a version that already exists on npm. Either:
- Bump the version number higher
- Use `version` input to specify a new version

### Build fails

Check the GitHub Actions logs for specific errors:
- Type errors: Fix TypeScript issues
- Missing dependencies: Run `bun install`
- Build script errors: Check `packages/opencode/script/build.ts`

## Testing Before Publishing

Always test locally before publishing:

```bash
# Build
cd packages/opencode
bun run build

# Test the binary
./dist/woreda-*/bin/woreda --version

# Test installation simulation
npm pack
# This creates woreda-X.X.X.tgz
# Install it locally: npm install -g ./woreda-X.X.X.tgz
```

## Changelog Generation

The publish script auto-generates changelog from git commits since the last published version. To get better changelogs:

- Write clear commit messages
- Use conventional commits: `feat:`, `fix:`, `refactor:`, etc.
- Avoid `chore:`, `ci:`, `test:` for user-facing changes (they're filtered out)

## NPM Package Info

- **Package name**: `woreda`
- **Registry**: https://registry.npmjs.org
- **Scope**: Unscoped (public package)
- **Binary**: `woreda` command
