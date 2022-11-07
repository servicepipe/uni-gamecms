<?php

namespace Demos\Methods;

interface Method
{
	function renew($server);
	function getCount($server);
	function getDemos($server, $start, $limit, $map = null);
}