<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// получем коды свойств из массива arParams 
$arFields = array();
foreach ($arParams['FIELDS'] as $key => $prop) {
	 	
	 	// Юридическое лицо за рубежом
	 	if($prop['CODE'] == 'LEGAL_ENTITY_ABROAD'){
	 		// pr($prop);
	 		$arFields['LEGAL_ENTITY_ABROAD']['PROPERTY_ID'] = $prop['ID'];
	 		$arFields['LEGAL_ENTITY_ABROAD']['PROPERTY_CODE'] = $prop['CODE'];
			$arFields['LEGAL_ENTITY_ABROAD']['PROPERTY_VALUE'] =  reset($arResult["TABS"]["tab_el"]["fields"]["PROPERTY_".$arFields['LEGAL_ENTITY_ABROAD']['PROPERTY_ID']]['value']);
	 	}

	 	// Наименование юридического лица за рубежом
	 	if($prop['CODE'] == 'LEGAL_ENTITY_NAME'){
	 		$arFields['LEGAL_ENTITY_NAME']['PROPERTY_ID'] = $prop['ID'];
	 		$arFields['LEGAL_ENTITY_NAME']['PROPERTY_CODE'] = $prop['CODE'];
	 	}
	 	// Страна в которой открыто юридическое лицо
	 	if($prop['CODE'] == 'LEGAL_ENTITY_COUNTRY'){
	 		$arFields['LEGAL_ENTITY_COUNTRY']['PROPERTY_ID'] = $prop['ID'];
	 		$arFields['LEGAL_ENTITY_COUNTRY']['PROPERTY_CODE'] = $prop['CODE'];
	 	}
	 	// Emil юридического лица за рубежом
	 	if($prop['CODE'] == 'LEGAL_ENTITY_EMAIL'){
	 		$arFields['LEGAL_ENTITY_EMAIL']['PROPERTY_ID'] = $prop['ID'];
	 		$arFields['LEGAL_ENTITY_EMAIL']['PROPERTY_CODE'] = $prop['CODE'];
	 	}
}

// получем XML_ID и ID значения свойства Юридическое лицо за рубежом
if($arFields['LEGAL_ENTITY_ABROAD']['PROPERTY_VALUE']){
 	$rsEnum = \Bitrix\Iblock\PropertyEnumerationTable::getList(array(
	    'filter' => array('ID'=>$arFields['LEGAL_ENTITY_ABROAD']['PROPERTY_VALUE']),
	));

	while($arEnum = $rsEnum->fetch()){
	    $arFields['LEGAL_ENTITY_ABROAD']['XML_ID'] = $arEnum['XML_ID'];
	    $arFields['LEGAL_ENTITY_ABROAD']['LE_ABROAD_ID'] = $arEnum['ID'];
	    break;
	}
}


// обходим табы
// добавляем к ним коды свойств в массив arResult
$arResult["XML_ID"] = 'no';
foreach($arResult["TABS"] as $keyTab => $tab){
	
	if($tab["fields"]){
 		
 		foreach ($tab["fields"] as $keyField => $field) {
			if($field['id'] == 'PROPERTY_'.$arFields['LEGAL_ENTITY_ABROAD']['PROPERTY_ID']){
			
 				if($arFields['LEGAL_ENTITY_ABROAD']['XML_ID'] == 'yes'){
 					
 					$arResult["TABS"][$keyTab]["fields"][$keyField]['le_abroad_id'] = $arField['LE_ABROAD_ID'];
 					
 					$arResult["XML_ID"] = 'yes';
 				}
 				else{
 					unset($field['items'][$arFields['LEGAL_ENTITY_ABROAD']['LE_ABROAD_ID']]);
 					foreach ($field['items'] as $key => $value){
 						$arResult["TABS"][$keyTab]["fields"][$keyField]['le_abroad_id'] = $key;
 					}
 				}

 				$arResult["TABS"][$keyTab]["fields"][$keyField]['property_code'] = $arFields['LEGAL_ENTITY_ABROAD']['PROPERTY_CODE'];
 				$arResult["TABS"][$keyTab]["fields"][$keyField]['property_value'] = $arFields['LEGAL_ENTITY_ABROAD']['PROPERTY_VALUE'];
 				// break;
 			}

 			if($field['id'] == 'PROPERTY_'.$arFields['LEGAL_ENTITY_NAME']['PROPERTY_ID']){
 				$arResult["TABS"][$keyTab]["fields"][$keyField]['property_code'] = $arFields['LEGAL_ENTITY_NAME']['PROPERTY_CODE'];
 			}


 			if($field['id'] == 'PROPERTY_'.$arFields['LEGAL_ENTITY_COUNTRY']['PROPERTY_ID']){
 					$arResult["TABS"][$keyTab]["fields"][$keyField]['property_code'] = $arFields['LEGAL_ENTITY_COUNTRY']['PROPERTY_CODE'];
 			}

 			if($field['id'] == 'PROPERTY_'.$arFields['LEGAL_ENTITY_EMAIL']['PROPERTY_ID']){
 					$arResult["TABS"][$keyTab]["fields"][$keyField]['property_code'] = $arFields['LEGAL_ENTITY_EMAIL']['PROPERTY_CODE'];
 			}
 		}
	}
}

// pr($arResult["TABS"]);