<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Código PHP Online</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f5f5f5;
        }
        h1 {
            color: #333;
        }
        textarea {
            width: 90%;
            max-width: 800px;
            height: 300px;
            margin-bottom: 20px;
            padding: 10px;
            font-size: 16px;
            font-family: monospace;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            resize: vertical;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .output {
            width: 90%;
            max-width: 800px;
            padding: 10px;
            margin-top: 20px;
            font-size: 16px;
            background-color: #e9ecef;
            border: 1px solid #ccc;
            border-radius: 5px;
            white-space: pre-wrap;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>Editor de Código PHP Online</h1>
    <form method="POST">
        <textarea name="code" placeholder="Digite seu código PHP aqui..."><?php echo isset($_POST['code']) ? htmlspecialchars($_POST['code']) : ''; ?></textarea>
        <br>
        <button type="submit">Executar Código</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $code = $_POST['code'] ?? '';

        // Segurança básica: impedir funções perigosas
        $proibidos = ['exec', 'shell_exec', 'system', 'passthru', 'proc_open', 'popen', 'curl_exec', 'curl_multi_exec', 'parse_ini_file', 'show_source'];
        foreach ($proibidos as $funcao) {
            if (stripos($code, $funcao) !== false) {
                echo '<div class="output">Uso de função proibida detectado!</div>';
                exit;
            }
        }

        // Executar o código usando eval
        echo '<div class="output">';
        try {
            eval($code);
        } catch (Throwable $e) {
            echo 'Erro: ' . htmlspecialchars($e->getMessage());
        }
        echo '</div>';
    }
    ?>
</body>
</html>