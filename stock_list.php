<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token");
header("Access-Control-Allow-Credentials: true");

$ComServer = null;

function CheckLogin()
{
    global $ComServer;
    $ComServer = new COM("SQLAcc.BizApp") or die("Could not initialise SQLAcc.BizApp object.");
    $status = $ComServer->IsLogin();

    if ($status == true)
    {
        $ComServer->Logout();
    }
    $ComServer->Login("ADMIN", "ADMIN", #UserName, Password
                      "C:\\eStream\\SQLAccounting\\Share\\Default.DCF", #DCF File 
                      "ACC-0001.FDB"); #Database Name
}

function GetTableNames()
{
    global $ComServer;

    $lSQL = "SELECT RDB\$RELATION_NAME FROM RDB\$RELATIONS WHERE RDB\$SYSTEM_FLAG = 0";

    $lDataSet = $ComServer->DBManager->NewDataSet($lSQL);

    if ($lDataSet->RecordCount > 0) {
        $lDataSet->First();
        $tableNames = array();
        
        while (!$lDataSet->Eof()) {
            $tableNames[] = trim($lDataSet->FindField('RDB$RELATION_NAME')->AsString());
            $lDataSet->Next();
        }
        
        echo json_encode($tableNames);
    } else {
        echo json_encode(array("message" => "No tables found"));
    }
}

function GetData($tableName)
{
    global $ComServer;
    
    $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
    $lSQL = "SELECT * FROM " . $tableName; 
    $lDataSet = $ComServer->DBManager->NewDataSet($lSQL);

    if ($lDataSet->RecordCount > 0) {
        $lDataSet->First();
        $fc = $lDataSet->Fields->Count - 1;
        $data = array();
    
        $fieldNames = array();
        for ($x = 0; $x <= $fc; $x++) {
            $fieldNames[] = $lDataSet->Fields->Items($x)->FieldName();
        }
    
        while (!$lDataSet->Eof()) {
            $record = array();
            for ($x = 0; $x <= $fc; $x++) {
                $lFN = $lDataSet->Fields->Items($x)->FieldName();
                $record[$lFN] = $lDataSet->FindField($lFN)->AsString();
            }
            $data[] = $record;
            $lDataSet->Next();
        }
    
        echo json_encode($data);
    } else {
        echo json_encode(array("message" => "Record Not Found"));
    }
}

if (isset($_POST['BtnData']))
{
    try
    {
        CheckLogin();
        if (isset($_POST['tableName'])) {
            GetData($_POST['tableName']);
        } else {
            GetTableNames();
        }
    }
    finally
    {
        $ComServer->Logout();
        $ComServer = null;
    }
}

?> 
