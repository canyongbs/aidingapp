# PHP CS Fixer (PHP Files)

This application comes with a PHP Coding Standards Fixer configuration [php-cs-fixer.php](../php-cs-fixer.php)

Within this configuration file is the agreed upon standards for the team.

When setting up the project you should set up your IDE to automatically apply these rules to the current file you are working in on save. Common IDE configurations on how to do that are listed below.

## IDE Configuration
### PHPStorm / Jetbrains Products
##### Inspections (fixer does not run, only highlights violations)
1. Set the correct php cs fixer binary in:
    - Language & Frameworks -> PHP -> Quality tools
2. Within phpstorm, go to:
    - Settings -> Editor -> Inspections -> PHP -> Quality tools
    - Make sure PHP CS Fixer validation is checked
    - In the right-hand pane, after selecting this option, click the browse (`...`) button and select the php-cs-fixer.php file previously saved in your home directory
    - Click Apply and OK
##### File Watcher (fixer runs on save action)
1. In PHPStorm, create a file watcher (under "Preferences->Tools")
2. Uncheck all the "Advanced Options"
3. Edit the following settings:
```
Name: PHP Style fixer
File type: PHP
Scope: Current File
Path: $ProjectFileDir$/vendor/friendsofphp/php-cs-fixer/php-cs-fixer
Arguments: fix $FileDir$/$FileName$ --verbose --config=$ProjectFileDir$/php-cs-fixer.php
```
### VS Code
1. Install [PHP CS Fixer](https://marketplace.visualstudio.com/items?itemName=fterrag.vscode-php-cs-fixer) extension
2. Configure Extension, in `Settings as JSON`
    ```
    "vscode-php-cs-fixer.config": "/{path_to_repository}/php-cs-fixer.php",
    "vscode-php-cs-fixer.toolPath": "/{path_to_repository}/vendor/bin/php-cs-fixer",
    "[php]": {
        "editor.defaultFormatter": "fterrag.vscode-php-cs-fixer"
    },
    ```
### Sublime Text 3
1. Find `PHP CS Fixer` using Package Control: Install Package (cmd-shift-p)
2. Under Preferences -> Package Settings -> PHP CS Fixer -> Settings - User, configure the extension using the following JSON
    ```
    {
        "config": "/{path_to_repository}/php-cs-fixer.php",
        "on_save": true,
    }
    ```

---

# Prettier (CSS, JS, and *.blade.php Files)

This application comes with [Prettier](https://www.npmjs.com/package/prettier) and the [Prettier Blade Plugin](https://www.npmjs.com/package/@shufo/prettier-plugin-blade) to be used in order to set and automatically apply code formatting rules to our code-base blade files.

Example of how to run the formatter manually:

```bash
spinc prettier-lint
```

When setting up the project you should set up your IDE to automatically apply these rules to the current file you are working in on save. Common IDE configurations on how to do that are listed below.

## IDE Configuration

### PHPStorm / Jetbrains Products
You can use Prettier Plugin for JetBrains IDE.

Add extension setting `blade.php` to `File | Settings | Languages & Frameworks | JavaScript | Prettier | Run for files:`

e.g.

`{**/*,*}.{css,js,ts,jsx,tsx,blade.php}`

and turn on checkbox `On 'Reformat Code' action`

### VSCode
You can use [Prettier extension for VSCode](https://github.com/prettier/prettier-vscode) to format blade within VSCode. You must install this plugin as local dependencies. see [https://github.com/prettier/prettier-vscode#prettier-resolution](https://github.com/prettier/prettier-vscode#prettier-resolution)

If you want to use formatter without Prettier, please consider using [vscode-blade-formatter](https://github.com/shufo/vscode-blade-formatter)

---

# LaraStan (PHP Files)

This application comes with a [LaraStan](https://github.com/nunomaduro/larastan) installed to provide static analysis (technically "code analysis" as it loads the Service Container) of the code-base.

Example of how to run the static analysis manually:

```bash
spinc larastan
```

Setup for automatic static analysis is per IDE and should be configured as such.

Configuration is set in the [phpstan.neon.dist](../phpstan.neon.dist) file. If additional local configuration is desired, you can create a `phpstan.neon` file in the root of the project and it will be loaded automatically.

Example `phpstan.neon` file:

```neon 
includes:
    - phpstan.neon.dist

parameters:
    editorUrl: 'phpstorm://open?file=%%file%%&line=%%line%%'
```

---

# IDE Helper

This application comes with a [Laravel IDE Helper](https://github.com/barryvdh/laravel-ide-helper) installed to provide better auto-completion for Laravel Facades and other classes.

Helper and meta files can be generated by running the following commands, and should be run after any changes to the code-base that would affect the auto-completion.

```bash
spina ide-helper:generate
spina ide-helper:meta
```

Additionally, a helper file and related DocBlocks are generated to provide auto-completion and static analysis for Models and their relationships. This is done by running the following command, and should be run after any changes to Models.

```bash
spina ide-helper:models -M
```
