## For Nette Framework

Provides interface to go to any destination within app.

## Installation

1. Get the source code:
	* Move `GotoPanel.php` to your libs directory.
	* Add `"Clevisaci/GotoPanel": "*"` to your `composer.json`.
2. Register `GotoPanel` as component (e.g. in `BasePresenter`).
3. Force initialization in `startup()`.

```php
protected function startup()
{
	parent::startup();
	$this['gotoPanel'];
}

protected function createComponentGotoPanel()
{
	return new GotoPanel;
}
```

![Panel used to show link](http://i46.tinypic.com/66gj5z.png)
