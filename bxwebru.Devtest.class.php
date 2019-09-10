<?php

namespace bxwebru;

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;

class Devtest
{
	/**
	 * @param int $iblockID Идентификатор инфоблока
	 * @param array $arOrder Массив сортировки
	 * @param bool|array $arSelect Массив полей для выбора или false - для выбора всех полей
	 * @param bool|array $arProperty Массив символьных коодов свойств для выбора, пустой массив для получениея всех свойств или false - если не нужно получать свойства
	 * @param bool|array $arFilter Массив фильтрации жлементоы или false - если не нужна фильтрация
	 * @param bool|array $arNavStartParams Массив постраничной навигации или false - если не нужна постраничная навигация
	 * @param int $cacheLifetime Время жизни кеша в секундах
	 * @return array|bool Массив полей и свойств выбратнных элементов или false - в случае ошибки
	 * @throws \Bitrix\Main\LoaderException
	 */
	public static function getElementListCache($iblockID, $arOrder = array("ID" => "asc"), $arSelect = false, $arProperty = false, $arFilter = false, $arNavStartParams = false, $cacheLifetime = 3600)
	{
		$cache = Cache::createInstance();

		$arResult = array();

		if (Loader::includeModule("iblock")):

			//Если не задан ID инфоблока
			if (!$iblockID)
				return false;

			//Добавим ID инфоблока в фильтрацию
			$arFilter["IBLOCK_ID"] = $iblockID;

			//Сформируем идентификатор кеша (все параметры метода считаем значимыми)
			$cacheID = $iblockID . http_build_query($arOrder) . http_build_query($arSelect) . http_build_query($arProperty) . http_build_query($arFilter) . http_build_query($arNavStartParams);

			if ($cache->initCache($cacheLifetime, $cacheID, "bxwebru/devtest"))
			{
				//Если кеш есть и он не истек, запросим данные из кеша
				$arResult = $cache->GetVars();
			} elseif ($cache->startDataCache())
			{
				//Иначе - получим даные из базы
				$arElementRes = \CIBlockElement::GetList($arOrder, $arFilter, false, $arNavStartParams, $arSelect);
				while ($arElement = $arElementRes->GetNext())
				{
					//Если нужно выбирвать свойства
					if (is_array($arProperty))
						$arElement['PROPERTIES'] = array();

					//Сохраним поля элемента
					$arResult[$arElement['ID']] = $arElement;
				}

				//Если нужно выбирвать свойства
				if (is_array($arProperty)):
					//Применим фильтр по свойствам
					$arPropertyFilter = false;
					if (is_array($arProperty))
						$arPropertyFilter["CODE"] = $arProperty;

					//Запросим свойства
					\CIBlockElement::GetPropertyValuesArray($arResult, $iblockID, $arFilter, $arPropertyFilter, $options = array());
				endif;
			}

			//Сохраним данные в кеш
			if ($cache->StartDataCache())
			{
				$cache->EndDataCache($arResult);
			}

			return $arResult;
		endif;

		return false;
	}
}