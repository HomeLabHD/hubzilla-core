### Versions and releases 

Hubzilla currently uses a standard version numbering sequence of $x.$y(.$z), for example ‘1.12’ or ‘1.12.1’. The first digit is the major version number. Major versions are released ‘approximately’ once a year, often in December.

The second digit is the minor version number. If this number is odd, it is a development version. If the number is even, it is a released version. Minor versions are usually released once a month (moved from the development version to the major version) when development is ‘stable’, but this is likely to increase. In the future, minor releases will be released between one and three months; this corresponds to a stable code point and when there is a general consensus in the community that the current code base is stable enough to consider a release.

The last digit is an interface or patch identifier. 

The release process involves changing the version number (by definition, the minor version number is odd, and the minor number is incremented). Once a year, the major version number of a major version is incremented and the minor version number is reset to 0.

The version candidate is moved to a new branch and testing begins or continues for a period of 1-2 weeks, or until all major issues have been resolved. This branch is usually labelled RC (Release Candidate); for example, 1.8RC stands for the upcoming release of version 1.8. At this point, the version number of the development branch is increased to the next odd number. (For example 1.9). New developments can then take place in the development branch.

Bug fixes should always be applied to ‘dev’ and transferred from there (usually with git cherry-pick) to the RC branch and, if necessary, to the master or official release branch.

At the time a release candidate is created, the language string file is frozen until a new version is released. Translation work can continue, but all translations should be submitted to ‘dev’ and merged into RC.
Once RC testing is complete, RC will be merged into ‘master’ and the RC version identifier removed; this will result in a final checkin to change the version number. The CHANGELOG file should also be updated at this point or shortly before. If there are conflicts during this final merge, the merge is cancelled and ‘git merge -s ours’ is applied. This results in the master branch being replaced by the content of the RC branch. Conflicts often arise due to string updates that were made to master after the last release and cannot simply be resolved without manual editing. As this is a release of tested code, manual editing is not recommended and the replacement merge strategy should be used instead. It is assumed that RC now contains the latest, well-tested code.

Once the release is live and merged into the master branch, the RC branch can be removed.

After the release, corrections can be made to master. If possible, these should be made in dev and ‘git cherry-pick’ should be used to merge; this preserves the commit information and avoids conflicts when merging in the next cycle. Only rarely does a patch only apply to the master branch. If necessary, this can be done. If the change is major, the version number of the interface should be increased. This is at the discretion of the community. In any case, a ‘git pull’ of the master branch should always result in the latest version, to which all patches are applied after release.

The interface number (the $z in $x.$y.$z) should be incremented in dev whenever a change is made that alters the interfaces or API in an incompatible way, so that any external packages (especially addons and API clients) that rely on the current behaviour can detect their own interfaces at the point where it has changed and change them accordingly.