# Behastan - Modern Static analysis for Behat tests

[![Downloads total](https://img.shields.io/packagist/dt/behastan/behastan.svg?style=flat-square)](https://packagist.org/packages/behastan/behastan/stats)

Find unused and duplicated definitions easily &ndash; without running Behat tests.

<br>

## Install

```bash
composer require rector/behastan --dev
```

## Usage

```bash
vendor/bin/behastan analyse tests
```

<br>

Do you want to skip some rule? You can:

```bash
vendor/bin/behastan analyse tests --skip=<rule-identifier>

# e.g.
vendor/bin/behastan analyse tests --skip=duplicated-contents
```

<br>

Here are the available rules:

### 1. Find duplicated definitions contents (`duplicated-contents`)

Some definitions have similar patterns, even identical contents:

```php
use Behat\Step\When;

#[When('load a user profile')]
public function loadAUserProfile()
{
    $this->loadRoute('/user-profile');
}

#[When('load user profile')]
public function loadUserProfile()
{
    $this->loadRoute('/user-profile');
}
```

Better use a one definition with single pattern, to make your tests more precise and easier to maintain.

<br>

### 2. Find duplicated masks (`duplicated-masks`)

Same as services, there should be unique definition masks:

```php
use Behat\Step\When;

#[When('load homepage')]
public function loadUserProfile()
{
    $this->loadRoute('/homepage');
}

#[When('load homepage')]
public function loadUserProfile()
{
    $this->loadRoute('/homepage/default');
}
```

Make them unique with different behavior, or merge them and use one definition instead.

<br>

### 3. Find unused definitions (`unused-definitions`)

Behat uses `@When()`, `@Then()` and `@Given()` annotations or attributes to define a class method that is  called in `*.feature` files. Sometimes test change and lines from `*.feature` files are deleted. But what about definitions?

```diff
# some *.feature file
 Scenario: Load admin dashboard
-  When load admin dashboard
+  When load homepage
```

↓

```php
use Behat\Step\When;

#[When('load admin dashboard')]
public function loadAdminDashboard()
{
    $this->loadRoute('/admin/dashboard');
}
```

This rule spots definitions that are no longer needed, so you can  remove them.

<br>

*Protip*: Add this command to your CI, to get instant feedback of any changes in every pull-request.

That's it!

<br>

Happy coding!
