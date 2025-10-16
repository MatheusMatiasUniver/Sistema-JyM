$baseNome = "jymF"
$parteAtual = 1
$contadorArquivos = 0
$maxArquivos = 100

# Listas de exclusão personalizáveis
$ignorarPastas = @('node_modules', '.git', 'dist', 'build', 'bin', 'obj', 'vendor', 'public\models')
$ignorarArquivos = @('*.env', 'secrets.txt', 'temp_*', '*.png', '*.svg', '*.jpg','*.jpeg', '*.gif', 'jymF_arvore.txt', 'jymF_*.txt')

# Gerar árvore de diretórios inicial
tree /F /A | Out-File -FilePath "${baseNome}_arvore.txt" -Encoding UTF8

Get-ChildItem -Recurse -File | ForEach-Object {
    $caminhoCompleto = $_.FullName
    $caminhoRelativo = $caminhoCompleto.Replace("$pwd", "")
    
    # Verificar exclusões em pastas
    $excluir = $false
    foreach ($pasta in $ignorarPastas) {
        if ($caminhoRelativo -like "*\$pasta\*") {
            $excluir = $true
            break
        }
    }
    if ($excluir) { return }

    # Verificar exclusões em arquivos
    foreach ($padrao in $ignorarArquivos) {
        if ($_.Name -like $padrao) {
            $excluir = $true
            break
        }
    }
    if ($excluir) { return }

    # Gerenciar divisão por partes
    if ($contadorArquivos -ge $maxArquivos) {
        $parteAtual++
        $contadorArquivos = 0
    }

    # Criar novo arquivo de parte se necessário
    if ($contadorArquivos -eq 0) {
        $arquivoSaida = "${baseNome}_${parteAtual}.txt"
        "===== PARTE ${parteAtual} =====" | Out-File -FilePath $arquivoSaida -Encoding UTF8
    }

    # Escrever conteúdo com tratamento de erros
    try {
        "`n----- INÍCIO: ${caminhoRelativo} -----" | Out-File -FilePath $arquivoSaida -Append -Encoding UTF8
        Get-Content -Path $caminhoCompleto -ErrorAction Stop | Out-File -FilePath $arquivoSaida -Append -Encoding UTF8
        "`n----- FIM: ${caminhoRelativo} -----" | Out-File -FilePath $arquivoSaida -Append -Encoding UTF8
        $contadorArquivos++
    }
    catch {
        Write-Warning "Erro ao processar ${caminhoRelativo}: $_"
        "`n[ERRO] Não foi possível ler: ${caminhoRelativo}" | Out-File -FilePath $arquivoSaida -Append -Encoding UTF8
    }
}

Write-Host "Exportação concluída com sucesso!"
Write-Host "Arquivos gerados:"
Get-ChildItem "${baseNome}_parte_*.txt"