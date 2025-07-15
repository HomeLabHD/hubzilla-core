## Comanche page description language

Comanche is a BBCode-like markup language that can be used to create elaborate and complex web pages by assembling them from a series of components, some of which are pre-built and others that can be defined on the fly. Comanche uses a page description language to create these pages.
Comanche primarily selects which content should appear in the various areas of the page. The various areas have names, and these names may change depending on the layout template selected.

### Page templates

There are currently five layout templates, unless your website offers additional layouts.

**Standard template**

The default template defines a ‘nav’ area at the top, ‘aside’ as a sidebar with a fixed width, ‘content’ for the main content area and ‘footer’ for a page footer.

**Full template**

The full template corresponds to the default template except that there is no ‘aside’ area.

**Choklet**

The Choklet template offers a range of fluid layout styles that can be set to taste:

- (default flavour) - a two-column layout similar to the default template, but more flexible
- bannertwo - a two-column layout with a banner area, compatible with the default template on small displays
- three - three-column layout (adds a ‘right_aside’ area to the standard template)
- edgestwo - two-column layout with fixed margins
- edgesthree - three-column layout with fixed margins
- full - three-column layout with fixed margins and the addition of a ‘header’ area below the navigation bar

**Redable**

A template for reading longer texts in full screen mode (i.e. without a navigation bar). Three columns: aside, content and right_aside.
For maximum readability, it is advisable to use only the middle content column.

**Zen**

Gives you the freedom to do everything yourself. Just a blank page with a content area.
To select a layout template, use the ‘template’ tag.

```
[template]full[/template]
```

To select the template ‘choklet’ with the flavour ‘three’:

```
[template=three]choklet[/template]
```

The default template is used if no other template is specified. The template can use arbitrary names for the content regions. You will use ‘region’ tags to decide what content should be placed in which regions.
Three ‘macros’ have been defined for your use.

```
$htmlhead - replaced with the site head content.
$nav - replaced with the site navigation bar content.
$content - replaced with the main page content.
```

By default, `$nav` is inserted into the ‘nav’ page area and `$content` into the ‘content’ area. You only need to use these macros if you want to change the order of the elements or move them to other areas.
To select a theme for your page, use the ‘theme’ tag.

```
[theme]suckerberg[/theme]
```

This selects the theme ‘suckerberg’. By default, the theme preferred by your channel is used.

```
[theme=passion]suckerberg[/theme]
```

This selects the theme named ‘suckerberg’ and chooses the ‘passion’ scheme (theme variant). Alternatively, it is also possible to use compressed theme notation.

```
[theme]suckerberg:passion[/theme]
```

The compressed notation is not part of Comanche itself, but it is recognised by the Hubzilla platform as a theme specifier.

**Navbar**

```
[navbar]tucson[/navbar]
```

Use the ‘tucson’ template for the navigation bar and CSS rules. By default, the ‘default’ template is used for the navigation bar.

**Regions**

Each region has a name, as mentioned above. You specify the region you are interested in with a ‘region’ tag containing the name. Any content you want to place in that region should be placed between the opening region tag and the closing tag.

```
[region=htmlhead]....content goes here....[/region]
[region=aside]....content goes here....[/region]
[region=nav]....content goes here....[/region]
[region=content]....content goes here....[/region]
```

**CSS and Javascript**

We have the option of including Javascript and CSS libraries in the htmlhead section. Currently we use jquery (js), bootstrap (css/js) and foundation (css/js).
This overwrites the htmlhead of the selected theme.

```
[region=htmlhead]
   [css]bootstrap[/css]
   [js]jquery[/js]
   [js]bootstrap[/js]
[/region]
```

**Menus and blocks**

The website creation tools allow you to create menus and blocks in addition to page content. These provide a set of existing content that can be placed in the areas and order you specify. Each of these elements has a name that you set when you create the menu or block.

```
[menu]mymenu[/menu]
```

This places the menu ‘mymenu’ at this point on the page, which must be within an area.

```
[menu=horizontal]mymenu[/menu]
```

This places the menu named ‘mymenu’ at this point on the page, which must be within an area. It also assigns the class ‘horizontal’ to the menu. The class ‘horizontal’ is defined in the redbasic theme. It may or may not be available in other themes.

```
[menu][var=wrap]none[/var]mymenu[/menu]
```

The `[var=wrap]none[/var]` variable in a block removes the enclosing div element from the menu.

```
[block]contributors[/block]
```

This places a block named ‘contributors’ in this region.

```
[block=someclass]contributors[/block]
```

This places a block named ‘contributors’ in this region. In addition, the class ‘someclass’ is applied to the block. This replaces the default block classes ‘bblock widget’.

```
[block][var=wrap]none[/var]contributors[/block]
```

The variable `[var=wrap]none[/var]` in a block removes the enclosing div element from the block.

**Widgets**

Widgets are executable applications provided by the system that you can place on your page. Some widgets require arguments that you can use to customise the widget to your purpose. System widgets are listed here. Widgets can also be created by plugins, themes or your website administrator to provide additional functions.
Widgets and arguments are specified with the tags ‘widget’ and ‘var’.

```
[widget=recent_visitors][var=count]24[/var][/widget]
```

This loads the ‘recent_visitors’ widget and sets the ‘count’ argument to ‘24’.

**Comments**

The ‘comment’ tag is used to delimit comments. These comments are not displayed on the rendered page.

```
[comment]This is a comment[/comment]
```

**Conditional execution**

You can use an ‘if’ construct to make decisions. These are currently based on the system configuration variable or the current observer.

```
[if $config.system.foo]
  ... the configuration variable system.foo evaluates to ‘true’.
[else]
  ... the configuration variable system.foo evaluates to ‘false’.
[/if]

[if $observer]
  ... this content will only be show to authenticated viewers
[/if]
```

The ‘else’ clause is optional.
In addition to the Boolean evaluation, several tests are supported.

```
[if $config.system.foo == bar]
  ... the configuration variable system.foo is equal to the string ‘bar’
[/if]
[if $config.system.foo != bar]
  ... the configuration variable system.foo is not equal to the string ‘bar’
[/if]
[if $config.system.foo {} bar ]...
 the configuration variable system.foo is a simple array containing a value ‘bar’
[/if]
[if $config.system.foo {*} bar]...
 the configuration variable system.foo is a simple array containing a key named ‘bar’
[/if]
```

**Complex example**

```
[comment]use an existing page template which provides a banner region plus 3 columns beneath it[/comment]

[template]3-column-with-header[/template]

[comment]Use the "darknight" theme[/comment]

[theme]darkknight[/theme]

[comment]Use the existing site navigation menu[/comment]

[region=nav]$nav[/region]

[region=side]

    [comment]Use my chosen menu and a couple of widgets[/comment]

    [menu]myfavouritemenu[/menu]

    [widget=recent_visitors]
        [var=count]24[/var]
        [var=names_only]1[/var]
    [/widget]

    [widget=tagcloud][/widget]
    [block]donate[/block]

[/region]



[region=middle]

    [comment]Show the normal page content[/comment]

    $content

[/region]



[region=right]

   [comment]Show my condensed channel "wall" feed and allow interaction if the observer is allowed to interact[/comment]

    [widget]channel[/widget]

[/region]
```

