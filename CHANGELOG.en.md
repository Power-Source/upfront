Change Log
==========

1.3.5 - 2026-10-29
-------------------------------------------------------------------------------
- Fix: The color picker again saves recently used colors permanently and separately for each active theme; new colors appear immediately in the selection.
- Fix: Duplicate theme-color values are now shown as a single swatch in the color picker.
- Fix: The Upfront Builder guidance assistant keeps the confirmation button and the “do not show again” checkbox side by side within the footer.
- Improvement: The static font catalog includes all 1,775 families available locally on the PSOURCE font server and is updated only by an explicit release command.
- Improvement: The font catalog shows readable family names, categories, and available weights and italic variants while font delivery continues to use the local server IDs.
- Improvement: Fonts used by a theme, including their CSS, are stored in that Child Theme under `fonts/upfront-google-fonts` and served directly from there.
- Improvement: The theme catalog is shipped as a static, versioned catalog with local preview images and German and English descriptions; the dashboard no longer queries GitHub on each visit.

1.3.3 - 2026-09-10
-------------------------------------------------------------------------------
- Fix: The Redactor link dialog preserves generated href attributes and restores escaped link markup as real anchors after saving.
- Fix: Text presets no longer write paragraph typography directly to links; global link and hover presets again determine their color and font.
- Fix: The text element normalizes invalid headings that contain only paragraph content before frontend output.
- Improvement: The link toolbar calculates its width including all margins.

1.3.2 - 2026-09-08
-------------------------------------------------------------------------------
- Fix: Virtual Builder pages can disable the general Upfront autostart so that the Page Editor and Theme Builder are no longer initialized together.
- Fix: Close Upfront and WP Admin reliably receive click handlers when their menu opens, navigate independently again, and retain consistent menu styling.
- Fix: Help & Support uses the Upfront documentation in the editor and provides the Builder with one overridable, context-dependent help link.
- Fix: Resizing slider images sends complete layout and storage context and saves to the correct Builder layout.
- Tests: Editor menu actions, event delegation, and context-dependent help are covered by focused regression tests.

1.3.1 - 2026-09-07
-------------------------------------------------------------------------------
- New: Code elements can be saved as named templates in the active Child Theme, managed in the CodePen style guide, and edited there with a local live preview.
- Improvement: The template editor uses the Ace files already included with the theme and provides HTML, CSS, and JavaScript tabs without loading CDN resources.
- Fix: Switching a local template fully loads HTML, CSS, and JavaScript into their respective editor tabs, and changes update the preview without overwriting content.
- New: Code elements can export HTML, CSS, and JavaScript as a new CodePen and import public Pens back into the three editor tabs.
- Improvement: The Code element editor marks unclosed and incorrectly nested HTML tags directly in Ace and prevents saving invalid markup.

1.3.0 - 2026-09-06
-------------------------------------------------------------------------------
- Fix: Regions accept CSS rules again.
- The Full Width Grid is temporarily disabled because it requires additional integration work.

1.2.9 - 2026-09-05
-------------------------------------------------------------------------------
- New: Regions and their grid contents start at 100 percent width by default; each region can still override its region type and grid width independently.
- Fix: Full-width grids calculate columns from their actual rendered width and are no longer constrained by fixed column or maximum widths in theme settings.
- Fix: Auto-snap, responsive wrappers, and spacers use the same dynamic column width in full-width mode.
- Improvement: Only one accordion panel remains open in element settings, and unsaved field values are retained when switching panels.
- Fix: Left, center, and right alignment correctly positions the burger button and is no longer overridden by editor hover states.
- Improvement: Navigation text uses a readable dark-gray default; the burger menu receives a light default background.
- Fix: Button hover animations use consistent defaults in the editor and delivered theme; legacy easing values and decimal commas are normalized compatibly.
- Fix: Link, color, icon, and formatting panels preserve complete text selections, and confirmed actions apply to the entire selected range.
- Fix: Older malformed text HTML is handled tolerantly without DOMDocument warnings.
- Fix: Image dimensions with non-numeric values such as `auto` no longer cause a TypeError under PHP 8.
- Tests: Dynamic full-width column calculation and its use in the grid editor are covered by a focused regression test.

