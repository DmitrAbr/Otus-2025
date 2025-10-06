<?php

namespace Otus\Handlers;

use Bitrix\Main\Diag\Debug;
use Bitrix\Crm\Service\Container;
use Bitrix\Crm\Service\Context;
use Bitrix\Crm\Item;
use Bitrix\Main\Loader;
use Bitrix\Iblock\Elements\ElementotuscrmiblockTable as IblockElement;


class CrmIblockUpdateHandler
{
	
	protected const IBLOCK_ID = 51;
	protected const SUM_VALUE_ID = 148;
	protected const RESPONSIBLE_VALUE_ID = 146;
	protected const DEAL_VALUE_ID = 147;
	
	public static function onUpdateIblockHandler(&$arFields)
	{
		Debug::writeToFile($arFields, "arFields", "local/arFields.log");
		if($arFields["IBLOCK_ID"] === self::IBLOCK_ID && isset($arFields["PROPERTY_VALUES"]))
		{
			$deal_field = $arFields["PROPERTY_VALUES"][self::DEAL_VALUE_ID];
			$deal_id = "";
			
			foreach($deal_field as $deal)
			{
				$deal_id = $deal["VALUE"];
			}
			
			$sum_field = $arFields["PROPERTY_VALUES"][self::SUM_VALUE_ID];
			$sum_value = "";
			
			foreach($sum_field as $sum)
			{
				$sum_value = explode("|",$sum["VALUE"])[0];
			}
			
			if(!empty($deal_id))
			{
				$deal_id = $deal_id;
				$modify_by = $arFields["MODIFIED_BY"];
				
				$CrmFields = [
					"ASSIGNED_BY_ID" => $arFields["PROPERTY_VALUES"][self::RESPONSIBLE_VALUE_ID],
					"OPPORTUNITY" => $sum_value
				];
				
				$result = self::UpdateDeal($CrmFields, $deal_id, $modify_by);
				
				if(!$result->isSuccess())
				{
					$error = $result->getErrorMessages();
					
					global $APPLICATION;
					$APPLICATION->throwException(implode(", ", $error));
					
					if(Loader::IncludeModule("im"))
					{
						$messageFields = [
			                "TO_USER_ID" => $modify_by,
			                "FROM_USER_ID" => 0,
			                "MESSAGE" => "Ошибка при обновлении сделки: " . implode(", ", $error),
			                "MESSAGE_TYPE" => IM_MESSAGE_SYSTEM,
			            ];
			            
			            \CIMNotify::Add($messageFields);
					}
					
					return false;
				}
				
				if(Loader::IncludeModule("im"))
				{
					$messageFields = [
		                "TO_USER_ID" => $modify_by,
		                "FROM_USER_ID" => 0,
		                "MESSAGE" => "Сделка успешно обновлена",
		                "MESSAGE_TYPE" => IM_MESSAGE_SYSTEM,
		            ];
		            
		            \CIMNotify::Add($messageFields);
				}
				
			}
		}
	}
	
	protected static function UpdateDeal($CrmFields, $deal_id, $modify_by)
	{
		if(Loader::IncludeModule("crm"))
		{
			$factory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
			
			if ($factory) 
			{
			    $item = $factory->getItem($deal_id);
			    
			    if ($item) 
			    {
			        foreach($CrmFields as $field => $value)
			        {
			        	$item->set($field, $value);
			        }
			        
			        $context = new Context();
			        $context->setUserId($modify_by);
					
			        $operation = $factory->getUpdateOperation($item, $context);
			        $result = $operation->launch();
			        
			        return $result;
			    }
			}
		}
		else 
		{
			global $APPLICATION;
			$APPLICATION->throwException("Модуль crm не установлен");
			return false;
		}
	}
	
	public static function onUpdateCrmHandler($arFields)
	{
		$deal_id = $arFields["ID"];
		
		if(Loader::IncludeModule("iblock"))
		{
			$element = IblockElement::getList([
				'select' => ["ID"],
				'filter' => ["ID_DEAL.VALUE" => $deal_id]
			])->fetch();
			
			if(!empty($element))
			{
				$PROP = [
					148 => $arFields["OPPORTUNITY"]."|RUB",
					146 => $arFields["ASSIGNED_BY_ID"],
					147 => $deal_id
				];
				
				$el = new \CIBlockElement;
				
				$arLoadProductArray = Array(
					"MODIFIED_BY"    => $arFields["MODIFY_BY_ID"],
					"PROPERTY_VALUES"=> $PROP,
				);
				
				$res = $el->Update($element["ID"], $arLoadProductArray);
				if(!$res)
				{
					$error = $el->LAST_ERROR;
					
					global $APPLICATION;
					$APPLICATION->throwException("Ошибка обновления элемента");
					if(Loader::IncludeModule("im"))
					{
						$messageFields = [
			                "TO_USER_ID" => $arFields["MODIFY_BY_ID"],
			                "FROM_USER_ID" => 0,
			                "MESSAGE" => "Ошибка при обновлении элемента: " . $error,
			                "MESSAGE_TYPE" => IM_MESSAGE_SYSTEM,
			            ];
			            
			            \CIMNotify::Add($messageFields);
					}
					return false;
				}
			}
		}
	}	
}