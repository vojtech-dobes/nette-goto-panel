## For Nette Framework

Provides interface to go to any destination within app.

## Installation

1. Move `GotoPanel.php` to your libs directory.
2. Register `GotoPanel` as component (e.g. in `BasePresenter`).

```php
protected function createComponentGotoPanel() { return new GotoPanel; }
```

3. Force initialization in `startup()`.

```php
$this['gotoPanel'];
```

![Panel used to show link](http://i46.tinypic.com/66gj5z.png)