1.2.8 - 2026-09-04
-------------------------------------------------------------------------------
- New: CodePen styles are again identified solely by their public Pen URL; browser-side result transport avoids blocked direct requests and displays the validated preview locally without third-party warning banners.
- Fix: CodePen imports write colors, fonts, and typography to the active Child Theme’s `settings.php` and synchronize only existing database overrides.
- Fix: The theme palette remains index-stable at exactly ten `ufc0` to `ufc9` slots, preserves duplicate values, and no longer shows additional recently used colors.
- Fix: The Redactor link dialog saves selected text before focus changes, creates the anchor directly, and retains its href when closing and hiding temporary visitor icons.
- Fix: The text color picker formats a selection only once and therefore works reliably again in text-editing mode.
- Fix: Dashboard tools for clearing the Upfront cache and resetting theme defaults again use consistent administrator permissions.
- Fix: Pako registers as a named AMD module and no longer produces a mismatched-anonymous-define error during parallel RequireJS loading.
- Tests: CodePen import, Child Theme persistence, ten theme-color slots, Pako AMD, and Redactor links are covered by focused regression tests.

1.2.7 - 2026-09-04
-------------------------------------------------------------------------------
- Security: HTTPS and same-origin URLs are normalized robustly; local font caches reliably replace obsolete absolute HTTP upload URLs.
- Privacy: The YouTube element strictly validates embeddable player and preview-image URLs and loads only permitted HTTPS resources from YouTube No-Cookie.
- Tests: URL hardening and delayed loading of the YouTube element are covered by PHP and JavaScript tests.
- Modernization: The modern drag, resize, sort, slider, and datepicker adapters use current jQuery APIs and reliably provide drag previews and target outlines.
- Fix: Help & Support remains directly available in the editor menu; older Upfront Builder versions can again load the still-required command-help module.
- Fix: Navigation and Redactor editors trigger blur events compatibly with jQuery 3 and handle empty editor content without type errors; the link box retains selected text, no longer shows an undefined URL, and calculates its full width.
- Fix: Visual hints for lower element spacing accept Backbone event options without stopping the editor with “$el.find is not a function”.
- Improvement: Tabs and accordion panels again have directly accessible plus buttons; the tab button is located outside the tab list in an ARIA-compliant way.

1.2.6 - 2026-09-03
-------------------------------------------------------------------------------
- Modernization: Remaining jQuery UI scripts were removed from the editor boot sequence and replaced with explicitly loaded modern adapters.
- Fix: Datepicker, drag, sort, slider, and resize features are available through direct RequireJS dependencies before their first use.
- Fix: The final region has an accessible vertical resize handle at its top edge; the handle follows newly added and removed regions automatically.
- New: The YouTube element optionally provides its own two-click consent and defers to PS-DSGVO YouTube decisions.
- Privacy: YouTube players use the No-Cookie endpoint; when blocking is active, preview images are also loaded only after consent and a noscript link is offered.

1.2.5 - 2026-09-03
-------------------------------------------------------------------------------
- Fix: RequireJS reliably associates anonymous AMD modules with the requesting script even during parallel loading; Pako and directly used editor dependencies no longer contaminate the module queue.
- Fix: Global theme-color references such as `ufc0` and `ufc1` remain stored and are resolved to their current color values only when CSS is generated for region backgrounds.
- New: Region contents can be explicitly set to 100 percent grid width independently of the region background.
- New: Webfonts are loaded server-side, cached under `wp-content/uploads/upfront-fonts`, and then served exclusively from the same origin with `font-display: swap`.
- Fix: The CSS editor handles Ace content safely as text and no longer fails on `innerText.split` when selectors are inserted.
- Fix: UI sprites work even with an empty `ui` directory in the active theme; first uploads, local image metadata, and controlled storage errors are handled correctly.
- Fix: The `en_US` language catalog is synchronized with current German source strings and fully switches the Builder to English when the website language is English.
- Modernization: Sliders, resizing, and file uploads use `Array.isArray` rather than the removed jQuery `$.isArray` helper.
- Improvement: Switches in region settings are aligned cleanly beneath each other and their labels are displayed consistently to the right.
- Fix: Invalid image-editor CSS values, missing declaration separation, and the text-size-adjust compatibility warning were corrected; outdated IE CSS hacks and dead rules were removed.

1.2.4 - 2026-09-02
-------------------------------------------------------------------------------
- Fix: Virtual Builder pages load `WP_Screen` compatibly in the frontend again and no longer cause a PHP fatal error.
- Fix: WordPress Backbone no longer conflicts with RequireJS during delayed script execution or swaps editor modules.

1.2.3 - 2026-09-02
-------------------------------------------------------------------------------
- New: CodePen style guides contain a versioned Upfront data set and can be checked through their public Pen URL.
- New: The active theme style and an importable CodePen style are compared directly for colors, fonts, and typography.
- New: Verified colors, fonts, and typography can be selected individually and deliberately adopted into the active Upfront theme.
- New: CodePens are displayed as rendered HTML, CSS, and JavaScript previews before import.
- New: A gallery of helpful CodePens adds links to the external collection and submitting custom Pens.

