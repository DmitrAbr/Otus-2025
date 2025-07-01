<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Врачи");
$APPLICATION->SetAdditionalCSS($_SERVER['HTTP_ORIGIN'].'/otus/vrachi-svyazyvanie-spiskov/css/style.css');

use Models\Lists\DoctorTable;
use Models\Lists\DoctorProcedureValuesPropertyTable as ProcedureTable;

$doctors = [];
$doctor = [];
$procs = [];

$path = trim($_GET['path'], '/');
$action = '';
$doctor_name = '';

$log = date('Y-m-d H:i:s') . ' ' . print_r($_POST, true);
file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);

if(!empty($path))
{
    $path_parts = explode('/', $path);
    if(sizeof($path_parts)<4)
    {
        if(sizeof($path_parts) == 3 && $path_parts[1] == 'edit')
        {
            $action = 'edit';
            $doctor_name = $path_parts[2];
        } else if(sizeof($path_parts)==2 && in_array($path_parts[1], ['new', 'newproc']))
        {
            $action = $path_parts[1];
        }else
        {
            $doctor_name = $path_parts[1];
        }
    }
}

if(!empty($doctor_name))
{
    $doctor = DoctorTable::query()
        ->setSelect([
            '*',
            'NAME' => 'ELEMENT.NAME',
            'PROCEDURE_ID',
            'ID' => 'ELEMENT.ID'
        ])
        ->where("NAME", $doctor_name)
        ->fetch();

        if(is_array($doctor))
        {
            if($doctor['PROCEDURE_ID'])
            {
                $procs = ProcedureTable::query()
                    ->setSelect([
                        'NAME' => 'ELEMENT.NAME'
                    ])
                    ->where("ELEMENT.ID", "in", $doctor['PROCEDURE_ID'])
                    ->fetchAll();
            }
        }
        else
        {
            header("Location: /otus/vrachi-svyazyvanie-spiskov/doctors");
            exit();
        }
}

if(empty($doctor_name) && empty($action))
{
    $doctors = DoctorTable::query()
        ->setSelect([
            '*',
            'NAME' => 'ELEMENT.NAME',
            'ID' => 'ELEMENT.ID'
        ])->fetchAll();
}

if($action == 'newproc')
{
    if(isset($_POST['proc-submit']))
    {
        unset($_POST['proc-submit']);
        if(ProcedureTable::add($_POST))
        {
            header("Location: /otus/vrachi-svyazyvanie-spiskov/doctors");
            exit();
        }
        else
        {
            echo"Ошибка";
        }
    }
}

if($action == 'new' || $action == 'edit')
{
    if(isset($_POST['doctor-submit']))
    {
        unset($_POST['doctor-submit']);
        if($action == 'edit' && !empty($_POST['ID']))
        {
            $ID = $_POST['ID'];
            unset($_POST['ID']);
            $_POST['IBLOCK_ELEMENT_ID']=$ID;

            $procs = $_POST['PROCEDURE_ID'];
            unset($_POST['PROCEDURE_ID']);
            CIBlockElement::SetPropertyValues($ID, DoctorTable::IBLOCK_ID, $procs, "PROCEDURE_ID");

            if(DoctorTable::update($_POST['ID'], $_POST))
            {
                header("Location: /otus/vrachi-svyazyvanie-spiskov/doctors");
                exit();
            }
            else
            {
                echo"Ошибка";
            }
        }
        if($action == 'new' && DoctorTable::add($_POST))
        {
            header("Location: /otus/vrachi-svyazyvanie-spiskov/doctors");
            exit();
        }
        else
        {
            echo"Ошибка";
        }
    }
    $proc_options = ProcedureTable::query()
        ->setSelect([
            'ID' => 'ELEMENT.ID',
            'NAME' => 'ELEMENT.NAME'
        ])->fetchAll();
    if(!empty($doctor_name))
    {
        $data = $doctor;
    }
}?>
<section class="doctors">
        <h1><a href="/otus/vrachi-svyazyvanie-spiskov/doctors">Врачи</a></h1>

        <div class="add-buttons">
            <?php if (empty($doctor_name)):?>
                <a href="/otus/vrachi-svyazyvanie-spiskov/doctors/new" class="btn btn-primary">
                    Добавить врача
                </a>
                <a href="/otus/vrachi-svyazyvanie-spiskov/doctors/newproc" class="btn btn-success">
                    Добавить процедуру
                </a>
            <?php else:?>
                <a href="/otus/vrachi-svyazyvanie-spiskov/doctors/edit/<?=$doctor_name?>" class="btn btn-warning">
                    Изменить данные врача
                </a>
            <?php endif;?>
        </div>

        <div class="cards-list">
            <?php foreach($doctors as $doc) {?>
                <a href="/otus/vrachi-svyazyvanie-spiskov/doctors/<?=$doc["NAME"]?>">
                    <div class="card">
                        <div class="name">
                            <?=$doc['DOCTOR_NAME']?>    
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>

        <?php if (is_array($doctor) && sizeof($doctor)>0 && $action != 'edit'):?>
            <div class="doctor-page">
                <h2><?=$doctor['DOCTOR_NAME']?></h2>
                <h3>Процедуры:</h3>
                <ul>
                    <?php foreach ($procs as $proc):?>
                        <li><?=$proc['NAME']?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if($action == 'new' || $action == 'edit'):?>
            <form method="POST">
                <h2>Данные врача</h2>
                <div class="doctor-add-form">
                    <?php if(isset($data['ID'])):?>
                        <input type="hidden" name="ID" value="<?=$data['ID']?>"/>
                    <?php endif; ?>

                    <input type="text" name="NAME" placeholder="Название страницы врача (фамилия латиницей)" value="<?=$data['NAME'] ?? ''?>"/>
                    <input type="text" name="DOCTOR_NAME" placeholder="ФИО врача" value="<?=$data['DOCTOR_NAME'] ?? ''?>"/>

                    <select multiple name="PROCEDURE_ID[]">
                        <option value="" selected disabled>Процедуры</option>
                        <?php foreach ($proc_options as $proc):?>
                        <option value="<?=$proc['ID']?>"
                                <?php if (isset($data['PROCEDURE_ID']) && in_array($proc['ID'], $data['PROCEDURE_ID'])):?>selected<?php endif;?>>
                            <?=$proc['NAME']?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="submit" name="doctor-submit" value="Сохранить"/>
                </div>
            </form>
        <?php endif; ?>

        <?php if($action =='newproc'):?>
            <form method="POST">
                <h2>Добавить процедуру</h2>
                <div class="doctor-add-form">
                    <input type="text" name="NAME" placeholder="Название процедуры"/>
                    <input type="submit" name="proc-submit" value="Сохранить"/>
                </div>
            </form>
        <?php endif; ?>
    </section>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>