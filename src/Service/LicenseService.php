<?php

namespace App\Service;

use \Smalot\PdfParser\Parser;

class LicenseService
{
    /**
     * @throws \Exception
     */
    public function getLicenses($pdfFile): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfFile->getPathname());

        $text = $pdf->getText();
        $licenses = [];

        $licenseNumberPattern = '/Номер лицензии\\t(.*)/';
        $licenseNumberPatternPriority = '/Номер лицензии, присвоенный в Едином\nреестре учета лицензий\s*(\S+)/';
        $licenseDatePattern = '/Дата начала действия лицензии\\t(.*)/';
        $activityTypePattern = '/Вид лицензируемой деятельности, на который\\nвыдана лицензия\\n(.*)Наименование лицензирующего органа/s';
        $issuerNamePattern = '/Наименование лицензирующего органа,\\nвыдавшего или переоформившего лицензию\\n(.*)ГРН и дата внесения в ЕГРЮЛ записи/s';

        // преобразуем в массив строк
        $parts = preg_split('/\n\d+\n/', $text);

        if (!empty($parts)) {
            foreach ($parts as $part) {
                $license = [];

                if (!empty(trim($part))) {
                    // Получаем номер лицензии. В приоритете: Номер лицензии, присвоенный в Едином реестре учета лицензий
                    if ($licenseNumberPriority = $this->searchByPattern($licenseNumberPatternPriority, $part)) {
                        $license['officialNum'] = $licenseNumberPriority;
                    } else if ($licenseNumber = $this->searchByPattern($licenseNumberPattern, $part)) {
                        $license['officialNum'] = $licenseNumber;
                    } else {
                        continue;
                    }

                    // лицензирующий орган
                    $license['issuerName'] = $this->searchByPattern($issuerNamePattern, $part);
                    // дата начала действия лицензии
                    $license['dateStart'] = $this->searchByPattern($licenseDatePattern, $part);
                    // вид деятельности
                    $license['activity'] = $this->searchByPattern($activityTypePattern, $part);

                    $licenses[] = $license;
                }
            }
        } else {
            return ['Licenses not found' => 'Лицензии не найдены'];
        }

        return $this->removeDuplicatesByLicenseNumber($licenses, 'officialNum');
    }

    /**
     * Удаление дублирующихся лицензий
     *
     * @param array $licenses
     * @param string $key
     * @return array
     */
    private function removeDuplicatesByLicenseNumber(array $licenses, string $key): array
    {
        // уникальные номера лицензий
        $uniqueLicenseNumber = [];

        return array_filter($licenses, function ($item) use (&$uniqueLicenseNumber, $key) {
            // если нет уникального ключа, то добавляем
            if (!in_array($item[$key], $uniqueLicenseNumber)) {
                $uniqueLicenseNumber[] = $item[$key];
                // копируем в новый массив лицензию с уникальным номером
                return true;
            }
            // номер лицензии уже есть, значит не копируем
            return false;
        });
    }

    /**
     * Поиск по шаблону
     *
     * @param $pattern
     * @param $part
     * @return array|string
     */
    private function searchByPattern($pattern, $part): array|string
    {

        if (preg_match($pattern, $part, $matches)) {
            return trim(str_replace(["\r", "\n"], ' ', $matches[1]));
        } else {
            return [];
        }

    }

}