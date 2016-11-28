<?php

class XFLiam_ImpersonateUser_Listener
{
	const CLASS_PROXY_PREFIX = "XFLiam_ImpersonateUser_Extend_";

	public static function extendClass($class, array &$extend)
	{
		$extend[] = self::CLASS_PROXY_PREFIX . $class;
	}
}