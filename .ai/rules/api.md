---
paths:
    - 'app-modules/*/src/Http/Controllers/Api/**'
---

# Api

## Keep the Bruno collection in sync with API endpoints

Any change that adds or changes a REST API endpoint must add or update its requests in the Bruno collection in `docs/bruno/` (OpenCollection YAML) in the same PR. Follow the existing requests: `{{baseUrl}}` URL, `auth: inherit`, status assertion, Create requests clear their ID runtime vars in a before-request script and store the new IDs in an after-response script for the other requests, `seq` ordered create → view → update → remove. Only commit the Local environment; never commit API keys or hosted-instance URLs (the repo is public). See `docs/how-tos/test-the-api-with-bruno.md`.
