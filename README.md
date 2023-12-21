# cPanel-Hooks
Customized actions for WHM cPanel functions

## CPanel Hooks for Custom Functionality

This repository contains two custom CPanel hooks that provide additional functionality for managing email forwarding and enforcing cron job intervals.

### Hooks Information

### File Paths:

- **Email Forwarding Hook:** `/usr/local/src/custom/disable_email_forward_hook.php`
- **Cron Job Interval Enforcement Hook:** `/usr/local/src/custom/enforce_cron_interval_hook.php`

### Registered Hooks:

To view a list of registered hooks, use the following command:

```
/usr/local/cpanel/bin/manage_hooks list
```

### Installation

1. Navigate to the custom source directory:

```
cd /usr/local/src/custom
```

2. Copy the desired hook file to the folder:

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

### Debug Mode

Toggle the debug mode using the "Debug Mode" option in the "Development" section of WHM's Tweak Settings:

- WHM >> Home >> Server Configuration >> Tweak Settings
