<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benvingut a la Botiga</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f0f0; margin: 0; }
        .container { text-align: center; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 2rem; color: #333; }
        .btn { display: inline-block; text-decoration: none; padding: 1rem 2rem; margin: 0 10px; border-radius: 4px; color: white; font-weight: bold; transition: background 0.3s; }
        .btn-client { background-color: #007bff; }
        .btn-client:hover { background-color: #0056b3; }
        .btn-worker { background-color: #28a745; }
        .btn-worker:hover { background-color: #1e7e34; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Benvingut</h1>
        <p>Com vols iniciar sessió?</p>
        <br>
        <a href="compra/login.php" class="btn btn-client">Sóc Client</a>
        <a href="gestio/app/login.php" class="btn btn-worker">Sóc Treballador</a>
    </div>
</body>
</html>