#THM Groups

Extends and integrates Joomla! user, user group, content and contact management.

Joomla users and contacts are combined into profiles which integrate the
functions of both to create profiles. Profiles are extensible via custom
attributes which can be anything from a telephone to a list of publications.

The formatting of profiles is accomplished by templates which determine the
ordering and labeling of the individual attributes. Labels can be textual or
icons from the IcoMoon set can be chosen. The exception to this customization
arte the attributes for names, titles and pictures which receive special
handling in formatting and as a rule are never labeled.

Profiles can be allowed to create personalized content and will then be
associated with a category which shares their name.

Groups can be assigned roles. While semantically similar to user groups. They
denote real world groupings which may or may not have additional
responsibilities. Because these groupings have no consequences as concerns
Joomla access and viewing rights they are uncoupled from them and do not
unnecessarily bloat this resource.

##Component

The component provides an administrative area for resource management and a
public area for the display of managed resources. Additionally the the
component provides general purpose functionality also used by the other package
extenstions.

###Public views

* Advanced - a formatted display of profiles associated with a group
* Content Manager - a list of the content in a profile's personal category
* Content - a single personal content item
* Overview - a list of links to the profiles associated with a group
* Profile - a single profile with all published attributes
* Profile Editor - a form for editing profile attribute values and publication
(restricted)

##Menu Module

Creates a menu related to the profile or profile related content being
displayed. This will always display menu-published content, and can be
configured to display a link to the profile itself. Administrators and the
profile user will also be shown links to views which manage personal resources:
the profile editor, the content manager and the joomla content editor
(with the category restricted).

##Profiles Module

Displays profiles as parametrized in content. The profiles display is designed
to resemble the output of the 'Advanced' view. (See **Profiles Button Plugin**.)

##Content Plugin

Resolves parameter hooks inserted into content. (See **Profiles Button Plugin**.)

##Profiles Button Plugin

Inserts profiles data into content in two forms: static links which directly
link profiles, and parameter hooks which are later resolved by the **content
plugin** and displayed by the **profiles module**.

##System Plugin

Resolves usernames from the **THM Organizer Subject Pools Plugin** to links to
profiles.

##User Plugin

Synchronizes Joomla users with profiles.
