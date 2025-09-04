<?php

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime as BitrixDateTime;
use Bitrix\Main\Diag\Debug;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$request = Context::getCurrent()->getRequest();
$response = ['success' => false, 'errors' => [], 'message' => ''];

if($request->isPost() && check_bitrix_sessid())
{
    $postData = $request->getPostList()->toArray();
    
    $errors = [];
    
    if(empty(trim($postData["NAME"]))) 
    {
        $errors[] = 'Укажите ФИО';
    }
    
    if(empty($postData["TIME"])) 
    {
        $errors[] = 'Укажите дату и время';
    } 
    else 
    {
        $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $postData["TIME"]);
        if (!$dateTime || $dateTime <= new DateTime()) 
        {
            $errors[] = 'Укажите корректную будущую дату';
        }
    }
    
    if(empty($postData["PROC_ID"])) 
    {
        $errors[] = 'Не указана процедура';
    }
    
    if(empty($postData["DOCTOR_ID"])) 
    {
        $errors[] = 'Не указан врач';
    }
    
    if (!empty($errors)) 
    {
        $response['errors'] = $errors;
        echo json_encode($response);
        exit();
    }
    
    if (empty($errors)) 
    {
        try 
        {
            $selectedDateTime = DateTime::createFromFormat('Y-m-d\TH:i', $postData["TIME"]);
            
            $startTime = clone $selectedDateTime;
            $startTime->modify('-30 minutes');
            
            $endTime = clone $selectedDateTime;
            $endTime->modify('+30 minutes');
            
            $startTimeFormatted = $startTime->format('Y-m-d H:i:s');
            $endTimeFormatted = $endTime->format('Y-m-d H:i:s');
            $selectedTimeFormatted = $selectedDateTime->format('Y-m-d H:i:s');
            
            Loader::IncludeModule("iblock");
            
            $filter = [
                'IBLOCK_ID' => 44,
                'PROPERTY_DOCTOR' => intval($postData["DOCTOR_ID"]),
                'ACTIVE' => 'Y',
                [
                    'LOGIC' => 'OR',
                    ['PROPERTY_DATE' => $selectedTimeFormatted],
                    [
                        '>=PROPERTY_DATE' => $startTimeFormatted,
                        '<=PROPERTY_DATE' => $endTimeFormatted,
                    ]
                ]
            ];
            
            $rsRecords = CIBlockElement::GetList(
                [],
                $filter,
                false,
                false,
                ['ID', 'NAME', 'PROPERTY_DATE']
            );
            
            if ($arRecord = $rsRecords->Fetch()) 
            {
                $recordTime = new DateTime($arRecord['PROPERTY_DATE_VALUE']);
                $formattedRecordTime = $recordTime->format('d.m.Y H:i');
                
                $errors[] = 'Врач занят в выбранное время. ';
                $errors[] = 'На ' . $formattedRecordTime . ' уже есть запись. ';
                $errors[] = 'Пожалуйста, выберите время за 30 минут до или после.';
            }
            
        } 
        catch (Exception $e) 
        {
            $errors[] = 'Ошибка при проверке доступности времени: ' . $e->getMessage();
        }
    }
    
    if (!empty($errors)) 
    {
        $response['errors'] = $errors;
        echo json_encode($response);
        exit();
    }
    
    try 
    {
        $el = new CIBlockElement();
        $newDate = str_replace('T', ' ', $postData["TIME"]) . ":00";
        
        $prop = [
            "PROC_IDS" => intval($postData["PROC_ID"]),
            "DATE" => $newDate,
            "PATIENT_NAME" => htmlspecialcharsbx(trim($postData["NAME"])),
            "DOCTOR" => intval($postData["DOCTOR_ID"])
        ];
        
        $arLoadIblockArray = [
            "IBLOCK_ID" => 44,
            "PROPERTY_VALUES" => $prop,
            "NAME" => htmlspecialcharsbx(trim($postData["NAME"])),
            "ACTIVE" => "Y"
        ];
        
        if($element_id = $el->Add($arLoadIblockArray)) 
        {
            $response['success'] = true;
            $response['message'] = 'Запись успешно создана на ' . $selectedDateTime->format('d.m.Y H:i');
            $response['id'] = $element_id;
        } 
        else 
        {
            $response['errors'][] = 'Ошибка сохранения: ' . $el->LAST_ERROR;
        }
        
    } 
    catch (Exception $e) 
    {
        $response['errors'][] = 'Системная ошибка: ' . $e->getMessage();
    }
} 
else 
{
    $response['errors'][] = 'Неверный запрос';
}

header('Content-Type: application/json');
echo json_encode($response);