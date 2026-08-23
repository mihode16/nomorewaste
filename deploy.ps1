# Script de déploiement automatique NO MORE WASTE (Docker) — variante Windows PowerShell.
# Équivalent de deploy.sh, pour tester le déploiement en local avant de le lancer sur le
# serveur de production (Linux, via deploy.sh).
#
# Usage : .\deploy.ps1

$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Error "Docker n'est pas installé ou n'est pas dans le PATH."
    exit 1
}

if (-not (Test-Path .env)) {
    Write-Host "Aucun fichier .env trouve, creation a partir de .env.example (a personnaliser si besoin)."
    Copy-Item .env.example .env
}

Write-Host "Construction des images et demarrage des services..."
docker compose up -d --build
if ($LASTEXITCODE -ne 0) { exit 1 }

function Get-EnvValue($cle, $defaut) {
    $ligne = Get-Content .env | Where-Object { $_ -match "^$cle=" }
    if ($ligne) { return ($ligne -split '=', 2)[1] }
    return $defaut
}

$frontendPort = Get-EnvValue "HTTP_PORT_HOTE" "80"
$apiPort = Get-EnvValue "API_PORT_HOTE" "8081"

Write-Host "Attente que les services soient prets..."
$attenteMax = 90
$ecoule = 0
$apiPrete = $false
while (-not $apiPrete -and $ecoule -lt $attenteMax) {
    try {
        Invoke-WebRequest -Uri "http://localhost:$apiPort/api/health" -UseBasicParsing -TimeoutSec 3 | Out-Null
        $apiPrete = $true
    } catch {
        Start-Sleep -Seconds 3
        $ecoule += 3
    }
}
if (-not $apiPrete) {
    Write-Error "L'API Go ne repond toujours pas apres $attenteMax s."
    docker compose logs --tail=50 backend
    exit 1
}
Write-Host "API Go operationnelle."

$frontendPret = $false
$ecoule = 0
while (-not $frontendPret -and $ecoule -lt $attenteMax) {
    try {
        Invoke-WebRequest -Uri "http://localhost:$frontendPort/" -UseBasicParsing -TimeoutSec 3 | Out-Null
        $frontendPret = $true
    } catch {
        Start-Sleep -Seconds 3
        $ecoule += 3
    }
}
if (-not $frontendPret) {
    Write-Error "Le frontend ne repond toujours pas apres $attenteMax s."
    docker compose logs --tail=50 frontend
    exit 1
}
Write-Host "Frontend operationnel."

Write-Host ""
Write-Host "Deploiement termine."
Write-Host "  Site : http://localhost:$frontendPort/"
Write-Host "  API  : http://localhost:$apiPort/api/health"
Write-Host ""
Write-Host "Pour arreter : docker compose down"
Write-Host "Pour tout reinitialiser (y compris la base de donnees) : docker compose down -v"
