# Upfront Theme Framework

[Deutsch](README.md) | **English**

[![Version](https://img.shields.io/badge/Version-1.3.7-2271b1?style=flat-square)](readme.txt)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb4?style=flat-square&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-bis%207.1.4-21759b?style=flat-square&logo=wordpress&logoColor=white)
![ClassicPress](https://img.shields.io/badge/ClassicPress-2.7.4-03768e?style=flat-square)
[![Lizenz](https://img.shields.io/badge/Lizenz-GPL--2.0--or--later-2ea44f?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)

With Upfront, you can design your website directly in your browser: arrange sections, edit text and images, and adapt your layout to different screen sizes.

Upfront consists of the **framework** and a compatible **theme** containing your website's design. The visual editor is included in Upfront. The separately available **Upfront Builder** also helps you create and export your own themes; you do not need it to edit an existing Upfront theme.

## Requirements

- ClassicPress 2.7 or later, or WordPress 5.0 or later
- PHP 8.0 or later
- An Upfront-compatible theme, such as an Upfront starter theme

## Installation and getting started

1. Install the Upfront Framework under **Appearance → Themes → Add New**. If you have a ZIP file, select it using **Upload Theme**.
2. Install a compatible Upfront theme in the same way and **activate that theme**. The framework must remain installed.
3. Open **Settings → Permalinks** and choose an option other than **Plain**, such as **Post name**. Save your changes.
4. Visit your website while logged in and click **Upfront** in the top admin bar to open the visual editor.

You can also install Upfront and compatible themes through the PSOURCE Manager. Find an overview on the [Upfront project page](https://psource.eimen.net/ps-upfront-theme-framework/).

## Editing your website

The editor shows your website alongside a sidebar. You work directly on the layout and see changes as you make them.

### Text, images, and other elements

1. Drag an element from the sidebar's element list to the desired position on the page.
2. Double-click a text element to edit it. Select text to format it, for example in bold, or add a link.
3. For an image element, select an image from the media library or upload a new one. Use the controls on the image to crop it or provide alternative text.
4. Drag existing elements to another position or resize them using the displayed handle. Open an element's settings with the gear icon.
5. Remove an element using its red **×**.

Available elements include galleries, sliders, menus, buttons, posts, and contact forms as well as text and images. Settings vary by element.

### Sections and backgrounds

**Sections**, also called **regions**, divide a page into areas such as the header, content, and footer. Elements are placed inside them.

1. Hover over a section in the editor and click the **pencil icon** to edit it.
2. Use the yellow **+** icons to add another section. The gear icon opens section settings, such as the background color or image.
3. Use the red **×** to delete a section. This also removes the elements inside it.
4. Finish editing the section using the button shown.

Headers and footers can be used as **global sections**. Changes appear in every layout that uses the same global section.

### Colors and fonts

Open the **theme settings** in the sidebar:

- Under **Colors**, change the theme color palette. Elements using those theme colors inherit the changes.
- Under **Typography**, set the font, size, and color for headings and text. Use the font manager to select additional fonts.

Individual elements can have their own settings and may therefore differ from the global defaults.

### Adapting for phones and tablets

1. Open **responsive mode** in the sidebar and enable the views you want to edit.
2. Select a screen size at the top. Check that text is readable and images, buttons, and menus have enough room.
3. Adjust the size and arrangement of elements. Use the **eye icon** to hide elements or sections in the selected view and show them again later.
4. To add or delete an element, return to the normal layout view.
5. Save your changes and also check the result on an actual phone or tablet.

### Saving changes

Save the layout in the editor before leaving it. For some post layouts, Upfront asks whether a change should apply only to the current post or to all posts. Choose the option you intend.

Pages and posts also have their own publishing options: saving a draft and publishing a post are separate from saving the layout.

## Pages and posts

Use the editor sidebar to create pages and posts or access existing content. Alternatively, manage them as usual under **Pages** and **Posts** in the dashboard.

A page template determines the page's layout. Before changing an existing template, check which content and layout changes are affected and back up your website. Shared global sections can appear on multiple pages.

## Frequently asked questions

**I cannot see “Upfront” in the admin bar.**

Check that a compatible Upfront theme is active, you are logged in, and your account has the necessary editing permissions. Also check under **Settings → Permalinks** that an option other than **Plain** is selected and saved.

**Why does my change appear on several pages?**

You may have edited a global section or a shared layout. Changes to a global section affect every layout that uses it.

**My changes are not visible on the website.**

First check that you saved the layout and, if applicable, published the post or page. Under **Upfront → General**, use the **clear Upfront cache** button. Other caches, such as those from the server, a plugin, or a CDN, are not necessarily cleared by this and may need to be checked separately.

**A video does not start automatically on a phone.**

Browsers can block automatic playback with sound. Test the video on the affected device and check whether it starts using the playback controls. Not every browser supports every video format.

## Detailed guides

The following guides are in German:

- [Basics, colors, and fonts](https://psource.eimen.net/wiki/upfront-dokumentation/upfront-part-1-die-basics-themefarben-und-schriftarten/)
- [Sections and backgrounds](https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-2-strukturierung-deiner-webseite-mit-bereichen/)
- [Elements and their settings](https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-3-layout-deiner-webseite-mit-elementen/)
- [Responsive design](https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-6-erstellung-von-responsiven-webseiten/)
- [Pages and posts](https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-7-arbeiten-mit-seiten-und-beitraegen/)
- [Creating themes with the Upfront Builder](https://psource.eimen.net/wiki/upfront-dokumentation/upfront-builder-dokumentation/)

## License

Upfront is licensed under the [GNU General Public License v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
