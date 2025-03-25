<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Código PHP Online</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #1e1e2f;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        header {
            background-color: #2b2b3d;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #444;
        }
        header h1 {
            font-size: 1.5rem;
            color: #61dafb;
        }
        textarea {
            flex: 1;
            width: 100%;
            padding: 20px;
            font-size: 16px;
            border: none;
            background-color: #1e1e2f;
            color: #ffffff;
            resize: none;
            outline: none;
        }
        .button-group {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        button {
            padding: 10px 20px;
            font-size: 14px;
            color: #ffffff;
            background-color: #61dafb;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }
        button:hover {
            background-color: #4a9ac9;
            transform: scale(1.05);
        }
        .output {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(30, 30, 47, 0.9);
            color: #ffffff;
            font-size: 16px;
            font-family: 'Courier New', Courier, monospace;
            white-space: pre-wrap;
            overflow-y: auto;
            padding: 20px;
            display: none;
            z-index: 1000;
        }
        .output.visible {
            display: block;
        }
        .output.error {
            color: #ff6b6b;
        }
    </style>
</head>
<body>
    <header>
        <h1>Editor de Código PHP Online</h1>
    </header>
    <form method="POST">
        <textarea name="code" id="code" placeholder="Digite seu código PHP aqui..."><?php echo isset($_POST['code']) ? htmlspecialchars($_POST['code']) : ''; ?></textarea>
        <div class="button-group">
            <button type="submit">Executar Código</button>
            <button type="button" onclick="clearCode()">Limpar</button>
        </div>
    </form>
    <div class="output" id="outputBox">
        <button style="position: absolute; top: 10px; right: 10px; background-color: #ff6b6b; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;" onclick="closeOutput()">Fechar</button>
        <div id="outputContent">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $code = $_POST['code'] ?? '';

                // Segurança básica: impedir funções perigosas
                $proibidos = ['exec', 'shell_exec', 'system', 'passthru', 'proc_open', 'popen', 'curl_exec', 'curl_multi_exec', 'parse_ini_file', 'show_source'];
                foreach ($proibidos as $funcao) {
                    if (stripos($code, $funcao) !== false) {
                        echo '<div class="output error">Erro: Uso de função proibida detectado!</div>';
                        exit;
                    }
                }

                // Criar um arquivo temporário
                $tempFile = tempnam(sys_get_temp_dir(), 'php_code_') . '.php';
                file_put_contents($tempFile, $code);

                // Capturar a saída
                ob_start();
                try {
                    include $tempFile;
                } catch (Throwable $e) {
                    echo '<div class="output error">Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
                $output = ob_get_clean();

                // Apagar o arquivo temporário
                unlink($tempFile);

                // Exibir a saída
                echo htmlspecialchars($output ?: 'Código executado sem saída.');
            }
            ?>
        </div>
    </div>

    <script>
        // Função para limpar o editor
        function clearCode() {
            document.getElementById('code').value = '';
        }

        // Função para fechar a saída
        function closeOutput() {
            document.getElementById('outputBox').classList.remove('visible');
        }

        // Mostrar a saída automaticamente (se existir)
        document.addEventListener('DOMContentLoaded', function () {
            const outputBox = document.getElementById('outputBox');
            if (outputBox.innerText.trim()) {
                outputBox.classList.add('visible');
            }
        });
    </script>
</body>
</html>