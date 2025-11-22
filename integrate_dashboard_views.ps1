param(
  [Parameter(Position=0, Mandatory=$true)]
  [string]$InputFile,

  # Racine du projet (par défaut: répertoire courant)
  [string]$Root = ".",

  # Ne pas créer de .bak si le fichier existe déjà
  [switch]$NoBackup,

  # Ne pas lancer "php -l" sur les fichiers créés
  [switch]$NoLint
)

$ErrorActionPreference = 'Stop'

# Résolution des chemins
$inputFileFull = Resolve-Path -LiteralPath $InputFile
$rootFull     = Resolve-Path -LiteralPath $Root

Write-Host "Input ..........: $inputFileFull"
Write-Host "Project root ...: $rootFull"
Write-Host "Backup enabled..: $([bool](-not $NoBackup))"
Write-Host "PHP lint enabled: $([bool](-not $NoLint))"
Write-Host ""

if (-not (Test-Path $inputFileFull)) {
  throw "Input file not found: $InputFile"
}

# Préfixes autorisés pour éviter d'écrire hors src/
$allowedPrefixes = @('src/Entity/', 'src/Repository/')

function Test-AllowedPath([string]$relPath) {
  $relUnix = ($relPath -replace '\\','/').TrimStart('/')
  foreach ($p in $allowedPrefixes) {
    if ($relUnix.StartsWith($p)) { return $true }
  }
  return $false
}

# Split sur les sections: chaque section commence par "<?php" et contient un header "// FILE: path"
$lines = Get-Content -Path $inputFileFull -Encoding UTF8

$currentPath = $null
$buffer = New-Object System.Collections.Generic.List[string]
$files  = New-Object System.Collections.Generic.List[string]

function Flush-Section([string]$path, [System.Collections.Generic.List[string]]$buf) {
  if ([string]::IsNullOrWhiteSpace($path)) { return }

  # Normalise chemin relatif et sécurité
  $rel = $path.Trim()
  $rel = $rel -replace '^[\\/]+',''
  if (-not (Test-AllowedPath $rel)) {
    Write-Warning "Skipping unexpected path outside allowed prefixes: $rel"
    return
  }

  $outPath = Join-Path $rootFull ($rel -replace '/', [IO.Path]::DirectorySeparatorChar)
  $outDir  = Split-Path $outPath -Parent

  if (-not (Test-Path $outDir)) {
    New-Item -ItemType Directory -Path $outDir -Force | Out-Null
  }

  if (-not $NoBackup -and (Test-Path $outPath)) {
    Copy-Item -LiteralPath $outPath -Destination ($outPath + '.bak') -Force
  }

  # Ecriture UTF-8 sans BOM
  [IO.File]::WriteAllText($outPath, ($buf -join [Environment]::NewLine), [Text.UTF8Encoding]::new($false))
  $files.Add($outPath) | Out-Null
}

foreach ($line in $lines) {
  if ($line -match '^\s*<\?php\b') {
    # Nouveau bloc => flush de l'ancien si en cours
    if ($buffer.Count -gt 0 -and $null -ne $currentPath) {
      Flush-Section -path $currentPath -buf $buffer
      $buffer = New-Object System.Collections.Generic.List[string]
      $currentPath = $null
    }
  }

  # Détecte le header // FILE: ...
  if ($line -match '^\s*//\s*FILE:\s*(.+)$') {
    $currentPath = $matches[1].Trim()
  }

  $buffer.Add($line)
}

# Flush final
if ($buffer.Count -gt 0 -and $null -ne $currentPath) {
  Flush-Section -path $currentPath -buf $buffer
}

Write-Host "Created/updated $($files.Count) file(s):"
$files | ForEach-Object { Write-Host " - $_" }

# Lint PHP (optionnel)
if (-not $NoLint) {
  $php = Get-Command php -ErrorAction SilentlyContinue
  if ($php) {
    Write-Host "`nRunning PHP lint:"
    $ok = $true
    foreach ($f in $files) {
      $res = & $php.Source -l $f
      Write-Host " $res"
      if ($LASTEXITCODE -ne 0) { $ok = $false }
    }
    if (-not $ok) {
      Write-Warning "Some files did not pass 'php -l'. See messages above."
      exit 2
    }
  } else {
    Write-Warning "php.exe not found in PATH; skipping lint."
  }
}