1.2.2 - 2026-09-01
-------------------------------------------------------------------------------
- Fix: Incomplete string escaping or encoding was corrected in several places.
- Fix: Virtual pages injected by plugins into archive queries are again identified as single pages and rendered with their actual content.
- Fix: PS Jobboard expert pages again use the appropriate single layout instead of the generic post list.
- Fix: URL fields in image-link masks retain input focus and target selections display their labels completely.
- Fix: Footers, search fields, and page navigation in selection and media popups are fully displayed again.
- Fix: Active subtabs display their contents immediately when sidebar accordions open.
- Improvement: Color pickers remain fully open, and global regions and lightboxes can be renamed or deleted in the manager.
- Modernization: Search and Social use the central element-settings sidebar; Social uses local modern SVG icons without external widgets or count APIs.

1.2.1 - 2026-08-16
-------------------------------------------------------------------------------
- Fix: Editor scripts are loaded in the frontend only when the Upfront Builder is active.
- Fix: Unnecessary `upfront_data` AJAX calls and resulting 500 errors on ordinary live pages are prevented.
- Fix: Virtual editor pages can load no-build core dependencies without an `editmode` parameter and start in the requested mode.
- Fix: The obsolete WPMUDEV Dashboard fallback for Snapshot no longer causes a PHP fatal error.
- Fix: Input fields in the Add Region dialog can again be reliably focused and used.
- Fix: The Region dialog is above competing overlays and receives pointer events correctly.

1.2.0 - 2026-08-14
-------------------------------------------------------------------------------
- Fix: Virtual pages, for example BuddyPress pages, are rendered correctly again.

1.1.9 - 2026-08-11
-------------------------------------------------------------------------------
- Fix: New menu names can again be entered reliably in the navigation element.
- Fix: Input fields and interactive controls no longer trigger module dragging while editing.
- Fix: The text cursor and focus outline in the new-menu-name field are clearly visible again.

1.1.8 - 2026-08-09
-------------------------------------------------------------------------------
- Fix: The Add Region modal in the Builder is completely clickable again; no blocking overlay remains.
- Fix: The drag/mousedown conflict in the inline modal is resolved, so inputs, radios, and selects can be focused reliably.
- Fix: The input caret in text fields of the Add Region dialog visibly blinks in the correct position again.
- Fix: Text selection in the Add Region dialog is possible again despite `upfront-no-select` rules.

1.1.7 - 2026-08-08
-------------------------------------------------------------------------------
- Fix: Breakpoint preset data accepts arrays and objects and prevents PHP fatal errors.
- Fix: Navigation handles missing breakpoint and preset data without PHP deprecation notices.
- Fix: RequireJS URL paths are processed without `preg_quote` deprecation notices even in root installations.
- Fix: Virtual Builder pages no longer cause a comment-feed warning without a global post.
- Modernization: jQuery Effects Core and Slide were replaced by performant vanilla JavaScript animations that respect reduced-motion preferences.

1.1.6 - 2026-08-07
-------------------------------------------------------------------------------
- Fix: Text selection remains stable while selecting and editing in the editor.
- Fix: Text-color and partial-selection behavior in the editor was revised.
- Fix: Google Maps scripts no longer load without an API key and do not produce NoApiKeys warnings.
- Fix: The theme-image import dialog remembers ignored missing images per layout and does not show them again.
- Modernization: Further jQuery and Backbone legacy code was cleaned up in editor and element code.
- Modernization: Event handling in several views and commands was migrated to `listenTo`, `on`, and `off`.
- New: The Ace editor is loaded locally from the theme, with no CDN dependency.
- New: External avatars can be enabled as an option under General in the Upfront dashboard.
- Fix: Avatar rendering uses local placeholder images by default rather than external Gravatar requests.

1.1.5 - 2026-08-07
-------------------------------------------------------------------------------
- Fix: Upfront AJAX no longer returns a 500 error in the link dialog after updating the Walker signature.
- Fix: Floating-region settings now guard `$el` in the sticky option.
- New: The Social element is visible and draggable in the editor again.
- Modernization: Google+ in the Social element was fully replaced by Instagram.

1.1.4 - 2026-08-06
-------------------------------------------------------------------------------
- Improved double-click interaction for text editing.
- Updated title attributes in German.

1.1.3 - 2026-08-06
-------------------------------------------------------------------------------
- Fix: Group editor.
- Fix: Saving new regions in the Builder.
- Modernization: jQuery and PHP.
- Editor UI adjustments.

1.1.2 - 2026-08-05
-------------------------------------------------------------------------------
- Fix: Images are saved correctly.
- Fixed a loading issue with some widgets.
- More and improved German translations.

1.1.1 - 2026-08-05
-------------------------------------------------------------------------------
- Fix: The color picker displays pickers correctly again.
- More and improved German translations.

1.1.0 - 2026-08-02
-------------------------------------------------------------------------------
- First release.
