# Available translations resolver

This branch introduces a small service that reports only the languages for which a content entity actually has a translation.

It deliberately does not manage translation workflows. Existing Drupal translation/content-translation tooling and TMGMT remain responsible for creating, reviewing, and publishing translations.

The resolver is intended as a reusable UX primitive for selective language switchers: a caller can ask which translations exist for the current entity and avoid presenting languages that would lead to untranslated content.

The implementation is intentionally minimal and covered by unit tests for source-only content and content with an existing translation.
