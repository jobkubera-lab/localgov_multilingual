# Available translations resolver

This branch introduces a small service that reports the languages for which a content entity actually has a translation.

It deliberately does not manage translation workflows. Drupal content translation tooling and TMGMT remain responsible for creating, reviewing, tracking and publishing translations.

For the primary public language-switcher use case, unpublished translations are excluded by default when the translated entity exposes publication state. Callers may explicitly opt in to unpublished translations for administrative workflows.

The resolver performs no entity access checking. A public language switcher must still verify `view` access for the selected translation before rendering a link.

The entity's untranslated/original language is used as the source language, and only configurable Drupal languages are considered.

This is intended as a reusable UX primitive for selective language switchers, not as a complete translation-selection or workflow system.
