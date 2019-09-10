<?

/**
 * @param $rssUrl string Адрес RSS-ленты
 * @param int $rssCount int Количество новостей для отображения
 * @return string Содержимое ленты или сообщение об ошибке
 */
function getLatestRss($rssUrl, $rssCount = 5)
{
	$result = "";

	$rssSource = file_get_contents($rssUrl);

	//Если не получили содержимое RSS
	if (!$rssSource)
		return "Unable to get RSS-feed\n";

	$rssXML = new SimpleXMLElement($rssSource);

	//Если ошибка в сожержимом
	if (!$rssXML)
		return "Unable to parse RSS-feed\n";

	$cnt = 1;

	//Обойдем ленту
	foreach ($rssXML->channel->item as $rssItem):

		$result .= $rssItem->title . "\n" . $rssItem->link . "\n" . trim($rssItem->description) . "\n\n";

		//Выведем результат
		if ($cnt >= $rssCount)
			return $result;

		$cnt++;
	endforeach;

	return "Unknown error";
}

echo getLatestRss("https://lenta.ru/rss", 5);