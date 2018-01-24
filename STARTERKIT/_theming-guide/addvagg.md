# Using AdvAgg with Cog
###### _Last updated January 2018_

[AdvAgg](https://www.drupal.org/project/advagg) (Advanced CSS/JS Aggregation) is a contributed module with a set of submodules that can improve the frontend performance of your Drupal site. **Please note that AdvAgg is NOT required by Cog.**

Despite the great benefits of optimizing your code, there are always risks and we recommend vigilant testing of your site when you optimize through Cog, AdvAgg or any other method.

#### Links
* [AdvAgg Project](https://www.drupal.org/project/advagg)
* [AdvAgg Documentation](https://www.drupal.org/docs/8/modules/advanced-cssjs-aggregation)

#### Contents
* [AdvAgg Recommended Settings](#advagg)
 * [Advanced Aggregates CDN](#cdn)
 * [Advanced Aggregates CSS/JS Validator](#validate) _(pending)_
 * [Advanced Aggregates External Minifier](#ex_mini)
 * [Advanced Aggregates Minify CSS](#css_mini)
 * [Advanced Aggregates Minify JS](#js_mini)
 * [Advanced Aggregates Modifier](#modify) _(pending)_
 * [Advanced Aggregates Old IE Compatibilty Enhancer](#ie)
* [Glossary of Terms](#glossary)

<a id="advagg"></a>
## Advanced Aggregates Settings
_Pending_
<!-- @TODO -->

<a id="cdn"></a>
## Advanced Aggregates CDN

Currently this submodule allows you to load only jQuery and jQuery UI from Google or Microsoft's CDN. The jQuery UI CSS that Drupal core uses is not available but a similar theme is.

Using the Drupal Core jQuery is recommended but if your site is experiencing performance issues, loading jQuery from a public file may help with your speed if a visitor's browser has cached the public jQuery files from another site.

<a id="validate"></a>
## Advanced Aggregates CSS/JS Validator
_Pending_
<!-- @TODO -->

<a id="ex_mini"></a>
## External Minifier
###### [What is minification?](#minification)

This option presents a security risk and is not supported by AdvAgg or the Cog team. Recommended only for advanced users at your own risk.

<a id="css_mini"></a>
## CSS Minification
###### [What is minification?](#minification)

CSS Minification is enabled by default in Cog, however, you may prefer to use AdvAgg to minify your CSS because it will also minify core and module CSS as well.

At the time this documentation was written, AdvAgg provides two built-in minifiers: Drupal's core cleaning algorithm and YUI. YUI is recommended because it returns smaller files than core and is well documented: [YUI CSS Minification](http://yui.github.io/yuicompressor/css.html).

If you are using AdvAgg for minification, we recommend disabling Cog's minification. Go to [gulp-tasks > build.js](../gulp-tasks/build.js) and remove the `'minify:css'` line from both the `build` and the `build:dev` tasks.

Checking the `Add licensing Comments` option will provide a comment with the path to your source file, which will help if you are trying to debug minified code.

<a id="js_mini"></a>
## JS Minification
###### [What is minification?](#minification)

<!-- @TODO: Update with latest Babel settings. -->
Currently, there is no default minification for JavaScript in Cog.

Enabling JS minification in AdvAgg is recommended to improve frontend performance across your site.

At the time this documentation was written, AdvAgg provides three built-in minifiers: JSMin+, JShrink and JSqueeze. Additionally, JSMin can be installed separately.

JSqueeze produces the smallest files and is recommended. 

Checking the `Add licensing Comments` option will provide a comment with the path to your source file in your minified file. This will help if you are trying to debug minified code.

<a id="modify"></a>
## Advanced Aggregates Modifier
_Pending_
<!-- @TODO -->

<a id="ie"></a>
## Advanced Aggregates Old IE Compatibilty Enhancer

There are no reported conflicts with this module but the method required to override core is high risk.

If you need to support Internet Explorer 9 or earlier and are using AdvAgg to combine CSS, it is recommended that you configure this module to prevent more than 4095 selectors in an aggregate CSS file. That is the maximum nuber of files that can be handled by older IE browsers.

_Please note that Internet Explorer 8, 9 and 10 were deprecated by Microsoft in January 2016._ 

It is best not to try support outdated browsers unless your current site analytics show significant traffic.

<a id="glossary"></a>
## GLOSSARY OF TERMS:

<a id="minification"></a>
* **Minification:** Reduces a size of a file by removing whitespace (e.g. spaces, tabs and new lines).

<a id="uglification"></a>
* **Uglification:** Reduces the size of a file at the expense of readability. Removes whitespace, unnecessary characters, shortens long variable and function names, simplifies conditional statements, booleans and functions.

<a id="compression"></a>
* **Compression:** Reduces the size of a file  by identifying repetitive patterns and replaces duplicate with a unique identifier that is shorter than the original pattern.

<a id="magnification"></a>
* **Magnification:** Increases the size of a file with the purpose of making it human readable with whitespace and formatting.
