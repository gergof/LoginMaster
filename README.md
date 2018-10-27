# LoginMaster
A login manager for PHP with two factor authentication that suits my needs.

---

This little PHP library is able to handle all the login jobs needed for you site.

### Features
- Log in users from a MySQL database
- Force users to fill a Captcha if they fail to log in
- Ban IP addresses if they fail to log in
- Remember users, so enable one click log in

### Usage
_Coming soon..._

### Needed database structure
```sql
CREATE TABLE `users`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `username` varchar(65) NOT NULL default '', /* optional */
    `password` varchar(255) NOT NULL default '',
    PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `login_history`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `user` int(4) UNSIGNED NOT NULL default 1,
    `date` timestamp NOT NULL default current_timestamp,
    `ip` varchar(45) NOT NULL default '0.0.0.0',
    `auth_token` varchar(65) NOT NULL default '',
    `user_agent` varchar(500) NOT NULL default '',
    `success` tinyint(1) NOT NULL default 0,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user`) REFERENCES users(`id`) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `login_remember`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `user` int(4) UNSIGNED NOT NULL default 0,
    `remember_token` varchar(65) NOT NULL default '',
    `until` timestamp NOT NULL default current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user`) REFERENCES users(`id`) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `login_bans`(
    `id` int(4) UNSIGNED NOT NULL auto_increment,
    `ip` varchar(45) NOT NULL default '0.0.0.0',
    `until` timestamp NOT NULL default current_timestamp,
    PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO users (`id`, `username`) VALUES (1, 'nouser');
```