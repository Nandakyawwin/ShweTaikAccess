<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token");
header("Access-Control-Allow-Credentials: true");

$ComServer = null;
$OBJ = $_POST['DATA_OBJ'];

$payload = json_decode($OBJ, true);

if ($payload === null) {
    echo "Error decoding JSON payload.";
    exit;
}

if (isset($payload['TOKEN']['USERNAME'])) {
    $username = $payload['TOKEN']['USERNAME'];
} else {
    echo "USERNAME not found in the payload.";
    exit;
}

if (isset($payload['TOKEN']['PASSWORD'])) {
    $password = $payload['TOKEN']['PASSWORD'];
} else {
    echo "PASSWORD not found in the payload.";
    exit;
}

if (isset($payload['TABLE']['NAME'])) {
    $tableName = $payload['TABLE']['NAME'];
} else {
    echo "TABLE not found in the payload.";
    exit;
}

function CheckLogin() {
    global $ComServer, $username, $password;

    $ComServer = new COM("SQLAcc.BizApp") or die("Could not initialise SQLAcc.BizApp object.");
    $status = $ComServer->IsLogin();

    if ($status == true) {
        $ComServer->Logout();
    }

    $ComServer->Login($username, $password,
                      "C:\\eStream\\SQLAccounting\\Share\\Default.DCF",
                      "ACC-0001.FDB");
}

function PostData() {
    global $ComServer, $tableName;
    
    $BizObject = $ComServer->BizObjects->Find($tableName);
    $lMain = $BizObject->DataSets->Find("MainDataSet");
    $lDtl  = $BizObject->DataSets->Find("cdsBranch");

    global $CODE;
    global $CONTROLACCOUNT;
    global $COMPANYNAME;
    global $COMPANYNAME2;
    
    $lDocKey = $BizObject->FindKeyByRef("CODE", $CODE);
    
    if ($lDocKey == null) {
        $BizObject->New();
        $lMain->FindField("CODE")->value = $CODE;
        $lMain->FindField("COMPANYNAME")->value = $COMPANYNAME;
        $lMain->FindField("COMPANYNAME2")->value = $COMPANYNAME2;
        
        $lDtl->Edit();
        $lDtl->FindField("BranchName")->AsString  = "BILLING";
        $lDtl->FindField("Address1")->AsString    = "Address1";
        $lDtl->FindField("Address2")->AsString    = "Address2";
        $lDtl->FindField("Address3")->AsString    = "Address3";
        $lDtl->FindField("Address4")->AsString    = "Address4"; 
        $lDtl->FindField("Attention")->AsString   = "Attention"; 
        $lDtl->FindField("Phone1")->AsString      = "Phone1";
        $lDtl->FindField("Fax1")->AsString        = "Fax1";
        $lDtl->FindField("Email")->AsString       = "EmailAddress";
        $lDtl->Post();

        $lDtl->Append();
        $lDtl->FindField("BranchName")->AsString  = "Branch1";
        $lDtl->FindField("Address1")->AsString    = "DAddress1";
        $lDtl->FindField("Address2")->AsString    = "DAddress2";
        $lDtl->FindField("Address3")->AsString    = "DAddress3";
        $lDtl->FindField("Address4")->AsString    = "DAddress4";  
        $lDtl->FindField("Attention")->AsString   = "DAttention";  
        $lDtl->FindField("Phone1")->AsString      = "DPhone1";
        $lDtl->FindField("Fax1")->AsString        = "DFax1";
        $lDtl->FindField("Email")->AsString       = "DEmailAddress";
        $lDtl->Post();    
    } else {
        $BizObject->Params->Find("CODE")->Value = $lDocKey;
        $BizObject->Open();
        $BizObject->Edit();
        $lMain->FindField("CompanyName")->value = "FAIRY TAIL WIZARD";
        
        $r = $lDtl->RecordCount();
        $x = 1;
        while ($x <= $r) {
            $lDtl->First();
            $lDtl->Delete();
            $x++;
        }
        
        $lDtl->Edit();
        $lDtl->FindField("BranchName")->AsString  = "BILLING";
        $lDtl->FindField("Address1")->AsString    = "New Address1";
        $lDtl->FindField("Address2")->AsString    = "New Address2";
        $lDtl->FindField("Address3")->AsString    = "New Address3";
        $lDtl->FindField("Address4")->AsString    = "New Address4";  
        $lDtl->FindField("Attention")->AsString   = "New Attention";  
        $lDtl->FindField("Phone1")->AsString      = "New Phone1";
        $lDtl->FindField("Fax1")->AsString        = "New Fax1";
        $lDtl->FindField("Email")->AsString       = "New EmailAddress";
        $lDtl->Post();

        $lDtl->Append();
        $lDtl->FindField("BranchName")->AsString  = "Branch1";
        $lDtl->FindField("Address1")->AsString    = "New DAddress1";
        $lDtl->FindField("Address2")->AsString    = "New DAddress2";
        $lDtl->FindField("Address3")->AsString    = "New DAddress3";
        $lDtl->FindField("Address4")->AsString    = "New DAddress4"; 
        $lDtl->FindField("Attention")->AsString   = "New DAttention";  
        $lDtl->FindField("Phone1")->AsString      = "New DPhone1";
        $lDtl->FindField("Fax1")->AsString        = "New DFax1";
        $lDtl->FindField("Email")->AsString       = "New DEmailAddress";
        $lDtl->Post();    
    
    }
    try {
        $BizObject->Save();
        echo "Posting Done";
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}

try {
    CheckLogin();
    PostData();
    echo "Done";
} finally {
    if ($ComServer != null) {
        $ComServer->Logout();
        $ComServer = null;
    }
}
?>
