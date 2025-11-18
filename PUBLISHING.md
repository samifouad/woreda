# Publishing Woreda

This document explains how to publish Woreda to npm.

## Prerequisites

1. **npm account**: You need an npm account with publish permissions
2. **npm token**: Set `NPM_TOKEN` as a GitHub secret (Settings → Secrets → Actions)
3. **Permissions**: Must have write access to the repository

## Publishing Process

### Tag-Based Automatic Releases (Recommended)

Publishing is automatic whenever you push a tag starting with `v`.

**Option 1: Using the helper script**

```bash
# Bump patch version (0.1.0 → 0.1.1)
./script/version.ts patch

# Bump minor version (0.1.0 → 0.2.0)
./script/version.ts minor

# Bump major version (0.1.0 → 1.0.0)
./script/version.ts major

# Or set specific version
./script/version.ts 1.5.0

# Then commit and push the tag
git add .
git commit -m "chore: bump version to X.Y.Z"
git tag vX.Y.Z
git push origin main --tags
```

**Option 2: Manual tagging**

```bash
# Create and push a tag directly
git tag v0.2.0
git push origin v0.2.0
# Note: GitHub Actions will update package.json automatically
```

Either way, the GitHub Actions workflow will automatically:
- ✅ Extract version from tag (v0.2.0 → 0.2.0)
- ✅ Update all package.json files
- ✅ Run type checking
- ✅ Build the package
- ✅ Publish to npm
- ✅ Generate changelog from commits
- ✅ Create GitHub release

### Manual Publishing (Local)

If you need to publish manually (testing, emergency, etc.):

```bash
# 1. Update version in package.json
cd packages/opencode
# Edit package.json to desired version

# 2. Build
bun run build

# 3. Test locally
npm pack
# This creates woreda-X.X.X.tgz
# Test: npm install -g ./woreda-X.X.X.tgz

# 4. Publish
npm publish --access public

# 5. Create git tag
git tag v0.2.0
git push origin v0.2.0
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
