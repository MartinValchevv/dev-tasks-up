=== DevtasksUp - ClickUp integration ===
Contributors: martinvalchev
Donate link: https://revolut.me/mvalchev
Tags: dev, task, projects, ClickUp, integration, admin ,task management, tasks, task priority, developers, clients
Requires at least: 5.3
Tested up to: 6.1.1
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The plugin integrates ClickUp into the admin for streamlined task management. Simply add an API key for full access to create tasks, leave comments, and view task priority. Ideal for developers to set up for clients for seamless task delegation.

== Description ==

Unlock effortless task organization with the ClickUp plugin's seamless integration into your admin. Simply configure your account with an API key and watch as task creation, comment writing, and task prioritization become a breeze. Ideal for developers to customize for their clients, ensuring direct and efficient task delegation.

The options for this plugin include:

*   Integration of ClickUp into the admin
*   Configuration using an API key on your account
*   Task creation
*   Comment writing
*   Viewing the most important aspect of a task
*   Customization by developers for their clients
*   Direct task delegation from the client to the developer.

The helper libraries plugin uses the following:

*   [Bootstrap v5.2](https://getbootstrap.com/docs/5.2/getting-started/introduction/)
*   [FontAwesome v5](https://fontawesome.com/)
*   [Select2](https://select2.org/)
*   [SweetAlert2](https://sweetalert2.github.io/)
*   [ClickUP API](https://clickup.com/api/)

Notes:

*   In order for the plugin to work correctly, it must be configured from an admin account
*   The corresponding Api key must be filled in, upon successful connection, it is mandatory to choose whether you want to configure it in a new environment or in a current one

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

== Screenshots ==

1. Settings
2. Task Center
3. Opened Task

== Changelog ==

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

