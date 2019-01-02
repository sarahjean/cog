<?php

namespace Drupal\cog_helper\Commands;

use Drush\Commands\DrushCommands;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class CogHelperCommands extends DrushCommands {

  /**
   * Create a theme using cog.
   *
   * @param array $options An associative array of options whose values come from cli, aliases, config, etc.
   * @option name
   *   A name for your theme.
   * @option machine-name
   *   [a-z, 0-9, _] A machine-readable name for your theme.
   * @option path
   *   The path where your theme will be created. Defaults to: themes/custom
   * @option description
   *   A description of your theme.
   * @usage drush cog-helper:create --name "Theme name"
   *   Create a sub-theme, using the default options.
   * @usage drush cog-helper:create --machine-name some_theme --name "Theme name"
   *   Create a sub-theme with a specific machine name.
   * @usage drush cog-helper:create --name "Theme name" --path=themes --description="This is a theme."
   *   Create a sub-theme in the specified directory with a custom description.
   *
   * @command cog-helper:create
   * @aliases cog-helper
   */
  public function create(array $options = ['name' => null, 'machine-name' => null, 'path' => 'themes/custom', 'description' => null]) {
    // See bottom of https://weitzman.github.io/blog/port-to-drush9 for details on what to change when porting a
    // legacy command.

    // Set up variables.
    $fileSystem = new Filesystem();
    $name = $options['name'];
    $machine_name = $options['machine-name'];

    if (!$name && !$machine_name) {
      //throw new \Exception()
      throw new \Exception(dt('The name of the theme was not specified.'));
    }

    if (!isset($machine_name)) {
      $machine_name = $name;
    }

    // Clean up the machine name.
    $machine_name = str_replace(' ', '_', strtolower($machine_name));
    $search = [
      '/[^a-z0-9_]/',
      // Remove characters not valid in function names.
      '/^[^a-z]+/',
      // Functions must begin with an alpha character.
    ];
    $machine_name = preg_replace($search, '', $machine_name);

    // Determine the path to the new sub-theme.
    if ($path = $options['path']) {
      $path = Path::canonicalize($path);
    }

    $sub_theme_path = Path::join(DRUPAL_ROOT,$path,$machine_name);

    // ***************************************************
    // Error check directories, then copy STARTERKIT.
    // ***************************************************.
    // Ensure the destination directory (not the sub-theme folder) exists.
    if (!is_dir(dirname($path))) {
      // throw new \Exception()
      throw new \Exception(dt('The directory "!directory" was not found.', ['!directory' => dirname($path)]));
    }

    // Ensure the STARTERKIT directory exists.
    $starterkit_path = Path::canonicalize(DRUPAL_ROOT . '/' . drupal_get_path('theme', 'cog') . '/STARTERKIT');

    if (!is_dir($starterkit_path)) {
      // throw new \Exception()
      throw new \Exception(dt('The STARTERKIT directory was not found in "!directory"', ['!directory' => dirname($starterkit_path)]));
    }

    $this->logger()->notice(dt('Copying files from STARTERKIT...'));

    // Make a fresh copy of the original starter kit.
    $fileSystem->mirror($starterkit_path, $sub_theme_path);

    // ***************************************************
    // Alter the contents of the .info.yml file.
    // ***************************************************.
    $this->logger()->notice(dt('Updating .info.yml file…'));

    $info_strings = [
      ': Cog Sub-theme Starter Kit' => ': ' . $name,
      '# core: 8.x' => 'core: 8.x',
      "core: '8.x'\n" => '',
      "project: 'cog'\n" => '',
    ];

    if ($description = $options['description']) {
      $info_strings['Read the included README.md on how to create a theme with cog.'] = $description;
    }

    // Remove unwanted theme info.
    $info_regexs = [
      ['pattern' => "/hidden: true\n/", 'replacement' => ''],
      ['pattern' => '/\# Information added by Drupal\.org packaging script on [\d-]+\n/', 'replacement' => ''],
      ['pattern' => "/version: '[^']+'\n/", 'replacement' => ''],
      ['pattern' => '/datestamp: \d+\n/', 'replacement' => ''],
    ];

    $this->cog_helper_file_replace($sub_theme_path . '/STARTERKIT.info.yml', $info_strings, $info_regexs);

    // ***************************************************
    // Replace STARTERKIT in file names and contents.
    // ***************************************************.
    $this->logger()->notice(dt('Replacing "STARTERKIT" in all files…'));

    // Iterate through the sub-theme directory finding files to filter.
    $directory_iterator = new \RecursiveDirectoryIterator($sub_theme_path);
    $starter_kit_filter = new \RecursiveCallbackFilterIterator($directory_iterator, function ($current, $key, $iterator) {
      // Skip hidden files and directories.
      if ($current->getFilename()[0] === '.') {
        return FALSE;
      }
      // Skip node_modules and the asset-builds folder.
      elseif ($current->getFilename() === 'node_modules' || $current->getFilename() === 'asset-builds') {
        return FALSE;
      }
      // Recursively go through all folders.
      if ($current->isDir()) {
        return TRUE;
      }
      else {
        // Only return Twig templates or files with "STARTERKIT" in their name.
        return strpos($current->getFilename(), '.twig') !== FALSE || strpos($current->getFilename(), 'STARTERKIT') !== FALSE;
      }
    });
    $iterator = new \RecursiveIteratorIterator($starter_kit_filter);
    $sub_theme_files = [];
    foreach ($iterator as $path => $info) {
      $sub_theme_files[$info->getFilename()] = $path;
    }

    // Add theme-settings.php to the list of files to filter.
    $sub_theme_files['package.json'] = $sub_theme_path . '/package.json';
    $sub_theme_files['theme-settings.php'] = $sub_theme_path . '/theme-settings.php';

    foreach ($sub_theme_files as $filename) {
      // Replace occurrences of 'STARTERKIT' with machine name of our sub theme.
      $this->cog_helper_file_replace($filename, ['STARTERKIT' => $machine_name]);

      // Rename all files with STARTERKIT in their name.
      if (strpos($filename, 'STARTERKIT') !== FALSE) {
        rename($filename, str_replace('STARTERKIT', $machine_name, $filename));
      }
    }

    // ***************************************************
    // Notify user of the newly created theme.
    // ***************************************************.
    $this->logger()->notice(dt('Starter kit for "!name" created in: !path', [
      '!name' => $name,
      '!path' => $sub_theme_path,
    ]));
  }

  /**
   * Replace strings in a file.
   */
  protected function cog_helper_file_replace($file_path, $strings, $regexs = []) {
    $file_path = Path::canonicalize($file_path);
    $file_contents = file_get_contents($file_path);

    if ($file_contents !== FALSE) {
      // Find text with strings.
      $find = array_keys($strings);
      $replace = $strings;
      $file_contents = str_replace($find, $replace, $file_contents);

      // Find text with regex.
      foreach ($regexs as $regex) {
        $file_contents = preg_replace($regex['pattern'], $regex['replacement'], $file_contents);
      }

      // Write to file.
      file_put_contents($file_path, $file_contents);
    }
  }
}
