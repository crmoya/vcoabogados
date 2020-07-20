<?php

if(!@session_id()) session_start ();

set_time_limit(0);
require_once __DIR__ . '/vendor/autoload.php';

if(!es_consultor())die;

define('APPLICATION_NAME', 'Drive API PHP Quickstart');
define('CREDENTIALS_PATH', 'drive-php-quickstart.json');
define('BACKUP_PATH', 'backup.json');
define('CLIENT_SECRET_PATH', 'client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/drive-php-quickstart.json
define('SCOPES', implode(' ', array(
    Google_Service_Drive::DRIVE)
));
/*
  if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
  } */

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
    $client = new Google_Client();
    $client->setApplicationName(APPLICATION_NAME);
    $client->setScopes(SCOPES);
    $client->setAuthConfig(CLIENT_SECRET_PATH);
    $client->setAccessType ("offline");
    $client->setApprovalPrompt ("force");

    // Load previously authorized credentials from a file.
    $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
    if (file_exists($credentialsPath)) {
        $accessToken = json_decode(file_get_contents($credentialsPath), true);
    } else {
        // Request authorization from the user.
        $authUrl = $client->createAuthUrl();
        printf("Open the following link in your browser:\n%s\n", $authUrl);
        print 'Enter verification code: ';
        $authCode = trim(fgets(STDIN));

        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        // Store the credentials to disk.
        if (!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }
        file_put_contents($credentialsPath, json_encode($accessToken));
        printf("Credentials saved to %s\n", $credentialsPath);
    }
    $client->setAccessToken($accessToken);
    
    // Refresh the token if it's expired.
    if ($client->isAccessTokenExpired()) {
        $accessToken['refresh_token']=$client->getRefreshToken();
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $encode = json_encode($accessToken);
        $ready = str_replace("\\", "", $encode);
        file_put_contents($credentialsPath, $ready);
    }
    return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
    $homeDirectory = getenv('HOME');
    if (empty($homeDirectory)) {
        $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
    }
    return str_replace('~', realpath($homeDirectory), $path);
}

function listar_invitados(){
    $mysqli = new mysqli("localhost", "root", "", "mvscl_guiresse");
    if ($mysqli->connect_errno) {
        echo "Falló la conexión con MySQL: (" . 
                $mysqli->connect_errno . ") " . 
                $mysqli->connect_error;
    }
    else{
        $stmt = $mysqli->prepare("select * from invitados");
        $stmt->execute();
        $resultados = $stmt->get_result();
        $invitados = array();
        while ($fila = $resultados->fetch_assoc()) {
            $invitados[] = $fila['email'];
        }
        return $invitados;
    }
}

function es_consultor(){
    $mysqli = new mysqli("localhost", "root", "", "mvscl_guiresse");
    if ($mysqli->connect_errno) {
        echo "Falló la conexión con MySQL: (" . 
                $mysqli->connect_errno . ") " . 
                $mysqli->connect_error;
    }
    else{
        $stmt = $mysqli->prepare("select * from consultores where email = ?");
        $stmt->bind_param("s", $_SESSION['consultor']);
        $stmt->execute();
        $resultados = $stmt->get_result();
        while ($fila = $resultados->fetch_assoc()) {
            return true;
        }
        return false;
    }
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Drive($client);
    
if(isset($_GET['request'])){
    $peticion = $_GET['request'];
    
    if($peticion == 'permissions'){
        if(isset($_GET['id'])){
            $fileId = $_GET['id'];
            //permisos para los invitados
            $invitados = listar_invitados();
            foreach($invitados as $invitado){
                $userPermission = new Google_Service_Drive_Permission(array(
                    'type' => 'user',
                    'role' => 'writer',
                    'emailAddress' => $invitado
                ));
                $request = $service->permissions->create($fileId, $userPermission, array('fields' => 'id','sendNotificationEmail' => false,));                
            }
            //permisos para el consultor
            $userPermission = new Google_Service_Drive_Permission(array(
                'type' => 'user',
                'role' => 'writer',
                'emailAddress' => $_SESSION['consultor']
            ));
            $request = $service->permissions->create($fileId, $userPermission, array('fields' => 'id','sendNotificationEmail' => false,));                
            echo "OK";
        }
    }
    
    
    $nombreArchivo = "";
    if($peticion == 'uploadDoc'){
        $error = false;
        foreach($_FILES as $file)
        {
            move_uploaded_file($file['tmp_name'], "temp.docx");
            $nombreArchivo = basename($file['name']);
            break;
        }
        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => basename($nombreArchivo),
            'mimeType' => 'application/vnd.google-apps.document'));
        $content = file_get_contents("temp.docx");
        $file = $service->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'uploadType' => 'multipart',
            'fields' => 'id'));
        unlink("temp.docx");
    }
    
    if($peticion == 'uploadXls'){
        $error = false;
        foreach($_FILES as $file)
        {
            move_uploaded_file($file['tmp_name'], "temp.xlsx");
            $nombreArchivo = basename($file['name']);
            break;
        }
        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => basename($nombreArchivo),
            'mimeType' => 'application/vnd.google-apps.spreadsheet'));
        $content = file_get_contents("temp.xlsx");
        $file = $service->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'uploadType' => 'multipart',
            'fields' => 'id'));
        unlink("temp.xlsx");
    }
    
    if($peticion == 'delete'){
        if(isset($_GET['id'])){
            $fileId = $_GET['id'];
            $file = $service->files->delete($fileId);
        }
    }
    if($peticion == 'list'){
        $optParams = array(
            'fields' => 'nextPageToken, files(id, name, mimeType)'
        );
        
        $results = $service->files->listFiles($optParams);
        
        $files = array();
        foreach ($results->getFiles() as $file) {
            $fileId = $file->getId();
            $files[] = array('name'=>$file->getName(),'id'=>$file->getId(),'type'=> $file->getMimeType()); 
        }
        echo json_encode($files);
    }
    if($peticion == 'createXls'){
        if(isset($_GET['name'])){
            $nombre = $_GET['name'];
            $fileMetadata = new Google_Service_Drive_DriveFile(array(
                'name' => $nombre,
                'mimeType' => 'application/vnd.google-apps.spreadsheet'));
            $file = $service->files->create($fileMetadata, array('fields' => 'id'));
            echo "OK";
        }
        else{
            echo "ERROR";
        }
    }
    if($peticion == 'createDoc'){
        if(isset($_GET['name'])){
            $nombre = $_GET['name'];
            $fileMetadata = new Google_Service_Drive_DriveFile(array(
                'name' => $nombre,
                'mimeType' => 'application/vnd.google-apps.document'));
            $file = $service->files->create($fileMetadata, array( 'fields' => 'id'));
            echo "OK";
        }
        else{
            echo "ERROR";
        }
    }
}



/*

 */
