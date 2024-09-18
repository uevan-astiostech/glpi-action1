# GLPI Plugin for Action1 Integration

## Overview
This plugin integrates Action1, a patch management tool, with the GLPI system. It allows the Super Admin in GLPI to manage patching operations, retrieve data from Action1, and perform relevant operations directly from GLPI using Action1's RESTful API. The plugin uses OAuth 2.0 for authentication, storing the `client_id`, `client_secret`, and `access_token` for secure API communication.

## Features
- OAuth 2.0 Authentication with Action1 (Client ID and Client Secret)
- Asynchronous data fetching using AJAX (or CRON jobs) from Action1
- Configuration page to manage credentials and token information
- Automatic token re-authentication when expired
- Restricted access to the Super Admin role for patch management

## Documentation
For detailed API documentation on Action1, visit [Action1 API Documentation](https://app.action1.com/apidocs/#/).

## Requirements
- GLPI 10.0.0+
- PHP 8.0+
- MySQL 5.7+
- Action1 API Credentials (Client ID and Client Secret)

## Installation
1. Clone the repository into your GLPI `plugins/` directory:
   ```bash
   git clone <repository_url> plugins/action1_integration


## Getting started

For convenience, you can place the `empty` directory in you GLPI plugins directory.

You can use provided `plugin.sh` script in the main directory to get started. You'll have to pass name and version of your plugin in the call:
```
./plugin.sh MyGreatPlugin 0.0.1
```

Please note than you really want to avoid special characters in name; as it will be used for paths, methods names, constants, and so on.

This will create a directory named `mygreatplugin` at the same level than the `empty` directory that contains the plugin;
all methods will be named accordingly (see result in `hook.php` and `setup.php`). Note that `My-Great-Plugin` would also create a directory named `mygreatplugin`.

You can also provide a destination path (ie. if your `empty` directory is not in the GLPI's plugins directory):
```
./plugin.sh MyGreatPlugin 0.0.1 /path/to/glpi/plugins/
```

### Replacements

* `{NAME}` will be replaced by the name you've provide, verbatim,
* `{VERSION}` will be replaced byt the version you've provided,
* `{LNAME}` will be replaced byt the lowercased name,
* `{UNAME}` will be replaced by the uppercased name,
* `{YEAR}` will be replaced by the current year.
