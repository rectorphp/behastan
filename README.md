# Behastan - Modern Static analysis for Behat tests

[![Downloads total](https://img.shields.io/packagist/dt/rector/behastan.svg?style=flat-square)](https://packagist.org/packages/rector/behastan/stats)

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

### 2. Find duplicated patterns (`duplicated-patterns`)

Same as services, there should be unique definition patterns:

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

### 4. Find duplicate scenario titles (`duplicate-scenario-titles`)

In Behat, each scenario should have a unique name to ensure clarity and avoid confusion during test execution and later debugging. This rule identifies scenarios that share the same name within your feature files:

```yaml
Feature: User Authentication

    Scenario: User logs in successfully
        When the user enters valid credentials
        Then login should be successful

    Scenario: User logs in successfully
        When the user enters invalid credentials
        Then an error message should be displayed
```

<br>

### 5. Rerport redundant regex definitions (`redundant-regex-definitions`)

When defining step definitions in Behat, it's common to use regular expressions to match patterns. However, sometimes these regex patterns can be overly complex or redundant, making them harder to read and maintain. This rule identifies such redundant regex definitions:

```diff
-#[When('#I have apples#')]
+#[When('I have apples')]
 public function iHaveApples()
 {
     // ...
 }
```

<br>

*Protip*: Add this command to your CI, to get instant feedback of any changes in every pull-request.

That's it!

<br>

Happy coding!
