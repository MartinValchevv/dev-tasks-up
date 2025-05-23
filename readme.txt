=== DevtasksUp - ClickUp integration ===
Contributors: martinvalchev
Donate link: https://linktr.ee/martinvalchev
Tags: ClickUp, integration, admin, task management, clients
Requires at least: 5.3
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The plugin integrates ClickUp into admin for streamlined task management. Add API key for full access: create tasks, leave comments, view priority.

== Description ==

Unlock effortless task organization with the ClickUp plugin's seamless integration into your admin. Simply configure your account with an API key and watch as task creation, comment writing, and task prioritization become a breeze. Ideal for developers to customize for their clients, ensuring direct and efficient task delegation.

The options for this plugin include:

*   Integration of ClickUp into the admin
*   Configuration using an API key on your account
*   Task creation
*   Create task and create custom fields if configured (supports 13 types of fields)
*   Comment writing
*   Viewing the most important aspect of a task
*   Customization by developers for their clients
*   Direct task delegation from the client to the developer.
*   Multiple workspace support with real-time switching

The helper libraries plugin uses the following:

*   [Bootstrap v5.2](https://getbootstrap.com/docs/5.2/getting-started/introduction/)
*   [FontAwesome v5](https://fontawesome.com/)
*   [Select2](https://select2.org/)
*   [SweetAlert2 v11.4.8](https://sweetalert2.github.io/)
*   [ClickUP API](https://clickup.com/api/)

Notes:

*   In order for the plugin to work correctly, it must be configured from an admin account
*   The corresponding Api key must be filled in, upon successful connection, it is mandatory to choose whether you want to configure it in a new environment or in a current one
*   If you want to use custom fields, they must be configured from Click Up, if they are configured, the field option will be displayed when you create a task
*   You can now select and switch between multiple workspaces in real-time
*   The plugin automatically resets configuration when switching workspaces to prevent conflicts

== Installation ==

To install this plugin:

1. Install the plugin through the WordPress admin interface, or upload the plugin folder to /wp-content/plugins/ using ftp.
2. Activate the plugin through the 'Plugins' screen in WordPress. On a Multisite you can either network activate it or let users activate it individually. 
3. Go to WordPress Admin > DevtasksUp > Settings

== Frequently Asked Questions ==

= What is the plugin? =
The plugin is an integration of the task management platform, ClickUp, into the admin.
= How do I configure the plugin? =
You can configure the plugin by adding an API key to your account.
= What tasks can I perform with this plugin? =
You can create tasks, write comments, and view the most important aspects of a task.
= Who is this plugin suitable for? =
This plugin is ideal for developers who want to set it up for their clients and delegate tasks directly.
= Does it support custom fields to Click up? =
Yes, it supports 13 types of fields:
*  URL Custom Field
*  Dropdown Custom Field
*  Email Custom Field
*  Phone Custom Field
*  Date Custom Field
*  Short Text Custom Field
*  Long Text Custom Field
*  Number Custom Field
*  Money Custom Field
*  Emoji (Rating) Custom Field
*  Label Custom Field
*  Attachment Custom Field
*  Checkbox Custom Field
= Can I use multiple workspaces? =
Yes, you can select and switch between multiple workspaces in real-time.

== Screenshots ==

1. Settings
2. Task Center
3. Opened Task
4. Create Task - custom fields

== Changelog ==

= 1.3.1 =
* **Fix:** Fixed issue with API errors after disconnecting from ClickUp
* **Fix:** Improved error handling when workspace is deleted from ClickUp
* **Added:** Automatic reset of configuration when API token is removed
* **Added:** Automatic detection of deleted workspaces with fallback to first available workspace
* **Improvement:** Better validation of API token and List ID before making API calls
* **Improvement:** Enhanced error handling for all ClickUp API endpoints
* **Improvement:** Added additional translations for workspace management
**Release date: March 11, 2025**

= 1.3.0 =
* **Added:** Multiple workspace support with real-time switching
* **Added:** Ability to select different workspaces and automatically reset configuration
* **Added:** Real-time space loading when switching workspaces
* **Added:** Automatic migration from previous versions
* **Added:** Mutual exclusion between workspace creation and selection options
* **Added:** Auto-refresh of workspaces list when loading settings page
* **Improvement:** Better API integration with ClickUp workspaces
* **Improvement:** Enhanced user experience when configuring workspaces
* **Improvement:** Improved settings page UI - only shows API token field when token is invalid
* **Improvement:** Added validation for API token field with visual feedback
* **Improvement:** Responsive workspace selector with Bootstrap button styling
* **Fix:** Fixed issue with empty List ID causing API errors
* **Fix:** Improved error handling for API requests
* **Fix:** Workspaces created in ClickUp now appear automatically when refreshing the settings page
* Tested with WordPress 6.7.2
**Release date: March 10, 2025**

= 1.2.7 =
* **Added:** New donate link
* Tested with WordPress 6.7
**Release date: November 19, 2024**

= 1.2.6 =
* **Fix:** Bug with session
**Release date: May 09, 2024**

= 1.2.5 =
* **Fix:** Problem with status when create task
**Release date: April 10, 2024**

= 1.2.4 =
* **Improvement:** Loading tasks with AJAX
* Tested with WordPress 6.5
**Release date: April 3, 2024**

= 1.2.3 =
* Stop function for feed back when deactivate plugin

= 1.2.2 =
* Changed version SweetAlert2 to v11.4.8

= 1.2.1 =
* Changed version SweetAlert to v11.7.10

= 1.2.0 =
* Bug fixes with capability
* Add Support 13 types custom fields for Click Up, when creating task (URL Custom Field, Dropdown Custom Field, Email Custom Field, Phone Custom Field, Date Custom Field, Short Text Custom Field, Long Text Custom Field, Number Custom Field, Money Custom Field, Emoji (Rating) Custom Field, Label Custom Field, Attachment Custom Field, Checkbox Custom Field )

= 1.1.3 =
* Visual fixes

= 1.1.2 =
* Tested with WordPress 6.2
* fix feedback option

= 1.1.1 =
* fix warning for start session
* add feedback when deactivate plugin

= 1.1.0 =
* New option settings - Receive email notify for new created task or new comment
* Update BG translations

= 1.0.4 =
* Create task and add comment - notify by email from ClickUp

= 1.0.3 =
* Update BG translations

= 1.0.2 =
* Update url path for translate

= 1.0.1 =
* Add error return from API when config. "Create New Workspace"
* Fixes warnings in code
* Fixes Select2 bug
* Fix Fatal error for PHP 8.0
* Add Bulgarian translate

= 1.0.0 =
* First release of the plugin.


== Upgrade Notice ==

= 1.3.0 =
* Added multiple workspace support with real-time switching and automatic configuration reset when changing workspaces.
* Automatic migration from previous versions - your existing settings will be preserved and enhanced with workspace selection.


= 1.2.0 =
* Add Support 13 types custom fields for Click Up, when creating task (URL Custom Field, Dropdown Custom Field, Email Custom Field, Phone Custom Field, Date Custom Field, Short Text Custom Field, Long Text Custom Field, Number Custom Field, Money Custom Field, Emoji (Rating) Custom Field, Label Custom Field, Attachment Custom Field, Checkbox Custom Field )

= 1.1.2 =
* Tested with WordPress 6.2
* fix feedback option

= 1.1.1 =
* fix warning for start session
* add feedback when deactivate plugin

= 1.1.0 =
* New option settings - Receive email notify for new created task or new comment
* Update BG translations

= 1.0.3 =
* Update BG translations

= 1.0.2 =
* Update url path for translate

= 1.0.1 =
* Add error return from API when config. "Create New Workspace"
* Fixes warnings in code
* Fixes Select2 bug
* Fix Fatal error for PHP 8.0
* Add Bulgarian translate

= 1.0.0 =
* First release of the plugin.

