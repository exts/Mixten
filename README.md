# Mixten
Mixten is a PHP 7.1 Microframework based on [Starch](https://github.com/starchphp/starch). The name comes from 'Mixed' and 'Extensions'. I like mixing components to build a rich application, but there's a lot of frameworks out there that are more 'their way' than 'your way' and that was a problem for me. 

Like Brammm (starch dev), I too am a fan of Slim and Silex. My issue is I never really needed all the features that slim and silex use, all I needed was a proper router, a decent dependency injector and that's about it.

I tried expressive 2.0 and it's good, it's real good (I might even attempt to use [Canister](https://github.com/exts/Canister) with it in the future), but the issue I had with Expressive was you'd end up with a ton of boilerplate factory classes for setting up Action classes. I thought that was unnecessary and should have been as simple as passing your class to the route and it passing the necessary dependencies when it needs them.

I'm going to put this on packagist, but this library/microframework won't be stable until I've run it in the ground with a few personal projects to see what pitfalls I run into and if I need to add any features.

# Example

```php
$app = new Application();
$app->any('/account/update', cc(Account::class, 'update'));
$app->run();
```