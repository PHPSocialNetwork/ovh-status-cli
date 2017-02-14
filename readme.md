### What is that ?

This little utility will allow you to watch the "OVH travaux" page to check if there's work in progress based on a set of rules that you must define.

### How it works ?

It works in command line interface (CLI) and prints the result of matching rules.

### What language has been used to build this utility ?

PHP of course !! It has a dependency to PhpFastCache to cache the matching rules for a while.
 
### How to install it ?

This project makes use of [composer](https://getcomposer.org/) to manage dependencies. Extract the archive then proceed to this command:
`composer install`

### What are the printed result ?
##### If everything is fine and no rule is matching:
`[OK] No rules were matching`

##### If one or more rule is matching ?
`
[PLANNED] Matching rule \*\*\*CLOUD*** for OVH task #23163 at http://travaux.ovh.net/?do=details&id=23163 with summary "Public cloud planned maintenance"

[KO] At least 1 rule(s) were matching
`

### Are you accepting pull request ?
As long they are relevant, yes we do, may this utility will help some people, so feel free to contribute :)

### Are you working for/with OVH company ?
No, we does not endorse anything or speak on behalf of the OVH company, we're just passionate developers that love free code. And beer also, we love beer.
