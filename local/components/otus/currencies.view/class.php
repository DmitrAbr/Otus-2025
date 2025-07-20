<?
use Bitrix\Currency\CurrencyTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\Filter\Options as FilterOptions;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}


class CurrencyViewComponent extends \CBitrixComponent
{
	protected $request;
	protected const GRID_ID = 'list_currency';
	protected const NAVIGATION_ID = 'page';
	protected const FILTER_ID = self::GRID_ID . '_filter';
	
	
	public function onPrepareComponentParams($arParams)
	{
		if($arParams['SHOW_ALL'] == "Y")
		{
			$currencyList = CurrencyTable::getList([
			    'select' => ['CURRENCY'],
			    'order'  => ['SORT' => 'ASC']
			]);
			
			$listCurrency == [];
			while ($currency = $currencyList->fetch()) 
			{
				$listCurrency[]=$currency['CURRENCY'];
			}
			$arParams['CURRENCY'] = $listCurrency;
		}
		
		return $arParams;
	}
	
	public function executeComponent()
	{
		try
		{
			$this->getOptions();
			$this->fillGridInfo();
			$this->fillGridData();
			
			$this->IncludeComponentTemplate();
		}
		catch(SystemException $e)
		{
			ShowError($e->getMessage());	
		}
	}
	
	private function getOptions()
	{
		$GridOptions = new GridOptions(static::GRID_ID);
		
		$this->arResult['GridOptions'] = $GridOptions;
		
		$filterOptions = new FilterOptions(static::FILTER_ID);
		
		$this->arResult['FilterOptions'] = $filterOptions;
	}
	
	private function getColumn()
	{
		return[
			[
				'id' => 'CURRENCY',
				'name' => 'Валюта',
				'default' => true,
				'sort' => 'CURRENCY',
			],	
			[
				'id' => 'AMOUNT',
				'name' => 'Курс',
				'sort' => 'AMOUNT',
				'default' => true
			],
			[
				'id' => 'AMOUNT_CNT',
				'name' => 'Номинал',
				'sort' => 'AMOUNT_CNT',
				'default' => true
			],
			[
				'id' => 'BASE',
				'name' => 'Базовая',
				'sort' => 'BASE',
				'default' => true
			]
		];
	}
	
	private function fillGridInfo(): void
	{
		$this->arResult['gridId'] = static::GRID_ID;
		$this->arResult['filterId'] = static::FILTER_ID;
		$this->arResult['navigationId'] = static::NAVIGATION_ID;
		$this->arResult['uiFilter'] = $this->getFilterFields();
		$this->arResult['gridColumns'] = $this->getColumn();
		$this->arResult['pageNavigation'] = $this->getPageNavigation();
		$this->arResult['pageSizes'] = $this->getPageSizes();
	}
	
	private function getPageNavigation()
	{
		$navParams = $this->arResult['GridOptions']->GetNavParams();

		$pageNavigation = new PageNavigation(static::NAVIGATION_ID);
		$pageNavigation->setPageSize($navParams['nPageSize'])->initFromUri();

		$currentPage = $this->request->getQuery(static::NAVIGATION_ID);
		
		if (is_numeric($currentPage))
		{
			$pageNavigation->setCurrentPage((int)$currentPage);
		}

		return $pageNavigation;
	}
	
	private function fillGridData(): void
	{
		/** @var \Bitrix\Main\UI\PageNavigation $pageNav */
		$pageNav = $this->arResult['pageNavigation'];

		$offset = $pageNav->getOffset();
		$limit = $pageNav->getLimit();
		$sort = $this->arResult['GridOptions']->GetSorting(['sort' => ['CURRENCY' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
		$filter = $this->arResult['FilterOptions']->getFilter($this->getFilterFields());
		$preparedFilter = [];
		if(!empty($filter))
		{
		    // Получаем список допустимых полей из метода getFilterFields()
		    $allowedFields = array_column($this->getFilterFields(), 'id');
		    
		    foreach($filter as $key => $value)
		    {
		        // Оставляем только те поля, которые есть в списке разрешенных
		        if(in_array($key, $allowedFields) && !empty($value))
		        {
		            $preparedFilter[$key] = $value;
		        }
		    }
		}
		
		if($this->arParams['SHOW_ALL'] == 'N' && empty($filter))
		{
			$preparedFilter['CURRENCY'] = $this->arParams['CURRENCY'];
		}
		$list = [];
		$data = CurrencyTable::getList([
			'filter' => $preparedFilter,
			'select' => ['CURRENCY', 'AMOUNT', 'AMOUNT_CNT', 'BASE'],
			'order' => $sort['sort'],
			'limit' => $limit,
			'offset' => $offset
		]);
		
		while($item = $data->fetch())
		{
			
			$list[] = [
				'data' => $item,
				'actions'=>[
					[
						'text' => 'Просмотр',
						'default' => true,
						'onclick' => 'document.location.href="?op=view&id='.$item['ID'].'"'
					],
					[
						'text' => 'Удалить',
						'default' => true,
						'onclick' => 'if(confirm("Точно?")){document.location.href="?op=delete&id='.$item['ID'].'"}'
					]
				]
			];
		}
		$pageNav->setRecordCount(CurrencyTable::getCount());
		$this->arResult['LIST'] = $list;
	}
	
	private function getFilterFields():array
	{
		return[
			['id' => 'CURRENCY', 'name' => 'Валюта', 'type' => 'text', 'default' => true],
			['id' => 'AMOUNT', 'name' => 'Курс', 'type' => 'text', 'default' => true],
			['id' => 'AMOUNT_CNT', 'name' => 'Номинал', 'type' => 'text', 'default' => true],
			['id' => 'BASE', 'name' => 'Базовая', 'type' => 'date', 'default' => true],
		];
	}
	
	private function getPageSizes(): array
	{
		return [
			['NAME' => '1', 'VALUE' => '1'],
			['NAME' => '2', 'VALUE' => '2'],
			['NAME' => '3', 'VALUE' => '3'],
			['NAME' => '50', 'VALUE' => '50'],
			['NAME' => '100', 'VALUE' => '100'],
		];
	}
	
}