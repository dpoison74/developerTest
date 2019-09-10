<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

//Подключим класс
require("bxwebru.Devtest.class.php");

$iblockID = 6;
$arOrder = array(
	"ID" => "desc"
);

$arSelect = array(
	"NAME",
	"DATE_ACTIVE_FROM"
);

$arFilter = array(
	/*"ACTIVE_DATE" => "N",*/
	"ACTIVE" => "Y"
);

$arProperty = array(
	"YEAR"
);

$arNavStartParams = array(
	"nPageSize" => 9
);

echo "<pre>" . print_r(bxwebru\Devtest::getElementListCache($iblockID, $arOrder, $arSelect, $arProperty, $arFilter, $arNavStartParams), true) . "</pre>";
