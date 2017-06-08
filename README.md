```
$ composer require barantaran/platformcraft
```
then

```php
<?php
use \Barantaran\Platformcraft\Platform;

$platform = new Platform($apiUserId, $HMACKey);

$platform->setupVideoPlayer($videoFilePath);
$platform->attachImageToPlayer($imageFilePath, $videoPlayerId);
```
