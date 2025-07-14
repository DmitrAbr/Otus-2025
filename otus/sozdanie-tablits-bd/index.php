<?
use Bitrix\Main\Entity\Query;
use Models\Lists\BookTable;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Создание своих таблиц БД");
?>

<?php

$q = new Query(BookTable::class);
$q->setSelect([
    'ID',
    'TITLE',
    'PAGES',
    'DESCRIPTION',
    'PUBLISH_DATE',
    'AUTHOR_FIRST_NAME' => 'AUTHORS.FIRST_NAME',
    'AUTHOR_LAST_NAME' => 'AUTHORS.LAST_NAME',
    'AUTHOR_SECOND_NAME' => 'AUTHORS.SECOND_NAME',
    'DOCTORS_ID' => 'RECOMMENDS_DOCTOR.ID',
    'DOCTORS_NAME' => 'RECOMMENDS_DOCTOR.DOCTOR_NAME.VALUE',
    'SPECS_ID' => 'SPECS.ID',
    'SPECS_NAME' => 'SPECS.NAME'
]);

$res = $q->exec();

$books = [];
$processedAuthors = [];
$processedDoctors = [];
$processedSpecs = [];

while ($arItem = $res->Fetch()) {
    $bookId = $arItem['ID'];
    $doctorId = $arItem['DOCTORS_ID'];
    $specId = $arItem['SPECS_ID'];

    if (!isset($books[$bookId])) {
        $books[$bookId] = [
            'ID' => $bookId,
            'TITLE' => $arItem['TITLE'],
            'PAGES' => $arItem['PAGES'],
            'DESCRIPTION' => $arItem['DESCRIPTION'],
            'PUBLISH_DATE' => $arItem['PUBLISH_DATE']->toString(),
            'AUTHORS' => [],
            'DOCTORS' => [],
            'SPECS' => []
        ];
        $processedAuthors[$bookId] = [];
        $processedDoctors[$bookId] = [];
        $processedSpecs[$bookId] = [];
    }

    $authorKey = implode('|', [
        $arItem['AUTHOR_FIRST_NAME'],
        $arItem['AUTHOR_LAST_NAME'],
        $arItem['AUTHOR_SECOND_NAME']
    ]);

    if (!empty($authorKey) && !in_array($authorKey, $processedAuthors[$bookId])) {
        $books[$bookId]['AUTHORS'][] = [
            'NAME' => trim(implode(' ', array_filter([
                $arItem['AUTHOR_FIRST_NAME'],
                $arItem['AUTHOR_LAST_NAME'],
                $arItem['AUTHOR_SECOND_NAME']
            ])))
        ];
        $processedAuthors[$bookId][] = $authorKey;
    }

    if (!empty($arItem['DOCTORS_NAME']) && !in_array($doctorId, $processedDoctors[$bookId])) {
        $books[$bookId]['DOCTORS'][] = [
            'ID' => $doctorId,
            'NAME' => $arItem['DOCTORS_NAME']
        ];
        $processedDoctors[$bookId][] = $doctorId;
    }

    if (!empty($arItem['SPECS_NAME']) && !in_array($specId, $processedSpecs[$bookId])) {
        $books[$bookId]['SPECS'][] = [
            'ID' => $specId,
            'NAME' => $arItem['SPECS_NAME']
        ];
        $processedSpecs[$bookId][] = $specId;
    }
}
?>


<style>
    .books-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px;
    }
    
    .book-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .book-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
    }
    
    .book-meta {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
    }
    
    .book-description {
        font-size: 14px;
        color: #444;
        margin-bottom: 15px;
    }
    
    .section-title {
        font-size: 15px;
        font-weight: bold;
        margin: 10px 0 5px;
        color: #2a5885;
    }
    
    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-bottom: 10px;
    }
    
    .tag {
        background: #e9f2ff;
        color: #2a5885;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
    }
    
    .doctor-tag {
        background: #e8f5e9;
        color: #2e7d32;
    }
    
    .spec-tag {
        background: #fff8e1;
        color: #ff8f00;
    }
</style>

<div class="books-container">
    <?php foreach ($books as $book): ?>
        <div class="book-card">
            <div class="book-title"><?= htmlspecialcharsbx($book['TITLE']) ?></div>
            
            <div class="book-meta">
                <span><?= (int)$book['PAGES'] ?> стр.</span> | 
                <span><?= htmlspecialcharsbx($book['PUBLISH_DATE']) ?></span>
            </div>
            
            <div class="book-description">
                <?= htmlspecialcharsbx(TruncateText($book['DESCRIPTION'], 150)) ?>
            </div>
            
            <?php if (!empty($book['AUTHORS'])): ?>
                <div class="section-title">Авторы:</div>
                <div class="tags-container">
                    <?php foreach ($book['AUTHORS'] as $author): ?>
                        <div class="tag"><?= htmlspecialcharsbx($author['NAME']) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($book['DOCTORS'])): ?>
                <div class="section-title">Рекомендовано врачами:</div>
                <div class="tags-container">
                    <?php foreach ($book['DOCTORS'] as $doctor): ?>
                        <div class="tag doctor-tag"><?= htmlspecialcharsbx($doctor['NAME']) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($book['SPECS'])): ?>
                <div class="section-title">Специализации:</div>
                <div class="tags-container">
                    <?php foreach ($book['SPECS'] as $spec): ?>
                        <div class="tag spec-tag"><?= htmlspecialcharsbx($spec['NAME']) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>