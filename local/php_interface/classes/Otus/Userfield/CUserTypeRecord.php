<?php

namespace Otus\Userfield;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Iblock\Elements\ElementdoctorsTable as DoctorTable;
use Bitrix\Main\Diag\Debug;

Loc::loadMessages(__FILE__);

class CUserTypeRecord
{
	public static function GetUserTypeDescription()
	{
		return [
			'PROPERTY_TYPE' => "S",
			'USER_TYPE' => 'record',
			'DESCRIPTION' => Loc::getMessage("TITLE_PROPERTY"),
			'GetPropertyFieldHtml' => array(self::class, 'GetPropertyFieldHtml'),
			'GetPublicViewHTML' => array(self::class, 'GetPublicViewHTML'),
			'GetPublicEditHTML' => array(self::class, 'GetPropertyFieldHtml'),
		];
	}
	
	public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		$strResult = '<button id="onl-btn-4890235589" type="button">Записать</button>';
		return $strResult;
	}
	
	public static function getDataValues($id)
	{
		if(empty($id))
		{
			return [];
		}
		
		
		Loader::IncludeModule("iblock");
		
		$data = DoctorTable::getList([
			'filter' => ["ID" => $id],
			'select' => ['PROCEDURE_ID.ELEMENT']
		])->fetchObject();
		
		
		
		$valuesElement = [];
		
		foreach($data->get("PROCEDURE_ID")->getAll() as $el)
		{	
			$element = $el->getElement();
			if(!empty($element))
			{
				$valuesElement[$element->getId()] = $el->getElement()->getName();
			}
		}
		
		return $valuesElement;
	}
	
	public static function GetPublicViewHTML($arProperty, $arValue, $strHTMLControlName)
	{
		$strResult = '';
		
		if(!isset($arProperty["ELEMENT_ID"]))
		{
			return $strResult;
		}
		
		$doctorId = $arProperty["ELEMENT_ID"] ?? 0;
		
		\CJSCore::Init(["popup"]);
		
		$inpid = 'rec_id_' . rand(0,99);
		
		$valuesElement = self::getDataValues($arProperty["ELEMENT_ID"]);
		if(empty($valuesElement))
		{
			return 'У врача нет процедур';
		}
		
		$count = 0;
		
		foreach($valuesElement as $id=>$el)
		{
			$strResult .= '<a data-pr-id="'.$id.'" data-doctor-id="'.$doctorId.'" 
				class="book_procedure" style="cursor:pointer;" id="elem_'.$inpid.'_'.$id.'">' . $el . '</a><br>';
				
			$count++;
		}
		
		$strResult .= '
			<script type="text/javascript">
			
			BX.ready(function(){
				let bookProcedure = document.querySelectorAll(".book_procedure");
				bookProcedure.forEach(function(procedure){
					procedure.addEventListener("click", onAddOnlineRecord);
				})
			});
			
			function LazyBanner(name, date, procedure, doctor)
			{
				BX.ajax({
					url: "/local/ajax/hendlers_userfields_online_records.php",
					method: "POST",
					data: {
			            NAME: name,
			            TIME: date,
			            PROC_ID: procedure,
			            DOCTOR_ID: doctor,
			            sessid: BX.bitrix_sessid()
			        },
			        dataType: "json",
			        onsuccess: function(response){
			            if (response.success) {
			                BX.UI.Notification.Center.notify({
			                    content: response.message,
			                    autoHideDelay: 3000
			                });
			            } else {
			                let errorMessage = response.errors.join("\n");
			                BX.UI.Dialogs.MessageBox.alert(errorMessage, "Ошибка записи");
			            }
			        },
			        onfailure: function(data){
			        	console.log(data);
			            alert("Ошибка соединения с сервером");
			        }
				});
			}
			
			function validateFormData(nameValue, dateValue) 
			{
			    let errors = [];
			    
			    if (!nameValue || nameValue.trim() === "") {
			        errors.push("Укажите ФИО");
			    }
			    
			    if (!dateValue) {
			        errors.push("Укажите дату и время");
			    } else {
			        const selectedDate = new Date(dateValue);
			        const now = new Date();
			        
			        if (selectedDate <= now) {
			            errors.push("Укажите дату");
			        }
			    }
			    
			    return errors;
			}
			
			function onAddOnlineRecord(e){
				e.preventDefault();
				e.stopPropagation();
				
				let pr_id = e.target.getAttribute("data-pr-id");
				let doctor_id = e.target.getAttribute("data-doctor-id");
				
				let content = BX.create("div", {
					children: [
						BX.create("input", {
							attrs: {
								type: "text",
								name: "name_online_record",
								className: "main-ui-control main-ui-control-string",
								placeholder: "ФИО",
								id: "input_name_online_record_"+pr_id,
							}
						}),
						BX.create("br"),
						BX.create("br"),
						BX.create("input", {
							attrs: {
								type: "datetime-local",
								name: "date_online_record",
								className: "main-ui-control main-ui-control-string",
								id: "input_date_online_record_"+pr_id,
							}
						}),
						BX.create("br")
					]	
				});
				
				BX.PopupWindowManager.create("bookingPopup_"+pr_id, pr_id, {
					content: content,
					closeIcon: {right: "20px", top: "10px"},
					width: 400,
					height: 400,
					zIndex: 100,
					closeByEsc: true,
					darkMode: false,
					draggable: true,
					resizable: true,
					min_height: 100,
					min_width: 100,
					lightShadow: true,
					angle: false,
					overlay: {
						backgroundColor: "black",
						opacity: 400
					},
					titleBar: "'.Loc::getMessage("TITLEBAR_POPUP").'",
					buttons: [
						new BX.PopupWindowButton({
							text: "'.Loc::getMessage("TITLE_BTN_ADD_RECORD").'",
							id: "add_new_record_"+pr_id,
							className: "ui-btn ui-btn-success" ,
							events: {
								click: function(){
							        let nameValue = BX("input_name_online_record_"+ pr_id).value;
							        let dateValue = BX("input_date_online_record_"+ pr_id).value;
							        
							        const errors = validateFormData(nameValue, dateValue);
							        
							        if (errors.length > 0) {
							            alert("Ошибки:\n" + errors.join("\n"));
							            return;
							        }
							        
							        LazyBanner(nameValue, dateValue, pr_id, doctor_id);
							        BX.PopupWindowManager.getCurrentPopup().close();
							    }
							}
						})
					]
				}).show();
			}
			</script>
		';
		
		return $strResult;
	}
	
}