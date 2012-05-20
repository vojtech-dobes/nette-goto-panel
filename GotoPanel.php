<?php

namespace Clevispace\Diagnostics;

use Nette\Application\UI;
use Nette\Diagnostics\Debugger;
use Nette\Diagnostics\IBarPanel;


/**
 * Provides interface to go to any destination within app
 *
 * @author    Vojtěch Dobeš
 * @copyright Clevis 2012
 * @license   New BSD License
 */
class GotoPanel extends UI\Form implements IBarPanel
{

	/** @var string|NULL */
	private $showDestination;

	/** @var string|NULL */
	private $showLink;

	/** @var string|NULL */
	private $showError;



	protected function attached($parent)
	{
		parent::attached($parent);
		if (!$parent instanceof UI\Presenter || !Debugger::isEnabled()) return;

		Debugger::$bar->addPanel($this);

		$this->addText('destination', 'Destination')
			->addRule($this::FILLED, 'Je třeba zadat destinaci.')
			->getControlPrototype()->placeholder = 'Please enter destination';
		$this->addSubmit('redirect', 'Redirect')
			->onClick[] = callback($this, 'processRedirect');
		$this->addSubmit('showLink', 'Show link')
			->onClick[] = callback($this, 'processShowLink');
	}



	public function processRedirect()
	{
		$destination = $this->prepareArguments($this->values->destination);
		call_user_func_array(callback($this->presenter, 'redirect'), $destination);
	}



	public function processShowLink()
	{
		$this->showDestination = $this->values->destination;
		$destination = $this->prepareArguments($this->values->destination);
		$originalLinkMode = $this->presenter->invalidLinkMode;
		try {
			$this->presenter->invalidLinkMode = UI\Presenter::INVALID_LINK_EXCEPTION;
			$this->showLink = call_user_func_array(callback($this->presenter, 'link'), $destination);
		} catch (UI\InvalidLinkException $e) {
			$this->showLink = TRUE;
			$this->showError = $e->getMessage();
		}
		$this->presenter->invalidLinkMode = $originalLinkMode;
	}



	private function prepareArguments($args)
	{
		return array_map(function ($arg) {
			return trim($arg);
		}, explode(',', $args));
	}



	public function getTab()
	{
		return '<img width="18" height="18" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3AUUADIDyso8hwAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAADB0lEQVQ4y62UvY+UVRTGf/edd2Z3dgFl3QisS1ZMCLuojYRoZQIdRmPiR7F2WNio2NDwF9BQ4BIbEiyMJiiUBhPsiUZtQTZmxe8lOzPvzLzz3nvf+3UsdpgVOxNPeXPyy/Oc59yjAEQkBx4BhP9WChgopUIuIrl14fxmtXm2MF2SJJJEZEIVRARBEEkARBFiDOxr72dpbumCiJxTIvLYL8XvnQ+/+UhQQfVtn6Eb4EMgxEiSgJdESA4XHT55KlfRr/o8Ws/J9bc/V0cXl+dzQArbQ2VJPbv/aTpVl67ewkRLiAHtKkywTLemqb1F+xoTSnY12/y4flf9NfiTo4vLkgEkSVRuRGEKSldSeY2uKzpVhxOHTnLl1StsdDfomYJB3Uc7Q5TEVLNNpjIAJqDCFNyv7lOYHmU9oqxHdKsuxxeOM9Oc4fIrl+npHl3ToXQlSQJNGijUDkgkUQdL5TTW19uziI6prMXqtVVurN/gyPwR1l66RCThCEQSSQljzlgRggkW6zSF6TG0fQrbp3AFqgEf3DzD2vdrvLD4PNffuMaW2ySqQFJpsgf5A2vaa7bMFp++9hkik+DH8UMjawCwMr/C+js/sfLxMjGL455/WosWn/wE8tDOKSYWHjzVLYdXYQIaKxKMszRVkze/eB0BvAS8OLSyjCh597n3OPPM+9we3ubYl8fYO7uXrur9G5TQ0aC9YSqfJs8yPIGgWgxdycUXL3Jq6RS3OrdY/fYtds3uxmqLS/XEwQRkvEEHDQoCDUQleq7g6stXWZlb4c7wDqe/O02tLDFzWOXHc0w7ICFhg6Z0JSghTzlJgUTh5r2vObjnICe+OklrpkXMEjFFQvIQd375dvxRGOkRo3rEwIwodcmwGuC849IPaxz+5DDtOIWratzIYbUnVAEqGAsiB9TC7BMsypPy690N1W61ybLGduyZkLJEaER8wxEJ2xISYGAfC3Jg9wEFKCUiuXH+/L2tn8/+0fuNTKmHo0ZtnxCVJjYEgQQLexY49PhTF2an2+fU/3XY/gZUEvwCMS1jtAAAAABJRU5ErkJggg==" />';
	}



	public function getPanel()
	{
		ob_start();
		echo '<h1>Goto</h1><div class="nette-inner">';
		if (isset($this->showLink) && isset($this->showDestination)) {
			echo '<table><tr><th>Link&nbsp;&rArr;&nbsp;' . (isset($this->showError) ? '<span style="color:#CD1818">' : '') . $this->showDestination . (isset($this->showError) ? '</span>' : '');
			if (isset($this->showError)) {
				echo '<tr><td style="background-color:#CD1818;color:white">' . $this->showError;
			} else {
				echo '<tr><td><a href="' . $this->showLink . '" style="font-family:Consolas">' . $this->showLink . '</a>';
			}
			echo '</table><br>';
		}
		$this->render('begin');
		echo '<table>'
		. '<tr><th>' . $this['destination']->label
		. '<tr><td style="background-color:white">' . $this['destination']->control->addAttributes(array(
			'style' => 'box-shadow:none;border:0;background-color:transparent;font-family:Consolas,monospace;min-width:300px',
		))
		. '<tr><td style="font-size:90%">Separate arguments with comma (including first argument).'
		. '</table>'
		. '<p>' . $this['redirect']->control . ' | ' . $this['showLink']->control . '</p>';
		echo $this->render('end');
		echo '</div>';
		return ob_get_clean();
	}

}
