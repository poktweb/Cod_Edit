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
            background-color: var(--background-color, #1e1e2f);
            color: var(--text-color, #ffffff);
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        header {
            background-color: var(--header-background, #2b2b3d);
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
        header .settings {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        header select, header input {
            padding: 5px 10px;
            font-size: 14px;
            color: #ffffff;
            background-color: #444;
            border: 1px solid #666;
            border-radius: 5px;
        }
        textarea {
            flex: 1;
            width: 100%;
            padding: 20px;
            font-size: var(--font-size, 16px);
            font-family: var(--font-family, 'Courier New'), monospace;
            border: none;
            background-color: var(--editor-background, #1e1e2f);
            color: var(--editor-text-color, #ffffff);
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
        <div class="settings">
            <label for="theme">Tema:</label>
            <select id="theme" onchange="updateTheme()">
                <option value="dark">Escuro</option>
                <option value="light">Claro</option>
            </select>
            <label for="fontSize">Tamanho da Fonte:</label>
            <input type="number" id="fontSize" value="16" min="12" max="24" onchange="updateFontSize()" />
            <label for="fontFamily">Fonte:</label>
            <select id="fontFamily" onchange="updateFontFamily()">
                <option value="'Courier New', monospace">Courier New</option>
                <option value="monospace">Monospace</option>
                <option value="Arial, sans-serif">Arial</option>
            </select>
        </div>
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
        // Função para limpar o código
        function clearCode() {
            document.getElementById('code').value = '';
        }

        // Função para fechar o painel de saída
        function closeOutput() {
            document.getElementById('outputBox').classList.remove('visible');
        }

        // Função para aplicar o tema
        function updateTheme() {
            const theme = document.getElementById('theme').value;
            if (theme === 'light') {
                document.documentElement.style.setProperty('--background-color', '#ffffff');
                document.documentElement.style.setProperty('--text-color', '#000000');
                document.documentElement.style.setProperty('--header-background', '#f5f5f5');
                document.documentElement.style.setProperty('--editor-background', '#fdfdfd');
                document.documentElement.style.setProperty('--editor-text-color', '#000000');
            } else {
                document.documentElement.style.setProperty('--background-color', '#1e1e2f');
                document.documentElement.style.setProperty('--text-color', '#ffffff');
                document.documentElement.style.setProperty('--header-background', '#2b2b3d');
                document.documentElement.style.setProperty('--editor-background', '#1e1e2f');
                document.documentElement.style.setProperty('--editor-text-color', '#ffffff');
            }
        }

        // Função para alterar o tamanho da fonte
        function updateFontSize() {
            const fontSize = document.getElementById('fontSize').value + 'px';
            document.documentElement.style.setProperty('--font-size', fontSize);
        }

        // Função para alterar a fonte
        function updateFontFamily() {
            const fontFamily = document.getElementById('fontFamily').value;
            document.documentElement.style.setProperty('--font-family', fontFamily);
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