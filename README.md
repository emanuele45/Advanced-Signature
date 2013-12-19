# Advanced-Signature

This mod provides few new features to the signature mechanism.

With this mod active it is possible to have multiple signatures per user (the maximum number can be defined by the admin panel), choose a different signatures per each post or personal message, choose to use a random signature (for each single post/PM, from the post page, or for all the posts/PMs, from the profile page) or to not show signature at all (for each single post/PM, from the post page, or for all the posts/PMs, from the profile page). Admins can define a default signature for all the users without one.

### SMF 2.0 only
Possibility to show/hide signatures on a per board and/or per topic level.


## Uninstall instructions for SMF 2.0
Before uninstall the mod please go to *Admin > Configuration > Features and Options > Signatures*, set the maximum number of signature to 1 and use SMF tool to apply rules to all existing signatures.

## Uninstall instructions for SMF 1.1.x
Before uninstall the mod use the maintenance action provided by the mod to reset all the signatures for all the users to the SMF-original single signature per user in: *admin > forum maitenance* and then select **Reset to single signature per user**.<br />
Additionally, to completely remove any trace of the mod (including database entries), please use the uninstall script provided. Please before use the uninstall script, uninstall the mod as usual and delete the package from the server, then upload the uninstall script and run it.

## Change log
  * _0.3.0 - beta_ several things including: a bit of redesign, removed useless queries, moved from implode/explode to un/serialize, added license, completely removed SMF 1.1.x support, other
  * _0.2.0_:
    * **stopped development for SMF 1.1.x series, all new features available only for SMF 2.0**
    * added support for hiding signatures at board level
    * added support for show/hide signatures at topic level
    * added permission to allow show/hide signatures in topics
    * logging of action hide/show signatures in topics
    * possibility to disable logging
  * _0.1.10_: fixed utf8 files
  * _0.1.9_:
    * updated version compatibility (SMF 1.1.14 and 2.0)
    * added UTF8 translations...hope they work
  * _0.1.8_: fixed an error log in SMF 2
  * _0.1.7_:
    * bug fixed - [reported by Kcmartz](http://www.simplemachines.org/community/index.php?topic=419856.msg3064843#msg3064843), the *characters remaining* was not working properly in SMF 2.0
    * fixed a php syntax error in Admin.php for SMF 1.1.x
  * _0.1.6_: added a couple of notes during the uninstall to remind about maintenance.
  * _0.1.5_: several improvements (thanks to the Customizer Team for the suggestions!)
  * _0.1.4_:
    * bug fixed - reported by [Kindred](http://www.simplemachines.org/community/index.php?topic=419856.msg2981110#msg2981110) (thanks!) it was possible to use HTML in signatures.
    fixed several errors reported in the log reported in the same post by Kindred
  * _0.1.3_:
    * fixed some errors in the log [a couple reported by Kindred](http://www.simplemachines.org/community/index.php?topic=419856.msg2965631#msg2965631) few others found by myself
    * drop down box for the signature choice not showed on new topics
    * the signatures were not shown at all in personal messages
    BBC not parsed (reported by [warezjasz](http://www.simplemachines.org/community/index.php?topic=419856.msg2965903#msg2965903))
  * _0.1.2_: added support for default signature (in case the user doesn't set one) and update to SMF 1.1.13 and SMF 2.0 RC5
  * _0.1.1_: bug fixed - [reported by DownloadPs3](http://www.simplemachines.org/community/index.php?topic=419856.msg2946353#msg2946353) (thanks Arantor) *Call to undefined function* in the profile page
  * _0.1.0_: initial release.
