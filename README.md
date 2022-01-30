# Dropbox-unresponsive-fix

On macOS Dropbox occasionally becomes unresponsive and starts using 100% of the CPU. It does not seem to fix itself after any amount of time, so this script aims to fix that.

This script checks if Dropbox is not using too much CPU. If it is, it kills and restarts Dropbox.

### Usage
To run the script: `php main.php`  

### Recommended usage:  
- Clone repository to `~/Documents/dropbox-unresponsive-fix`:
  ```bash
  git clone git@github.com:mindaugasw/dropbox-unresponsive-fix.git ~/Documents/dropbox-unresponsive-fix
  ```
- If needed, modify configuration in `config.local.php`:
  ```bash
  cp config.php config.local.php
  ```
- Add to Crontab with `crontab -e` to run every 3 minutes:
  ```
  */3 * * * * /opt/homebrew/bin/php ~/Documents/dropbox-unresponsive-fix/main.php 2>>~/Documents/dropbox-unresponsive-fix/var/main.log
  ```
- Run command on startup to rotate logs:
  ```bash
  tail -5000 ~/Documents/dropbox-unresponsive-fix/var/main.log | sponge ~/Documents/dropbox-unresponsive-fix/var/main.log
  ```
- If needed, see script log in `var/main.log`
