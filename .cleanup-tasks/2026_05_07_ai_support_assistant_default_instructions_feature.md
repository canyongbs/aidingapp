---
title: Ai Support Assistant Default Instructions Feature
created: 2026-05-07
---

## Feature Flags

- App\Features\AiSupportAssistantDefaultInstructionsFeature

## Temporary Migrations

## Additional Cleanup

- In `app-modules/ai/src/Jobs/PortalAssistant/SendMessage.php`, remove the `AiSupportAssistantDefaultInstructionsFeature::active()` branch and keep only the active path that reads from `AiSupportAssistantSettings::instructions` (with the `defaultInstructions()` fallback).
- In `app-modules/ai/src/Filament/Pages/ManageAiSupportAssistantSettings.php`, remove the `->visible(fn () => AiSupportAssistantDefaultInstructionsFeature::active())` call on the `Assistant Instructions` section so it is always shown.
- In `app-modules/ai/database/migrations/2026_05_07_094741_add_instructions_to_ai_support_assistant_settings.php`, remove the `AiSupportAssistantDefaultInstructionsFeature::activate()` / `deactivate()` calls and the `App\Features\AiSupportAssistantDefaultInstructionsFeature` import. Do **not** delete the migration file — it also seeds `ai-support-assistant.instructions` for fresh installs.
