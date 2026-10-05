# npm publishing

Woreda 6 starts the GitHub Actions self-hosting project. Earlier versions, through 5.0.3, belong to the previous AI coding agent. The npm name already exists; no placeholder publish is needed.

Set the npm trusted publisher to:

| Field | Value |
| --- | --- |
| Organization or user | `samifouad` |
| Repository | `woreda` |
| Workflow filename | `publish.yml` |
| Environment | `release` |
| Allowed actions | Allow `npm publish` |

The workflow does not need an npm token. It uses GitHub's OIDC identity and runs on tag pushes matching `v*.*.*`. The tag must match `package.json` exactly. After configuring the publisher, release version 6 with:

```sh
git tag -s v6.0.0 -m 'Woreda v6.0.0'
git push origin v6.0.0
```

Type checking, tests and a packed installation smoke test must pass before publishing. Failed jobs can be retried through GitHub's rerun controls.

npm's [trusted publishing](https://docs.npmjs.com/trusted-publishers/) currently requires a GitHub-hosted runner. If GitHub blocks hosted jobs for the account, publishing cannot complete until that restriction is resolved. Local publishing remains possible after `npm login`:

```sh
npm publish --access public
```

The npm CLI requires Bun on PATH. Standalone executable builds include the runtime and are built separately with `bun run build`.
