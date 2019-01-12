# LoginMaster
A fully customizable login manager for PHP.

---

This little PHP library is able to handle all the login jobs needed for you site.

---

## Features
- Log in users from a MySQL database
- Force users to fill a Captcha if they fail to log in
- Ban IP addresses if they fail to log in
- Remember users, so enable one click log in

---

## Usage

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

### Create a configuration object
```php
$config=new \LoginMaster\Config($pdo, $sessionLifetime, $captchaEnable, $captchaAfter, $captchaSitekey, $captchaSecretkey, $banEnable, $banAfter, $banTime, $lookTime, $rememberEnable, $rememberTime, $usernameField);
```
__Parameters__
- _$pdo_: A PDO instance to interface with the database
- _$sessionLifetime_: For how much time the user is supposed to stay logged in
- _$captchaEnable_: Do you want to enable captcha?
- _$captchaAfter_: Enable captcha only after X unsuccessfull login attempts (0 enables it always)
- _$captchaSitekey_: Site key from [Captcha admin](https://www.google.com/recaptcha/admin)
- _$captchaSecretkey_: Secret key from [Captcha admin](https://www.google.com/recaptcha/admin)
- _$banEnable_: Enable banning users
- _$banAfter_: Ban users after X unsuccessfull login attempts
- _$banTime_: Ban users for X seconds
- _$lookTime_: Seconds to look in login history when counting failed login attempts
- _$rememberEnable_: Do we want to allow users the option of one-click login (Not the safest. Only use with factor authentication!)
- _$rememberTime_: Remember users for X days
- _$usernameField_: The database field which's conent is entered as username. It __must__ be unique

### Implement the interfaces

#### Handler
```php
interface Handler{
    public function handle($state, $target=0);
}
```

This is the class that will be used to handle events.

__Available events__
- LoginMaster::LOGIN_FAILED
- LoginMaster::CAPTCHA_FAILED
- LoginMaster::BANNED
- LoginMaster::LOGIN_OK

With LOGIN_OK there will be an additional parameter passed (_$target_) which contains the ID of the logged in user.

#### PasswordEngine
```php
interface PasswordEngine{
    public function verify($input, $database);
}
```

This is used to validate the entered password. You can use any password storage library (I recommend [PasswordStorage](https://github.com/defuse/password-hashing) since it is cross platform and easy to implement.) you want, just __don't store the passwords cleartext__.

#### TwoFactor
```php
interface TwoFactor{
    public function challange($userId);
}
```

This is used to validate if the two factor authentication was successfull. You can implement here anything you want. Just keep in mind that the two factor auth info needs to be available during login time (wrong implementation. I know. Will be changed in the future).

If you don't want to use two factor authentication, just live it on _defaultTwoFactor_ which's _challange_ function simply returns a ```true```  no matter what.

### Create an instance
```php
$lm=new LoginMaster($config, new implementedHandler(), new implementedPasswordEngine(), new implementedTwoFactor());
$lm->init();
```

### Use it in your files
At top of you login form type:
```php
$lm->loginPrepare();
```

Then you can use the following public functions:
- _$lm->login($username, $password, $remember)_: Log in
- _$lm->logout()_: Log out
- _$lm->validateLogin()_: Check if the user is logged in
- _$lm->isRememberingUser()_: Returns the user's ID if it has the remember tokin of an user saved
- _$lm->forgetUser()_: Forget user
- _$lm->printCaptcha($dark)_: This will print the captcha when we need it

---

## Notes
This library isn't tested very well yet. I'm also not a security expert, so feel free to commit if you have an idea to improve this.
I just created this to suit my need and be fully customizable for future uses.

## Upcoming
I will add support to use this when authenticating with REST APIs.