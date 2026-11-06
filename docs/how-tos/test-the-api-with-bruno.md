# Testing the API with Bruno

This guide covers exercising the REST API (`/api/v1`) locally with the shared [Bruno](https://www.usebruno.com/) collection in `docs/bruno/`, and keeping that collection up to date.

---

## Opening the Collection

1. Install Bruno v3.1 or newer. The collection is stored in the OpenCollection YAML format (`.yml` files).
2. In Bruno, choose **Open Collection** and select the `docs/bruno/` folder of this repository.
3. Select the **Local** environment in the top-right environment selector.

## Configuring the Local Environment

1. `baseUrl` defaults to `https://test.aidingapp.local`. Change it if your local tenant uses a different domain (no trailing slash).
2. Create an API key in your local tenant under **Users > Programmatic Users**: create a programmatic user, give it the permissions for the endpoints you want to call, and copy its generated API key.
3. Paste the API key into the `apiKey` secret of the **Local** environment. Bruno stores secret values on your machine, never in the collection files.

All requests inherit Bearer authentication and the `Accept: application/json` header from the collection.

> This repository is public. Only commit the **Local** environment. If you need to call another environment, create your own environment in Bruno and do not commit it, and never commit API keys or the URLs of hosted instances.

## Running Requests

- Run a single request with **Send**. Each request's **Docs** tab explains its parameters and any runtime variables it uses.
- Run a whole folder, or the whole collection, with the **Runner**. The **Create** requests store the records they create in runtime variables that the View, Update, and Archive / Delete requests in the same folder use, and the last request in each folder removes the record created by its run.
- Every request asserts the expected status code, so the Runner reports which endpoints fail.

## Adding or Changing Endpoints

Any change that adds or changes an API endpoint must add or update its requests in the collection, in the same pull request.

1. Add requests from Bruno, or by copying an existing `.yml` request file, inside the folder for the resource. For a new resource, create a folder with a `folder.yml` that sets `request.auth` to `inherit` (in Bruno: folder **Settings > Auth > Inherit**); otherwise the folder defaults to no auth and its requests are sent without the API key.
2. Follow the existing requests:
    - Use `{{baseUrl}}` in the URL and `auth: inherit`.
    - Assert the expected status code.
    - In **Create** requests, store the created record's ID in a runtime variable with an after-response script, and use that variable as the path parameter in the other requests.
    - Make values that must be unique (such as names) unique per run, for example with a `Date.now()` suffix set in a before-request script.
    - Order the requests with `seq` so running the folder creates, views, updates, and then removes its records.
    - Describe the request and its fields in the **Docs** tab.
3. Run the folder with the **Runner** against your local tenant before opening the pull request.
