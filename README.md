# cPanel-Hooks
Customized actions for WHM cPanel functions

## cPanel Hooks for Custom Functionality

This repository contains two custom cPanel hooks that provide additional functionality for managing email forwarding and enforcing cron job intervals.

### Hooks Information

### File Paths:

- **Email Forwarding Restriction Hook:** `/usr/local/src/custom/disable_email_forward_hook.php`
- **Cron Job Interval Enforcement Hook:** `/usr/local/src/custom/enforce_cron_interval_hook.php`

### Registered Hooks:

To view a list of registered hooks, use the following command:

```
/usr/local/cpanel/bin/manage_hooks list
```

### Installation

1. Create the custom source directory:

```
mkdir /usr/local/src/custom
```

2. Copy the desired hook file to the created directory:

```
cp /path/to/[file].php /usr/local/src/custom/
```

3. Set ownership and permissions for the copied file:

```
chown root:root /usr/local/src/custom/[file].php
chmod 755 /usr/local/src/custom/[file].php
```

4. Add the hook using the following command:

```
/usr/local/cpanel/bin/manage_hooks add script /usr/local/src/custom/[file].php
```

### Uninstallation

To remove the hook, use the following command:

```
/usr/local/cpanel/bin/manage_hooks delete script /usr/local/src/custom/[file].php
```

### PHP Log

Error logs for PHP can be found at:

```
/usr/local/cpanel/logs/error_log
```

### Debug Mode (Optional)

Toggle the debug mode using the "Debug Mode" option in the "Development" section of WHM's Tweak Settings:

- WHM >> Home >> Server Configuration >> Tweak Settings

### Reference

- https://features.cpanel.net/topic/option-to-prevent-forwarders-to-free-email-accounts?_ga=2.23695548.721040204.1686896615-206909826.1686896615
- https://gist.github.com/gmariani/70359dd09c702111979835e52f980366
- https://api.docs.cpanel.net/guides/quickstart-development-guide/tutorial-create-a-standardized-hook/
