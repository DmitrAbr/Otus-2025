<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Bizproc\Activity\BaseActivity;
use Bitrix\Bizproc\FieldType;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Bitrix\Bizproc\Activity\PropertiesDialog;
use Bitrix\Main\Loader;
use Bitrix\Crm\CompanyTable;
use Bitrix\Crm\RequisiteTable;
use Bitrix\Crm\PresetTable;
use Bitrix\Crm\EntityPreset;
use Bitrix\Main\Config\Option;

class CBPSearchByInnActivity extends BaseActivity
{
    /**
     * @see parent::_construct()
     * @param $name string Activity name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->arProperties = [
            'Inn' => '',
            'CompanyId' => null,
        ];

        $this->SetPropertiesTypes([
            'CompanyId' => ['Type' => FieldType::INT],
        ]);
    }

    /**
     * Return activity file path
     * @return string
     */
    protected static function getFileName(): string
    {
        return __FILE__;
    }

    /**
     * @return ErrorCollection
     */
    protected function internalExecute(): ErrorCollection
    {
        $errors = parent::internalExecute();
        
        if (!$errors->isEmpty()) {
            return $errors;
        }

        $inn = trim((string)$this->Inn);
        
        if (empty($inn)) {
            $errors->setError(new \Bitrix\Main\Error('ИНН не может быть пустым'));
            return $errors;
        }

        if (!Loader::includeModule('crm')) {
            $errors->setError(new \Bitrix\Main\Error('Модуль CRM не установлен'));
            return $errors;
        }

        $companyId = $this->findCompanyByInn($inn);
        
        if ($companyId) {
            $this->preparedProperties['CompanyId'] = $companyId;
            return $errors;
        }

        $companyData = $this->findCompanyByDadata($inn);
        
        if ($companyData && is_array($companyData) && !empty($companyData['suggestions'])) {
            $companyId = $this->createCompanyFromDadata($companyData['suggestions'][0]);
            
            if ($companyId) {
                $this->preparedProperties['CompanyId'] = $companyId;
            } else {
                $errors->setError(new \Bitrix\Main\Error('Не удалось создать компанию в Bitrix24'));
            }
        } else {
            $errors->setError(new \Bitrix\Main\Error('Компания с ИНН ' . $inn . ' не найдена в Dadata'));
        }

        return $errors;
    }

    /**
     * Поиск компании по ИНН в Bitrix24
     * @param string $inn
     * @return int|null
     */
    private function findCompanyByInn(string $inn): ?int
    {
        try {
            $requisite = RequisiteTable::getList([
                'select' => ['ENTITY_ID'],
                'filter' => [
                    '=RQ_INN' => $inn,
                    '=ENTITY_TYPE_ID' => \CCrmOwnerType::Company
                ],
                'limit' => 1
            ])->fetch();

            return $requisite ? (int)$requisite['ENTITY_ID'] : null;
        } catch (\Exception $e) {
            $this->writeToBizprocLog('Ошибка поиска компании по ИНН: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Поиск компании через сервис Dadata
     * @param string $inn
     * @return array|null
     */
    private function findCompanyByDadata(string $inn): ?array
    {
        try {
            $dadata = new Dadata();
            $dadata->init();

            $fields = [
                "query" => $inn, 
                "count" => 1,
                "type" => "LEGAL"
            ];
            
            $result = $dadata->findById("party", $fields);
            $dadata->close();

            return $result;
        } catch (\Exception $e) {
            $this->writeToBizprocLog('Ошибка поиска в Dadata: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Создание компании в Bitrix24 на основе данных из Dadata
     * @param array $dadataCompany
     * @return int|null
     */
    private function createCompanyFromDadata(array $dadataCompany): ?int
    {
        try {
            global $USER;
            $userId = $USER->getId();
            
            $companyData = [
                'TITLE' => $dadataCompany['value'] ?? '',
                'COMPANY_TYPE' => 'CUSTOMER',
                'INDUSTRY' => 'IT',
                'ADDRESS' => $dadataCompany['data']['address']['unrestricted_value'] ?? '',
                'CREATED_BY_ID' => $userId,
				'ASSIGNED_BY_ID' => $userId,
				'MODIFY_BY_ID' => $userId
            ];

            $companyResult = CompanyTable::add($companyData);
            
            if (!$companyResult->isSuccess()) {
                $this->writeToBizprocLog('Ошибка создания компании: ' . implode(', ', $companyResult->getErrorMessages()));
                return null;
            }

            $companyId = $companyResult->getId();

            $this->createRequisites($companyId, $dadataCompany);

            return $companyId;

        } catch (\Exception $e) {
            $this->writeToBizprocLog('Ошибка создания компании из Dadata: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Создание реквизитов для компании
     * @param int $companyId
     * @param array $dadataCompany
     */
    private function createRequisites(int $companyId, array $dadataCompany): void
    {
        try {
            $data = $dadataCompany['data'] ?? [];

            $presetId = $this->getDefaultRequisitePresetId();
            
            $requisiteData = [
                'ENTITY_TYPE_ID' => \CCrmOwnerType::Company,
                'ENTITY_ID' => $companyId,
                'PRESET_ID' => $presetId,
                'NAME' => 'Основные реквизиты',
                'RQ_INN' => $data['inn'] ?? '',
                'RQ_KPP' => $data['kpp'] ?? '',
                'RQ_OGRN' => $data['ogrn'] ?? '',
                'RQ_OKPO' => $data['okpo'] ?? '',
                'RQ_COMPANY_NAME' => $data['name']['short_with_opf'] ?? '',
                'RQ_COMPANY_FULL_NAME' => $data['name']['full_with_opf'] ?? '',
                'RQ_DIRECTOR' => $data['management']['name'] ?? '',
            ];

            $requisiteData = array_filter($requisiteData);

            $requisiteResult = RequisiteTable::add($requisiteData);
            
            if (!$requisiteResult->isSuccess()) {
                $this->writeToBizprocLog('Ошибка создания реквизитов: ' . implode(', ', $requisiteResult->getErrorMessages()));
            }

        } catch (\Exception $e) {
            $this->writeToBizprocLog('Ошибка создания реквизитов: ' . $e->getMessage());
        }
    }

    /**
     * Получение ID пресета реквизитов по умолчанию
     * @return int
     */
    private function getDefaultRequisitePresetId(): int
    {
        try {
            $preset = PresetTable::getList([
                'filter' => [
                    '=ENTITY_TYPE_ID' => \CCrmOwnerType::Company,
                ],
                'limit' => 1
            ])->fetch();

            return $preset ? (int)$preset['ID'] : 1;
        } catch (\Exception $e) {
            $this->writeToBizprocLog('Ошибка получения пресета реквизитов: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Логирование в бизнес-процесс
     * @param string $message
     */
    private function writeToBizprocLog(string $message): void
    {
        if (method_exists($this, 'log')) {
            $this->log($message);
        }
    }

    /**
     * @param PropertiesDialog|null $dialog
     * @return array[]
     */
    public static function getPropertiesDialogMap(?PropertiesDialog $dialog = null): array
    {
        $map = [
            'Inn' => [
                'Name' => GetMessage("BPAA_TITLE_INN_FIELD"),
                'FieldName' => 'inn',
                'Type' => FieldType::INT,
                'Required' => true,
            ],
        ];
        return $map;
    }
}