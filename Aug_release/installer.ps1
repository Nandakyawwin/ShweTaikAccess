# Set execution policy to allow script running
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser -Force

# Variables
$phpVersion = "8.2.6"
$phpZipUrl = "https://windows.php.net/downloads/releases/php-$phpVersion-Win32-vs16-x64.zip"
$mysqlInstallerUrl = "https://dev.mysql.com/get/Downloads/MySQLInstaller/mysql-installer-web-community-8.0.30.0.msi"
$phpIniUrl = "https://gist.githubusercontent.com/Nandakyawwin/bc5e9ca156bc4b37e475e0bf2f8f4fb8/raw/e03ec23397a5db03225412168dd9cb6378796fba/php.ini"
$gitRepoUrl = "https://github.com/Nandakyawwin/ShweTaikAccess.git"
$installDir = "C:\ShweTaikServer"
$htdocsDir = "$installDir\htdocs"

# Step 1: Install PHP
Write-Host "Installing PHP..."
$phpDir = "$installDir\php"
$phpZip = "$installDir\php.zip"
New-Item -Path $installDir -ItemType Directory -Force
Invoke-WebRequest -Uri $phpZipUrl -OutFile $phpZip
Expand-Archive -Path $phpZip -DestinationPath $phpDir
Remove-Item $phpZip

# Step 2: Install MySQL
Write-Host "Installing MySQL..."
$mysqlInstaller = "$installDir\mysql-installer.msi"
Invoke-WebRequest -Uri $mysqlInstallerUrl -OutFile $mysqlInstaller
Start-Process -Wait -FilePath msiexec.exe -ArgumentList "/i $mysqlInstaller /quiet /norestart"
Remove-Item $mysqlInstaller

# Step 3: Configure PHP
Write-Host "Configuring PHP..."
$phpIniPath = "$phpDir\php.ini"
Invoke-WebRequest -Uri $phpIniUrl -OutFile $phpIniPath

# Step 4: Set up PHP and MySQL as services
Write-Host "Setting up PHP and MySQL as services..."
$phpServiceName = "PHP"
$phpExePath = "$phpDir\php-cgi.exe"
New-Service -Name $phpServiceName -BinaryFilePath $phpExePath -StartupType Automatic

$mysqlServiceName = "MySQL80"
Start-Service -Name $mysqlServiceName
Set-Service -Name $mysqlServiceName -StartupType Automatic

# Step 5: Clone the GitHub repository
Write-Host "Cloning GitHub repository..."
git clone $gitRepoUrl $htdocsDir

# Move files from the cloned repo to htdocs
Get-ChildItem -Path "$htdocsDir\ShweTaikAccess" | Move-Item -Destination $htdocsDir -Force
Remove-Item -Recurse -Force "$htdocsDir\ShweTaikAccess"

Write-Host "PHP and MySQL installation and configuration completed."
