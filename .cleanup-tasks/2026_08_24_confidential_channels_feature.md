---
title: Confidential Channels Feature
created: 2026-08-24
---

## Feature Flags

- App\Features\ConfidentialChannelsFeature

## Temporary Migrations

## Additional Cleanup

- `app-modules/in-app-communication/database/migrations/2026_08_24_152244_add_confidentiality_to_conversations_table.php` — remove the `ConfidentialChannelsFeature::activate()` call (in `up()`) and the `ConfidentialChannelsFeature::deactivate()` call (in `down()`), along with the now-unused import. Do NOT remove the `is_confidential` / `ephemeral_period` columns or the `conversations_ephemeral_lookup_index` index — those are permanent. Unwrap the `DB::transaction()` closures in `up()` and `down()`.

- `app-modules/in-app-communication/src/Models/Conversation.php` — in `confidentialityPayload()`, delete the `if (! ConfidentialChannelsFeature::active())` early return so the method always returns the live values.

- `app-modules/in-app-communication/src/Actions/CreateConversation.php` — drop `&& ConfidentialChannelsFeature::active()` from the `$isConfidential` expression, and remove the `if (ConfidentialChannelsFeature::active())` wrapper around the `is_confidential` / `ephemeral_period` assignments so they are always written.

- `app-modules/in-app-communication/src/Actions/UpdateConversation.php` — remove `ConfidentialChannelsFeature::active() &&` from the guard that throws `InvalidArgumentException` for a confidential channel being made public.

- `app-modules/in-app-communication/src/Actions/GetUserConversations.php` — in both `__invoke()` and `pinned()`, change the `->when(ConfidentialChannelsFeature::active() && $confidential !== null, ...)` condition to `->when($confidential !== null, ...)`.

- `app-modules/in-app-communication/src/Actions/GetPublicChannels.php` — replace the `->when(ConfidentialChannelsFeature::active(), ...)` wrapper with a direct `->where('is_confidential', false)`.

- `app-modules/in-app-communication/src/Jobs/PruneEphemeralMessages.php` — delete the `if (! ConfidentialChannelsFeature::active()) { return; }` early return at the top of `handle()`.

- Delete the `confidentialChannelsEnabled` flag chain from the chat front end, so the confidential UI always renders. All paths are under `app-modules/in-app-communication/`:
    - `resources/views/filament/pages/user-chat.blade.php` — delete the `@if (ConfidentialChannelsFeature::active())` block along with the `data-confidential-channels-enabled` attribute it renders, and the `use App\Features\ConfidentialChannelsFeature;` import.
    - `resources/js/chat.js` — delete the `confidentialChannelsEnabled:` line from the `createApp` props.
    - `resources/js/App.vue` — delete `confidentialChannelsEnabled` from `defineProps`, both `:confidential-channels-enabled` bindings (on `ConversationList` and `NewConversationModal`), and simplify `listFilters` so the users tab always sends `confidential: false`.
    - `resources/js/components/NewConversationModal.vue` — delete `confidentialChannelsEnabled` from `defineProps` and change `showConfidentialFields` to `computed(() => conversationType.value === 'channel')`.
    - `resources/js/components/ConversationList.vue` — delete `confidentialChannelsEnabled` from `defineProps` and change the `availableTabs` filter so the `confidential` tab returns `true`.
    - Run `npm run build` afterwards.

- Delete the feature-inactive test cases, which exist only to prove the pre-migration path: `tests/Tenant/Actions/CreateConversationTest.php` ("does not store confidentiality when the feature is inactive"), `tests/Tenant/Actions/UpdateConversationTest.php` ("lets a confidential channel be made public when the feature is inactive"), `tests/Tenant/Actions/GetUserConversationsTest.php` ("ignores the confidentiality filter when the feature is inactive"), `tests/Tenant/Actions/GetPublicChannelsTest.php` ("does not exclude confidential channels when the feature is inactive"), `tests/Tenant/Jobs/PruneEphemeralMessagesTest.php` ("does nothing when the confidential channels feature is inactive") and `tests/Tenant/Models/ConversationTest.php` ("reports a conversation as not confidential when the feature is inactive"), each under `app-modules/in-app-communication/`. Remove the now-unused `ConfidentialChannelsFeature` import from each file.

- Delete `app/Features/ConfidentialChannelsFeature.php`
